<?php
require_once 'config/db.php';

echo "<h2>Fixing Images Column in Halls Table</h2>";

// Check if images column exists
$check_query = "SHOW COLUMNS FROM halls LIKE 'images'";
$result = mysqli_query($conn, $check_query);

if (mysqli_num_rows($result) > 0) {
    echo "<div style='color: orange;'>⚠ 'images' column already exists!</div><br>";
} else {
    // Add images column
    $alter_query = "ALTER TABLE halls ADD COLUMN images TEXT DEFAULT NULL AFTER image";
    
    if (mysqli_query($conn, $alter_query)) {
        echo "<div style='color: green; font-weight: bold;'>✓ 'images' column added successfully!</div><br>";
    } else {
        echo "<div style='color: red;'>✗ Error adding column: " . mysqli_error($conn) . "</div><br>";
    }
}

// Show current table structure
echo "<h3>Current Halls Table Structure:</h3>";
$structure_query = "DESCRIBE halls";
$structure_result = mysqli_query($conn, $structure_query);

echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
while ($row = mysqli_fetch_assoc($structure_result)) {
    echo "<tr>";
    echo "<td>" . $row['Field'] . "</td>";
    echo "<td>" . $row['Type'] . "</td>";
    echo "<td>" . $row['Null'] . "</td>";
    echo "<td>" . $row['Key'] . "</td>";
    echo "<td>" . ($row['Default'] ?? 'NULL') . "</td>";
    echo "</tr>";
}
echo "</table>";

echo "<br><div style='color: green; font-weight: bold;'>✓ Database is now ready! You can add halls.</div>";

mysqli_close($conn);
?>
