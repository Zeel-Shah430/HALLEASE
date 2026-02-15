<?php
session_start();
include '../includes/functions.php';

// If user is logged in, redirect to book_hall or create a nice landing
// But usually dashboard is where they land.
// Let's make dashboard.php the main landing page.

$user_name = $_SESSION['user_name'] ?? 'Guest';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | HallEase</title>
    <link href="../assets/css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600&display=swap"
        rel="stylesheet">
</head>

<body>

    <!-- Navbar -->
    <?php include '../includes/navbar.php'; ?>

    <!-- Hero Section -->
    <section class="hero" style="text-align: center; padding: 120px 0;">
        <div class="hero-shapes">
            <div class="shape-1"></div>
            <div class="shape-2"></div>
            <div class="shape-3"></div>
        </div>
        <div class="container hero-content">
            <div
                style="background: rgba(255,255,255,0.2); display: inline-block; padding: 5px 15px; border-radius: 20px; margin-bottom: 20px; backdrop-filter: blur(5px);">
                <i class="fas fa-sparkles text-warning"></i> Welcome back, <?php echo htmlspecialchars($user_name); ?>!
            </div>

            <h1 class="hero-title">
                Find & Book The Perfect <br> <span style="color: #ffd700;">Event Venue</span>
            </h1>

            <p class="hero-description" style="max-width: 600px; margin: 0 auto 40px; font-size: 1.1rem; opacity: 0.9;">
                Weddings, Corporate Events, Parties & More. <br>
                Discover premium halls with instant booking confirmation.
            </p>

            <div style="display: flex; gap: 15px; justify-content: center;">
                <a href="book_hall.php" class="btn" style="background: white; color: var(--primary); font-weight: 600;">
                    <i class="fas fa-search"></i> Browse Halls
                </a>
                <a href="my_bookings.php" class="btn"
                    style="background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.4);">
                    <i class="fas fa-calendar-alt"></i> My Bookings
                </a>
            </div>
        </div>
    </section>

    <!-- Features / Stats -->
    <section class="section">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-card" style="border-top: 4px solid var(--primary);">
                    <div class="stat-icon" style="background: var(--gray-100); color: var(--primary);">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="stat-info">
                        <h3>20+</h3>
                        <p>Verified Halls</p>
                    </div>
                </div>

                <div class="stat-card" style="border-top: 4px solid var(--info);">
                    <div class="stat-icon" style="background: var(--gray-100); color: var(--info);">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <h3>500+</h3>
                        <p>Happy Guests</p>
                    </div>
                </div>

                <div class="stat-card" style="border-top: 4px solid var(--success);">
                    <div class="stat-icon" style="background: var(--gray-100); color: var(--success);">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-info">
                        <h3>100%</h3>
                        <p>Secure Booking</p>
                    </div>
                </div>

                <div class="stat-card" style="border-top: 4px solid var(--warning);">
                    <div class="stat-icon" style="background: var(--gray-100); color: var(--warning);">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="stat-info">
                        <h3>4.9</h3>
                        <p>Average Rating</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Quick Steps -->
    <section class="section" style="background: var(--white);">
        <div class="container text-center">
            <h2 class="mb-20">How It Works</h2>
            <p class="text-secondary mb-20">Book your dream venue in 3 simple steps</p>

            <div class="card-grid" style="grid-template-columns: repeat(3, 1fr); gap: 40px; margin-top: 40px;">
                <div style="text-align: center; padding: 20px;">
                    <div
                        style="width: 80px; height: 80px; background: var(--gray-100); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; font-size: 2rem; color: var(--primary);">
                        <i class="fas fa-search-location"></i>
                    </div>
                    <h3>1. Find a Hall</h3>
                    <p class="text-secondary">Browse our curated list of premium venues.</p>
                </div>

                <div style="text-align: center; padding: 20px;">
                    <div
                        style="width: 80px; height: 80px; background: var(--gray-100); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; font-size: 2rem; color: var(--info);">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <h3>2. Select Dates</h3>
                    <p class="text-secondary">Choose your event dates and see exact pricing.</p>
                </div>

                <div style="text-align: center; padding: 20px;">
                    <div
                        style="width: 80px; height: 80px; background: var(--gray-100); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; font-size: 2rem; color: var(--success);">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <h3>3. Book Securely</h3>
                    <p class="text-secondary">Pay securely via Razorpay to confirm your booking.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer style="background: var(--dark); color: white; padding: 60px 0;">
        <div class="container">
            <div class="d-flex justify-between" style="flex-wrap: wrap; gap: 40px;">
                <div style="max-width: 300px;">
                    <h2 style="color: white; margin-bottom: 20px;">HallEase.</h2>
                    <p style="color: var(--gray-400);">Based in India, HallEase is your trusted partner for finding and
                        booking the perfect venue for your special moments.</p>
                </div>
                <div>
                    <h4 style="color: white; margin-bottom: 20px;">Quick Links</h4>
                    <ul style="opacity: 0.8;">
                        <li style="margin-bottom: 10px;"><a href="dashboard.php" style="color: white;">Home</a></li>
                        <li style="margin-bottom: 10px;"><a href="book_hall.php" style="color: white;">Browse Halls</a>
                        </li>
                        <li style="margin-bottom: 10px;"><a href="my_bookings.php" style="color: white;">My Bookings</a>
                        </li>
                    </ul>
                </div>
                <div>
                    <h4 style="color: white; margin-bottom: 20px;">Contact</h4>
                    <p style="color: var(--gray-400);">hello@hallease.com</p>
                    <p style="color: var(--gray-400);">+91 98765 43210</p>
                </div>
            </div>
            <div
                style="border-top: 1px solid var(--gray-600); margin-top: 40px; padding-top: 20px; text-align: center; color: var(--gray-400);">
                &copy; <?php echo date('Y'); ?> HallEase. All rights reserved.
            </div>
        </div>
    </footer>

</body>

</html>