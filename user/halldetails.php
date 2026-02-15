<?php
include '../config/db.php';
include '../includes/navbar.php';
include '../includes/auth.php';

$hall_id = $_GET['id'];

$sql = "SELECT h.*, o.full_name AS owner_name, o.email 
        FROM halls h 
        JOIN hall_owners o ON h.owner_id = o.owner_id 
        WHERE h.hall_id = $hall_id";

$res = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($res);
?>
<!DOCTYPE html>
<html>

<head>
    <title><?php echo $row['hall_name']; ?></title>
    <link rel="stylesheet" href="/HALLEASE/assets/css/halldetails.css">
</head>

<body>

    <div class="container">

        <div class="left">
            <div class="image-box">
                <img style="background-size: cover;" src="/HALLEASE/assets/images/<?php echo $row['hall_id']; ?>.jpg">
            </div>

            <h1><?php echo $row['hall_name']; ?></h1>
            <p class="location">📍 <?php echo $row['location']; ?></p>

            <p class="rating">⭐ 4.5 (50 reviews) <span class="verified">Verified</span></p>

            <p class="price">Starting from ₹<?php echo $row['price']; ?>/day</p>

            <div class="pricing-section">
                <h2>Pricing Options</h2>

                <div class="price-card peak">

                    <h3>₹<?php echo $row['price'] + 1000; ?>/hr</h3>
                    <span class="tag">Peak </span>
                </div>

                <div class="price-card offpeak">

                    <h3>₹<?php echo $row['price']; ?>/hr</h3>
                    <span class="tag green">Off-Peak</span>
                </div>
            </div>
        </div>

        <div class="right">
            <div class="book-card">
                <h2>Book This Venue</h2>
                <p>Peak: ₹<?php echo $row['price'] + 1000; ?>/hr</p>
                <p>Off-Peak: ₹<?php echo $row['price']; ?>/hr</p>

                <a href="login.php">
                    <button>Book</button>
                </a>
                <small>Free cancellation up to 7 days before booking</small>
            </div>

            <div class="owner-card">
                <h2>Contact Ground Owner</h2>
                <p><b>Owner:</b> <?php echo $row['owner_name']; ?></p>
                <p><b>Email:</b> <?php echo $row['email']; ?></p>
                <span class="verified-owner">Verified Owner</span>
            </div>

            <div class="stats-card">
                <h2>Quick Stats</h2>
                <p>Total Bookings: 10</p>
                <p>Average Rating: 4.5/5</p>
                <p>Response Rate: 98%</p>
            </div>
        </div>

    </div>

</body>

</html>