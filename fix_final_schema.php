<?php
include 'config/db.php';

// 1. Fix 'users' table: Rename 'name' to 'full_name'
$check = mysqli_query($conn, "SHOW COLUMNS FROM users LIKE 'name'");
if (mysqli_num_rows($check) > 0) {
    if (mysqli_query($conn, "ALTER TABLE users CHANGE name full_name VARCHAR(100) NOT NULL")) {
        echo "Renamed 'name' to 'full_name' in 'users'.<br>";
    } else {
        echo "Error renaming 'name': " . mysqli_error($conn) . "<br>";
    }
}

// 2. Fix 'halls' table: Add 'created_at'
$check = mysqli_query($conn, "SHOW COLUMNS FROM halls LIKE 'created_at'");
if (mysqli_num_rows($check) == 0) {
    if (mysqli_query($conn, "ALTER TABLE halls ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP")) {
        echo "Added 'created_at' to 'halls'.<br>";
    } else {
        echo "Error adding 'created_at': " . mysqli_error($conn) . "<br>";
    }
}

// 3. Fix 'hall_owners' table: Add 'created_at' (just in case)
$check = mysqli_query($conn, "SHOW COLUMNS FROM hall_owners LIKE 'created_at'");
if (mysqli_num_rows($check) == 0) {
    if (mysqli_query($conn, "ALTER TABLE hall_owners ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP")) {
        echo "Added 'created_at' to 'hall_owners'.<br>";
    } else {
        echo "Error adding 'created_at': " . mysqli_error($conn) . "<br>";
    }
}

// 4. Fix 'admins' table: Add 'created_at' (just in case)
$check = mysqli_query($conn, "SHOW COLUMNS FROM admins LIKE 'created_at'");
if (mysqli_num_rows($check) == 0) {
    if (mysqli_query($conn, "ALTER TABLE admins ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP")) {
        echo "Added 'created_at' to 'admins'.<br>";
    } else {
        echo "Error adding 'created_at': " . mysqli_error($conn) . "<br>";
    }
}

echo "<h3>Final Schema Fix Complete</h3>";
?>
