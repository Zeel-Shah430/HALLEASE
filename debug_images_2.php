<?php
include 'config/db.php';
$stmt = $pdo->query("SELECT images FROM halls WHERE hall_id = 3");
$hall = $stmt->fetch();
$images = json_decode($hall['images'], true);
foreach ($images as $img) {
    echo "Image: $img\n";
    if (filter_var($img, FILTER_VALIDATE_URL)) {
        echo "Valid URL\n";
    } else {
        echo "Invalid URL\n";
    }
}
?>