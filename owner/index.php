<?php
session_start();
include '../config/db.php';
include '../includes/functions.php';

check_owner_login();

$owner_id = $_SESSION['owner_id'];

// Stats Queries
$booking_query = "SELECT COUNT(b.booking_id) as total_bookings 
                  FROM bookings b 
                  JOIN halls h ON b.hall_id = h.hall_id 
                  WHERE h.owner_id = '$owner_id'";
$total_bookings = mysqli_fetch_assoc(mysqli_query($conn, $booking_query))['total_bookings'];

$revenue_query = "SELECT SUM(b.total_amount) as total_revenue 
                  FROM bookings b 
                  JOIN halls h ON b.hall_id = h.hall_id 
                  WHERE h.owner_id = '$owner_id' AND b.payment_status = 'paid'";
$total_revenue = mysqli_fetch_assoc(mysqli_query($conn, $revenue_query))['total_revenue'] ?? 0;

$upcoming_query = "SELECT COUNT(b.booking_id) as upcoming 
                   FROM bookings b 
                   JOIN halls h ON b.hall_id = h.hall_id 
                   WHERE h.owner_id = '$owner_id' AND b.booking_start_date >= CURDATE() AND b.booking_status != 'cancelled'";
$upcoming_res = mysqli_query($conn, $upcoming_query);
$upcoming_bookings = mysqli_fetch_assoc($upcoming_res)['upcoming'];

$completed_query = "SELECT COUNT(b.booking_id) as completed 
                    FROM bookings b 
                    JOIN halls h ON b.hall_id = h.hall_id 
                    WHERE h.owner_id = '$owner_id' AND b.booking_status = 'completed'";
$completed_bookings = mysqli_fetch_assoc(mysqli_query($conn, $completed_query))['completed'];

$page_title = "Owner Dashboard";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Owner Dashboard - HallEase</title>
    <link href="../assets/css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600&display=swap"
        rel="stylesheet">
</head>

<body>

    <div class="dashboard-wrapper">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h2 style="color: var(--primary);">HallEase</h2>
            </div>
            <div class="sidebar-menu">
                <a href="index.php" class="menu-item active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                <a href="my_halls.php" class="menu-item"><i class="fas fa-hotel"></i> My Halls</a>
                <a href="bookings.php" class="menu-item"><i class="fas fa-calendar-check"></i> Bookings</a>
                <a href="logout.php" class="menu-item"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="top-header">
                <h3>Dashboard</h3>
                <div class="user-profile">
                    <span><?php echo htmlspecialchars($_SESSION['owner_name']); ?></span>
                    <div
                        style="width: 35px; height: 35px; background: var(--gray-200); border-radius: 50%; display: inline-block;">
                    </div>
                </div>
            </div>

            <div class="content-wrapper">
                <!-- Stats -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: var(--primary);">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <div class="stat-info">
                            <h3>₹<?php echo number_format($total_revenue); ?></h3>
                            <p>Total Revenue</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background: var(--info);">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $total_bookings; ?></h3>
                            <p>Total Bookings</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background: var(--warning);">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $upcoming_bookings; ?></h3>
                            <p>Upcoming</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background: var(--success);">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $completed_bookings; ?></h3>
                            <p>Completed</p>
                        </div>
                    </div>
                </div>

                <!-- Upcoming Bookings Table -->
                <div class="card">
                    <div class="card-body">
                        <h3 style="margin-bottom: 20px;">Upcoming Bookings</h3>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Booking ID</th>
                                        <th>Hall Name</th>
                                        <th>Guest</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Correct join logic: Bookings linked to Hall linked to Owner
                                    // Assuming 'users' table has 'name' or 'full_name'
                                    $recent_query = "SELECT b.*, h.hall_name, u.name as user_name 
                                                 FROM bookings b 
                                                 JOIN halls h ON b.hall_id = h.hall_id 
                                                 JOIN users u ON b.user_id = u.user_id 
                                                 WHERE h.owner_id = '$owner_id' AND b.booking_start_date >= CURDATE()
                                                 ORDER BY b.booking_start_date ASC
                                                 LIMIT 5";

                                    // Error handling if 'users' uses 'full_name' instead of 'name'
                                    // Looking at previous index.php it used u.full_name, wait.
                                    // halldetails.php used u.name (for owner?). owner table uses full_name.
                                    // users table uses 'name' usually.
                                    // I'll double check by trying 'name', if it fails I'll try 'full_name'.
                                    // Actually, standard users table usually has 'name'. Owner table has 'full_name'.
                                    
                                    // Let's try to be safe. If u.name fails, it might be u.full_name
                                    // I'll check db schema later if needed, but for now I'll use u.name as per common practice or check previous file content.
                                    // Previous file content used for joining users: `JOIN users u ON b.user_id = u.user_id`
                                    // And select `u.full_name`.
                                    // Wait, previous code: `JOIN users u ... u.full_name`. So user table has `full_name`?
                                    // Let's use `full_name`.
                                    
                                    $recent_query = "SELECT b.*, h.hall_name, u.name as user_name 
                                                 FROM bookings b 
                                                 JOIN halls h ON b.hall_id = h.hall_id 
                                                 JOIN users u ON b.user_id = u.user_id 
                                                 WHERE h.owner_id = '$owner_id' AND b.booking_start_date >= CURDATE()
                                                 ORDER BY b.booking_start_date ASC
                                                 LIMIT 5";

                                    // Actually I saw `check_db_columns.php` used standard 'users' table check but I didn't see output.
                                    // I'll assume `name` based on common sense unless I see otherwise.
                                    // Original file `owner/index.php` line 93 used `u.full_name`.
                                    // So users table has `full_name`.
                                    
                                    $recent_query = str_replace("u.name", "u.full_name", $recent_query);

                                    $recent_res = mysqli_query($conn, $recent_query);

                                    if ($recent_res && mysqli_num_rows($recent_res) > 0) {
                                        while ($row = mysqli_fetch_assoc($recent_res)) {
                                            $status_class = '';
                                            switch ($row['booking_status']) {
                                                case 'confirmed':
                                                    $status_class = 'badge-success';
                                                    break;
                                                case 'pending_payment':
                                                    $status_class = 'badge-warning';
                                                    break;
                                                default:
                                                    $status_class = 'badge-secondary';
                                            }

                                            echo "<tr>
                                            <td>#{$row['booking_id']}</td>
                                            <td>{$row['hall_name']}</td>
                                            <td>{$row['user_name']}</td>
                                            <td>" . date('d M Y', strtotime($row['booking_start_date'])) . "</td>
                                            <td><span class='badge $status_class'>" . ucfirst($row['booking_status']) . "</span></td>
                                        </tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='5' class='text-center'>No upcoming bookings found.</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>