<?php
session_start();
require_once '../config/db.php';
require_once '../config/razorpay.php';
require_once '../includes/functions.php';

// Check if user is logged in
check_user_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('dashboard.php');
}

$razorpay_payment_id = $_POST['razorpay_payment_id'] ?? '';
$razorpay_order_id = $_POST['razorpay_order_id'] ?? '';
$razorpay_signature = $_POST['razorpay_signature'] ?? '';
$booking_id = (int) ($_POST['booking_id'] ?? 0);

if (!$razorpay_payment_id || !$razorpay_order_id || !$razorpay_signature || !$booking_id) {
    redirect_with_message('my_bookings.php', 'Invalid payment data received.', 'error');
}

try {
    // Verify signature (CRITICAL SECURITY CHECK)
    if (!verifyRazorpaySignature($razorpay_order_id, $razorpay_payment_id, $razorpay_signature)) {
        // Log failed verification attempt
        log_audit('payment_verification_failed', 'bookings', $booking_id, 'Invalid signature');

        // Update booking status
        $stmt = $pdo->prepare("UPDATE bookings SET booking_status = 'payment_failed', payment_status = 'failed' WHERE booking_id = :booking_id");
        $stmt->execute([':booking_id' => $booking_id]);

        redirect_with_message('my_bookings.php', 'Payment verification failed. Please contact support.', 'error');
    }

    // Fetch payment details from Razorpay to double-confirm
    $paymentDetails = fetchPaymentDetails($razorpay_payment_id);

    if (!$paymentDetails || $paymentDetails['status'] !== 'captured') {
        log_audit('payment_not_captured', 'bookings', $booking_id, 'Payment not captured');
        redirect_with_message('my_bookings.php', 'Payment not confirmed. Please try again.', 'error');
    }

    // Begin transaction
    $pdo->beginTransaction();

    // Update booking with payment details
    $stmt = $pdo->prepare("
        UPDATE bookings 
        SET booking_status = 'confirmed',
            payment_status = 'paid',
            razorpay_payment_id = :payment_id,
            razorpay_signature = :signature,
            updated_at = NOW()
        WHERE booking_id = :booking_id
        AND razorpay_order_id = :order_id
        AND user_id = :user_id
    ");

    $result = $stmt->execute([
        ':payment_id' => $razorpay_payment_id,
        ':signature' => $razorpay_signature,
        ':booking_id' => $booking_id,
        ':order_id' => $razorpay_order_id,
        ':user_id' => $_SESSION['user_id']
    ]);

    if ($stmt->rowCount() === 0) {
        $pdo->rollBack();
        redirect_with_message('my_bookings.php', 'Booking not found or unauthorized.', 'error');
    }

    // Insert payment record
    $stmt = $pdo->prepare("
        INSERT INTO payments (booking_id, amount, payment_method, transaction_id, payment_date)
        VALUES (:booking_id, :amount, 'Razorpay', :transaction_id, NOW())
    ");

    $stmt->execute([
        ':booking_id' => $booking_id,
        ':amount' => $paymentDetails['amount'] / 100, // Convert from paise to rupees
        ':transaction_id' => $razorpay_payment_id
    ]);

    // Commit transaction
    $pdo->commit();

    // Log successful payment
    log_audit('payment_successful', 'bookings', $booking_id, "Payment ID: {$razorpay_payment_id}");

    // Clear pending booking from session
    unset($_SESSION['pending_booking']);

    // Redirect with success message
    redirect_with_message('booking_success.php?booking_id=' . $booking_id, 'Payment successful! Your booking is confirmed.', 'success');

} catch (Exception $e) {
    // Rollback on error
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    error_log("Payment verification error: " . $e->getMessage());
    log_audit('payment_error', 'bookings', $booking_id, $e->getMessage());

    redirect_with_message('my_bookings.php', 'An error occurred while processing your payment. Please contact support.', 'error');
}
?>