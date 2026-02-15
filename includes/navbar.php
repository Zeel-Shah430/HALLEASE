<?php

?>
<link rel="stylesheet" href="/HALLEASE/assets/css/navbar.css">

<nav class="navbar">
    <div class="nav-left">
        <div class="logo">HallEase</div>
    </div>

    <div class="nav-center">
        <a href="/HALLEASE/user/dashboard.php">Home</a>
        <a href="/HALLEASE/user/book_hall.php">Book Hall</a>
        <a href="/HALLEASE/user/my_bookings.php">My Bookings</a>
        <a href="/HALLEASE/about.php">About Us</a>

    </div>

    <div class="nav-right">
        <span class="user-role">
            <?php echo ucfirst($_SESSION['role'] ?? 'Guest'); ?>
        </span>
        <a href="/HALLEASE/user/logout.php" class="logout-btn">Logout</a>

        <div class="menu-toggle" onclick="toggleMenu()">☰</div>
    </div>
</nav>

<script src="/HALLEASE/assets/js/navbar.js"></script>
