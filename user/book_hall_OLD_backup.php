<?php
include '../config/db.php';
include '../includes/navbar.php';
include '../includes/auth.php';
$query = "SELECT * FROM halls WHERE status='available' LIMIT 5";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Your Perfect Hall</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Hero Section -->
    <div class="hero-section">
        <div class="hero-content">
            <h1 class="hero-title">Find Your Perfect Event Hall</h1>
            <p class="hero-subtitle">Discover premium venues for your special occasions</p>
            <div class="search-bar">
                <i class="fas fa-search search-icon"></i>
                <input type="text" placeholder="Search by location, capacity, or name..." id="searchInput">
            </div>
        </div>
        <div class="hero-shapes">
            <div class="shape shape-1"></div>
            <div class="shape shape-2"></div>
            <div class="shape shape-3"></div>
        </div>
    </div>

    <!-- Halls Grid -->
    <div class="container">
        <div class="section-header">
            <h2>Available Halls</h2>
            <p>Choose from our carefully curated selection of premium event spaces</p>
        </div>

        <div class="hall-container">
            <?php while($row = mysqli_fetch_assoc($result)) { ?>
                <div class="hall-card" data-aos="fade-up">
                    <!-- Card Header with Image Placeholder -->
                    <div class="card-header">
                        <div class="hall-image">
                            <div class="image-overlay">
                                <span class="status-badge">
                                    <i class="fas fa-check-circle"></i> Available
                                </span>
                            </div>
                        </div>
                        <div class="price-badge">
                            <span class="currency">₹</span>
                            <span class="amount"><?php echo number_format($row['price']); ?></span>
                            <span class="period">/day</span>
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div class="card-body">
                        <h3 class="hall-title"><?php echo htmlspecialchars($row['hall_name']); ?></h3>
                        
                        <div class="hall-details">
                            <div class="detail-item">
                                <i class="fas fa-map-marker-alt icon-location"></i>
                                <span><?php echo htmlspecialchars($row['location']); ?></span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-users icon-capacity"></i>
                                <span>Up to <?php echo htmlspecialchars($row['capacity']); ?> guests</span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-building icon-facilities"></i>
                                <span><?php echo htmlspecialchars($row['facilities']); ?></span>
                            </div>
                        </div>

                        <div class="features-tags">
                            <span class="tag"><i class="fas fa-wifi"></i> WiFi</span>
                            <span class="tag"><i class="fas fa-parking"></i> Parking</span>
                            <span class="tag"><i class="fas fa-utensils"></i> Catering</span>
                        </div>
                    </div>

                    <!-- Card Footer -->
                    <div class="card-footer">
                        <a href="halldetails.php?id=<?php echo $row['hall_id']; ?>" class="btn btn-secondary">
                            <i class="fas fa-info-circle"></i> Details
                        </a>
                        <a href="book_hall.php?id=<?php echo $row['hall_id']; ?>" class="btn btn-primary">
                            <i class="fas fa-calendar-check"></i> Book Now
                        </a>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>

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
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 20% 50%, rgba(120, 119, 198, 0.3), transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(236, 72, 153, 0.3), transparent 50%),
                radial-gradient(circle at 40% 20%, rgba(59, 130, 246, 0.2), transparent 50%);
            pointer-events: none;
            z-index: 0;
        }

        /* Hero Section */
        .hero-section {
            position: relative;
            padding: 80px 20px 60px;
            text-align: center;
            overflow: hidden;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            max-width: 800px;
            margin: 0 auto;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 15px;
            text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            animation: fadeInDown 0.8s ease;
        }

        .hero-subtitle {
            font-size: 1.3rem;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 40px;
            font-weight: 300;
            animation: fadeInUp 0.8s ease 0.2s both;
        }

        .search-bar {
            position: relative;
            max-width: 600px;
            margin: 0 auto;
            animation: fadeInUp 0.8s ease 0.4s both;
        }

        .search-bar input {
            width: 100%;
            padding: 18px 60px 18px 55px;
            border: none;
            border-radius: 50px;
            font-size: 16px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }

        .search-bar input:focus {
            outline: none;
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.3);
            transform: translateY(-2px);
        }

        .search-icon {
            position: absolute;
            left: 25px;
            top: 50%;
            transform: translateY(-50%);
            color: #667eea;
            font-size: 18px;
        }

        /* Animated Shapes */
        .hero-shapes {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 1;
        }

        .shape {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            opacity: 0.4;
            animation: float 20s infinite ease-in-out;
        }

        .shape-1 {
            width: 300px;
            height: 300px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            top: -100px;
            left: -100px;
            animation-delay: 0s;
        }

        .shape-2 {
            width: 400px;
            height: 400px;
            background: linear-gradient(135deg, #f093fb, #f5576c);
            top: 50%;
            right: -150px;
            animation-delay: 7s;
        }

        .shape-3 {
            width: 250px;
            height: 250px;
            background: linear-gradient(135deg, #4facfe, #00f2fe);
            bottom: -50px;
            left: 40%;
            animation-delay: 14s;
        }

        /* Container */
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 40px 20px 80px;
            position: relative;
            z-index: 1;
        }

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
            color: rgba(255, 255, 255, 0.8);
            font-size: 1.1rem;
        }

        /* Hall Cards Grid */
        .hall-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
            gap: 35px;
        }

        .hall-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            animation: fadeInUp 0.6s ease;
        }

        .hall-card:hover {
            transform: translateY(-15px) scale(1.02);
            box-shadow: 
                0 30px 80px rgba(0, 0, 0, 0.3),
                0 0 0 1px rgba(255, 255, 255, 0.5),
                0 0 60px rgba(102, 126, 234, 0.4);
        }

        /* Card Header */
        .card-header {
            position: relative;
            height: 220px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            overflow: hidden;
        }

        .hall-image {
            width: 100%;
            height: 100%;
            background: 
                linear-gradient(45deg, rgba(102, 126, 234, 0.8), rgba(118, 75, 162, 0.8)),
                repeating-linear-gradient(45deg, transparent, transparent 10px, rgba(255,255,255,.05) 10px, rgba(255,255,255,.05) 20px);
            position: relative;
        }

        .image-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to bottom, transparent, rgba(0,0,0,0.3));
            display: flex;
            align-items: flex-start;
            justify-content: flex-start;
            padding: 20px;
        }

        .status-badge {
            background: rgba(34, 197, 94, 0.95);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(34, 197, 94, 0.4);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .price-badge {
            position: absolute;
            top: 20px;
            right: 20px;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 12px 20px;
            border-radius: 16px;
            box-shadow: 0 8px 25px rgba(245, 87, 108, 0.4);
            font-weight: 600;
            display: flex;
            align-items: baseline;
            gap: 4px;
        }

        .price-badge .currency {
            font-size: 14px;
        }

        .price-badge .amount {
            font-size: 24px;
            font-weight: 700;
        }

        .price-badge .period {
            font-size: 12px;
            opacity: 0.9;
        }

        /* Card Body */
        .card-body {
            padding: 25px;
        }

        .hall-title {
            font-size: 1.5rem;
            color: #1e293b;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .hall-details {
            margin-bottom: 20px;
        }

        .detail-item {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
            color: #475569;
            font-size: 14px;
        }

        .detail-item i {
            width: 20px;
            font-size: 16px;
        }

        .icon-location { color: #ef4444; }
        .icon-capacity { color: #3b82f6; }
        .icon-facilities { color: #8b5cf6; }

        .features-tags {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .tag {
            background: linear-gradient(135deg, #e0e7ff, #ddd6fe);
            color: #5b21b6;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* Card Footer */
        .card-footer {
            padding: 20px 25px;
            background: linear-gradient(to bottom, transparent, rgba(102, 126, 234, 0.05));
            display: flex;
            gap: 12px;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
        }

        .btn {
            flex: 1;
            padding: 12px 20px;
            border: none;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-family: 'Poppins', sans-serif;
        }

        .btn-secondary {
            background: #f1f5f9;
            color: #475569;
            border: 2px solid #e2e8f0;
        }

        .btn-secondary:hover {
            background: #e2e8f0;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-primary:hover {
            box-shadow: 0 6px 25px rgba(102, 126, 234, 0.6);
            transform: translateY(-2px);
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

        @keyframes float {
            0%, 100% {
                transform: translateY(0) rotate(0deg);
            }
            33% {
                transform: translateY(-30px) rotate(5deg);
            }
            66% {
                transform: translateY(30px) rotate(-5deg);
            }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }

            .hero-subtitle {
                font-size: 1.1rem;
            }

            .hall-container {
                grid-template-columns: 1fr;
                gap: 25px;
            }

            .section-header h2 {
                font-size: 2rem;
            }
        }

        @media (max-width: 480px) {
            .hero-title {
                font-size: 2rem;
            }

            .card-footer {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }
        }

        /* Loading animation for cards */
        .hall-card {
            animation-fill-mode: both;
        }

        .hall-card:nth-child(1) { animation-delay: 0.1s; }
        .hall-card:nth-child(2) { animation-delay: 0.2s; }
        .hall-card:nth-child(3) { animation-delay: 0.3s; }
        .hall-card:nth-child(4) { animation-delay: 0.4s; }
        .hall-card:nth-child(5) { animation-delay: 0.5s; }
    </style>

    <script>
        // Simple search functionality
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const cards = document.querySelectorAll('.hall-card');
            
            cards.forEach(card => {
                const title = card.querySelector('.hall-title').textContent.toLowerCase();
                const location = card.querySelector('.icon-location').nextElementSibling.textContent.toLowerCase();
                
                if (title.includes(searchTerm) || location.includes(searchTerm)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>