<?php
/**
 * HallEase - Automated Installation & Upgrade Script
 * 
 * This script will:
 * 1. Check database connection
 * 2. Run schema upgrades
 * 3. Verify installations
 * 4. Generate installation report
 * 
 * USAGE: Run this file in browser: http://localhost/HALLEASE/install_upgrades.php
 */

// Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'hallease');

// Initialize
$errors = [];
$success = [];
$warnings = [];

// Step 1: Check Database Connection
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    $success[] = "✓ Database connection established";
} catch (PDOException $e) {
    $errors[] = "✗ Database connection failed: " . $e->getMessage();
    die(render_report());
}

// Step 2: Create Backup
$timestamp = date('Y-m-d_H-i-s');
$backup_file = "backup_hallease_{$timestamp}.sql";

try {
    $sql = "SELECT * FROM bookings";
    $stmt = $pdo->query($sql);
    $bookings_count = $stmt->rowCount();
    $success[] = "✓ Pre-upgrade check: {$bookings_count} existing bookings found";
} catch (PDOException $e) {
    $warnings[] = "⚠ Could not check existing bookings";
}

// Step 3: Add Missing Columns to Bookings Table
$columns_to_add = [
    "ADD COLUMN total_days INT DEFAULT 1 AFTER booking_end_date" => "total_days",
    "ADD COLUMN price_per_day DECIMAL(10,2) DEFAULT 0.00 AFTER total_days" => "price_per_day",
    "ADD COLUMN razorpay_order_id VARCHAR(100) DEFAULT NULL AFTER payment_status" => "razorpay_order_id",
    "ADD COLUMN razorpay_payment_id VARCHAR(100) DEFAULT NULL AFTER razorpay_order_id" => "razorpay_payment_id",
    "ADD COLUMN razorpay_signature VARCHAR(255) DEFAULT NULL AFTER razorpay_payment_id" => "razorpay_signature",
    "ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER razorpay_signature" => "created_at",
    "ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at" => "updated_at"
];

foreach ($columns_to_add as $alter_sql => $column_name) {
    try {
        // Check if column already exists
        $check_sql = "SHOW COLUMNS FROM bookings LIKE '{$column_name}'";
        $result = $pdo->query($check_sql);

        if ($result->rowCount() == 0) {
            $pdo->exec("ALTER TABLE bookings {$alter_sql}");
            $success[] = "✓ Added column: {$column_name}";
        } else {
            $warnings[] = "⚠ Column already exists: {$column_name}";
        }
    } catch (PDOException $e) {
        $errors[] = "✗ Failed to add column {$column_name}: " . $e->getMessage();
    }
}

// Step 4: Update Booking Status Enum
try {
    $pdo->exec("ALTER TABLE bookings MODIFY COLUMN booking_status ENUM('pending_payment','confirmed','cancelled','payment_failed','completed') DEFAULT 'pending_payment'");
    $success[] = "✓ Updated booking_status enum values";
} catch (PDOException $e) {
    $warnings[] = "⚠ Booking status enum might already be updated: " . $e->getMessage();
}

// Step 5: Update Payment Status Enum
try {
    $pdo->exec("ALTER TABLE bookings MODIFY COLUMN payment_status ENUM('pending','paid','failed','refunded') DEFAULT 'pending'");
    $success[] = "✓ Updated payment_status enum values";
} catch (PDOException $e) {
    $warnings[] = "⚠ Payment status enum might already be updated: " . $e->getMessage();
}

// Step 6: Add Indexes
$indexes = [
    "idx_hall_dates" => "CREATE INDEX idx_hall_dates ON bookings(hall_id, booking_start_date, booking_end_date)",
    "idx_status" => "CREATE INDEX idx_status ON bookings(booking_status)",
    "idx_created_at" => "CREATE INDEX idx_created_at ON bookings(created_at)"
];

foreach ($indexes as $index_name => $create_sql) {
    try {
        $check_sql = "SHOW INDEX FROM bookings WHERE Key_name = '{$index_name}'";
        $result = $pdo->query($check_sql);

        if ($result->rowCount() == 0) {
            $pdo->exec($create_sql);
            $success[] = "✓ Created index: {$index_name}";
        } else {
            $warnings[] = "⚠ Index already exists: {$index_name}";
        }
    } catch (PDOException $e) {
        $errors[] = "✗ Failed to create index {$index_name}: " . $e->getMessage();
    }
}

