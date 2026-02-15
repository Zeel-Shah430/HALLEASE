<?php
session_start();
include '../config/db.php';
include '../includes/functions.php';

check_owner_login();
$owner_id = $_SESSION['owner_id'];

if (isset($_GET['confirm'])) {
    $booking_id = clean_input($_GET['confirm']);
    
    mysqli_query($conn, "UPDATE bookings b JOIN halls h ON b.hall_id = h.hall_id SET b.booking_status = 'confirmed' WHERE b.booking_id = '$booking_id' AND h.owner_id = '$owner_id'");
    $message = "Booking confirmed!";
}

$page_title = "Bookings";
include '../includes/header.php';
?>

<div class="dashboard-container">
    <div class="sidebar" style="background: #2980b9;">
        <h2>Hall Owner</h2>
        <ul>
            <li><a href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="my_halls.php"><i class="fas fa-hotel"></i> My Halls</a></li>
            <li><a href="bookings.php" class="active"><i class="fas fa-calendar-check"></i> Bookings</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="top-bar">
            <h3>Bookings Management</h3>
        </div>

        <?php if(isset($message)): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>

        <div class="card">
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Hall</th>
                            <th>Customer</th>
                            <th>Dates</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        
                        $query = "SELECT b.*, h.hall_name, u.full_name, u.email, u.phone 
                                  FROM bookings b 
                                  JOIN halls h ON b.hall_id = h.hall_id 
                                  JOIN users u ON b.user_id = u.user_id 
                                  WHERE h.owner_id = '$owner_id' 
                                  ORDER BY b.booking_start_date DESC";
                        
                        $result = mysqli_query($conn, $query);

                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                $status_class = match($row['booking_status']) {
                                    'confirmed' => 'badge-success',
                                    'cancelled' => 'badge-danger',
                                    'completed' => 'badge-success',
                                    default => 'badge-warning'
                                };
                                
                                echo "<tr>
                                    <td>#{$row['booking_id']}</td>
                                    <td>{$row['hall_name']}</td>
                                    <td>
                                        {$row['full_name']}<br>
                                        <small>{$row['phone']}</small>
                                    </td>
                                    <td>" . date('M j', strtotime($row['booking_start_date'])) . " - " . date('M j, Y', strtotime($row['booking_end_date'])) . "</td>
                                    <td>" . ($row['total_amount']) . "</td>
                                    <td><span class='badge $status_class'>" . ucfirst($row['booking_status']) . "</span></td>
                                    <td>";
                                
                                if ($row['booking_status'] == 'pending') {
                                    echo "<a href='?confirm={$row['booking_id']}' class='btn' style='padding: 5px 10px; font-size: 12px;'>Confirm</a>";
                                } else {
                                    echo "<span>-</span>";
                                }
                                
                                echo "</td></tr>";
                            }
                        } else {
                            echo "<tr><td colspan='7'>No bookings found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
