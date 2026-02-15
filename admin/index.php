<?php
session_start();
include '../config/db.php';
include '../includes/functions.php';

check_admin_login();

// Fetch stats
$total_halls = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM halls"))['count'];
$total_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM users"))['count'];
$total_owners = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM hall_owners"))['count'];
$total_bookings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM bookings"))['count'];

$page_title = "Admin Dashboard";
include '../includes/header.php';
?>

<div class="dashboard-container">
    <div class="sidebar">
        <h2>HallEase Admin</h2>
        <ul>
            <li><a href="index.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="manage_halls.php"><i class="fas fa-building"></i> Manage Halls</a></li>
            <li><a href="add_hall.php"><i class="fas fa-plus-circle"></i> Add New Hall</a></li>
            <li><a href="manage_owners.php"><i class="fas fa-user-tie"></i> Hall Owners</a></li>
            <li><a href="manage_users.php"><i class="fas fa-users"></i> Users</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="top-bar">
            <h3>Welcome, <?php echo $_SESSION['admin_name']; ?></h3>
            <div class="date"><?php echo date('F j, Y'); ?></div>
        </div>

        <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px;">
            <div class="card" style="border-left: 5px solid #667eea;">
                <h3><?php echo $total_halls; ?></h3>
                <p>Total Halls</p>
            </div>
            <div class="card" style="border-left: 5px solid #764ba2;">
                <h3><?php echo $total_owners; ?></h3>
                <p>Hall Owners</p>
            </div>
            <div class="card" style="border-left: 5px solid #4facfe;">
                <h3><?php echo $total_users; ?></h3>
                <p>Registered Users</p>
            </div>
            <div class="card" style="border-left: 5px solid #43e97b;">
                <h3><?php echo $total_bookings; ?></h3>
                <p>Total Bookings</p>
            </div>
        </div>

        <div class="card">
            <h3>Recent Bookings</h3>
           
            <p>No recent bookings to display.</p>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
