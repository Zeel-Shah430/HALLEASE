<?php
include 'config/db.php';

$email = 'admin@hallease.com';
$password = 'Admin@123';

echo "<h2>Debug Login</h2>";
echo "Testing login for: <strong>$email</strong><br>";
echo "Testing password: <strong>$password</strong><br><br>";

$query = "SELECT * FROM admins WHERE email = '$email'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    echo "User Found.<br>";
    echo "Stored ID: " . $row['admin_id'] . "<br>";
    echo "Stored Email: " . $row['email'] . "<br>";
    echo "Stored Hash: " . $row['password'] . "<br><br>";
    
    echo "Verifying password...<br>";
    if (password_verify($password, $row['password'])) {
        echo "<strong>SUCCESS:</strong> Password matches hash.<br>";
    } else {
        echo "<strong>FAILURE:</strong> Password does NOT match hash.<br>";
        echo "Test Hash Re-generation: " . password_hash($password, PASSWORD_DEFAULT) . "<br>";
    }
} else {
    echo "User NOT found in database.";
}
?>
