<?php
session_start();
include '../config/db.php';
include '../includes/functions.php';

$error = "";

if (isset($_POST['login'])) {
    $role = $_POST['role'];
    $email = clean_input($_POST['email']);
    $password = trim($_POST['password']); 

    if (empty($email) || empty($password)) {
        $error = "Please fill all fields";
    } else {
        if ($role == 'admin') {
            // --- ADMIN LOGIN LOGIC ---
            $query = "SELECT * FROM admins WHERE email = '$email'";
            $result = mysqli_query($conn, $query);
            if (mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                if (password_verify($password, $row['password'])) {
                    $_SESSION['admin_id'] = $row['admin_id'];
                    $_SESSION['admin_email'] = $row['email'];
                    $_SESSION['admin_name'] = $row['username'];
                    header("Location: ../admin/index.php");
                    exit();
                } else {
                    $error = "Invalid password for Admin";
                }
            } else {
                $error = "Admin account not found";
            }

        } elseif ($role == 'hallowner') {
            // --- OWNER LOGIN LOGIC ---
            $query = "SELECT * FROM hall_owners WHERE email = '$email'";
            $result = mysqli_query($conn, $query);
            if (mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                if (password_verify($password, $row['password'])) {
                    $_SESSION['owner_id'] = $row['owner_id'];
                    $_SESSION['owner_email'] = $row['email'];
                    $_SESSION['owner_name'] = $row['full_name'];
                    header("Location: ../owner/index.php");
                    exit();
                } else {
                    $error = "Invalid password for Owner";
                }
            } else {
                $error = "Owner account not found";
            }

        } elseif ($role == 'user') {
            // --- USER LOGIN LOGIC ---
            $query = "SELECT * FROM users WHERE email = '$email'";
            $result = mysqli_query($conn, $query);
            if (mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                if (password_verify($password, $row['password'])) {
                    $_SESSION['user_id'] = $row['user_id'];
                    $_SESSION['user_email'] = $row['email'];
                    $_SESSION['user_name'] = $row['full_name'];
                    
                    if (isset($_GET['redirect'])) {
                        header("Location: ../" . $_GET['redirect']);
                    } else {
                        header("Location: ../index.php");
                    }
                    exit();
                } else {
                    $error = "Invalid password";
                }
            } else {
                $error = "User account not found";
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
    <title>Login | HallEase</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #0f0f1e; color: white; margin: 0; display: flex; justify-content: center; align-items: center; height: 100vh; overflow: hidden; }
        .background { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); z-index: -1; }
        
        .login-wrapper { background: rgba(255, 255, 255, 0.95); padding: 40px; border-radius: 20px; width: 100%; max-width: 450px; color: #333; box-shadow: 0 15px 35px rgba(0,0,0,0.2); }
        
        .form-header { text-align: center; margin-bottom: 30px; }
        .form-header h2 { margin: 0; color: #333; }
        .form-header p { color: #666; font-size: 0.9rem; }
        
        .input-group { margin-bottom: 20px; position: relative; }
        .input-group label { display: block; margin-bottom: 8px; font-weight: 600; font-size: 0.9rem; }
        .input-group input { width: 100%; padding: 12px; padding-right: 40px; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; }
        
        .roles-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-bottom: 20px; }
        .role-card { cursor: pointer; text-align: center; border: 1px solid #ddd; padding: 10px; border-radius: 8px; transition: all 0.3s; position: relative; }
        .role-card:hover { border-color: #667eea; background: #f0f4ff; }
        .role-card input { position: absolute; opacity: 0; width: 0; height: 0; }
        
        /* Highlight selected role */
        .role-card input:checked + .role-content {
            color: #667eea;
            font-weight: bold;
        }
        .role-card input:checked ~ .role-border {
            border: 2px solid #667eea;
        }
        .role-card:has(input:checked) {
            border-color: #667eea;
            background: #f0f4ff;
            box-shadow: 0 0 0 1px #667eea;
        }

        .login-btn { width: 100%; padding: 15px; background: linear-gradient(135deg, #667eea, #764ba2); color: white; border: none; border-radius: 10px; font-size: 1rem; font-weight: 600; cursor: pointer; margin-top: 10px; }
        .login-btn:hover { opacity: 0.9; }
        
        .signup-link { display: block; text-align: center; margin-top: 20px; color: #667eea; text-decoration: none; font-weight: 500; }
        .error-message { background: #fee2e2; color: #b91c1c; padding: 10px; border-radius: 8px; margin-bottom: 20px; text-align: center; font-size: 0.9rem; }

        .toggle-password {
            position: absolute;
            right: 12px;
            top: 40px;
            cursor: pointer;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="background"></div>
    
    <div class="login-wrapper">
        <div class="form-header">
            <h2>Sign In</h2>
            <p>Welcome back to HallEase</p>
        </div>

        <?php if ($error): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="input-group">
                <label>Select Login Type</label>
                <div class="roles-grid">
                    <label class="role-card">
                        <input type="radio" name="role" value="admin">
                        <div class="role-content"><i class="fas fa-user-shield"></i><br>Admin</div>
                    </label>
                    <label class="role-card">
                        <input type="radio" name="role" value="hallowner">
                        <div class="role-content"><i class="fas fa-building"></i><br>Owner</div>
                    </label>
                    <label class="role-card">
                        <input type="radio" name="role" value="user" checked>
                        <div class="role-content"><i class="fas fa-user"></i><br>User</div>
                    </label>
                </div>
            </div>

            <div class="input-group">
                <label>Email Address</label>
                <input type="email" name="email" required placeholder="Enter your email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>

            <div class="input-group">
                <label>Password</label>
                <input type="password" name="password" id="password" required placeholder="Enter password">
                <i class="fas fa-eye toggle-password" onclick="togglePassword()"></i>
            </div>

            <button type="submit" name="login" class="login-btn">Sign In</button>
            <a href="register.php" class="signup-link">Don't have an account? Sign Up</a>
        </form>
    </div>

    <script>
        function togglePassword() {
            var passwordInput = document.getElementById("password");
            var icon = document.querySelector(".toggle-password");
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            } else {
                passwordInput.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
        }
    </script>
</body>
</html>