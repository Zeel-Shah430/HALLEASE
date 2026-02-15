<?php
include 'config/db.php';

$result = mysqli_query($conn, "SELECT * FROM halls");
echo "<h2>Halls in Database</h2>";
while ($row = mysqli_fetch_assoc($result)) {
    echo "ID: " . $row['hall_id'] . " - Name: " . $row['hall_name'] . "<br>";
}
?>
