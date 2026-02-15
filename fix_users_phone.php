<?php
include 'config/db.php';

// Check if 'phone' exists
$check = mysqli_query($conn, "SHOW COLUMNS FROM users LIKE 'phone'");
if (mysqli_num_rows($check) == 0) {
    // Add phone column
    $sql = "ALTER TABLE users ADD COLUMN phone VARCHAR(20) DEFAULT NULL AFTER password";
    if (mysqli_query($conn, $sql)) {
        echo "SUCCESS: Added 'phone' column.<br>";
    } else {
        echo "ERROR: Could not add 'phone'. " . mysqli_error($conn) . "<br>";
    }
} else {
    echo "INFO: 'phone' already exists.<br>";
}

echo "<h3>Users Table Update Complete</h3>";
?>
