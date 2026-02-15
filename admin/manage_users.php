<?php
session_start();
include '../config/db.php';
include '../includes/functions.php';

check_admin_login();

if (isset($_GET['delete'])) {
    $user_id = clean_input($_GET['delete']);
    mysqli_query($conn, "DELETE FROM users WHERE user_id = '$user_id'");
    $message = "User deleted successfully";
}

$page_title = "Manage Users";
include '../includes/header.php';
?>

<div class="dashboard-container">
    <div class="sidebar">
        <h2>HallEase Admin</h2>
        <ul>
            <li><a href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="manage_halls.php"><i class="fas fa-building"></i> Manage Halls</a></li>
            <li><a href="add_hall.php"><i class="fas fa-plus-circle"></i> Add New Hall</a></li>
            <li><a href="manage_owners.php"><i class="fas fa-user-tie"></i> Hall Owners</a></li>
            <li><a href="manage_users.php" class="active"><i class="fas fa-users"></i> Users</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="top-bar">
            <h3>Manage Users</h3>
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
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Joined Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "SELECT * FROM users ORDER BY created_at DESC";
                        $result = mysqli_query($conn, $query);

                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>
                                    <td>#{$row['user_id']}</td>
                                    <td>{$row['full_name']}</td>
                                    <td>{$row['email']}</td>
                                    <td>{$row['phone']}</td>
                                    <td>" . date('M j, Y', strtotime($row['created_at'])) . "</td>
                                    <td>
                                        <a href='?delete={$row['user_id']}' class='btn' style='background: #e74c3c; padding: 5px 10px; font-size: 12px;' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                                    </td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6'>No users found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
