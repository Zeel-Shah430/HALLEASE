<?php
include 'config/db.php';

// Change 'image' column to 'images' TEXT to store multiple URLs (JSON or separated)
$check = mysqli_query($conn, "SHOW COLUMNS FROM halls LIKE 'image'");
if (mysqli_num_rows($check) > 0) {
    // Rename and change type
    if (mysqli_query($conn, "ALTER TABLE halls CHANGE image images TEXT DEFAULT NULL")) {
        echo "SUCCESS: Changed 'image' to 'images' (TEXT).<br>";
    } else {
        echo "ERROR: " . mysqli_error($conn) . "<br>";
    }
} else {
    // Check if 'images' already exists
    $check_new = mysqli_query($conn, "SHOW COLUMNS FROM halls LIKE 'images'");
    if (mysqli_num_rows($check_new) > 0) {
        echo "INFO: 'images' column already exists.<br>";
    } else {
        // Create if neither exists
        mysqli_query($conn, "ALTER TABLE halls ADD COLUMN images TEXT DEFAULT NULL");
        echo "SUCCESS: Added 'images' (TEXT).<br>";
    }
}

echo "<h3>Schema Updated for Multiple Images</h3>";
?>
