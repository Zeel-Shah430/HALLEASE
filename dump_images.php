<?php
include 'config/db.php';
$stmt = $pdo->query("SELECT images FROM halls WHERE hall_id = 3");
$hall = $stmt->fetch();
file_put_contents('hall_images.txt', $hall['images']);
?>