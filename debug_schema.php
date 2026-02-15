<?php
include 'config/db.php';

echo "<h2>Table: halls</h2>";
$result = mysqli_query($conn, "DESCRIBE halls");
if ($result) {
    echo "<table border='1'><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        foreach ($row as $key => $val) {
            echo "<td>$val</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "Error describing table: " . mysqli_error($conn);
}
?>
