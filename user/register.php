<?php
session_start();
include '../config/db.php';
include '../includes/functions.php';

$error = "";
$message = "";

if (isset($_POST['register'])) {
    $full_name = clean_input($_POST['full_name']);
    $email = clean_input($_POST['email']);
    $phone = clean_input($_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error = "Passwords do not match";
    } else {
        // Check if email exists
        $check = mysqli_query($conn, "SELECT user_id FROM users WHERE email = '$email'");
        if (mysqli_num_rows($check) > 0) {
            $error = "Email already registered";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $query = "INSERT INTO users (full_name, email, password, phone) VALUES ('$full_name', '$email', '$hashed_password', '$phone')";
            
            if (mysqli_query($conn, $query)) {
                $message = "Registration successful! You can now login.";
            } else {
                $error = "Error: " . mysqli_error($conn);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up | HallEase</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #0f0f1e; margin: 0; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .background { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); z-index: -1; }
        
        .login-wrapper { background: rgba(255, 255, 255, 0.95); padding: 40px; border-radius: 20px; width: 100%; max-width: 450px; color: #333; }
        
        .form-header h2 { margin: 0 0 20px; text-align: center; }
        
        .input-group { margin-bottom: 20px; }
        .input-group label { display: block; margin-bottom: 8px; font-weight: 600; font-size: 0.9rem; }
        .input-group input { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; }
        
        .login-btn { width: 100%; padding: 15px; background: linear-gradient(135deg, #667eea, #764ba2); color: white; border: none; border-radius: 10px; font-weight: 600; cursor: pointer; }
        .error-message { background: #fee2e2; color: #b91c1c; padding: 10px; border-radius: 8px; margin-bottom: 20px; text-align: center; }
        .success-message { background: #d1fae5; color: #065f46; padding: 10px; border-radius: 8px; margin-bottom: 20px; text-align: center; }
    </style>
</head>
<body>
    <div class="background"></div>
    
    <div class="login-wrapper">
        <div class="form-header">
            <h2>Create Account</h2>
        </div>

        <?php if ($error): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($message): ?>
            <div class="success-message">
                <?php echo $message; ?>
                <br><a href="login.php" style="color: #065f46; font-weight: bold;">Login Now</a>
            </div>
        <?php else: ?>

        <form method="POST">
            <div class="input-group">
                <label>Full Name</label>
                <input type="text" name="full_name" required>
            </div>

            <div class="input-group">
                <label>Email Address</label>
                <input type="email" name="email" required>
            </div>
            
            <div class="input-group">
                <label>Phone Number</label>
                <input type="text" name="phone" required>
            </div>

            <div class="input-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>

            <div class="input-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" required>
            </div>

            <button type="submit" name="register" class="login-btn">Sign Up</button>
            <div style="text-align: center; margin-top: 15px;">
                <a href="login.php" style="color: #667eea; text-decoration: none;">Already have an account? Login</a>
            </div>
        </form>
        <?php endif; ?>
    </div>
</body>
</html>
