<?php 
include '../includes/auth.php';
include '../includes/navbar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | HallEase</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <!-- Animated Background -->
    <div class="bg-shapes">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>
        <div class="shape shape-4"></div>
    </div>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <div class="welcome-badge">
                <i class="fas fa-hand-sparkles"></i>
                <span>Welcome Back!</span>
            </div>
            <h1 class="hero-title">
                Book Your Perfect <span class="gradient-text">Event Hall</span>
            </h1>
            <p class="hero-description">
                Discover premium halls for weddings, conferences, parties, and programs.
                Easy booking, instant confirmation, and trusted venues — all in one place.
            </p>
            <div class="hero-decorations">
                <div class="decoration-icon">🎉</div>
                <div class="decoration-icon">🎊</div>
                <div class="decoration-icon">✨</div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="stat-icon icon-purple">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="stat-content">
                        <h2 class="stat-number">20+</h2>
                        <p class="stat-label">Verified Event Halls</p>
                    </div>
                    <div class="stat-decoration"></div>
                </div>

                <div class="stat-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="stat-icon icon-blue">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-content">
                        <h2 class="stat-number">500+</h2>
                        <p class="stat-label">Happy Customers</p>
                    </div>
                    <div class="stat-decoration"></div>
                </div>

                <div class="stat-card" data-aos="fade-up" data-aos-delay="300">
                    <div class="stat-icon icon-pink">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stat-content">
                        <h2 class="stat-number">1000+</h2>
                        <p class="stat-label">Successful Events</p>
                    </div>
                    <div class="stat-decoration"></div>
                </div>

                <div class="stat-card" data-aos="fade-up" data-aos-delay="400">
                    <div class="stat-icon icon-green">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="stat-content">
                        <h2 class="stat-number">4.9</h2>
                        <p class="stat-label">Average Rating</p>
                    </div>
                    <div class="stat-decoration"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Quick Actions Section -->
    <section class="actions-section">
        <div class="container">
            <div class="section-header">
                <h2>Quick Actions</h2>
                <p>Get started with your event planning journey</p>
            </div>
            
            <div class="actions-grid">
                <a href="book_hall.php" class="action-card primary-action" data-aos="zoom-in" data-aos-delay="100">
                    <div class="action-icon">
                        <i class="fas fa-calendar-plus"></i>
                    </div>
                    <div class="action-content">
                        <h3>Book a Hall</h3>
                        <p>Browse and book your perfect venue</p>
                    </div>
                    <div class="action-arrow">
                        <i class="fas fa-arrow-right"></i>
                    </div>
                    <div class="action-glow"></div>
                </a>

                <a href="my_bookings.php" class="action-card secondary-action" data-aos="zoom-in" data-aos-delay="200">
                    <div class="action-icon">
                        <i class="fas fa-list-check"></i>
                    </div>
                    <div class="action-content">
                        <h3>My Bookings</h3>
                        <p>View and manage your reservations</p>
                    </div>
                    <div class="action-arrow">
                        <i class="fas fa-arrow-right"></i>
                    </div>
                    <div class="action-glow"></div>
                </a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section">
        <div class="container">
            <div class="features-grid">
                <div class="feature-item" data-aos="fade-right" data-aos-delay="100">
                    <div class="feature-icon">
                        <i class="fas fa-shield-halved"></i>
                    </div>
                    <div class="feature-text">
                        <h4>100% Secure</h4>
                        <p>Safe & encrypted bookings</p>
                    </div>
                </div>

                <div class="feature-item" data-aos="fade-up" data-aos-delay="200">
                    <div class="feature-icon">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <div class="feature-text">
                        <h4>Instant Confirmation</h4>
                        <p>Book in seconds</p>
                    </div>
                </div>

                <div class="feature-item" data-aos="fade-left" data-aos-delay="300">
                    <div class="feature-icon">
                        <i class="fas fa-badge-check"></i>
                    </div>
                    <div class="feature-text">
                        <h4>Verified Venues</h4>
                        <p>Trusted locations only</p>
                    </div>
                </div>

                <div class="feature-item" data-aos="fade-right" data-aos-delay="400">
                    <div class="feature-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <div class="feature-text">
                        <h4>24/7 Support</h4>
                        <p>Always here to help</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Slider (Optional) -->
    <section class="testimonials-section">
        <div class="container">
            <div class="section-header">
                <h2>What Our Customers Say</h2>
                <p>Real experiences from real people</p>
            </div>
            
            <div class="testimonials-grid">
                <div class="testimonial-card" data-aos="flip-left" data-aos-delay="100">
                    <div class="quote-icon">
                        <i class="fas fa-quote-left"></i>
                    </div>
                    <p class="testimonial-text">
                        "HallEase made booking our wedding venue incredibly easy. The process was smooth and the venue exceeded our expectations!"
                    </p>
                    <div class="testimonial-author">
                        <div class="author-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="author-info">
                            <h5>Priya Sharma</h5>
                            <div class="rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="testimonial-card" data-aos="flip-left" data-aos-delay="200">
                    <div class="quote-icon">
                        <i class="fas fa-quote-left"></i>
                    </div>
                    <p class="testimonial-text">
                        "Professional service, amazing venues, and great support. Perfect for corporate events and conferences!"
                    </p>
                    <div class="testimonial-author">
                        <div class="author-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="author-info">
                            <h5>Rahul Patel</h5>
                            <div class="rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="testimonial-card" data-aos="flip-left" data-aos-delay="300">
                    <div class="quote-icon">
                        <i class="fas fa-quote-left"></i>
                    </div>
                    <p class="testimonial-text">
                        "Best platform for event bookings! Saved us time and money. Highly recommended for any occasion."
                    </p>
                    <div class="testimonial-author">
                        <div class="author-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="author-info">
                            <h5>Anita Desai</h5>
                            <div class="rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

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
            overflow-x: hidden;
            position: relative;
        }

        /* Animated Background Shapes */
        .bg-shapes {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
            pointer-events: none;
        }

        .shape {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.4;
            animation: float 25s infinite ease-in-out;
        }

        .shape-1 {
            width: 400px;
            height: 400px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            top: -100px;
            left: -100px;
            animation-delay: 0s;
        }

        .shape-2 {
            width: 500px;
            height: 500px;
            background: linear-gradient(135deg, #f093fb, #f5576c);
            top: 40%;
            right: -150px;
            animation-delay: 8s;
        }

        .shape-3 {
            width: 350px;
            height: 350px;
            background: linear-gradient(135deg, #4facfe, #00f2fe);
            bottom: -100px;
            left: 30%;
            animation-delay: 16s;
        }

        .shape-4 {
            width: 300px;
            height: 300px;
            background: linear-gradient(135deg, #43e97b, #38f9d7);
            top: 60%;
            left: -100px;
            animation-delay: 12s;
        }

        @keyframes float {
            0%, 100% {
                transform: translate(0, 0) rotate(0deg) scale(1);
            }
            33% {
                transform: translate(50px, -50px) rotate(120deg) scale(1.1);
            }
            66% {
                transform: translate(-50px, 50px) rotate(240deg) scale(0.9);
            }
        }

        /* Container */
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
            position: relative;
            z-index: 1;
        }

        /* Hero Section */
        .hero {
            text-align: center;
            padding: 100px 20px 80px;
            position: relative;
            z-index: 1;
        }

        .hero-content {
            max-width: 900px;
            margin: 0 auto;
        }

        .welcome-badge {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: rgba(255, 255, 255, 0.95);
            padding: 12px 24px;
            border-radius: 50px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            animation: fadeInDown 0.8s ease;
            font-weight: 600;
            color: #667eea;
        }

        .welcome-badge i {
            font-size: 20px;
            animation: wave 1.5s infinite;
        }

        @keyframes wave {
            0%, 100% { transform: rotate(0deg); }
            25% { transform: rotate(20deg); }
            75% { transform: rotate(-20deg); }
        }

        .hero-title {
            font-size: 4rem;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 25px;
            line-height: 1.2;
            text-shadow: 0 4px 30px rgba(0, 0, 0, 0.3);
            animation: fadeInUp 0.8s ease 0.2s both;
        }

        .gradient-text {
            background: linear-gradient(135deg, #fff, #fde68a);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            position: relative;
            display: inline-block;
        }

        .hero-description {
            font-size: 1.25rem;
            color: rgba(255, 255, 255, 0.95);
            max-width: 750px;
            margin: 0 auto 30px;
            line-height: 1.8;
            animation: fadeInUp 0.8s ease 0.4s both;
        }

        .hero-decorations {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-top: 40px;
            animation: fadeInUp 0.8s ease 0.6s both;
        }

        .decoration-icon {
            font-size: 40px;
            animation: bounce 2s infinite;
        }

        .decoration-icon:nth-child(2) {
            animation-delay: 0.3s;
        }

        .decoration-icon:nth-child(3) {
            animation-delay: 0.6s;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }

        /* Stats Section */
        .stats-section {
            padding: 40px 0 60px;
            position: relative;
            z-index: 1;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            padding: 40px 30px;
            border-radius: 24px;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-15px) scale(1.02);
            box-shadow: 0 30px 80px rgba(0, 0, 0, 0.3);
        }

        .stat-decoration {
            position: absolute;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(102, 126, 234, 0.1), transparent);
            border-radius: 50%;
            top: -50px;
            right: -50px;
            pointer-events: none;
        }

        .stat-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            color: white;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .icon-purple {
            background: linear-gradient(135deg, #667eea, #764ba2);
        }

        .icon-blue {
            background: linear-gradient(135deg, #4facfe, #00f2fe);
        }

        .icon-pink {
            background: linear-gradient(135deg, #f093fb, #f5576c);
        }

        .icon-green {
            background: linear-gradient(135deg, #43e97b, #38f9d7);
        }

        .stat-number {
            font-size: 3rem;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 10px;
        }

        .stat-label {
            font-size: 1rem;
            color: #64748b;
            font-weight: 600;
        }

        /* Section Header */
        .section-header {
            text-align: center;
            margin-bottom: 50px;
        }

        .section-header h2 {
            font-size: 2.5rem;
            color: #ffffff;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .section-header p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 1.1rem;
        }

        /* Actions Section */
        .actions-section {
            padding: 60px 0;
            position: relative;
            z-index: 1;
        }

        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
            max-width: 900px;
            margin: 0 auto;
        }

        .action-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            padding: 40px;
            border-radius: 24px;
            display: flex;
            align-items: center;
            gap: 25px;
            text-decoration: none;
            color: inherit;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
        }

        .action-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 30px 80px rgba(0, 0, 0, 0.3);
        }

        .action-glow {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            opacity: 0;
            transition: opacity 0.4s ease;
            pointer-events: none;
        }

        .primary-action:hover .action-glow {
            opacity: 1;
            background: radial-gradient(circle at center, rgba(102, 126, 234, 0.1), transparent);
        }

        .secondary-action:hover .action-glow {
            opacity: 1;
            background: radial-gradient(circle at center, rgba(67, 233, 123, 0.1), transparent);
        }

        .action-icon {
            width: 70px;
            height: 70px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            color: white;
            flex-shrink: 0;
        }

        .primary-action .action-icon {
            background: linear-gradient(135deg, #667eea, #764ba2);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }

        .secondary-action .action-icon {
            background: linear-gradient(135deg, #43e97b, #38f9d7);
            box-shadow: 0 8px 25px rgba(67, 233, 123, 0.4);
        }

        .action-content {
            flex: 1;
        }

        .action-content h3 {
            font-size: 1.5rem;
            color: #1e293b;
            margin-bottom: 8px;
            font-weight: 700;
        }

        .action-content p {
            color: #64748b;
            font-size: 0.95rem;
        }

        .action-arrow {
            font-size: 24px;
            color: #94a3b8;
            transition: transform 0.3s ease;
        }

        .action-card:hover .action-arrow {
            transform: translateX(10px);
            color: #667eea;
        }

        /* Features Section */
        .features-section {
            padding: 60px 0 80px;
            position: relative;
            z-index: 1;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .feature-item {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            padding: 30px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            gap: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }

        .feature-item:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateX(10px);
        }

        .feature-icon {
            width: 60px;
            height: 60px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: #ffffff;
            flex-shrink: 0;
        }

        .feature-text h4 {
            color: #ffffff;
            font-size: 1.1rem;
            margin-bottom: 5px;
            font-weight: 600;
        }

        .feature-text p {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.9rem;
        }

        /* Testimonials Section */
        .testimonials-section {
            padding: 60px 0 100px;
            position: relative;
            z-index: 1;
        }

        .testimonials-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 30px;
        }

        .testimonial-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            padding: 35px;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }

        .testimonial-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 30px 80px rgba(0, 0, 0, 0.3);
        }

        .quote-icon {
            font-size: 32px;
            color: #667eea;
            margin-bottom: 20px;
            opacity: 0.3;
        }

        .testimonial-text {
            font-size: 1rem;
            color: #475569;
            line-height: 1.8;
            margin-bottom: 25px;
            font-style: italic;
        }

        .testimonial-author {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .author-avatar {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
        }

        .author-info h5 {
            color: #1e293b;
            font-size: 1rem;
            margin-bottom: 5px;
            font-weight: 600;
        }

        .rating {
            color: #fbbf24;
            font-size: 14px;
        }

        /* Animations */
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }

            .hero-description {
                font-size: 1.1rem;
            }

            .stats-grid,
            .actions-grid,
            .features-grid,
            .testimonials-grid {
                grid-template-columns: 1fr;
            }

            .section-header h2 {
                font-size: 2rem;
            }

            .decoration-icon {
                font-size: 30px;
            }
        }

        @media (max-width: 480px) {
            .hero {
                padding: 60px 20px 40px;
            }

            .hero-title {
                font-size: 2rem;
            }

            .stat-number {
                font-size: 2.5rem;
            }

            .action-card {
                flex-direction: column;
                text-align: center;
            }

            .action-arrow {
                transform: rotate(90deg);
            }

            .action-card:hover .action-arrow {
                transform: rotate(90deg) translateX(10px);
            }
        }

        /* Smooth scroll */
        html {
            scroll-behavior: smooth;
        }

        /* Loading animation */
        [data-aos] {
            opacity: 0;
            transition: opacity 0.6s ease, transform 0.6s ease;
        }

        [data-aos].aos-animate {
            opacity: 1;
        }
    </style>

    <script>
        // Simple AOS (Animate On Scroll) implementation
        document.addEventListener('DOMContentLoaded', function() {
            const elements = document.querySelectorAll('[data-aos]');
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('aos-animate');
                    }
                });
            }, {
                threshold: 0.1
            });

            elements.forEach(el => {
                observer.observe(el);
            });
        });

        // Counter animation for stats
        function animateCounter(element, target, duration = 2000) {
            let start = 0;
            const increment = target / (duration / 16);
            
            const timer = setInterval(() => {
                start += increment;
                if (start >= target) {
                    element.textContent = target + (element.textContent.includes('+') ? '+' : '');
                    clearInterval(timer);
                } else {
                    element.textContent = Math.floor(start) + (element.textContent.includes('+') ? '+' : '');
                }
            }, 16);
        }

        // Trigger counter animation when stats come into view
        const statNumbers = document.querySelectorAll('.stat-number');
        const statsObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !entry.target.classList.contains('counted')) {
                    entry.target.classList.add('counted');
                    const text = entry.target.textContent;
                    const number = parseFloat(text);
                    animateCounter(entry.target, number);
                }
            });
        }, { threshold: 0.5 });

        statNumbers.forEach(stat => statsObserver.observe(stat));
    </script>
</body>
</html>