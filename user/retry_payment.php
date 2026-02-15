<?php
session_start();
require_once '../config/db.php';
require_once '../config/razorpay.php';
require_once '../includes/functions.php';

check_user_login();

if (!isset($_POST['booking_id'])) {
    redirect('my_bookings_new.php');
}

$booking_id = (int) $_POST['booking_id'];
$user_id = $_SESSION['user_id'];

// Fetch booking details
$stmt = $pdo->prepare("
    SELECT b.*, h.hall_name 
    FROM bookings b
    JOIN halls h ON b.hall_id = h.hall_id
    WHERE b.booking_id = :booking_id 
    AND b.user_id = :user_id
    AND b.booking_status = 'pending_payment'
    AND b.payment_status = 'pending'
");

$stmt->execute([
    ':booking_id' => $booking_id,
    ':user_id' => $user_id
]);

$booking = $stmt->fetch();

if (!$booking) {
    redirect_with_message('my_bookings_new.php', 'Booking not found or payment already completed.', 'error');
}

// Check if payment window hasn't expired
$created_time = strtotime($booking['created_at']);
$current_time = time();
$minutes_passed = ($current_time - $created_time) / 60;

if ($minutes_passed >= PAYMENT_TIMEOUT_MINUTES) {
    // Mark as failed
    $stmt = $pdo->prepare("UPDATE bookings SET booking_status = 'payment_failed', payment_status = 'failed'  WHERE booking_id = :booking_id");
    $stmt->execute([':booking_id' => $booking_id]);

    redirect_with_message('my_bookings_new.php', 'Payment window expired. Please create a new booking.', 'error');
}

// Check if Razorpay order exists, if not create new one
if (empty($booking['razorpay_order_id'])) {
    $receipt = 'booking_' . $booking_id . '_retry_' . time();
    $notes = [
        'booking_id' => $booking_id,
        'hall_name' => $booking['hall_name'],
        'user_id' => $user_id,
        'retry' => true
    ];

    $razorpay_order = createRazorpayOrder($booking['total_amount'], $receipt, $notes);

    if ($razorpay_order && isset($razorpay_order['id'])) {
        $stmt = $pdo->prepare("UPDATE bookings SET razorpay_order_id = :order_id WHERE booking_id = :booking_id");
        $stmt->execute([
            ':order_id' => $razorpay_order['id'],
            ':booking_id' => $booking_id
        ]);

        $booking['razorpay_order_id'] = $razorpay_order['id'];
    } else {
        redirect_with_message('my_bookings_new.php', 'Failed to create payment order. Please try again.', 'error');
    }
}

// Set session data for payment page
$_SESSION['pending_booking'] = [
    'booking_id' => $booking['booking_id'],
    'order_id' => $booking['razorpay_order_id'],
    'amount' => $booking['total_amount'],
    'hall_name' => $booking['hall_name'],
    'from_date' => $booking['booking_start_date'],
    'to_date' => $booking['booking_end_date'],
    'total_days' => $booking['total_days']
];

// Redirect to payment page
redirect('process_payment.php');
?>