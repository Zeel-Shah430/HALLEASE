<?php
// Database Configuration
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "hallease";

// 1. Connect to MySQL Server
$conn = new mysqli($host, $user, $pass);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 2. Create Database if not exists
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === TRUE) {
    echo "Database '$dbname' created or already exists.<br>";
} else {
    die("Error creating database: " . $conn->error);
}

// 3. Select Database
$conn->select_db($dbname);

// 4. Read contents of database.sql
$sql_file = 'database.sql';
if (!file_exists($sql_file)) {
    die("Error: database.sql file not found.");
}

$sql_content = file_get_contents($sql_file);

// 5. Execute Multi Query
if ($conn->multi_query($sql_content)) {
    echo "Tables imported successfully!<br>";
    
    // Cycle through results to clear them
    do {
        if ($result = $conn->store_result()) {
            $result->free();
        }
    } while ($conn->more_results() && $conn->next_result());
    
    echo "<h3>Setup Complete!</h3>";
    echo "<p>You can now <a href='admin/login.php'>Login to Admin Panel</a></p>";
} else {
    echo "Error importing tables: " . $conn->error;
}

$conn->close();
?>
