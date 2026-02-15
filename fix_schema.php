<?php
include 'config/db.php';

// 1. Add 'city' column if it doesn't exist
$check = mysqli_query($conn, "SHOW COLUMNS FROM halls LIKE 'city'");
if (mysqli_num_rows($check) == 0) {
    if (mysqli_query($conn, "ALTER TABLE halls ADD COLUMN city VARCHAR(100) NOT NULL AFTER location")) {
        echo "Added 'city' column.<br>";
    } else {
        echo "Error adding 'city': " . mysqli_error($conn) . "<br>";
    }
}

// 2. Add 'description' column
$check = mysqli_query($conn, "SHOW COLUMNS FROM halls LIKE 'description'");
if (mysqli_num_rows($check) == 0) {
    if (mysqli_query($conn, "ALTER TABLE halls ADD COLUMN description TEXT DEFAULT NULL")) {
        echo "Added 'description' column.<br>";
    } else {
        echo "Error adding 'description': " . mysqli_error($conn) . "<br>";
    }
}

// 3. Add 'image' column
$check = mysqli_query($conn, "SHOW COLUMNS FROM halls LIKE 'image'");
if (mysqli_num_rows($check) == 0) {
    if (mysqli_query($conn, "ALTER TABLE halls ADD COLUMN image VARCHAR(255) DEFAULT NULL")) {
        echo "Added 'image' column.<br>";
    } else {
        echo "Error adding 'image': " . mysqli_error($conn) . "<br>";
    }
}

// 4. Rename 'price' to 'price_per_day' logic
$check_old = mysqli_query($conn, "SHOW COLUMNS FROM halls LIKE 'price'");
$check_new = mysqli_query($conn, "SHOW COLUMNS FROM halls LIKE 'price_per_day'");

if (mysqli_num_rows($check_old) > 0 && mysqli_num_rows($check_new) == 0) {
    if (mysqli_query($conn, "ALTER TABLE halls CHANGE price price_per_day DECIMAL(10,2) NOT NULL")) {
        echo "Renamed 'price' to 'price_per_day'.<br>";
    } else {
        echo "Error renaming 'price': " . mysqli_error($conn) . "<br>";
    }
} elseif (mysqli_num_rows($check_new) == 0) {
    // Neither exists, create it
    mysqli_query($conn, "ALTER TABLE halls ADD COLUMN price_per_day DECIMAL(10,2) NOT NULL");
    echo "Created 'price_per_day'.<br>";
}

echo "<h3>Schema Update Complete</h3>";
echo "You can now try adding a hall.";
?>
