<?php
session_start();
include 'config/db.php';
include 'includes/functions.php';

// Search Logic
$where_clause = "status = 'available'";
if (isset($_GET['search'])) {
    $location = clean_input($_GET['location']);
    if (!empty($location)) {
        $where_clause .= " AND (city LIKE '%$location%' OR location LIKE '%$location%' OR hall_name LIKE '%$location%')";
    }
}

$page_title = "Home";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HallEase - Find Your Perfect Venue</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Inherit some styles or define new for landing page */
        body { margin: 0; font-family: 'Poppins', sans-serif; background: #f9f9f9; overflow-x: hidden; }
   

        .hero {
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('assets/hero-bg.jpg');
            background-size: cover;
            background-position: center; 
            background-color: #667eea; /* Fallback */
            height: 500px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
            width: 100%;
    box-sizing: border-box;
        }
        
        .hero h1 { font-size: 3.5rem; margin-bottom: 20px; }
        .hero p { font-size: 1.2rem; margin-bottom: 30px; }
        
        .search-box {
            background: white;
            padding: 10px;
            border-radius: 50px;
            display: flex;
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .search-box input {
            flex: 1;
            padding: 15px 25px;
            border: none;
            outline: none;
            border-radius: 50px;
            font-size: 1rem;
        }
        
        .search-box button {
            background: #667eea;
            color: white;
            border: none;
            padding: 15px 40px;
            border-radius: 50px;
            cursor: pointer;
            font-weight: 600;
            transition: background 0.3s;
        }
        .search-box button:hover { background: #764ba2; }
        
        .container { max-width: 1200px; margin: 0 auto; padding: 50px 20px; }
        .section-title { text-align: center; margin-bottom: 50px; }
        .section-title h2 { font-size: 2.5rem; color: #333; margin-bottom: 15px; }
        
        .halls-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 30px;
        }
        
        .hall-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: transform 0.3s;
        }
        
        .hall-card:hover { transform: translateY(-10px); }
        
        .hall-img {
            height: 200px;
            background: #ddd;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #888;
        }
        
        .hall-content { padding: 25px; }
        .hall-price { color: #667eea; font-weight: 700; font-size: 1.2rem; float: right; }
        .hall-title { font-size: 1.4rem; margin-bottom: 10px; color: #333; }
        .hall-meta { color: #666; font-size: 0.9rem; margin-bottom: 15px; display: flex; gap: 15px; }
        .hall-meta i { color: #667eea; }
        
        .btn-book {
            display: block;
            width: 100%;
            padding: 12px;
            text-align: center;
            background: #333;
            color: white;
            border-radius: 8px;
            font-weight: 600;
            transition: background 0.3s;
            text-decoration: none;
        }
        .btn-book:hover { background: #555; }
        
        .navbar {
            padding: 20px 50px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: absolute;
            width: 100%;
            top: 0;
            left: 0;
            z-index: 100;
            box-sizing: border-box;
        }
        
        .logo { font-size: 1.8rem; font-weight: 700; color: white; text-decoration: none; }
        
        .nav-links a {
            color: white;
            text-decoration: none;
            margin-left: 30px;
            font-weight: 500;
        }
        
        .nav-links .btn-login {
            background: rgba(255,255,255,0.2);
            padding: 10px 25px;
            border-radius: 50px;
            border: 1px solid rgba(255,255,255,0.4);
            transition: all 0.3s;
        }
        .nav-links .btn-login:hover { background: white; color: #333; }
    </style>
</head>
<body>

    <nav class="navbar">
        <a href="index.php" class="logo">HallEase.</a>
        <div class="nav-links">
            <a href="index.php">Home</a>
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="my_bookings.php">My Bookings</a>
                <a href="logout.php" class="btn-login">Logout</a>
            <?php else: ?>
                <a href="admin/login.php">Admin</a>
                <a href="owner/login.php">Partner</a>
                <a href="user/login.php" class="btn-login">Login / Sign Up</a>
            <?php endif; ?>
        </div>
    </nav>

    <header class="hero">
        <div class="hero-content">
            <h1>Find Your Perfect Venue</h1>
            <p>Discover and book the best halls for your special events</p>
            
            <form method="GET" class="search-box">
                <input type="text" name="location" placeholder="Search by City, Area or Hall Name..." value="<?php echo isset($_GET['location']) ? htmlspecialchars($_GET['location']) : ''; ?>">
                <button type="submit" name="search"><i class="fas fa-search"></i> Search</button>
            </form>
        </div>
    </header>

    <div class="container">
        <div class="section-title">
            <h2>Available Halls</h2>
            <p>Handpicked venues for your weddings, parties, and conferences</p>
        </div>

        <div class="halls-grid">
            <?php
            $query = "SELECT * FROM halls WHERE $where_clause ORDER BY created_at DESC";
            $result = mysqli_query($conn, $query);

            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    // Logic to find image
                    $images = json_decode($row['images'], true);
                    $thumb = "https://via.placeholder.com/400x250?text=Hall+Image"; // Default
                    
                    if (!empty($images) && is_array($images)) {
                        $thumb = $images[0];
                    } elseif (!empty($row['images']) && !is_array($images)) {
                        // Fallback for string
                        $thumb = $row['images'];
                    }
                     
                    $image = $thumb;
                    
                    echo "
                    <div class='hall-card'>
                        <div class='hall-img' style='background-image: url(\"$image\"); background-size: cover;'></div>
                        <div class='hall-content'>
                            <span class='hall-price'>" . format_currency($row['price_per_day']) . "/day</span>
                            <h3 class='hall-title'>{$row['hall_name']}</h3>
                            <div class='hall-meta'>
                                <span><i class='fas fa-map-marker-alt'></i> {$row['city']}</span>
                                <span><i class='fas fa-users'></i> {$row['capacity']} Pax</span>
                            </div>
                            <a href='hall_details.php?id={$row['hall_id']}' class='btn-book'>View Details</a>
                        </div>
                    </div>";
                }
            } else {
                echo "<p style='text-align:center; grid-column: 1/-1;'>No halls found matching your criteria.</p>";
            }
            ?>
        </div>
    </div>

</body>
</html>
