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
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - HallEase Admin</title>
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
                <a href="manage_halls.php" class="menu-item"><i class="fas fa-building"></i> Manage Halls</a>
                <a href="add_hall.php" class="menu-item"><i class="fas fa-plus-circle"></i> Add New Hall</a>
                <a href="manage_owners.php" class="menu-item"><i class="fas fa-user-tie"></i> Hall Owners</a>
                <a href="manage_users.php" class="menu-item active"><i class="fas fa-users"></i> Users</a>
                <a href="logout.php" class="menu-item"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>

        <div class="main-content">
            <div class="top-header">
                <h3>Manage Users</h3>
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

                <div class="card">
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Registered On</th>
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
                                        <td>" . date('d M Y', strtotime($row['created_at'])) . "</td>
                                        <td>
                                            <a href='?delete={$row['user_id']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure?\")'><i class='fas fa-trash'></i></a>
                                        </td>
                                    </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='6' class='text-center'>No users found.</td></tr>";
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