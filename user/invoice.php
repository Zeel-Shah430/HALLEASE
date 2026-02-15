<?php
session_start();
require_once '../config/db.php';
require_once '../includes/functions.php';

// Check login
check_user_login();

if (!isset($_GET['booking_id'])) {
    die("Invalid Request");
}

$booking_id = clean_input($_GET['booking_id']);
$user_id = $_SESSION['user_id'];

// Fetch booking details
$query = "SELECT b.*, h.hall_name, h.location, h.price_per_day, u.full_name, u.email, u.phone 
          FROM bookings b 
          JOIN halls h ON b.hall_id = h.hall_id 
          JOIN users u ON b.user_id = u.user_id 
          WHERE b.booking_id = '$booking_id' AND b.user_id = '$user_id'";

$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    die("Invoice not found or access denied.");
}

$booking = mysqli_fetch_assoc($result);

// Calculate days
$start_date = strtotime($booking['booking_start_date']);
$end_date = strtotime($booking['booking_end_date']);
$days = ceil(abs($end_date - $start_date) / 86400) + 1;

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #
        <?php echo $booking_id; ?> - HallEase
    </title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f5f5f5;
            color: #333;
            padding: 20px;
        }

        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
            border-bottom: 2px solid #eee;
            padding-bottom: 20px;
        }

        .logo {
            font-size: 24px;
            font-weight: 700;
            color: #667eea;
        }

        .invoice-title {
            font-size: 32px;
            font-weight: 700;
            color: #333;
            text-align: right;
        }

        .invoice-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
        }

        .box-title {
            font-size: 14px;
            font-weight: 600;
            color: #888;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .company-info,
        .client-info {
            width: 45%;
        }

        .table-container {
            margin-bottom: 40px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #f9f9f9;
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: #555;
            border-bottom: 1px solid #ddd;
        }

        td {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }

        .total-section {
            display: flex;
            justify-content: flex-end;
            margin-top: 20px;
        }

        .total-box {
            width: 300px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            font-size: 16px;
        }

        .final-total {
            font-size: 20px;
            font-weight: 700;
            color: #667eea;
            border-top: 2px solid #eee;
            padding-top: 10px;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .paid {
            background: #d4edda;
            color: #155724;
        }

        .footer {
            text-align: center;
            margin-top: 50px;
            color: #888;
            font-size: 14px;
        }

        .print-btn {
            background: #333;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            margin-top: 20px;
        }

        .print-btn:hover {
            background: #555;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }

            .invoice-container {
                box-shadow: none;
                padding: 0;
            }

            .print-btn {
                display: none;
            }
        }
    </style>
</head>

<body>

    <div class="invoice-container">
        <div class="header">
            <div class="logo">HallEase.</div>
            <div>
                <div class="invoice-title">INVOICE</div>
                <div style="text-align: right; color: #666;">#
                    <?php echo str_pad($booking_id, 6, '0', STR_PAD_LEFT); ?>
                </div>
                <div style="text-align: right; color: #666; font-size: 14px;">Date:
                    <?php echo date('d M Y', strtotime($booking['created_at'])); ?>
                </div>
            </div>
        </div>

        <div class="invoice-details">
            <div class="company-info">
                <div class="box-title">From</div>
                <strong>HallEase Booking Platform</strong><br>
                123 Event Street, Tech Park<br>
                Bangalore, India 560001<br>
                support@hallease.com
            </div>
            <div class="client-info">
                <div class="box-title">To</div>
                <strong>
                    <?php echo htmlspecialchars($booking['full_name']); ?>
                </strong><br>
                <?php echo htmlspecialchars($booking['email']); ?><br>
                <?php echo htmlspecialchars($booking['phone']); ?>
            </div>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Description</th>
                        <th>Dates</th>
                        <th>Days</th>
                        <th style="text-align: right;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <strong>
                                <?php echo htmlspecialchars($booking['hall_name']); ?>
                            </strong><br>
                            <span style="font-size: 13px; color: #777;">
                                <?php echo htmlspecialchars($booking['location']); ?>
                            </span>
                        </td>
                        <td>
                            <?php echo date('d M Y', strtotime($booking['booking_start_date'])); ?> -
                            <?php echo date('d M Y', strtotime($booking['booking_end_date'])); ?>
                        </td>
                        <td>
                            <?php echo $days; ?>
                        </td>
                        <td style="text-align: right;">₹
                            <?php echo number_format($booking['price_per_day'] * $days, 2); ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="total-section">
            <div class="total-box">
                <div class="total-row">
                    <span>Subtotal:</span>
                    <span>₹
                        <?php echo number_format($booking['total_amount'], 2); ?>
                    </span>
                </div>
                <div class="total-row">
                    <span>Tax (0%):</span>
                    <span>₹0.00</span>
                </div>
                <div class="total-row final-total">
                    <span>Total:</span>
                    <span>₹
                        <?php echo number_format($booking['total_amount'], 2); ?>
                    </span>
                </div>
                <div class="total-row" style="margin-top: 10px;">
                    <span>Status:</span>
                    <span class="status-badge paid">PAID</span>
                </div>
            </div>
        </div>

        <div class="footer">
            <p>Thank you for choosing HallEase!</p>
            <p style="font-size: 12px; margin-top: 5px;">This is a computer generated invoice and does not require a
                signature.</p>

            <button onclick="window.print()" class="print-btn">Print Invoice</button>
            <a href="my_bookings.php" class="print-btn"
                style="text-decoration: none; background: #667eea; margin-left: 10px;">Back to Bookings</a>
        </div>
    </div>

</body>

</html>