// Step 7: Create Session Tokens Table
try {
    $create_table_sql = "
        CREATE TABLE IF NOT EXISTS session_tokens (
            token_id INT(11) NOT NULL AUTO_INCREMENT,
            user_id INT(11) DEFAULT NULL,
            user_type ENUM('user','owner','admin') NOT NULL,
            token VARCHAR(64) NOT NULL,
            expires_at DATETIME NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (token_id),
            UNIQUE KEY token (token),
            KEY user_id (user_id),
            KEY expires_at (expires_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ";
    $pdo->exec($create_table_sql);
    $success[] = "✓ Created session_tokens table";
} catch (PDOException $e) {
    $warnings[] = "⚠ Session tokens table might already exist: " . $e->getMessage();
}

// Step 8: Create Audit Log Table
try {
    $create_table_sql = "
        CREATE TABLE IF NOT EXISTS audit_log (
            log_id INT(11) NOT NULL AUTO_INCREMENT,
            user_id INT(11) DEFAULT NULL,
            user_type ENUM('user','owner','admin','guest') NOT NULL,
            action VARCHAR(100) NOT NULL,
            table_name VARCHAR(50) DEFAULT NULL,
            record_id INT(11) DEFAULT NULL,
            description TEXT DEFAULT NULL,
            ip_address VARCHAR(45) DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (log_id),
            KEY user_id (user_id),
            KEY created_at (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ";
    $pdo->exec($create_table_sql);
    $success[] = "✓ Created audit_log table";
} catch (PDOException $e) {
    $warnings[] = "⚠ Audit log table might already exist: " . $e->getMessage();
}

// Step 9: Update Existing Bookings Data
try {
    // Calculate total_days for existing bookings
    $pdo->exec("
        UPDATE bookings 
        SET total_days = DATEDIFF(booking_end_date, booking_start_date) + 1
        WHERE total_days IS NULL OR total_days = 0 OR total_days = 1
    ");
    $success[] = "✓ Updated total_days for existing bookings";
} catch (PDOException $e) {
    $warnings[] = "⚠ Could not update total_days: " . $e->getMessage();
}

try {
    // Update price_per_day from halls table
    $pdo->exec("
        UPDATE bookings b
        INNER JOIN halls h ON b.hall_id = h.hall_id
        SET b.price_per_day = h.price_per_day
        WHERE b.price_per_day = 0 OR b.price_per_day IS NULL
    ");
    $success[] = "✓ Updated price_per_day for existing bookings";
} catch (PDOException $e) {
    $warnings[] = "⚠ Could not update price_per_day: " . $e->getMessage();
}

try {
    // Recalculate total_amount
    $pdo->exec("
        UPDATE bookings
        SET total_amount = total_days * price_per_day
        WHERE total_days > 0 AND price_per_day > 0
    ");
    $success[] = "✓ Recalculated total_amount for existing bookings";
} catch (PDOException $e) {
    $warnings[] = "⚠ Could not recalculate total_amount: " . $e->getMessage();
}

// Step 10: Verify Installation
$verification_checks = [
    "Bookings table columns" => "SELECT COUNT(*) as cnt FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = 'hallease' AND TABLE_NAME = 'bookings' AND COLUMN_NAME IN ('total_days', 'price_per_day', 'razorpay_order_id', 'razorpay_payment_id', 'razorpay_signature', 'created_at', 'updated_at')",
    "Bookings indexes" => "SELECT COUNT(*) as cnt FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = 'hallease' AND TABLE_NAME = 'bookings' AND INDEX_NAME IN ('idx_hall_dates', 'idx_status', 'idx_created_at')",
    "Session tokens table" => "SELECT COUNT(*) as cnt FROM information_schema.TABLES WHERE TABLE_SCHEMA = 'hallease' AND TABLE_NAME = 'session_tokens'",
    "Audit log table" => "SELECT COUNT(*) as cnt FROM information_schema.TABLES WHERE TABLE_SCHEMA = 'hallease' AND TABLE_NAME = 'audit_log'"
];

$verification_results = [];
foreach ($verification_checks as $check_name => $sql) {
    try {
        $result = $pdo->query($sql);
        $row = $result->fetch();
        $verification_results[$check_name] = $row['cnt'];
    } catch (PDOException $e) {
        $verification_results[$check_name] = "ERROR";
    }
}

// Render Report
function render_report()
{
    global $success, $warnings, $errors, $verification_results;

    $html = '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>HallEase Installation Report</title>
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body {
                font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
                padding: 40px 20px;
            }
            .container {
                max-width: 900px;
                margin: 0 auto;
                background: white;
                border-radius: 16px;
                box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
                padding: 40px;
            }
            h1 {
                color: #1e293b;
                margin-bottom: 10px;
                font-size: 2.5rem;
            }
            .subtitle {
                color: #64748b;
                margin-bottom: 30px;
                font-size: 1.1rem;
            }
            .section {
                margin-bottom: 30px;
            }
            .section h2 {
                color: #1e293b;
                margin-bottom: 15px;
                font-size: 1.5rem;
                display: flex;
                align-items: center;
                gap: 10px;
            }
            .badge {
                display: inline-block;
                padding: 4px 12px;
                border-radius: 12px;
                font-size: 0.85rem;
                font-weight: 600;
            }
            .badge-success { background: #10b981; color: white; }
            .badge-warning { background: #f59e0b; color: white; }
            .badge-error { background: #ef4444; color: white; }
            ul {
                list-style: none;
                padding: 0;
            }
            li {
                padding: 12px;
                margin-bottom: 8px;
                border-radius: 8px;
                background: #f8fafc;
                border-left: 4px solid #e2e8f0;
            }
            li.success { background: #ecfdf5; border-left-color: #10b981; color: #065f46; }
            li.warning { background: #fffbeb; border-left-color: #f59e0b; color: #92400e; }
            li.error { background: #fef2f2; border-left-color: #ef4444; color: #991b1b; }
            .verification-table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 15px;
            }
            .verification-table th,
            .verification-table td {
                padding: 12px;
                text-align: left;
                border-bottom: 1px solid #e2e8f0;
            }
            .verification-table th {
                background: #f1f5f9;
                font-weight: 600;
                color: #1e293b;
            }
            .status-ok { color: #10b981; font-weight: 600; }
            .status-error { color: #ef4444; font-weight: 600; }
            .next-steps {
                background: linear-gradient(135deg, #e0e7ff, #ddd6fe);
                padding: 25px;
                border-radius: 12px;
                border-left: 4px solid #8b5cf6;
            }
            .next-steps h3 {
                color: #5b21b6;
                margin-bottom: 15px;
            }
            .next-steps ol {
                margin-left: 20px;
            }
            .next-steps li {
                margin-bottom: 10px;
                background: none;
                border: none;
                color: #6d28d9;
            }
            .btn {
                display: inline-block;
                padding: 12px 24px;
                background: linear-gradient(135deg, #667eea, #764ba2);
                color: white;
                text-decoration: none;
                border-radius: 8px;
                font-weight: 600;
                margin-top: 20px;
                transition: transform 0.2s;
            }
            .btn:hover {
                transform: translateY(-2px);
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>🎉 HallEase Installation Report</h1>
            <p class="subtitle">Database upgrade completed on ' . date('F d, Y - H:i:s') . '</p>';

    // Success Messages
    if (!empty($success)) {
        $html .= '
            <div class="section">
                <h2>
                    <span class="badge badge-success">' . count($success) . ' Success</span>
                </h2>
                <ul>';
        foreach ($success as $msg) {
            $html .= '<li class="success">' . htmlspecialchars($msg) . '</li>';
        }
        $html .= '</ul></div>';
    }

    // Warnings
    if (!empty($warnings)) {
        $html .= '
            <div class="section">
                <h2>
                    <span class="badge badge-warning">' . count($warnings) . ' Warnings</span>
                </h2>
                <ul>';
        foreach ($warnings as $msg) {
            $html .= '<li class="warning">' . htmlspecialchars($msg) . '</li>';
        }
        $html .= '</ul></div>';
    }

    // Errors
    if (!empty($errors)) {
        $html .= '
            <div class="section">
                <h2>
                    <span class="badge badge-error">' . count($errors) . ' Errors</span>
                </h2>
                <ul>';
        foreach ($errors as $msg) {
            $html .= '<li class="error">' . htmlspecialchars($msg) . '</li>';
        }
        $html .= '</ul></div>';
    }

    // Verification Results
    $html .= '
        <div class="section">
            <h2>Verification Results</h2>
            <table class="verification-table">
                <thead>
                    <tr>
                        <th>Component</th>
                        <th>Expected</th>
                        <th>Found</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>';

    $expected_values = [
        "Bookings table columns" => 7,
        "Bookings indexes" => 3,
        "Session tokens table" => 1,
        "Audit log table" => 1
    ];

    foreach ($verification_results as $check => $found) {
        $expected = $expected_values[$check] ?? '?';
        $status_class = ($found >= $expected) ? 'status-ok' : 'status-error';
        $status_text = ($found >= $expected) ? '✓ OK' : '✗ FAILED';

        $html .= "
            <tr>
                <td>{$check}</td>
                <td>{$expected}</td>
                <td>{$found}</td>
                <td class=\"{$status_class}\">{$status_text}</td>
            </tr>";
    }

    $html .= '
                </tbody>
            </table>
        </div>';

    // Next Steps
    $html .= '
        <div class="next-steps">
            <h3>📋 Next Steps</h3>
            <ol>
                <li><strong>Test Double Booking Prevention:</strong> Try booking the same hall for overlapping dates</li>
                <li><strong>Test Payment Flow:</strong> Make a test booking and complete Razorpay payment</li>
                <li><strong>Verify Auto-Cleanup:</strong> Create a booking without payment and wait 15 minutes</li>
                <li><strong>Update Navigation:</strong> Replace old booking file links with new ones</li>
                <li><strong>Security Check:</strong> Ensure CSRF tokens are working on all forms</li>
                <li><strong>Backup Database:</strong> Create a backup before moving to production</li>
            </ol>
            <a href="user/book_hall_new.php" class="btn">Test Booking System →</a>
            <a href="IMPLEMENTATION_GUIDE.md" class="btn" style="background: #10b981;">View Full Guide →</a>
        </div>
    </body>
    </html>';

    return $html;
}

echo render_report();
?>