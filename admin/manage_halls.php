<?php
session_start();
include '../config/db.php';
include '../includes/functions.php';

check_admin_login();


if (isset($_GET['delete'])) {
    $hall_id = clean_input($_GET['delete']);
    mysqli_query($conn, "DELETE FROM halls WHERE hall_id = '$hall_id'");
    $message = "Hall deleted successfully";
}

$page_title = "Manage Halls";
include '../includes/header.php';
?>

<div class="dashboard-container">
    <div class="sidebar">
        <h2>HallEase Admin</h2>
        <ul>
            <li><a href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="manage_halls.php" class="active"><i class="fas fa-building"></i> Manage Halls</a></li>
            <li><a href="add_hall.php"><i class="fas fa-plus-circle"></i> Add New Hall</a></li>
            <li><a href="manage_owners.php"><i class="fas fa-user-tie"></i> Hall Owners</a></li>
            <li><a href="manage_users.php"><i class="fas fa-users"></i> Users</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="top-bar">
            <h3>Manage Halls</h3>
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
                            <th>Hall Name</th>
                            <th>Owner</th>
                            <th>Location</th>
                            <th>Capacity</th>
                            <th>Price/Day</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "SELECT h.*, o.full_name as owner_name 
                                  FROM halls h 
                                  JOIN hall_owners o ON h.owner_id = o.owner_id 
                                  ORDER BY h.created_at DESC";
                        $result = mysqli_query($conn, $query);

                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                $status_badge = $row['status'] == 'available' ? 'badge-success' : 'badge-warning';
                                echo "<tr>
                                    <td>#{$row['hall_id']}</td>
                                    <td>{$row['hall_name']}</td>
                                    <td>{$row['owner_name']}</td>
                                    <td>{$row['city']} ({$row['location']})</td>
                                    <td>{$row['capacity']}</td>
                                    <td>" . ($row['price_per_day']) . "</td>
                                    <td><span class='badge $status_badge'>" . ucfirst($row['status']) . "</span></td>
                                    <td>
                                        <a href='?delete={$row['hall_id']}' class='btn' style='background: #e74c3c; padding: 5px 10px; font-size: 12px;' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                                    </td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='8'>No halls found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
