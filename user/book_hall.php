<?php
include '../config/db.php';
include '../includes/navbar.php';
include '../includes/auth.php';

$query = "SELECT * FROM halls WHERE status='available' LIMIT 5";
$result = mysqli_query($conn, $query);
?>

<style>
body {
    background: linear-gradient(135deg, #f0f9ff, #ecfeff, #fdf2f8);
}

.hall-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 35px;
    padding: 40px 60px;
    min-height: 100vh;
}

.hall-card {
    background: linear-gradient(160deg, #ffffff, #f0fdf4);
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.12);
    overflow: hidden;
    position: relative;
    height: 420px;
    transition: all 0.4s ease;
}

.hall-card:hover {
    transform: translateY(-10px) scale(1.02);
    box-shadow:
        0 0 20px rgba(34,197,94,0.6),
        0 0 40px rgba(59,130,246,0.4),
        0 0 60px rgba(236,72,153,0.3);
}

.price-badge {
    position: absolute;
    top: 15px;
    right: 15px;
    background: linear-gradient(135deg, #22c55e, #16a34a);
    color: #fff;
    padding: 8px 18px;
    border-radius: 25px;
    font-weight: bold;
    font-size: 14px;
    box-shadow: 0 0 15px rgba(34,197,94,0.6);
}

.hall-card h3 {
    margin: 60px 20px 10px;
    font-size: 22px;
    color: #0f172a;
}

.location, .capacity, .facilities {
    margin: 0 20px 10px;
    color: #475569;
    font-size: 15px;
}

.btn-row {
    display: flex;
    justify-content: space-between;
    padding: 20px;
    margin-top: auto;
}

.details-btn {
    border: 2px solid #3b82f6;
    background: transparent;
    color: #3b82f6;
    padding: 10px 20px;
    border-radius: 12px;
    font-weight: 500;
    cursor: pointer;
    transition: 0.3s;
}

.details-btn:hover {
    background: #3b82f6;
    color: white;
    box-shadow: 0 0 15px rgba(59,130,246,0.7);
}

.book-btn {
    background: linear-gradient(135deg, #22c55e, #16a34a);
    color: white;
    border: none;
    padding: 10px 22px;
    border-radius: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: 0.3s;
}

.book-btn:hover {
    box-shadow: 0 0 20px rgba(34,197,94,0.8);
    transform: scale(1.05);
}
</style>


<div class="hall-container">

<?php while($row = mysqli_fetch_assoc($result)) { ?>

    <div class="hall-card">
        <div class="price-badge">₹<?php echo $row['price']; ?>/day</div>

        <h3><?php echo $row['hall_name']; ?></h3>
        <p class="location">📍 <?php echo $row['location']; ?></p>
        <p class="capacity">👥 Capacity: <?php echo $row['capacity']; ?></p>
        <p class="facilities">🏢 <?php echo $row['facilities']; ?></p>

        <div class="btn-row">
            <a href="halldetails.php?id=<?php echo $row['hall_id']; ?>" class="details-btn">View Details</a>
            <a href="book_hall.php?id=<?php echo $row['hall_id']; ?>" class="book-btn">Book Now</a>
        </div>
    </div>

<?php } ?>

</div>

