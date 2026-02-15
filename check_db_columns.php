<?php
require 'c:/xampp/htdocs/HALLEASE/config/db.php';

try {
    $stmt = $pdo->query("DESCRIBE halls");
    $columns = $stmt->fetchAll(PDO::FETCH_Column);
    echo "Columns: " . implode(", ", $columns) . "\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>