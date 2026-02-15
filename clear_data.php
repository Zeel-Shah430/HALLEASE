<?php
include 'config/db.php';

// Disable FK checks to allow clearing parent tables
mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 0");

// Truncate tables
if (mysqli_query($conn, "TRUNCATE TABLE halls")) {
    echo "All Halls removed.<br>";
}
if (mysqli_query($conn, "TRUNCATE TABLE hall_owners")) {
    echo "All Hall Owners removed.<br>";
}
if (mysqli_query($conn, "TRUNCATE TABLE bookings")) {
    echo "All Bookings removed.<br>";
}
if (mysqli_query($conn, "TRUNCATE TABLE payments")) {
    echo "All Payments removed.<br>";
}

// Enable FK checks
mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 1");

echo "<h3>Cleanup Successful</h3>";
echo "You can now add fresh halls from the Admin Panel.";
?>
