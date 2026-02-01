<?php
session_start();
include 'C:\xampp\htdocs\HALLEASE\config\db.php';

if (isset($_POST['register'])) {

    // Server-side enforcement (ONLY user)
    if ($_POST['role'] !== 'user') {
        die("Unauthorized registration attempt.");
    }

    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // OPTIONAL: prevent duplicate email crash
    $check = mysqli_query($conn, "SELECT user_id FROM users WHERE email='$email'");
    if (mysqli_num_rows($check) > 0) {
        $error = "Email already registered. Please login.";
    } else {

        // ✅ FIXED ROLE VALUE
        $query = "INSERT INTO users (name, email, password, role, status)
                  VALUES ('$name', '$email', '$password', 'user', 'active')";

        if (mysqli_query($conn, $query)) {
            $_SESSION['user_id'] = mysqli_insert_id($conn);
            $_SESSION['role'] = 'user';

            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Registration failed. Try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sign Up | HallEase</title>
    <link rel="stylesheet" href="/HALLEASE/assets/css/signup.css">
</head>
<body>

<div class="signup-container">
    <h2>Create Account</h2>

    <?php if (isset($error)) { ?>
        <p class="error"><?php echo $error; ?></p>
    <?php } ?>

    <form method="POST" onsubmit="return validateForm()">

        <input type="text" name="name" placeholder="Full Name" required>

        <input type="email" name="email" placeholder="Email Address" required>

        <input type="password" name="password" placeholder="Password" required>

        <!-- ✅ FIXED ROLE -->
        <input type="hidden" name="role" value="user">

        <button type="submit" name="register">Sign Up</button>
    </form>

    <p class="login-text">
        Already have an account?
        <a href="login.php">Login</a>
    </p>
</div>

<script src="/HALLEASE/assets/js/signup.js"></script>
</body>
</html>
        