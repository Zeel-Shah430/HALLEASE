<?php
include 'config/db.php';

// Drop legacy columns that might cause insert errors
$columns_to_drop = ['booking_date', 'time_slot', 'event_type'];

foreach ($columns_to_drop as $col) {
    $check = mysqli_query($conn, "SHOW COLUMNS FROM bookings LIKE '$col'");
    if (mysqli_num_rows($check) > 0) {
        if (mysqli_query($conn, "ALTER TABLE bookings DROP COLUMN $col")) {
            echo "Dropped legacy column '$col'.<br>";
        } else {
            echo "Error dropping '$col': " . mysqli_error($conn) . "<br>";
        }
    }
}

echo "<h3>Cleanup Complete</h3>";
?>
