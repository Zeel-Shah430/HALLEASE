<?php
session_start();
include '../config/db.php';
include '../includes/functions.php';

check_admin_login();

if (isset($_GET['delete'])) {
    $hall_id = clean_input($_GET['delete']);
    // Optional: Delete associated images from server
    $res = mysqli_query($conn, "SELECT images FROM halls WHERE hall_id = '$hall_id'");
    if ($row = mysqli_fetch_assoc($res)) {
        $images = json_decode($row['images'], true);
        if (is_array($images)) {
            foreach ($images as $img) {
                if (file_exists('../assets/images/halls/' . $img)) {
                    unlink('../assets/images/halls/' . $img);
                }
            }
        }
    }

    mysqli_query($conn, "DELETE FROM halls WHERE hall_id = '$hall_id'");
    $message = "Hall deleted successfully";
}

$page_title = "Manage Halls";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Halls - HallEase Admin</title>
    <link href="../assets/css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600&display=swap"
        rel="stylesheet">
</head>

<body>

    <div class="dashboard-wrapper">
        <div class="sidebar">
            <div class="sidebar-header">
                <h2 style="color: var(--primary);">HallEase</h2>
            </div>
            <div class="sidebar-menu">
                <a href="index.php" class="menu-item"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                <a href="manage_halls.php" class="menu-item active"><i class="fas fa-building"></i> Manage Halls</a>
                <a href="add_hall.php" class="menu-item"><i class="fas fa-plus-circle"></i> Add New Hall</a>
                <a href="manage_owners.php" class="menu-item"><i class="fas fa-user-tie"></i> Hall Owners</a>
                <a href="manage_users.php" class="menu-item"><i class="fas fa-users"></i> Users</a>
                <a href="logout.php" class="menu-item"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>

        <div class="main-content">
            <div class="top-header">
                <h3>Manage Halls</h3>
                <div class="user-profile">
                    <span>Admin</span>
                    <div
                        style="width: 35px; height: 35px; background: var(--gray-200); border-radius: 50%; display: inline-block;">
                    </div>
                </div>
            </div>

            <div class="content-wrapper">
                <?php if (isset($message)): ?>
                    <div class="alert alert-success"><?php echo $message; ?></div>
                <?php endif; ?>

                <div class="d-flex justify-between align-center mb-20">
                    <input type="text" class="form-control" placeholder="Search halls..." style="max-width: 300px;">
                    <a href="add_hall.php" class="btn btn-primary"><i class="fas fa-plus"></i> Add New Hall</a>
                </div>

                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Hall Name</th>
                                <th>Location</th>
                                <th>Price/Day</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT * FROM halls ORDER BY created_at DESC";
                            $result = mysqli_query($conn, $query);

                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $images = json_decode($row['images'], true);
                                    $img_src = (!empty($images) && is_array($images)) ? '../assets/images/halls/' . $images[0] : 'https://via.placeholder.com/50';

                                    $status_badge = $row['status'] == 'available' ? 'badge-success' : 'badge-warning';

                                    echo "<tr>
                                    <td>
                                        <div style='width: 50px; height: 50px; border-radius: var(--radius-md); overflow: hidden;'>
                                            <img src='{$img_src}' style='width: 100%; height: 100%; object-fit: cover;'>
                                        </div>
                                    </td>
                                    <td>
                                        <div style='font-weight: 500; color: var(--dark);'>{$row['hall_name']}</div>
                                        <div style='font-size: 0.8rem; color: var(--text-secondary);'>ID: #{$row['hall_id']}</div>
                                    </td>
                                    <td>{$row['city']}, {$row['location']}</td>
                                    <td>₹" . number_format($row['price_per_day']) . "</td>
                                    <td><span class='badge $status_badge'>" . ucfirst($row['status']) . "</span></td>
                                    <td>
                                        <div class='d-flex gap-10'>
                                            <a href='edit_hall.php?id={$row['hall_id']}' class='btn btn-secondary btn-sm'><i class='fas fa-edit'></i></a>
                                            <a href='?delete={$row['hall_id']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure?\")'><i class='fas fa-trash'></i></a>
                                        </div>
                                    </td>
                                </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='6' class='text-center'>No halls found.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</body>

</html>