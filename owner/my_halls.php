<?php
session_start();
include '../config/db.php';
include '../includes/functions.php';

check_owner_login();
$owner_id = $_SESSION['owner_id'];

$page_title = "My Halls";
include '../includes/header.php';
?>

<div class="dashboard-container">
    <div class="sidebar" style="background: #2980b9;">
        <h2>Hall Owner</h2>
        <ul>
            <li><a href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="my_halls.php" class="active"><i class="fas fa-hotel"></i> My Halls</a></li>
            <li><a href="bookings.php"><i class="fas fa-calendar-check"></i> Bookings</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="top-bar">
            <h3>My Halls</h3>
        </div>

        <div class="card">
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Hall Name</th>
                            <th>Location</th>
                            <th>Capacity</th>
                            <th>Price/Day</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "SELECT * FROM halls WHERE owner_id = '$owner_id' ORDER BY created_at DESC";
                        $result = mysqli_query($conn, $query);

                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                $status_badge = $row['status'] == 'available' ? 'badge-success' : 'badge-warning';
                                echo "<tr>
                                    <td>
                                        <strong>{$row['hall_name']}</strong><br>
                                        <small style='color: #777;'>{$row['facilities']}</small>
                                    </td>
                                    <td>{$row['city']} ({$row['location']})</td>
                                    <td>{$row['capacity']}</td>
                                    <td>" . ($row['price_per_day']) . "</td>
                                    <td><span class='badge $status_badge'>" . ucfirst($row['status']) . "</span></td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5'>No halls assigned to you yet.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
