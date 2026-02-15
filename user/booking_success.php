<?php
session_start();
require_once '../config/db.php';
require_once '../includes/functions.php';

check_user_login();

$booking_id = (int) ($_GET['booking_id'] ?? 0);

if (!$booking_id) {
    redirect('dashboard.php');
}

// Fetch booking details
$stmt = $pdo->prepare("
    SELECT b.*, h.hall_name, h.location, h.city, h.capacity
    FROM bookings b
    JOIN halls h ON b.hall_id = h.hall_id
    WHERE b.booking_id = :booking_id AND b.user_id = :user_id
");

$stmt->execute([
    ':booking_id' => $booking_id,
    ':user_id' => $_SESSION['user_id']
]);

$booking = $stmt->fetch();

if (!$booking) {
    redirect_with_message('my_bookings.php', 'Booking not found.', 'error');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmed - HallEase</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .success-container {
            max-width: 700px;
            width: 100%;
        }

        .success-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 50px 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            text-align: center;
            animation: slideUp 0.6s ease;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .success-icon {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #34d399, #10b981);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            color: white;
            font-size: 50px;
            box-shadow: 0 15px 40px rgba(16, 185, 129, 0.4);
            animation: scaleIn 0.5s 0.3s both;
        }

        @keyframes scaleIn {
            from {
                transform: scale(0);
            }

            to {
                transform: scale(1);
            }
        }

        .checkmark {
            animation: drawCheck 0.5s 0.5s both;
        }

        h1 {
            color: #1e293b;
            margin-bottom: 15px;
            font-size: 2.5rem;
        }

        .subtitle {
            color: #64748b;
            margin-bottom: 40px;
            font-size: 1.1rem;
        }

        .booking-details {
            background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
            padding: 30px;
            border-radius: 16px;
            margin-bottom: 30px;
            text-align: left;
            border-left: 4px solid #0ea5e9;
        }

        .booking-id {
            background: #0369a1;
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 700;
            display: inline-block;
            margin-bottom: 20px;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding: 12px 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-row span:first-child {
            color: #64748b;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .detail-row span:last-child {
            font-weight: 600;
            color: #1e293b;
        }

        .detail-row i {
            color: #0ea5e9;
            width: 20px;
        }

        .status-confirmed {
            background: linear-gradient(135deg, #34d399, #10b981);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            padding: 14px 30px;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'Poppins', sans-serif;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(102, 126, 234, 0.6);
        }

        .btn-secondary {
            background: #f1f5f9;
            color: #475569;
            border: 2px solid #e2e8f0;
        }

        .btn-secondary:hover {
            background: #e2e8f0;
        }

        .info-box {
            background: rgba(34, 197, 94, 0.1);
            border: 1px solid rgba(34, 197, 94, 0.3);
            padding: 20px;
            border-radius: 12px;
            margin-top: 30px;
            color: #166534;
            text-align: left;
        }

        .info-box h3 {
            margin-bottom: 15px;
            color: #15803d;
        }

        .info-box ul {
            list-style: none;
            padding: 0;
        }

        .info-box li {
            margin-bottom: 10px;
            display: flex;
            align-items: start;
            gap: 10px;
        }

        .info-box li i {
            color: #16a34a;
            margin-top: 3px;
        }

        .confetti {
            position: fixed;
            width: 10px;
            height: 10px;
            background: #f59e0b;
            position: absolute;
            animation: confetti-fall 3s linear infinite;
        }

        @keyframes confetti-fall {
            to {
                transform: translateY(100vh) rotate(360deg);
                opacity: 0;
            }
        }
    </style>
</head>

<body>
    <div class="success-container">
        <div class="success-card">
            <div class="success-icon">
                <i class="fas fa-check checkmark"></i>
            </div>

            <h1>Booking Confirmed!</h1>
            <p class="subtitle">Your hall has been successfully booked. We've sent a confirmation email.</p>

            <div class="booking-details">
                <div class="booking-id">
                    Booking ID: #
                    <?php echo str_pad($booking['booking_id'], 6, '0', STR_PAD_LEFT); ?>
                </div>

                <div class="detail-row">
                    <span><i class="fas fa-building"></i> Hall Name</span>
                    <span>
                        <?php echo htmlspecialchars($booking['hall_name']); ?>
                    </span>
                </div>

                <div class="detail-row">
                    <span><i class="fas fa-map-marker-alt"></i> Location</span>
                    <span>
                        <?php echo htmlspecialchars($booking['location'] . ', ' . $booking['city']); ?>
                    </span>
                </div>

                <div class="detail-row">
                    <span><i class="fas fa-calendar-alt"></i> Check-in</span>
                    <span>
                        <?php echo format_date($booking['booking_start_date']); ?>
                    </span>
                </div>

                <div class="detail-row">
                    <span><i class="fas fa-calendar-check"></i> Check-out</span>
                    <span>
                        <?php echo format_date($booking['booking_end_date']); ?>
                    </span>
                </div>

                <div class="detail-row">
                    <span><i class="fas fa-clock"></i> Duration</span>
                    <span>
                        <?php echo $booking['total_days']; ?> day(s)
                    </span>
                </div>

                <div class="detail-row">
                    <span><i class="fas fa-money-bill-wave"></i> Total Amount</span>
                    <span>
                        <?php echo format_currency($booking['total_amount']); ?>
                    </span>
                </div>

                <div class="detail-row">
                    <span><i class="fas fa-info-circle"></i> Status</span>
                    <span class="status-confirmed">
                        <i class="fas fa-check-circle"></i> Confirmed
                    </span>
                </div>

                <div class="detail-row">
                    <span><i class="fas fa-credit-card"></i> Payment</span>
                    <span class="status-confirmed">
                        <i class="fas fa-check-circle"></i> Paid
                    </span>
                </div>
            </div>

            <div class="actions">
                <a href="my_bookings.php" class="btn btn-primary">
                    <i class="fas fa-list"></i> View My Bookings
                </a>
                <a href="dashboard.php" class="btn btn-secondary">
                    <i class="fas fa-home"></i> Back to Home
                </a>
            </div>

            <div class="info-box">
                <h3><i class="fas fa-lightbulb"></i> What's Next?</h3>
                <ul>
                    <li>
                        <i class="fas fa-check-circle"></i>
                        <span>A confirmation email has been sent to your registered email address.</span>
                    </li>
                    <li>
                        <i class="fas fa-check-circle"></i>
                        <span>You can download your invoice from the "My Bookings" section.</span>
                    </li>
                    <li>
                        <i class="fas fa-check-circle"></i>
                        <span>Contact the hall owner for any special arrangements or questions.</span>
                    </li>
                    <li>
                        <i class="fas fa-check-circle"></i>
                        <span>You can cancel your booking up to 24 hours before the event date.</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <script>
        // Create confetti effect
        function createConfetti() {
            const colors = ['#f59e0b', '#10b981', '#3b82f6', '#8b5cf6', '#ec4899'];
            for (let i = 0; i < 50; i++) {
                const confetti = document.createElement('div');
                confetti.classList.add('confetti');
                confetti.style.left = Math.random() * 100 + '%';
                confetti.style.animationDelay = Math.random() * 3 + 's';
                confetti.style.background = colors[Math.floor(Math.random() * colors.length)];
                document.body.appendChild(confetti);

                setTimeout(() => confetti.remove(), 3000);
            }
        }

        // Trigger confetti on load
        createConfetti();
    </script>
</body>

</html>