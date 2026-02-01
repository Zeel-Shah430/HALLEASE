<?php
$conn = mysqli_connect("localhost", "root", "", "hallease");

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>
