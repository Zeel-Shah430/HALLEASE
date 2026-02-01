<?php
include 'config/db.php';
$q = "SELECT * FROM halls WHERE status='available'";
$res = mysqli_query($conn, $q);

while ($row = mysqli_fetch_assoc($res)) {
    echo "<a href='hall_details.php?id={$row['hall_id']}'>{$row['hall_name']}</a><br>";
}
?>