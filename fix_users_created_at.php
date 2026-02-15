<?php
include 'config/db.php';

// Fix 'users' table: Add 'created_at'
$check = mysqli_query($conn, "SHOW COLUMNS FROM users LIKE 'created_at'");
if (mysqli_num_rows($check) == 0) {
    if (mysqli_query($conn, "ALTER TABLE users ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP")) {
        echo "SUCCESS: Added 'created_at' to 'users'.<br>";
    } else {
        echo "ERROR: Could not add 'created_at': " . mysqli_error($conn) . "<br>";
    }
} else {
    echo "INFO: 'created_at' already exists in 'users'.<br>";
}

echo "<h3>Users Table Schema Update Complete</h3>";
?>
