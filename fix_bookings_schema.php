<?php
include 'config/db.php';

// Disable foreign key checks to allow truncate
mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 0");

// 0. Truncate table to avoid data issues during alter
mysqli_query($conn, "TRUNCATE TABLE bookings");
echo "Cleared existing booking data.<br>";

// 1. Rename client_id to user_id
$check = mysqli_query($conn, "SHOW COLUMNS FROM bookings LIKE 'client_id'");
if (mysqli_num_rows($check) > 0) {
    mysqli_query($conn, "ALTER TABLE bookings CHANGE client_id user_id INT(11) NOT NULL");
    echo "Renamed 'client_id' to 'user_id'.<br>";
}

// 2. Add total_amount
$check = mysqli_query($conn, "SHOW COLUMNS FROM bookings LIKE 'total_amount'");
if (mysqli_num_rows($check) == 0) {
    mysqli_query($conn, "ALTER TABLE bookings ADD COLUMN total_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00");
    echo "Added 'total_amount'.<br>";
}

// 3. Add booking_start_date
$check = mysqli_query($conn, "SHOW COLUMNS FROM bookings LIKE 'booking_start_date'");
if (mysqli_num_rows($check) == 0) {
    mysqli_query($conn, "ALTER TABLE bookings ADD COLUMN booking_start_date DATE NOT NULL");
    echo "Added 'booking_start_date'.<br>";
}

// 4. Add booking_end_date
$check = mysqli_query($conn, "SHOW COLUMNS FROM bookings LIKE 'booking_end_date'");
if (mysqli_num_rows($check) == 0) {
    mysqli_query($conn, "ALTER TABLE bookings ADD COLUMN booking_end_date DATE NOT NULL");
    echo "Added 'booking_end_date'.<br>";
}

// 5. Add payment_status
$check = mysqli_query($conn, "SHOW COLUMNS FROM bookings LIKE 'payment_status'");
if (mysqli_num_rows($check) == 0) {
    mysqli_query($conn, "ALTER TABLE bookings ADD COLUMN payment_status ENUM('pending','paid') DEFAULT 'pending'");
    echo "Added 'payment_status'.<br>";
}

// 6. Fix booking_status
$check = mysqli_query($conn, "SHOW COLUMNS FROM bookings LIKE 'status'");
if (mysqli_num_rows($check) > 0) {
    mysqli_query($conn, "ALTER TABLE bookings CHANGE status booking_status ENUM('pending','confirmed','cancelled','completed') DEFAULT 'pending'");
    echo "Renamed/Modified 'status' to 'booking_status'.<br>";
}

// Re-enable foreign key checks
mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 1");

echo "<h3>Bookings Table Fixed</h3>";
?>
