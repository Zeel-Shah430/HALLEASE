<?php
session_start();
include 'C:\xampp\htdocs\HALLEASE\config\db.php';

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    $q = "SELECT * FROM users 
          WHERE email='$email' 
          AND password='$password' 
          AND role='$role' 
          AND status='active'";

    $res = mysqli_query($conn, $q);
    $user = mysqli_fetch_assoc($res);

    if ($user) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['role'] = $user['role'];

        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid login credentials";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login | HallEase</title>
    <!-- ✅ CORRECT CSS PATH -->
    <link rel="stylesheet" href="/HALLEASE/assets/css/login.css">
</head>
<body>

<div class="login-container">
    <h2>HallEase Login</h2>

    <?php if (isset($error)) { ?>
        <p class="error"><?php echo $error; ?></p>
    <?php } ?>

    <form method="POST" id="loginForm">

        <input type="email" name="email" placeholder="Email" required>

        <input type="password" name="password" placeholder="Password" required>

        <!-- ✅ FIXED ROLE MARKUP (VERY IMPORTANT) -->
        <div class="roles">
            <label>
                <input type="radio" name="role" value="admin" required>
                <span>Admin</span>
            </label>

            <label>
                <input type="radio" name="role" value="hallowner">
                <span>Hall Owner</span>
            </label>

            <label>
                <input type="radio" name="role" value="user">
                <span>User</span>
            </label>
        </div>

        <button type="submit" name="login">Login</button>
    </form>

    <p class="signup-text">
        New here?
        <a href="user_signup.php" onclick="checkSignup()">Sign Up</a>
    </p>
</div>

<!-- ✅ CORRECT JS PATH (THIS WAS YOUR BIGGEST BUG) -->
<script src="/HALLEASE/assets/js/login.js"></script>

</body>
</html>
