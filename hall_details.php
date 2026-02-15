<?php
session_start();
include 'config/db.php';
include 'includes/functions.php';

if (!isset($_GET['id'])) {
    redirect('index.php');
}

$hall_id = clean_input($_GET['id']);
$query = "SELECT h.*, o.full_name as owner_name, o.phone as owner_phone, o.email as owner_email 
          FROM halls h 
          JOIN hall_owners o ON h.owner_id = o.owner_id 
          WHERE h.hall_id = '$hall_id'";

$result = mysqli_query($conn, $query);
$hall = mysqli_fetch_assoc($result);

if (!$hall) {
    redirect('index.php'); // Or show 404
}

// Check availability if date selected (Optional advanced feature)
// For now validation happens in book_hall.php

$page_title = $hall['hall_name'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $hall['hall_name']; ?> | HallEase</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f9f9f9; color: #333; margin: 0; }
        
        .container { max-width: 1000px; margin: 40px auto; padding: 0 20px; }
        
        .back-btn { display: inline-block; margin-bottom: 20px; color: #666; text-decoration: none; }
        .back-btn:hover { color: #333; }
        
        .detail-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            display: grid;
            grid-template-columns: 1.5fr 1fr;
        }
        
        .detail-img {
            background: #ddd;
            min-height: 400px;
            background-size: cover;
            background-position: center;
            transition: background-image 0.3s ease-in-out;
        }
        
        .detail-content { padding: 40px; }
        
        .detail-title { font-size: 2rem; margin-bottom: 10px; color: #333; }
        .detail-loc { color: #666; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
        
        .price-tag {
            font-size: 1.5rem;
            color: #667eea;
            font-weight: 700;
            margin-bottom: 30px;
            display: block;
        }
        
        .features { margin-bottom: 30px; }
        .features h4 { margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 5px; }
        .feature-chips { display: flex; flex-wrap: wrap; gap: 10px; }
        .chip { background: #f0f2f5; padding: 8px 15px; border-radius: 20px; font-size: 0.9rem; }
        
        .booking-form {
            background: #f8fafc;
            padding: 25px;
            border-radius: 10px;
            margin-top: 20px;
        }
        
        .form-group { margin-bottom: 15px; }
        .form-label { display: block; margin-bottom: 5px; font-weight: 500; font-size: 0.9rem; }
        .form-control { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; }
        
        .btn-book {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: opacity 0.3s;
        }
        .btn-book:hover { opacity: 0.9; }

        @media (max-width: 768px) {
            .detail-card { grid-template-columns: 1fr; }
            .detail-img { height: 250px; min-height: auto; }
        }
    </style>
</head>
<body>

<div class="container">
    <a href="index.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back to Listing</a>
    
    <div class="detail-card">
        <?php
        $images = json_decode($hall['images'], true);
        
        // Fallback or legacy support
        if(empty($images) || !is_array($images)) {
             if(!empty($hall['images']) && !is_array(json_decode($hall['images'], true))) {
                // If it was a single string, wrap it
                $images = [$hall['images']];
             } else {
                $images = ["https://via.placeholder.com/800x600?text=No+Image"];
             }
        }
        $main_image = $images[0];
        ?>
        <div class="gallery-section" style="padding: 20px 20px 20px 40px;">
            <div class="detail-img" id="mainImage" style="background-image: url('<?php echo $main_image; ?>'); border-radius: 10px;"></div>
            
            <?php if(count($images) > 1): ?>
            <div class="thumbnail-grid" style="display: flex; gap: 10px; margin-top: 15px; overflow-x: auto;">
                <?php foreach($images as $img): ?>
                    <div onclick="changeImage('<?php echo $img; ?>')" style="width: 80px; height: 60px; background-image: url('<?php echo $img; ?>'); background-size: cover; border-radius: 5px; cursor: pointer; border: 2px solid #ddd; flex-shrink: 0;"></div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="detail-content">
            <h1 class="detail-title"><?php echo $hall['hall_name']; ?></h1>
            <div class="detail-loc">
                <i class="fas fa-map-marker-alt"></i> 
                <?php echo $hall['location'] . ", " . $hall['city']; ?>
            </div>
            
            <span class="price-tag"><?php echo format_currency($hall['price_per_day']); ?> <span style="font-size:0.9rem; color:#888; font-weight:400;">/ day</span></span>
            
            <div class="features">
                <h4>About Venue</h4>
                <p style="line-height: 1.6; color: #555;"><?php echo nl2br($hall['description']); ?></p>
            </div>
            
            <div class="features">
                <h4>Capacity & Facilities</h4>
                <p><strong>Capacity:</strong> <?php echo $hall['capacity']; ?> Guests</p>
                <div class="feature-chips" style="margin-top: 10px;">
                    <?php 
                    $facilities = explode(',', $hall['facilities']);
                    foreach($facilities as $f) {
                        echo "<span class='chip'><i class='fas fa-check'></i> " . trim($f) . "</span>";
                    }
                    ?>
                </div>
            </div>

            <div class="booking-form">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <form action="book_hall.php" method="POST">
                        <input type="hidden" name="hall_id" value="<?php echo $hall['hall_id']; ?>">
                        <input type="hidden" name="price" value="<?php echo $hall['price_per_day']; ?>">
                        
                        <div class="form-group">
                            <label class="form-label">Event Start Date</label>
                            <input type="date" name="start_date" class="form-control" required min="<?php echo date('Y-m-d'); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Event End Date</label>
                            <input type="date" name="end_date" class="form-control" required min="<?php echo date('Y-m-d'); ?>">
                        </div>
                        
                        <button type="submit" name="book_now" class="btn-book">Book Now</button>
                    </form>
                <?php else: ?>
                    <div style="text-align: center;">
                        <p style="margin-bottom: 15px;">Login to book this hall</p>
                        <a href="user/login.php?redirect=hall_details.php?id=<?php echo $hall_id; ?>" class="btn-book">Login to Book</a>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<script>
    function changeImage(src) {
        document.getElementById('mainImage').style.backgroundImage = "url('" + src + "')";
    }
</script>
</body>
</html>
