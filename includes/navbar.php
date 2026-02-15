<?php
// Determine current page for active state
$current_page = basename($_SERVER['PHP_SELF']);
?>
<nav class="navbar">
    <div class="container nav-container">
        <a href="dashboard.php" class="navbar-brand">HallEase.</a>
        <div class="nav-links">
            <a href="dashboard.php"
                class="nav-link <?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>">Home</a>
            <a href="book_hall.php"
                class="nav-link <?php echo $current_page == 'book_hall.php' ? 'active' : ''; ?>">Halls</a>
            <a href="my_bookings.php"
                class="nav-link <?php echo $current_page == 'my_bookings.php' ? 'active' : ''; ?>">My Bookings</a>

            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="nav-user">
                    <span style="font-weight: 500; color: var(--dark);">Hi,
                        <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?></span>
                    <a href="logout.php" class="btn btn-secondary btn-sm">Logout</a>
                </div>
            <?php else: ?>
                <a href="login.php" class="btn btn-primary btn-sm">Login</a>
                <a href="register.php" class="btn btn-secondary btn-sm">Register</a>
            <?php endif; ?>
        </div>
        <div class="mobile-menu-btn" onclick="document.querySelector('.nav-links').classList.toggle('active')"><i
                class="fas fa-bars"></i></div>
    </div>
</nav>

<style>
    @media (max-width: 768px) {
        .nav-links.active {
            display: flex;
            flex-direction: column;
            position: absolute;
            top: 70px;
            left: 0;
            width: 100%;
            background: white;
            padding: 20px;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
        }
    }
</style>