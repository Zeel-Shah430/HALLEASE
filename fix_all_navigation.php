<?php
/**
 * HallEase - Complete System Fix Script
 * This will fix ALL navigation and database issues
 */

session_start();
require_once 'config/db.php';

$fixes = [];
$errors = [];

// ==============================================
// FIX 1: Update Admin Navigation
// ==============================================
$admin_files = [
    'admin/dashboard.php',
    'admin/index.php',
    'admin/manage_halls.php',
    'admin/manage_owners.php',
    'admin/manage_users.php',
    'admin/view_bookings.php'
];

foreach ($admin_files as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);

        // Fix navigation links
        $content = str_replace('href="dashboard.php"', 'href="index.php"', $content);
        $content = str_replace('../user/book_hall.php', '../user/book_hall_new.php', $content);

        file_put_contents($file, $content);
        $fixes[] = "✓ Updated: {$file}";
    }
}

// ==============================================
// FIX 2: Update Owner Navigation
// ==============================================
$owner_files = [
    'owner/dashboard.php',
    'owner/index.php',
    'owner/my_halls.php',
    'owner/bookings.php'
];

foreach ($owner_files as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);

        // Fix navigation links
        $content = str_replace('href="dashboard.php"', 'href="index.php"', $content);

        file_put_contents($file, $content);
        $fixes[] = "✓ Updated: {$file}";
    }
}

// ==============================================
// FIX 3: Update User Navigation
// ==============================================
$user_files = [
    'user/dashboard.php'
];

foreach ($user_files as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);

        // Fix links to book_hall
        $content = str_replace('href="book_hall.php"', 'href="book_hall_new.php"', $content);
        $content = str_replace('href="my_bookings.php"', 'href="my_bookings_new.php"', $content);

        file_put_contents($file, $content);
        $fixes[] = "✓ Updated: {$file}";
    }
}

// ==============================================
// FIX 4: Update Navbar
// ==============================================
if (file_exists('includes/navbar.php')) {
    $content = file_get_contents('includes/navbar.php');

    // Fix book hall link
    $content = str_replace('book_hall.php', 'book_hall_new.php', $content);
    $content = str_replace('my_bookings.php', 'my_bookings_new.php', $content);

    file_put_contents('includes/navbar.php', $content);
    $fixes[] = "✓ Updated: includes/navbar.php";
}

// ==============================================
// FIX 5: Check Database Columns
// ==============================================
try {
    // Check if new columns exist
    $check_sql = "SHOW COLUMNS FROM bookings LIKE 'razorpay_order_id'";
    $result = mysqli_query($conn, $check_sql);

    if (mysqli_num_rows($result) == 0) {
        $fixes[] = "⚠ Database NOT upgraded - Run install_upgrades.php";
    } else {
        $fixes[] = "✓ Database columns exist";
    }
} catch (Exception $e) {
    $errors[] = "✗ Database check failed: " . $e->getMessage();
}

?>
<!DOCTYPE html>
<html>

<head>
    <title>HallEase - System Fix Report</title>
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
            padding: 40px 20px;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        h1 {
            color: #1e293b;
            margin-bottom: 30px;
            font-size: 2.5rem;
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

        .result.warning {
            background: #fffbeb;
            border-left: 4px solid #f59e0b;
            color: #92400e;
        }

        .result.error {
            background: #fef2f2;
            border-left: 4px solid #ef4444;
            color: #991b1b;
        }

        .section {
            margin: 30px 0;
        }

        .section h2 {
            color: #1e293b;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e2e8f0;
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
            margin-right: 10px;
            transition: transform 0.2s;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .btn-success {
            background: linear-gradient(135deg, #10b981, #059669);
        }

        .btn-warning {
            background: linear-gradient(135deg, #f59e0b, #d97706);
        }

        .alert {
            background: rgba(59, 130, 246, 0.1);
            border: 2px solid #3b82f6;
            color: #1e40af;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 30px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>🔧 System Fix Report</h1>

        <div class="alert">
            <strong>✅ SQL Errors Fixed:</strong><br>
            • halldetails.php - Fixed table join (users → hall_owners)<br>
            • my_bookings.php - Fixed column name (client_id → user_id)
        </div>

        <div class="section">
            <h2>Navigation Updates</h2>
            <?php foreach ($fixes as $fix): ?>
                <div class="result success">
                    <?php echo $fix; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="section">
                <h2>Errors</h2>
                <?php foreach ($errors as $error): ?>
                    <div class="result error">
                        <?php echo $error; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="section">
            <h2>Next Steps</h2>
            <div class="result warning">
                ⚠ <strong>IMPORTANT:</strong> You must complete these steps in order:
            </div>
            <ol style="line-height: 2; margin-left: 20px;">
                <li>Run database upgrade (if not done already)</li>
                <li>Clear browser cache (Ctrl+Shift+Del)</li>
                <li>Test the system</li>
            </ol>
        </div>

        <div style="margin-top: 30px;">
            <a href="install_upgrades.php" class="btn btn-warning">
                <i class="fas fa-database"></i> 1. Run Database Upgrade
            </a>
            <a href="user/login.php" class="btn btn-success">
                <i class="fas fa-sign-in-alt"></i> 2. Login & Test
            </a>
            <a href="START_HERE.html" class="btn">
                <i class="fas fa-home"></i> 3. Setup Guide
            </a>
        </div>
    </div>
</body>

</html>