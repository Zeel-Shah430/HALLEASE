<?php
/**
 * HallEase - System Activation Script
 * This will replace old files with new upgraded versions
 */

$files_to_replace = [
    'user/book_hall.php' => 'user/book_hall_new.php',
    'user/my_bookings.php' => 'user/my_bookings_new.php'
];

$results = [];

foreach ($files_to_replace as $old => $new) {
    if (file_exists($new)) {
        // Backup old file
        $backup = $old . '.backup_' . date('Y-m-d_H-i-s');
        if (file_exists($old)) {
            copy($old, $backup);
            $results[] = "✓ Backed up: {$old} → {$backup}";
        }

        // Copy new file over old
        copy($new, $old);
        $results[] = "✓ Activated: {$new} → {$old}";
    } else {
        $results[] = "✗ Missing: {$new}";
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>HallEase - System Activation</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            max-width: 700px;
            width: 100%;
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        h1 {
            color: #1e293b;
            margin-bottom: 30px;
            font-size: 2rem;
        }

        .result {
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 8px;
            font-weight: 500;
        }

        .result.success {
            background: #ecfdf5;
            border-left: 4px solid #10b981;
            color: #065f46;
        }

        .result.error {
            background: #fef2f2;
            border-left: 4px solid #ef4444;
            color: #991b1b;
        }

        .btn {
            display: inline-block;
            padding: 14px 28px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            margin-top: 20px;
            transition: transform 0.2s;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .warning {
            background: #fffbeb;
            border: 2px solid #f59e0b;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            color: #92400e;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>🚀 System Activation Results</h1>

        <div class="warning">
            <strong>⚠️ Important:</strong> Old files have been backed up with timestamps.
        </div>

        <?php foreach ($results as $result): ?>
            <div class="result <?php echo strpos($result, '✓') !== false ? 'success' : 'error'; ?>">
                <?php echo $result; ?>
            </div>
        <?php endforeach; ?>

        <h2 style="margin-top: 30px; margin-bottom: 15px; color: #1e293b;">Next Steps:</h2>
        <ol style="line-height: 2;">
            <li>Clear browser cache (Ctrl+Shift+Del)</li>
            <li><a href="user/book_hall.php" class="btn">Test New Booking System →</a></li>
            <li><a href="user/my_bookings.php" class="btn" style="background: #10b981;">View My Bookings →</a></li>
        </ol>
    </div>
</body>

</html>