<?php
include 'config/db.php';

$email = 'admin@hallease.com';
$password = 'Admin@123';
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$sql = "UPDATE admins SET password = '$hashed_password' WHERE email = '$email'";

if (mysqli_query($conn, $sql)) {
    echo "Password reset successfully for $email.<br>";
    echo "New Password: $password<br>";
    echo "Hash: $hashed_password";
} else {
    echo "Error updating password: " . mysqli_error($conn);
}
?>
