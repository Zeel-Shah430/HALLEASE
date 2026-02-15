<?php
session_start();
include 'config/db.php';
include 'includes/functions.php';

check_user_login();

if (isset($_POST['book_now'])) {
    $user_id = $_SESSION['user_id'];
    $hall_id = clean_input($_POST['hall_id']);
    $start_date = clean_input($_POST['start_date']);
    $end_date = clean_input($_POST['end_date']);
    $price_per_day = clean_input($_POST['price']);
    
    // Calculate days
    $start = new DateTime($start_date);
    $end = new DateTime($end_date);
    
    if ($start > $end) {
        die("Invalid date range");
    }
    
    $days = $end->diff($start)->format("%a") + 1;
    $total_amount = $days * $price_per_day;
    
    // Create Booking
    $query = "INSERT INTO bookings (user_id, hall_id, booking_start_date, booking_end_date, total_amount, booking_status, payment_status) 
              VALUES ('$user_id', '$hall_id', '$start_date', '$end_date', '$total_amount', 'pending', 'pending')";
              
    if (mysqli_query($conn, $query)) {
        $booking_id = mysqli_insert_id($conn);
        
        // Mock Payment (Auto-create payment entry for simplicity if desired, or leave pending)
        // For this demo, let's redirect to bookings page with success
        redirect('my_bookings.php?success=1');
    } else {
        die("Error booking hall: " . mysqli_error($conn));
    }
} else {
    redirect('index.php');
}
?>
