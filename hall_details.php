<?php
include 'config/db.php';
$id = $_GET['id'];

$q = "SELECT * FROM halls WHERE hall_id=$id";
$row = mysqli_fetch_assoc(mysqli_query($conn, $q));

echo "<h2>{$row['hall_name']}</h2>";
echo "Capacity: {$row['capacity']}<br>";
echo "Facilities: {$row['facilities']}";
