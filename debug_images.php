<?php
include 'config/db.php';
$stmt = $pdo->query("SELECT hall_id, hall_name, images FROM halls WHERE hall_id = 3");
$hall = $stmt->fetch();
echo "Images JSON: " . $hall['images'] . "\n";
print_r(json_decode($hall['images'], true));
?>