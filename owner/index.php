<?php
session_start();
include '../config/db.php';
include '../includes/functions.php';

check_owner_login();

$owner_id = $_SESSION['owner_id'];


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
$upcoming_bookings = mysqli_fetch_assoc(mysqli_query($conn, $upcoming_query))['upcoming'];


$completed_query = "SELECT COUNT(b.booking_id) as completed 
                    FROM bookings b 
                    JOIN halls h ON b.hall_id = h.hall_id 
                    WHERE h.owner_id = '$owner_id' AND b.booking_status = 'completed'";
$completed_bookings = mysqli_fetch_assoc(mysqli_query($conn, $completed_query))['completed'];

$page_title = "Owner Dashboard";
include '../includes/header.php';
?>

<div class="dashboard-container">
    <div class="sidebar" style="background: #2980b9;">
        <h2>Hall Owner</h2>
        <ul>
            <li><a href="index.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="my_halls.php"><i class="fas fa-hotel"></i> My Halls</a></li>
            <li><a href="bookings.php"><i class="fas fa-calendar-check"></i> Bookings</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="top-bar">
            <h3>Welcome, <?php echo $_SESSION['owner_name']; ?></h3>
            <div class="date"><?php echo date('F j, Y'); ?></div>
        </div>

        <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px;">
            <div class="card" style="border-left: 5px solid #2980b9;">
                <h3><?php echo ($total_revenue); ?></h3>
                <p>Total Revenue</p>
            </div>
            <div class="card" style="border-left: 5px solid #3498db;">
                <h3><?php echo $total_bookings; ?></h3>
                <p>Total Bookings</p>
            </div>
            <div class="card" style="border-left: 5px solid #e67e22;">
                <h3><?php echo $upcoming_bookings; ?></h3>
                <p>Upcoming Bookings</p>
            </div>
            <div class="card" style="border-left: 5px solid #27ae60;">
                <h3><?php echo $completed_bookings; ?></h3>
                <p>Completed Bookings</p>
            </div>
        </div>

        <div class="card">
            <h3>Upcoming Bookings</h3>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Booking ID</th>
                            <th>Hall Name</th>
                            <th>User Name</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $recent_query = "SELECT b.*, h.hall_name, u.full_name 
                                         FROM bookings b 
                                         JOIN halls h ON b.hall_id = h.hall_id 
                                         JOIN users u ON b.user_id = u.user_id 
                                         WHERE h.owner_id = '$owner_id' AND b.booking_start_date >= CURDATE()
                                         ORDER BY b.booking_start_date ASC
                                         LIMIT 5";
                        $recent_res = mysqli_query($conn, $recent_query);

                        if (mysqli_num_rows($recent_res) > 0) {
                            while ($row = mysqli_fetch_assoc($recent_res)) {
                                echo "<tr>
                                    <td>#{$row['booking_id']}</td>
                                    <td>{$row['hall_name']}</td>
                                    <td>{$row['full_name']}</td>
                                    <td>" . date('M j, Y', strtotime($row['booking_start_date'])) . "</td>
                                    <td><span class='badge badge-warning'>" . ucfirst($row['booking_status']) . "</span></td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5'>No upcoming bookings found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
