<?php
require_once 'config/db.php';

// Generate new password hash for Admin@123
$password = 'Admin@123';
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

echo "<h2>Fixing Admin Password</h2>";
echo "Password: <strong>$password</strong><br>";
echo "New Hash: <code>$hashed_password</code><br><br>";

// Update the admin password
$update_query = "UPDATE admins SET password = '$hashed_password' WHERE email = 'admin@hallease.com'";

if (mysqli_query($conn, $update_query)) {
    echo "<div style='color: green; font-weight: bold;'>✓ Admin password updated successfully!</div><br>";
    
    // Verify the update
    $check_query = "SELECT * FROM admins WHERE email = 'admin@hallease.com'";
    $result = mysqli_query($conn, $check_query);
    
    if ($row = mysqli_fetch_assoc($result)) {
        echo "<h3>Admin Account Details:</h3>";
        echo "Username: " . $row['username'] . "<br>";
        echo "Email: " . $row['email'] . "<br>";
        echo "Password Hash: <code>" . $row['password'] . "</code><br><br>";
        
        // Test password verification
        if (password_verify($password, $row['password'])) {
            echo "<div style='color: green; font-weight: bold;'>✓ Password verification test PASSED!</div><br>";
            echo "<h3>You can now login with:</h3>";
            echo "Email: <strong>admin@hallease.com</strong><br>";
            echo "Password: <strong>Admin@123</strong><br>";
        } else {
            echo "<div style='color: red; font-weight: bold;'>✗ Password verification test FAILED!</div>";
        }
    }
} else {
    echo "<div style='color: red;'>Error updating password: " . mysqli_error($conn) . "</div>";
}

mysqli_close($conn);
?>
