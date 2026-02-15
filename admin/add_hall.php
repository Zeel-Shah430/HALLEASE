<?php
session_start();
include '../config/db.php';
include '../includes/functions.php';

check_admin_login();

$message = "";
$error = "";

if (isset($_POST['add_hall'])) {
    // Owner Details
    $owner_name = clean_input($_POST['owner_name']);
    $owner_email = clean_input($_POST['owner_email']);
    $owner_phone = clean_input($_POST['owner_phone']);
    $owner_password = $_POST['owner_password'];
    
    // Hall Details
    $hall_name = clean_input($_POST['hall_name']);
    $location = clean_input($_POST['location']);
    $city = clean_input($_POST['city']);
    $capacity = clean_input($_POST['capacity']);
    $price = clean_input($_POST['price']);
    $facilities = clean_input($_POST['facilities']);
    $description = clean_input($_POST['description']);
    
    // Image Handling
    $images = [];
    if(!empty($_POST['img1'])) $images[] = trim($_POST['img1']);
    if(!empty($_POST['img2'])) $images[] = trim($_POST['img2']);
    if(!empty($_POST['img3'])) $images[] = trim($_POST['img3']);
    if(!empty($_POST['img4'])) $images[] = trim($_POST['img4']);
    
    $images_json = json_encode($images); // Store as ["url1", "url2", ...]
    
    // Basic Validation
    if (empty($owner_email) || empty($owner_password) || empty($hall_name)) {
        $error = "Please fill all required fields.";
    } else {
        // Start Transaction
        mysqli_begin_transaction($conn);
        
        try {
            // 1. Create Hall Owner
            // Check if owner email exists
            $check_owner = mysqli_query($conn, "SELECT owner_id FROM hall_owners WHERE email = '$owner_email'");
            if (mysqli_num_rows($check_owner) > 0) {
                 throw new Exception("Owner with this email already exists.");
            }

            $hashed_password = password_hash($owner_password, PASSWORD_DEFAULT);
            $owner_query = "INSERT INTO hall_owners (full_name, email, password, phone) 
                            VALUES ('$owner_name', '$owner_email', '$hashed_password', '$owner_phone')";
            
            if (!mysqli_query($conn, $owner_query)) {
                throw new Exception("Error creating owner: " . mysqli_error($conn));
            }
            
            $owner_id = mysqli_insert_id($conn);
            
            // 2. Add Hall
            $hall_query = "INSERT INTO halls (owner_id, hall_name, location, city, capacity, price_per_day, facilities, description, status, images) 
                           VALUES ('$owner_id', '$hall_name', '$location', '$city', '$capacity', '$price', '$facilities', '$description', 'available', '$images_json')";
            
            if (!mysqli_query($conn, $hall_query)) {
                throw new Exception("Error adding hall: " . mysqli_error($conn));
            }
            
            mysqli_commit($conn);
            $message = "Hall and Owner account created successfully!";
            
        } catch (Exception $e) {
            mysqli_rollback($conn);
            $error = $e->getMessage();
        }
    }
}

$page_title = "Add Hall";
include '../includes/header.php';
?>

<div class="dashboard-container">
    <div class="sidebar">
        <h2>HallEase Admin</h2>
        <ul>
            <li><a href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="manage_halls.php"><i class="fas fa-building"></i> Manage Halls</a></li>
            <li><a href="add_hall.php" class="active"><i class="fas fa-plus-circle"></i> Add New Hall</a></li>
            <li><a href="manage_owners.php"><i class="fas fa-user-tie"></i> Hall Owners</a></li>
            <li><a href="manage_users.php"><i class="fas fa-users"></i> Users</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="top-bar">
            <h3>Add New Hall</h3>
        </div>

        <?php if($message): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <?php if($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="card">
            <form method="POST">
                <h4>1. Hall Owner Details (Account Creation)</h4>
                <hr style="margin-bottom: 20px; border: 0; border-top: 1px solid #eee;">
                
                <div class="form-group">
                    <label class="form-label">Owner Name</label>
                    <input type="text" name="owner_name" class="form-control" required>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label class="form-label">Owner Email (Login ID)</label>
                        <input type="email" name="owner_email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Owner Phone</label>
                        <input type="text" name="owner_phone" class="form-control" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Owner Password</label>
                    <input type="password" name="owner_password" class="form-control" required>
                </div>

                <h4 style="margin-top: 30px;">2. Hall Details</h4>
                <hr style="margin-bottom: 20px; border: 0; border-top: 1px solid #eee;">

                <div class="form-group">
                    <label class="form-label">Hall Name</label>
                    <input type="text" name="hall_name" class="form-control" required>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label class="form-label">Location (Area)</label>
                        <input type="text" name="location" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">City</label>
                        <input type="text" name="city" class="form-control" required>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label class="form-label">Capacity (Persons)</label>
                        <input type="number" name="capacity" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Price Per Day (Rs)</label>
                        <input type="number" name="price" class="form-control" step="0.01" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Facilities (Comma Separated, e.g. AC, WiFi, Parking)</label>
                    <input type="text" name="facilities" class="form-control">
                </div>

                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="4"></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Hall Images (Paste Links)</label>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                        <input type="url" name="img1" class="form-control" placeholder="Image Link 1 (Main Image)">
                        <input type="url" name="img2" class="form-control" placeholder="Image Link 2">
                        <input type="url" name="img3" class="form-control" placeholder="Image Link 3">
                        <input type="url" name="img4" class="form-control" placeholder="Image Link 4">
                    </div>
                </div>

                <button type="submit" name="add_hall" class="btn">Create Hall & Owner Account</button>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
