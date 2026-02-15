<?php
session_start();
require_once '../config/db.php';
require_once '../includes/functions.php';

check_user_login();

// Auto-cleanup expired bookings
cleanup_expired_bookings();

$user_id = $_SESSION['user_id'];

// Handle booking cancellation
if (isset($_POST['cancel_booking'])) {
    if (verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $booking_id = (int) $_POST['booking_id'];

        $stmt = $pdo->prepare("
            SELECT * FROM bookings 
            WHERE booking_id = :booking_id 
            AND user_id = :user_id
            AND booking_status NOT IN ('cancelled', 'completed')
        ");
        $stmt->execute([':booking_id' => $booking_id, ':user_id' => $user_id]);
        $booking = $stmt->fetch();

        if ($booking && !is_past_date($booking['booking_start_date'])) {
            $stmt = $pdo->prepare("
                UPDATE bookings 
                SET booking_status = 'cancelled',
                    updated_at = NOW()
                WHERE booking_id = :booking_id AND user_id = :user_id
            ");
            $stmt->execute([':booking_id' => $booking_id, ':user_id' => $user_id]);

            log_audit('booking_cancelled', 'bookings', $booking_id, 'User cancelled booking');
            redirect_with_message('my_bookings.php', 'Booking cancelled successfully.', 'success');
        }
    }
}

// Fetch all user bookings
$stmt = $pdo->prepare("
    SELECT b.*, h.hall_name, h.location,h.city, h.capacity, h.facilities
    FROM bookings b
    JOIN halls h ON b.hall_id = h.hall_id
    WHERE b.user_id = :user_id
    ORDER BY b.created_at DESC
");
$stmt->execute([':user_id' => $user_id]);
$bookings = $stmt->fetchAll();

// Calculate statistics
$total_bookings = count($bookings);
$confirmed_bookings = count(array_filter($bookings, fn($b) => $b['booking_status'] === 'confirmed'));
$pending_bookings = count(array_filter($bookings, fn($b) => $b['booking_status'] === 'pending_payment'));
$total_spent = array_sum(array_map(fn($b) => $b['booking_status'] === 'confirmed' ? $b['total_amount'] : 0, $bookings));
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings - HallEase</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <?php include '../includes/navbar.php'; ?>

    <div class="container">
        <div class="page-header">
            <h1>My Bookings</h1>
            <p>Manage all your hall reservations</p>
        </div>

        <?php
        $flash = get_flash_message();
        if ($flash):
            ?>
            <div class="alert alert-<?php echo $flash['type']; ?>">
                <i class="fas fa-<?php echo $flash['type'] === 'success' ? 'check' : 'exclamation'; ?>-circle"></i>
                <?php echo $flash['message']; ?>
            </div>
        <?php endif; ?>

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #3b82f6, #2563eb);">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-info">
                    <h3>
                        <?php echo $total_bookings; ?>
                    </h3>
                    <p>Total Bookings</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-info">
                    <h3>
                        <?php echo $confirmed_bookings; ?>
                    </h3>
                    <p>Confirmed</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-info">
                    <h3>
                        <?php echo $pending_bookings; ?>
                    </h3>
                    <p>Pending Payment</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
                    <i class="fas fa-rupee-sign"></i>
                </div>
                <div class="stat-info">
                    <h3>
                        <?php echo format_currency($total_spent); ?>
                    </h3>
                    <p>Total Spent</p>
                </div>
            </div>
        </div>

        <!-- Bookings List -->
        <div class="bookings-section">
            <?php if (empty($bookings)): ?>
                <div class="empty-state">
                    <i class="fas fa-calendar-times"></i>
                    <h3>No Bookings Yet</h3>
                    <p>You haven't made any bookings. Start exploring our amazing halls!</p>
                    <a href="book_hall_new.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Book Your First Hall
                    </a>
                </div>
            <?php else: ?>
                <?php foreach ($bookings as $booking): ?>
                    <div class="booking-card status-<?php echo $booking['booking_status']; ?>">
                        <div class="booking-header">
                            <div class="booking-info">
                                <h3>
                                    <?php echo htmlspecialchars($booking['hall_name']); ?>
                                </h3>
                                <p class="booking-id">Booking #
                                    <?php echo str_pad($booking['booking_id'], 6, '0', STR_PAD_LEFT); ?>
                                </p>
                            </div>
                            <div class="booking-status">
                                <?php
                                $statusClasses = [
                                    'confirmed' => 'success',
                                    'pending_payment' => 'warning',
                                    'cancelled' => 'danger',
                                    'payment_failed' => 'danger',
                                    'completed' => 'info'
                                ];
                                $statusClass = $statusClasses[$booking['booking_status']] ?? 'secondary';
                                ?>
                                <span class="badge badge-<?php echo $statusClass; ?>">
                                    <?php echo ucwords(str_replace('_', ' ', $booking['booking_status'])); ?>
                                </span>
                            </div>
                        </div>

                        <div class="booking-details">
                            <div class="detail-grid">
                                <div class="detail-item">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <div>
                                        <span class="label">Location</span>
                                        <span class="value">
                                            <?php echo htmlspecialchars($booking['location'] . ', ' . $booking['city']); ?>
                                        </span>
                                    </div>
                                </div>

                                <div class="detail-item">
                                    <i class="fas fa-calendar-alt"></i>
                                    <div>
                                        <span class="label">From</span>
                                        <span class="value">
                                            <?php echo format_date($booking['booking_start_date']); ?>
                                        </span>
                                    </div>
                                </div>

                                <div class="detail-item">
                                    <i class="fas fa-calendar-check"></i>
                                    <div>
                                        <span class="label">To</span>
                                        <span class="value">
                                            <?php echo format_date($booking['booking_end_date']); ?>
                                        </span>
                                    </div>
                                </div>

                                <div class="detail-item">
                                    <i class="fas fa-clock"></i>
                                    <div>
                                        <span class="label">Duration</span>
                                        <span class="value">
                                            <?php echo $booking['total_days']; ?> day(s)
                                        </span>
                                    </div>
                                </div>

                                <div class="detail-item">
                                    <i class="fas fa-money-bill-wave"></i>
                                    <div>
                                        <span class="label">Total Amount</span>
                                        <span class="value">
                                            <?php echo format_currency($booking['total_amount']); ?>
                                        </span>
                                    </div>
                                </div>

                                <div class="detail-item">
                                    <i class="fas fa-credit-card"></i>
                                    <div>
                                        <span class="label">Payment</span>
                                        <span class="value">
                                            <?php
                                            $paymentClass = $booking['payment_status'] === 'paid' ? 'success' : 'warning';
                                            echo '<span class="badge badge-' . $paymentClass . '">' . ucfirst($booking['payment_status']) . '</span>';
                                            ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="booking-actions">
                            <?php if ($booking['booking_status'] === 'pending_payment' && $booking['payment_status'] === 'pending'): ?>
                                <?php
                                // Check if booking hasn't expired
                                $created_time = strtotime($booking['created_at']);
                                $current_time = time();
                                $minutes_passed = ($current_time - $created_time) / 60;

                                if ($minutes_passed < PAYMENT_TIMEOUT_MINUTES):
                                    ?>
                                    <form method="POST" action="retry_payment.php" style="display: inline;">
                                        <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-primary">
                                            <i class="fas fa-credit-card"></i> Complete Payment
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <span class="badge badge-danger">Payment Expired</span>
                                <?php endif; ?>
                            <?php endif; ?>

                            <?php if ($booking['booking_status'] === 'confirmed' && !is_past_date($booking['booking_start_date'])): ?>
                                <form method="POST" action=""
                                    onsubmit="return confirm('Are you sure you want to cancel this booking?');">
                                    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                    <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id']; ?>">
                                    <button type="submit" name="cancel_booking" class="btn btn-sm btn-danger">
                                        <i class="fas fa-times-circle"></i> Cancel Booking
                                    </button>
                                </form>
                            <?php endif; ?>

                            <?php if ($booking['booking_status'] === 'confirmed'): ?>
                                <a href="download_invoice.php?booking_id=<?php echo $booking['booking_id']; ?>"
                                    class="btn btn-sm btn-secondary">
                                    <i class="fas fa-download"></i> Download Invoice
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .page-header {
            text-align: center;
            color: white;
            margin-bottom: 40px;
        }

        .page-header h1 {
            font-size: 3rem;
            margin-bottom: 10px;
            text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }

        .alert {
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-weight: 500;
            animation: slideDown 0.3s ease;
        }

        .alert-success {
            background: rgba(34, 197, 94, 0.95);
            color: white;
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.95);
            color: white;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.95);
            padding: 25px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            gap: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
        }

        .stat-info h3 {
            font-size: 2rem;
            color: #1e293b;
        }

        .stat-info p {
            color: #64748b;
        }

        .booking-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 16px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            border-left: 4px solid #667eea;
        }

        .booking-card.status-confirmed {
            border-left-color: #10b981;
        }

        .booking-card.status-cancelled {
            border-left-color: #ef4444;
        }

        .booking-card.status-pending_payment {
            border-left-color: #f59e0b;
        }

        .booking-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f1f5f9;
        }

        .booking-info h3 {
            color: #1e293b;
            margin-bottom: 5px;
        }

        .booking-id {
            color: #64748b;
            font-size: 14px;
        }

        .badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
        }

        .badge-success {
            background: #10b981;
            color: white;
        }

        .badge-warning {
            background: #f59e0b;
            color: white;
        }

        .badge-danger {
            background: #ef4444;
            color: white;
        }

        .badge-info {
            background: #3b82f6;
            color: white;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .detail-item {
            display: flex;
            gap: 12px;
        }

        .detail-item i {
            color: #667eea;
            font-size: 18px;
            margin-top: 2px;
        }

        .detail-item .label {
            display: block;
            color: #64748b;
            font-size: 13px;
        }

        .detail-item .value {
            display: block;
            color: #1e293b;
            font-weight: 600;
        }

        .booking-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            padding-top: 15px;
            border-top: 2px solid #f1f5f9;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'Poppins', sans-serif;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-sm {
            padding: 8px 16px;
            font-size: 14px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }

        .btn-secondary {
            background: #e2e8f0;
            color: #475569;
        }

        .btn-danger {
            background: #ef4444;
            color: white;
        }

        .empty-state {
            text-align: center;
            padding: 80px 20px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 16px;
        }

        .empty-state i {
            font-size: 80px;
            color: #cbd5e1;
            margin-bottom: 20px;
        }

        .empty-state h3 {
            color: #1e293b;
            margin-bottom: 10px;
        }

        .empty-state p {
            color: #64748b;
            margin-bottom: 30px;
        }
    </style>
</body>

</html>