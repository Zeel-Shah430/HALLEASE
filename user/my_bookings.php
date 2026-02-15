<?php
session_start();
require_once '../config/db.php';
require_once '../config/razorpay.php';
require_once '../includes/functions.php';

check_user_login();

$user_id = $_SESSION['user_id'];
$success = "";
$error = "";

// Auto-cleanup expired bookings
cleanup_expired_bookings();

// Handle cancellation
if (isset($_POST['cancel_booking'])) {
    $booking_id = clean_input($_POST['booking_id']);

    // Check if booking belongs to user and is pending or confirmed
    $check_query = "SELECT * FROM bookings WHERE booking_id = '$booking_id' AND user_id = '$user_id' AND booking_status IN ('pending_payment', 'confirmed')";
    $check_res = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_res) > 0) {
        $booking = mysqli_fetch_assoc($check_res);

        // Refund logic (if paid) could go here...

        $update_query = "UPDATE bookings SET booking_status = 'cancelled' WHERE booking_id = '$booking_id'";
        if (mysqli_query($conn, $update_query)) {
            $success = "Booking #$booking_id cancelled successfully.";
        } else {
            $error = "Failed to cancel booking.";
        }
    } else {
        $error = "Invalid booking or cannot cancel this booking.";
    }
}

// Fetch Cancelled Bookings Count
$cancelled_count_q = "SELECT COUNT(*) as count FROM bookings WHERE user_id = '$user_id' AND booking_status = 'cancelled'";
$cancelled_res = mysqli_query($conn, $cancelled_count_q);
$cancelled_count = mysqli_fetch_assoc($cancelled_res)['count'];

