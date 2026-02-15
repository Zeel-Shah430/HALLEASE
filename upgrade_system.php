<?php
require 'config/db.php';

echo "<h1>HallEase Upgrade Script</h1>";

// 1. Create Directories
$dirs = [
    'assets/images/halls',
    'assets/css',
    'assets/js'
];

foreach ($dirs as $dir) {
    if (!file_exists($dir)) {
        if (mkdir($dir, 0777, true)) {
            echo "<p style='color: green;'>Created directory: $dir</p>";
        } else {
            echo "<p style='color: red;'>Failed to create directory: $dir</p>";
        }
    } else {
        echo "<p style='color: blue;'>Directory exists: $dir</p>";
    }
}

// 2. Add 'images' column to 'halls' table if not exists
try {
    $result = $pdo->query("SHOW COLUMNS FROM halls LIKE 'images'");
    if ($result->rowCount() == 0) {
        $pdo->exec("ALTER TABLE halls ADD COLUMN images TEXT DEFAULT NULL");
        echo "<p style='color: green;'>Added 'images' column to 'halls' table.</p>";
    } else {
        echo "<p style='color: blue;'>Column 'images' already exists in 'halls'.</p>";

        // Optional: Check if it's TEXT or VARCHAR
        // If existing system heavily used it, we assume we can create a script to migrate if needed.
        // But for "Redesign", we assume we start using it as JSON.
    }
} catch (PDOException $e) {
    echo "<p style='color: red;'>Database Error: " . $e->getMessage() . "</p>";
}

echo "<h3>Upgrade Complete!</h3>";
echo "<a href='user/dashboard.php'>Go to User Dashboard</a> | <a href='admin/index.php'>Go to Admin Panel</a>";
?>