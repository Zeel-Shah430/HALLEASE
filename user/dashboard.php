<?php 
include '../includes/auth.php';
include '../includes/navbar.php';


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard | HallEase</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #e8f5e9, #e3f2fd);
            color: #1f2937;
        }

        /* NAVBAR */
        .navbar {
            background: #ffffff;
            padding: 14px 40px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .logo {
            font-size: 22px;
            font-weight: 700;
            color: #7b1fa2;
        }

        .nav-links a {
            margin-left: 25px;
            text-decoration: none;
            font-weight: 600;
            color: #333;
        }

        .nav-links a.logout {
            color: #d32f2f;
        }

        /* HERO */
        .hero {
            text-align: center;
            padding: 80px 20px 50px;
        }

        .hero h1 {
            font-size: 48px;
            font-weight: 800;
            margin-bottom: 15px;
        }

        .hero h1 span {
            background: linear-gradient(90deg, #7b1fa2, #0288d1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero p {
            font-size: 18px;
            max-width: 720px;
            margin: 0 auto;
            color: #4b5563;
        }

        /* STATS */
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 25px;
            max-width: 1000px;
            margin: 60px auto;
            padding: 0 20px;
        }

        .stat-card {
            background: #ffffff;
            padding: 30px;
            border-radius: 16px;
            text-align: center;
            box-shadow: 0 20px 40px rgba(0,0,0,0.08);
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-6px);
        }

        .stat-card h2 {
            font-size: 36px;
            color: #7b1fa2;
            margin-bottom: 8px;
        }

        .stat-card p {
            font-weight: 600;
            color: #374151;
        }

        /* ACTIONS */
        .actions {
            display: flex;
            justify-content: center;
            gap: 25px;
            margin-bottom: 70px;
            flex-wrap: wrap;
        }

        .action-btn {
            padding: 14px 30px;
            border-radius: 30px;
            font-size: 16px;
            font-weight: 700;
            text-decoration: none;
            color: #fff;
            background: linear-gradient(135deg, #7b1fa2, #0288d1);
            box-shadow: 0 10px 25px rgba(123, 31, 162, 0.35);
            transition: all 0.3s ease;
        }

        .action-btn.secondary {
            background: linear-gradient(135deg, #43a047, #2e7d32);
        }

        .action-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.25);
        }

        /* FOOTER INFO */
        .features {
            display: flex;
            justify-content: center;
            gap: 40px;
            padding-bottom: 40px;
            font-weight: 600;
            color: #2e7d32;
            flex-wrap: wrap;
        }

        @media (max-width: 768px) {
            .hero h1 {
                font-size: 36px;
            }
        }
    </style>
</head>

<body>


<!-- HERO -->
<section class="hero">
    <h1>Book Your Perfect <span>Event Hall</span></h1>
    <p>
        Discover premium halls for weddings, conferences, parties, and programs.
        Easy booking, instant confirmation, and trusted venues — all in one place.
    </p>
</section>

<!-- STATS -->
<section class="stats">
    <div class="stat-card">
        <h2>20+</h2>
        <p>Verified Event Halls</p>
    </div>

    <div class="stat-card">
        <h2>500+</h2>
        <p>Happy Customers</p>
    </div>

    <div class="stat-card">
        <h2>1000+</h2>
        <p>Successful Events</p>
    </div>
</section>

<!-- ACTIONS -->
<div class="actions">
    <a href="book_hall.php" class="action-btn">Book a Hall</a>
    <a href="my_bookings.php" class="action-btn secondary">View My Bookings</a>
</div>

<!-- FEATURES -->
<div class="features">
    <div>✔ 100% Secure Booking</div>
    <div>✔ Instant Confirmation</div>
    <div>✔ Verified Venues</div>
</div>

</body>
</html>
