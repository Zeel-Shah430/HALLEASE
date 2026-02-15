<?php
session_start();
require_once '../config/db.php';
require_once '../config/razorpay.php';
require_once '../includes/functions.php';

// Check if user is logged in
check_user_login();

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Auto-cleanup expired bookings
cleanup_expired_bookings();

// Get hall details if hall_id is provided
$hall = null;
if (isset($_GET['hall_id'])) {
    $hall_id = (int) $_GET['hall_id'];

    $stmt = $pdo->prepare("SELECT * FROM halls WHERE hall_id = :hall_id AND status = 'available'");
    $stmt->execute([':hall_id' => $hall_id]);
    $hall = $stmt->fetch();

    if (!$hall) {
        redirect_with_message('dashboard.php', 'Hall not found or unavailable.', 'error');
    }
}

// Handle booking submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_booking'])) {
    // Verify CSRF token
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token. Please try again.';
    } else {
        $hall_id = (int) $_POST['hall_id'];
        $from_date = clean_input($_POST['from_date']);
        $to_date = clean_input($_POST['to_date']);

        // Validate dates
        if (!validate_date($from_date) || !validate_date($to_date)) {
            $error = 'Invalid date format.';
        } elseif (is_past_date($from_date)) {
            $error = 'Cannot book halls for past dates.';
        } elseif (strtotime($to_date) < strtotime($from_date)) {
            $error = 'End date cannot be before start date.';
        } else {
            // Check for overlapping bookings (CRITICAL: Double booking prevention)
            if (check_date_overlap($hall_id, $from_date, $to_date)) {
                $error = 'This hall is already booked for the selected dates. Please choose different dates.';
            } else {
                // Get hall details
                $stmt = $pdo->prepare("SELECT * FROM halls WHERE hall_id = :hall_id AND status = 'available'");
                $stmt->execute([':hall_id' => $hall_id]);
                $hall = $stmt->fetch();

                if (!$hall) {
                    $error = 'Hall not found or unavailable.';
                } else {
                    // Calculate booking details
                    $total_days = calculate_days_difference($from_date, $to_date);
                    $price_per_day = $hall['price_per_day'];
                    $total_amount = $total_days * $price_per_day;

                    try {
                        // Start transaction
                        $pdo->beginTransaction();

                        // Create booking with pending_payment status
                        $stmt = $pdo->prepare("
                            INSERT INTO bookings 
                            (user_id, hall_id, booking_start_date, booking_end_date, total_days, price_per_day, total_amount, booking_status, payment_status, created_at)
                            VALUES 
                            (:user_id, :hall_id, :from_date, :to_date, :total_days, :price_per_day, :total_amount, 'pending_payment', 'pending', NOW())
                        ");

                        $stmt->execute([
                            ':user_id' => $user_id,
                            ':hall_id' => $hall_id,
                            ':from_date' => $from_date,
                            ':to_date' => $to_date,
                            ':total_days' => $total_days,
                            ':price_per_day' => $price_per_day,
                            ':total_amount' => $total_amount
                        ]);

                        $booking_id = $pdo->lastInsertId();

                        // Create Razorpay order
                        $receipt = 'booking_' . $booking_id . '_' . time();
                        $notes = [
                            'booking_id' => $booking_id,
                            'hall_name' => $hall['hall_name'],
                            'user_id' => $user_id
                        ];

                        $razorpay_order = createRazorpayOrder($total_amount, $receipt, $notes);

                        if ($razorpay_order && isset($razorpay_order['id'])) {
                            // Update booking with Razorpay order ID
                            $stmt = $pdo->prepare("UPDATE bookings SET razorpay_order_id = :order_id WHERE booking_id = :booking_id");
                            $stmt->execute([
                                ':order_id' => $razorpay_order['id'],
                                ':booking_id' => $booking_id
                            ]);

                            // Commit transaction
                            $pdo->commit();

                            // Log audit
                            log_audit('booking_created', 'bookings', $booking_id, "Booking created for hall: {$hall['hall_name']}");

                            // Store booking details in session for payment page
                            $_SESSION['pending_booking'] = [
                                'booking_id' => $booking_id,
                                'order_id' => $razorpay_order['id'],
                                'amount' => $total_amount,
                                'hall_name' => $hall['hall_name'],
                                'from_date' => $from_date,
                                'to_date' => $to_date,
                                'total_days' => $total_days
                            ];

                            // Redirect to payment page
                            redirect('process_payment.php');
                        } else {
                            $pdo->rollBack();
                            $error = 'Failed to create payment order. Please try again.';
                            log_audit('booking_failed', 'bookings', null, 'Razorpay order creation failed');
                        }
                    } catch (Exception $e) {
                        $pdo->rollBack();
                        $error = 'An error occurred while creating your booking. Please try again.';
                        error_log("Booking creation error: " . $e->getMessage());
                    }
                }
            }
        }
    }
}

