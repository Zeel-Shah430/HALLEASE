<?php
include '../includes/auth.php';
include '../config/db.php';

$id = $_SESSION['user_id'];
$q = "SELECT * FROM bookings WHERE client_id=$id";
$res = mysqli_query($conn, $q);

while ($row = mysqli_fetch_assoc($res)) {
    echo "Booking ID: {$row['booking_id']} - Status: {$row['status']}<br>";
}
