<?php
session_start();
include '../config/db.php';
include '../includes/functions.php';

check_owner_login();
$owner_id = $_SESSION['owner_id'];

$page_title = "My Halls";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Halls - HallEase Owner</title>
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
                <a href="index.php" class="menu-item"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                <a href="my_halls.php" class="menu-item active"><i class="fas fa-hotel"></i> My Halls</a>
                <a href="bookings.php" class="menu-item"><i class="fas fa-calendar-check"></i> Bookings</a>
                <a href="logout.php" class="menu-item"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="top-header">
                <h3>My Halls</h3>
                <div class="user-profile">
                    <span><?php echo htmlspecialchars($_SESSION['owner_name']); ?></span>
                    <div
                        style="width: 35px; height: 35px; background: var(--gray-200); border-radius: 50%; display: inline-block;">
                    </div>
                </div>
            </div>

            <div class="content-wrapper">
                <div class="card">
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Hall Name</th>
                                    <th>Details</th>
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
                                        $images = json_decode($row['images'], true);
                                        $img_src = (!empty($images) && is_array($images)) ? '../assets/images/halls/' . $images[0] : 'https://via.placeholder.com/50';

                                        $status_badge = $row['status'] == 'available' ? 'badge-success' : 'badge-warning';

                                        echo "<tr>
                                        <td>
                                            <div style='width: 60px; height: 40px; border-radius: var(--radius-sm); overflow: hidden;'>
                                                <img src='{$img_src}' style='width: 100%; height: 100%; object-fit: cover;'>
                                            </div>
                                        </td>
                                        <td>
                                            <div style='font-weight: 500; color: var(--dark);'>{$row['hall_name']}</div>
                                        </td>
                                        <td>
                                            <div style='font-size: 0.9rem;'>{$row['city']}, {$row['location']}</div>
                                            <small style='color: var(--text-secondary);'>{$row['facilities']}</small>
                                        </td>
                                        <td>{$row['capacity']} Pax</td>
                                        <td>₹" . number_format($row['price_per_day']) . "</td>
                                        <td><span class='badge $status_badge'>" . ucfirst($row['status']) . "</span></td>
                                    </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='6' class='text-center'>No halls assigned to you.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>