// Fetch all available halls if no specific hall is selected
if (!$hall) {
    $stmt = $pdo->query("SELECT * FROM halls WHERE status = 'available' ORDER BY created_at DESC");
    $halls = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Your Perfect Hall - HallEase</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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

        .page-header p {
            font-size: 1.2rem;
            opacity: 0.9;
        }

        /* Alert Messages */
        .alert {
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-weight: 500;
            animation: slideDown 0.3s ease;
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.95);
            color: white;
            border-left: 4px solid #dc2626;
        }

        .alert-success {
            background: rgba(34, 197, 94, 0.95);
            color: white;
            border-left: 4px solid #16a34a;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Booking Form Card */
        .booking-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            margin-bottom: 30px;
        }

        .booking-card h2 {
            color: #1e293b;
            margin-bottom: 10px;
            font-size: 2rem;
        }

        .booking-card .subtitle {
            color: #64748b;
            margin-bottom: 30px;
        }

        .hall-info {
            background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
            padding: 20px;
            border-radius: 16px;
            margin-bottom: 30px;
            border-left: 4px solid #0ea5e9;
        }

        .hall-info h3 {
            color: #0369a1;
            margin-bottom: 15px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #475569;
        }

        .info-item i {
            color: #0ea5e9;
            font-size: 18px;
        }

        /* Form Styles */
        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #1e293b;
            font-weight: 600;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 14px 18px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 16px;
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s ease;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .price-summary {
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            padding: 20px;
            border-radius: 16px;
            margin-top: 20px;
            border-left: 4px solid #f59e0b;
        }

        .price-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            color: #78350f;
        }

        .price-row.total {
            font-size: 1.3rem;
            font-weight: 700;
            padding-top: 15px;
            border-top: 2px solid #fbbf24;
            color: #92400e;
        }

        .btn {
            padding: 14px 30px;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'Poppins', sans-serif;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(102, 126, 234, 0.6);
        }

        .btn-secondary {
            background: #e2e8f0;
            color: #475569;
        }

        .btn-secondary:hover {
            background: #cbd5e1;
        }

        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: white;
            text-decoration: none;
            margin-bottom: 20px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .back-link:hover {
            transform: translateX(-5px);
        }

        /* Halls Grid */
        .halls-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 30px;
        }

        .hall-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }

        .hall-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
        }

        .hall-image {
            height: 200px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            position: relative;
        }

        .price-tag {
            position: absolute;
            top: 15px;
            right: 15px;
            background: rgba(255, 255, 255, 0.95);
            padding: 10px 15px;
            border-radius: 20px;
            font-weight: 700;
            color: #667eea;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .hall-body {
            padding: 25px;
        }

        .hall-body h3 {
            color: #1e293b;
            margin-bottom: 15px;
            font-size: 1.3rem;
        }

        .hall-details {
            margin-bottom: 20px;
        }

        .detail-item {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
            color: #64748b;
        }

        .detail-item i {
            color: #667eea;
            width: 20px;
        }
    </style>
</head>

