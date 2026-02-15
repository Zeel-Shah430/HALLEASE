<?php
include 'config/db.php';

echo "<h2>Current Users Table Schema</h2>";
$result = mysqli_query($conn, "DESCRIBE users");
if ($result) {
    echo "<table border='1'><tr><th>Field</th><th>Type</th></tr>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr><td>{$row['Field']}</td><td>{$row['Type']}</td></tr>";
    }
    echo "</table>";
}

// Attempt fix again with checking
echo "<h2>Applying Fix</h2>";

// Check if 'full_name' exists
$check_full = mysqli_query($conn, "SHOW COLUMNS FROM users LIKE 'full_name'");
$full_exists = (mysqli_num_rows($check_full) > 0);

// Check if 'name' exists
$check_name = mysqli_query($conn, "SHOW COLUMNS FROM users LIKE 'name'");
$name_exists = (mysqli_num_rows($check_name) > 0);

if (!$full_exists) {
    if ($name_exists) {
        // Rename name to full_name
        $sql = "ALTER TABLE users CHANGE COLUMN name full_name VARCHAR(100) NOT NULL";
        if (mysqli_query($conn, $sql)) {
            echo "SUCCESS: Renamed 'name' to 'full_name'.<br>";
        } else {
            echo "ERROR: Could not rename 'name'. " . mysqli_error($conn) . "<br>";
        }
    } else {
        // Add full_name
        $sql = "ALTER TABLE users ADD COLUMN full_name VARCHAR(100) NOT NULL AFTER user_id";
        if (mysqli_query($conn, $sql)) {
            echo "SUCCESS: Added 'full_name' column.<br>";
        } else {
            echo "ERROR: Could not add 'full_name'. " . mysqli_error($conn) . "<br>";
        }
    }
} else {
    echo "INFO: 'full_name' already exists.<br>";
}

// Re-verify
echo "<h2>New Users Table Schema</h2>";
$result = mysqli_query($conn, "DESCRIBE users");
if ($result) {
    echo "<table border='1'><tr><th>Field</th><th>Type</th></tr>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr><td>{$row['Field']}</td><td>{$row['Type']}</td></tr>";
    }
    echo "</table>";
}
?>