// Fetch Confirmed Bookings Count
$confirmed_count_q = "SELECT COUNT(*) as count FROM bookings WHERE user_id = '$user_id' AND booking_status = 'confirmed'";
$confirmed_res = mysqli_query($conn, $confirmed_count_q);
$confirmed_count = mysqli_fetch_assoc($confirmed_res)['count'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings - HallEase</title>
    <link href="../assets/css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600&display=swap"
        rel="stylesheet">
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar">
        <div class="container nav-container">
            <a href="dashboard.php" class="navbar-brand">HallEase.</a>
            <div class="nav-links">
                <a href="dashboard.php" class="nav-link">Home</a>
                <a href="book_hall.php" class="nav-link">Halls</a>
                <a href="my_bookings.php" class="nav-link active">My Bookings</a>
                <div class="nav-user">
                    <span style="font-weight: 500; color: var(--dark);">Hi,
                        <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?></span>
                    <a href="logout.php" class="btn btn-secondary btn-sm">Logout</a>
                </div>
            </div>
            <div class="mobile-menu-btn"><i class="fas fa-bars"></i></div>
        </div>
    </nav>

    <div class="container section">
        <div class="d-flex justify-between align-center mb-20">
            <h1>My Bookings</h1>
            <a href="book_hall.php" class="btn btn-primary"><i class="fas fa-plus"></i> New Booking</a>
        </div>

        <!-- Stats Summary -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background: var(--success);">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $confirmed_count; ?></h3>
                    <p>Confirmed Bookings</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: var(--danger);">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $cancelled_count; ?></h3>
                    <p>Cancelled Bookings</p>
                </div>
            </div>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Bookings List -->
        <h3 class="mb-20">Booking History</h3>

        <div style="display: flex; flex-direction: column; gap: 20px;">
            <?php
            $query = "SELECT b.*, h.hall_name, h.location, h.images 
                  FROM bookings b 
                  JOIN halls h ON b.hall_id = h.hall_id 
                  WHERE b.user_id = '$user_id' 
                  ORDER BY b.created_at DESC";
            $result = mysqli_query($conn, $query);

            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $status_class = '';
                    $status_label = '';

                    switch ($row['booking_status']) {
                        case 'confirmed':
                            $status_class = 'badge-success';
                            $status_label = 'Confirmed';
                            break;
                        case 'pending_payment':
                            $status_class = 'badge-warning';
                            $status_label = 'Pending Payment';
                            break;
                        case 'cancelled':
                            $status_class = 'badge-danger';
                            $status_label = 'Cancelled';
                            break;
                        case 'completed':
                            $status_class = 'badge-info';
                            $status_label = 'Completed';
                            break;
                        default:
                            $status_class = 'badge-secondary';
                            $status_label = ucfirst($row['booking_status']);
                    }

                    $images = json_decode($row['images'], true);
                    $first_image = (!empty($images) && is_array($images)) ? $images[0] : 'default_hall.jpg';

                    if (filter_var($first_image, FILTER_VALIDATE_URL)) {
                        $img_src = $first_image;
                    } else {
                        $img_src = '../assets/images/halls/' . $first_image;
                    }

                    // Format dates
                    $start = date('d M Y', strtotime($row['booking_start_date']));
                    $end = date('d M Y', strtotime($row['booking_end_date']));
                    ?>
                    <div class="hall-card" style="display: flex; flex-direction: row; align-items: stretch; min-height: 180px;">
                        <div
                            style="width: 250px; flex-shrink: 0; position: relative; overflow: hidden; border-radius: var(--radius-lg) 0 0 var(--radius-lg);">
                            <img src="<?php echo htmlspecialchars($img_src); ?>"
                                style="width: 100%; height: 100%; object-fit: cover;"
                                onerror="this.src='https://via.placeholder.com/300x200?text=No+Image'">
                        </div>
                        <div class="card-body"
                            style="flex: 1; display: flex; flex-direction: column; justify-content: space-between;">
                            <div class="d-flex justify-between">
                                <div>
                                    <h3 class="card-title" style="margin-bottom: 5px;">
                                        <?php echo htmlspecialchars($row['hall_name']); ?>
                                    </h3>
                                    <p style="color: var(--text-secondary); font-size: 0.9rem;">
                                        <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($row['location']); ?>
                                    </p>
                                </div>
                                <div class="text-right">
                                    <div class="card-price" style="font-size: 1.25rem;">
                                        <?php echo format_currency($row['total_amount']); ?>
                                    </div>
                                    <span class="badge <?php echo $status_class; ?>"><?php echo $status_label; ?></span>
                                </div>
                            </div>

                            <div
                                style="margin: 15px 0; background: var(--gray-100); padding: 10px 15px; border-radius: var(--radius-md); display: inline-flex; gap: 20px;">
                                <div>
                                    <span class="text-secondary" style="font-size: 0.8rem; display: block;">Check-in</span>
                                    <strong><?php echo $start; ?></strong>
                                </div>
                                <div style="border-left: 1px solid var(--gray-300); padding-left: 20px;">
                                    <span class="text-secondary" style="font-size: 0.8rem; display: block;">Check-out</span>
                                    <strong><?php echo $end; ?></strong>
                                </div>
                                <div style="border-left: 1px solid var(--gray-300); padding-left: 20px;">
                                    <span class="text-secondary" style="font-size: 0.8rem; display: block;">Duration</span>
                                    <strong><?php echo $row['total_days']; ?> Days</strong>
                                </div>
                            </div>

                            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                                <?php if ($row['booking_status'] == 'pending_payment'): ?>
                                    <a href="retry_payment.php?booking_id=<?php echo $row['booking_id']; ?>"
                                        class="btn btn-primary btn-sm">
                                        <i class="fas fa-credit-card"></i> Pay Now
                                    </a>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="booking_id" value="<?php echo $row['booking_id']; ?>">
                                        <button type="submit" name="cancel_booking" class="btn btn-danger btn-sm"
                                            onclick="return confirm('Are you sure you want to cancel?')">Cancel</button>
                                    </form>
                                <?php elseif ($row['booking_status'] == 'confirmed'): ?>
                                    <a href="invoice.php?booking_id=<?php echo $row['booking_id']; ?>"
                                        class="btn btn-secondary btn-sm" target="_blank">
                                        <i class="fas fa-file-invoice"></i> Valid Invoice
                                    </a>
                                    <?php if (strtotime($row['booking_start_date']) > time()): ?>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="booking_id" value="<?php echo $row['booking_id']; ?>">
                                            <button type="submit" name="cancel_booking" class="btn btn-secondary btn-sm"
                                                onclick="return confirm('Are you sure you want to cancel?')">Cancel</button>
                                        </form>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo '<div class="alert alert-info" style="background: var(--white); border: 1px solid var(--gray-200);">No bookings found. <a href="book_hall.php">Book your first hall!</a></div>';
            }
            ?>
        </div>
    </div>

    <style>
        @media (max-width: 768px) {
            .hall-card {
                flex-direction: column !important;
            }

            .hall-card>div:first-child {
                width: 100% !important;
                height: 180px !important;
            }
        }
    </style>

</body>

</html>