<body>
    <?php include '../includes/navbar.php'; ?>

    <div class="container">
        <a href="dashboard.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>

        <div class="page-header">
            <h1>Book Your Perfect Hall</h1>
            <p>Secure your venue with instant confirmation</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <?php if ($hall): ?>
            <!-- Single Hall Booking Form -->
            <div class="booking-card">
                <h2>Complete Your Booking</h2>
                <p class="subtitle">Fill in the details below to proceed with payment</p>

                <div class="hall-info">
                    <h3>
                        <?php echo htmlspecialchars($hall['hall_name']); ?>
                    </h3>
                    <div class="info-grid">
                        <div class="info-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>
                                <?php echo htmlspecialchars($hall['location'] . ', ' . $hall['city']); ?>
                            </span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-users"></i>
                            <span>Capacity:
                                <?php echo format_number($hall['capacity']); ?> guests
                            </span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-tag"></i>
                            <span>
                                <?php echo format_currency($hall['price_per_day']); ?> per day
                            </span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-check-circle"></i>
                            <span>Available</span>
                        </div>
                    </div>
                </div>

                <form method="POST" action="" id="bookingForm">
                    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                    <input type="hidden" name="hall_id" value="<?php echo $hall['hall_id']; ?>">

                    <div class="form-row">
                        <div class="form-group">
                            <label for="from_date"><i class="fas fa-calendar-alt"></i> From Date *</label>
                            <input type="date" id="from_date" name="from_date" required min="<?php echo date('Y-m-d'); ?>">
                        </div>

                        <div class="form-group">
                            <label for="to_date"><i class="fas fa-calendar-check"></i> To Date *</label>
                            <input type="date" id="to_date" name="to_date" required min="<?php echo date('Y-m-d'); ?>">
                        </div>
                    </div>

                    <div class="price-summary" id="priceSummary" style="display: none;">
                        <div class="price-row">
                            <span>Price per day:</span>
                            <span>
                                <?php echo format_currency($hall['price_per_day']); ?>
                            </span>
                        </div>
                        <div class="price-row">
                            <span>Total days:</span>
                            <span id="totalDays">0</span>
                        </div>
                        <div class="price-row total">
                            <span>Total Amount:</span>
                            <span id="totalAmount">₹0.00</span>
                        </div>
                    </div>

                    <div style="margin-top: 30px; display: flex; gap: 15px;">
                        <button type="submit" name="create_booking" class="btn btn-primary">
                            <i class="fas fa-lock"></i> Proceed to Payment
                        </button>
                        <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        <?php else: ?>
            <!-- Show all halls -->
            <div class="halls-grid">
                <?php foreach ($halls as $h): ?>
                    <div class="hall-card">
                        <div class="hall-image">
                            <div class="price-tag">
                                <?php echo format_currency($h['price_per_day']); ?>/day
                            </div>
                        </div>
                        <div class="hall-body">
                            <h3>
                                <?php echo htmlspecialchars($h['hall_name']); ?>
                            </h3>
                            <div class="hall-details">
                                <div class="detail-item">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span>
                                        <?php echo htmlspecialchars($h['location'] . ', ' . $h['city']); ?>
                                    </span>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-users"></i>
                                    <span>Up to
                                        <?php echo format_number($h['capacity']); ?> guests
                                    </span>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-check-circle"></i>
                                    <span>
                                        <?php echo htmlspecialchars($h['facilities']); ?>
                                    </span>
                                </div>
                            </div>
                            <a href="book_hall.php?hall_id=<?php echo $h['hall_id']; ?>" class="btn btn-primary"
                                style="width: 100%; justify-content: center;">
                                <i class="fas fa-calendar-check"></i> Book Now
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Price calculation
        const pricePerDay = <?php echo $hall['price_per_day'] ?? 0; ?>;
        const fromDateInput = document.getElementById('from_date');
        const toDateInput = document.getElementById('to_date');
        const priceSummary = document.getElementById('priceSummary');
        const totalDaysSpan = document.getElementById('totalDays');
        const totalAmountSpan = document.getElementById('totalAmount');

        function calculatePrice() {
            const fromDate = new Date(fromDateInput.value);
            const toDate = new Date(toDateInput.value);

            if (fromDate && toDate && toDate >= fromDate) {
                const diffTime = Math.abs(toDate - fromDate);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                const totalAmount = diffDays * pricePerDay;

                totalDaysSpan.textContent = diffDays;
                totalAmountSpan.textContent = '₹' + totalAmount.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                priceSummary.style.display = 'block';
            } else {
                priceSummary.style.display = 'none';
            }
        }

        if (fromDateInput && toDateInput) {
            fromDateInput.addEventListener('change', calculatePrice);
            toDateInput.addEventListener('change', calculatePrice);

            // Set minimum date for to_date based on from_date
            fromDateInput.addEventListener('change', function () {
                toDateInput.min = this.value;
                if (toDateInput.value && toDateInput.value < this.value) {
                    toDateInput.value = this.value;
                }
                calculatePrice();
            });
        }
    </script>
</body>

</html>