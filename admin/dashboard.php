<?php
session_start();
include '../config/db.php';
include '../includes/functions.php';

check_admin_login();

// Fetch Stats
$total_halls = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM halls"))['count'];
$total_bookings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM bookings"))['count'];
$pending_bookings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM bookings WHERE booking_status = 'pending_payment'"))['count'];
$total_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM users"))['count'];
$total_revenue = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_amount) as total FROM bookings WHERE payment_status = 'paid'"))['total'];

$page_title = "Admin Dashboard";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - HallEase</title>
    <link href="../assets/css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600&display=swap"
        rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                <a href="manage_halls.php" class="menu-item"><i class="fas fa-building"></i> Manage Halls</a>
                <a href="add_hall.php" class="menu-item"><i class="fas fa-plus-circle"></i> Add New Hall</a>
                <a href="manage_owners.php" class="menu-item"><i class="fas fa-user-tie"></i> Hall Owners</a>
                <a href="manage_users.php" class="menu-item"><i class="fas fa-users"></i> Users</a>
                <a href="logout.php" class="menu-item"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="top-header">
                <h3>Dashboard Overview</h3>
                <div class="user-profile">
                    <span>Admin</span>
                    <div
                        style="width: 35px; height: 35px; background: var(--gray-200); border-radius: 50%; display: inline-block;">
                    </div>
                </div>
            </div>

            <div class="content-wrapper">
                <!-- Stats Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: var(--primary);">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <div class="stat-info">
                            <h3>₹
                                <?php echo number_format($total_revenue ?? 0); ?>
                            </h3>
                            <p>Total Revenue</p>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon" style="background: var(--info);">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div class="stat-info">
                            <h3>
                                <?php echo $total_bookings; ?>
                            </h3>
                            <p>Total Bookings</p>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon" style="background: var(--warning);">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-info">
                            <h3>
                                <?php echo $pending_bookings; ?>
                            </h3>
                            <p>Pending Actions</p>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon" style="background: var(--success);">
                            <i class="fas fa-building"></i>
                        </div>
                        <div class="stat-info">
                            <h3>
                                <?php echo $total_halls; ?>
                            </h3>
                            <p>Active Halls</p>
                        </div>
                    </div>
                </div>

                <!-- Recent Bookings Table -->
                <div class="card">
                    <div class="card-body">
                        <h3 style="margin-bottom: 20px;">Recent Bookings</h3>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Booking ID</th>
                                        <th>User</th>
                                        <th>Hall</th>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $recent_q = "SELECT b.*, u.name as user_name, h.hall_name 
                                             FROM bookings b 
                                             JOIN users u ON b.user_id = u.user_id 
                                             JOIN halls h ON b.hall_id = h.hall_id 
                                             ORDER BY b.created_at DESC LIMIT 5";
                                    $recent_res = mysqli_query($conn, $recent_q);

                                    if (mysqli_num_rows($recent_res) > 0) {
                                        while ($row = mysqli_fetch_assoc($recent_res)) {
                                            $status_badge = '';
                                            switch ($row['booking_status']) {
                                                case 'confirmed':
                                                    $status_badge = 'badge-success';
                                                    break;
                                                case 'pending_payment':
                                                    $status_badge = 'badge-warning';
                                                    break;
                                                case 'cancelled':
                                                    $status_badge = 'badge-danger';
                                                    break;
                                                default:
                                                    $status_badge = 'badge-secondary';
                                            }

                                            echo "<tr>
                                            <td>#{$row['booking_id']}</td>
                                            <td>{$row['user_name']}</td>
                                            <td>{$row['hall_name']}</td>
                                            <td>" . date('d M Y', strtotime($row['booking_start_date'])) . "</td>
                                            <td>₹" . number_format($row['total_amount']) . "</td>
                                            <td><span class='badge $status_badge'>" . ucfirst($row['booking_status']) . "</span></td>
                                        </tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='6' class='text-center'>No recent bookings.</td></tr>";
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