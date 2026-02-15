<?php
session_start();
require_once '../config/db.php';
require_once '../config/razorpay.php';
require_once '../includes/functions.php';

// Check if user is logged in
check_user_login();

// Check if there's a pending booking
if (!isset($_SESSION['pending_booking'])) {
    redirect_with_message('dashboard.php', 'No pending booking found.', 'error');
}

$booking = $_SESSION['pending_booking'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Payment - HallEase</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
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

        .payment-container {
            max-width: 600px;
            width: 100%;
        }

        .payment-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            text-align: center;
        }

        .payment-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            color: white;
            font-size: 35px;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
        }

        h1 {
            color: #1e293b;
            margin-bottom: 10px;
            font-size: 2rem;
        }

        .subtitle {
            color: #64748b;
            margin-bottom: 30px;
        }

        .booking-summary {
            background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
            padding: 25px;
            border-radius: 16px;
            margin-bottom: 30px;
            text-align: left;
            border-left: 4px solid #0ea5e9;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            color: #475569;
        }

        .summary-row strong {
            color: #1e293b;
        }

        .summary-row.total {
            font-size: 1.4rem;
            font-weight: 700;
            padding-top: 15px;
            margin-top: 15px;
            border-top: 2px solid #bae6fd;
            color: #0369a1;
        }

        .btn {
            padding: 16px 40px;
            border: none;
            border-radius: 12px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'Poppins', sans-serif;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            margin: 10px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.6);
        }

        .btn-secondary {
            background: #e2e8f0;
            color: #475569;
        }

        .btn-secondary:hover {
            background: #cbd5e1;
        }

        .security-note {
            background: rgba(34, 197, 94, 0.1);
            border: 1px solid rgba(34, 197, 94, 0.3);
            padding: 15px;
            border-radius: 12px;
            margin-top: 25px;
            color: #166534;
            font-size: 14px;
        }

        .security-note i {
            color: #16a34a;
            margin-right: 8px;
        }

        .timer {
            background: rgba(245, 158, 11, 0.1);
            border: 1px solid rgba(245, 158, 11, 0.3);
            padding: 12px;
            border-radius: 12px;
            margin-bottom: 20px;
            color: #92400e;
            font-weight: 600;
        }

        .timer i {
            color: #f59e0b;
            margin-right: 8px;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.7;
            }
        }

        .timer span {
            animation: pulse 2s infinite;
        }
    </style>
</head>

<body>
    <div class="payment-container">
        <div class="payment-card">
            <div class="payment-icon">
                <i class="fas fa-lock"></i>
            </div>

            <h1>Complete Your Payment</h1>
            <p class="subtitle">You're one step away from booking your perfect hall!</p>

            <div class="timer">
                <i class="fas fa-clock"></i>
                Complete payment within <span>
                    <?php echo PAYMENT_TIMEOUT_MINUTES; ?> minutes
                </span> to secure your booking
            </div>

            <div class="booking-summary">
                <h3 style="margin-bottom: 20px; color: #0369a1;">
                    <i class="fas fa-file-invoice"></i> Booking Summary
                </h3>

                <div class="summary-row">
                    <span><i class="fas fa-building"></i> Hall:</span>
                    <strong>
                        <?php echo htmlspecialchars($booking['hall_name']); ?>
                    </strong>
                </div>

                <div class="summary-row">
                    <span><i class="fas fa-calendar-alt"></i> From:</span>
                    <strong>
                        <?php echo format_date($booking['from_date']); ?>
                    </strong>
                </div>

                <div class="summary-row">
                    <span><i class="fas fa-calendar-check"></i> To:</span>
                    <strong>
                        <?php echo format_date($booking['to_date']); ?>
                    </strong>
                </div>

                <div class="summary-row">
                    <span><i class="fas fa-clock"></i> Total Days:</span>
                    <strong>
                        <?php echo $booking['total_days']; ?> day(s)
                    </strong>
                </div>

                <div class="summary-row total">
                    <span>Total Amount:</span>
                    <span>
                        <?php echo format_currency($booking['amount']); ?>
                    </span>
                </div>
            </div>

            <button id="payButton" class="btn btn-primary">
                <i class="fas fa-credit-card"></i> Pay Now with Razorpay
            </button>

            <a href="my_bookings.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Pay Later
            </a>

            <div class="security-note">
                <i class="fas fa-shield-halved"></i>
                <strong>100% Secure Payment</strong> - Your payment information is encrypted and secure.
            </div>
        </div>
    </div>

    <script>
        const payButton = document.getElementById('payButton');

        const options = {
            "key": "<?php echo RAZORPAY_KEY_ID; ?>",
            "amount": "<?php echo $booking['amount'] * 100; ?>",
            "currency": "INR",
            "name": "HallEase",
            "description": "Hall Booking Payment",
            "order_id": "<?php echo $booking['order_id']; ?>",
            "handler": function (response) {
                // Payment successful, verify on server
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'verify_payment.php';

                const fields = {
                    'razorpay_payment_id': response.razorpay_payment_id,
                    'razorpay_order_id': response.razorpay_order_id,
                    'razorpay_signature': response.razorpay_signature,
                    'booking_id': '<?php echo $booking['booking_id']; ?>'
                };

                for (const key in fields) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = key;
                    input.value = fields[key];
                    form.appendChild(input);
                }

                document.body.appendChild(form);
                form.submit();
            },
            "prefill": {
                "name": "<?php echo $_SESSION['user_name'] ?? 'Customer'; ?>",
                "email": "<?php echo $_SESSION['user_email'] ?? ''; ?>",
                "contact": "<?php echo $_SESSION['user_phone'] ?? ''; ?>"
            },
            "theme": {
                "color": "#667eea"
            },
            "modal": {
                "ondismiss": function () {
                    alert('Payment cancelled. You can complete the payment from "My Bookings" within 15 minutes.');
                    window.location.href = 'my_bookings.php';
                }
            }
        };

        const rzp = new Razorpay(options);

        payButton.addEventListener('click', function (e) {
            e.preventDefault();
            rzp.open();
        });

        // Auto-open payment modal on page load (optional)
        // rzp.open();
    </script>
</body>

</html>