<?php
session_start();
include 'config/db.php';
include 'includes/functions.php';

check_user_login();
$user_id = $_SESSION['user_id'];

$page_title = "My Bookings";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings | HallEase</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f9f9f9; color: #333; margin: 0; }
        
        .navbar { background: #333; padding: 15px 50px; display: flex; justify-content: space-between; align-items: center; color: white; }
        .navbar a { color: white; text-decoration: none; margin-left: 20px; }
        .navbar .logo { font-size: 1.5rem; font-weight: bold; margin: 0; }
        
        .container { max-width: 1000px; margin: 40px auto; padding: 20px; }
        .page-header { margin-bottom: 30px; border-bottom: 1px solid #ddd; padding-bottom: 10px; }
        
        .booking-card { background: white; border-radius: 10px; padding: 20px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center; }
        .booking-info h3 { margin: 0 0 5px; color: #333; }
        .booking-meta { color: #666; font-size: 0.9rem; margin-top: 5px; }
        
        .badge { padding: 5px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; }
        .badge-pending { background: #fff3cd; color: #856404; }
        .badge-confirmed { background: #d4edda; color: #155724; }
        .badge-cancelled { background: #f8d7da; color: #721c24; }
        
        .amount { font-size: 1.2rem; font-weight: bold; color: #667eea; }
    </style>
</head>
<body>

    <nav class="navbar">
        <a href="index.php" class="logo">HallEase.</a>
        <div>
            <a href="index.php">Home</a>
            <a href="my_bookings.php" style="font-weight: bold;">My Bookings</a>
            <a href="logout.php">Logout</a>
        </div>
    </nav>

    <div class="container">
        <div class="page-header">
            <h2>My Bookings</h2>
        </div>
        
        <?php if(isset($_GET['success'])): ?>
            <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                Booking request submitted successfully!
            </div>
        <?php endif; ?>

        <?php
        $query = "SELECT b.*, h.hall_name, h.location, h.city, h.images 
                  FROM bookings b 
                  JOIN halls h ON b.hall_id = h.hall_id 
                  WHERE b.user_id = '$user_id' 
                  ORDER BY b.booking_start_date DESC";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $status_class = 'badge-' . $row['booking_status'];
                
                // Image Logic
                $images = json_decode($row['images'], true);
                $thumb = "https://via.placeholder.com/800x600?text=Hall+Image";
                if (!empty($images) && is_array($images)) {
                    $thumb = $images[0];
                } elseif (!empty($row['images']) && !is_array($images)) {
                    $thumb = $row['images'];
                }

                echo "
                <div class='booking-card'>
                    <div style='display:flex; align-items:center;'>
                         <div style='width: 80px; height: 80px; background-image: url(\"$thumb\"); background-size: cover; border-radius: 5px; margin-right: 20px; background-color: #ddd;'></div>
                        <div class='booking-info'>
                            <h3>{$row['hall_name']}</h3>
                            <div class='booking-meta'>
                                <i class='fas fa-map-marker-alt'></i> {$row['city']} ({$row['location']})
                            </div>
                            <div class='booking-meta'>
                                <i class='fas fa-calendar'></i> " . date('M j, Y', strtotime($row['booking_start_date'])) . " - " . date('M j, Y', strtotime($row['booking_end_date'])) . "
                            </div>
                        </div>
                    </div>
                    <div style='text-align: right;'>
                        <div class='amount'>" . format_currency($row['total_amount']) . "</div>
                        <span class='badge $status_class'>" . ucfirst($row['booking_status']) . "</span>
                    </div>
                </div>";
            }
        } else {
            echo "<p>You haven't made any bookings yet.</p>";
        }
        ?>
    </div>

</body>
</html>
