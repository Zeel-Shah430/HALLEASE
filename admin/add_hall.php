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
    $uploaded_images = [];
    $upload_dir = '../assets/images/halls/';

    // Ensure directory exists
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Process up to 4 images (File Uploads AND URLs)
    for ($i = 1; $i <= 4; $i++) {
        $file_key = 'hall_image' . $i;
        $url_key = 'hall_image_url' . $i;

        // Priority 1: File Upload
        if (isset($_FILES[$file_key]) && $_FILES[$file_key]['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES[$file_key]['tmp_name'];
            $file_name = $_FILES[$file_key]['name'];
            $file_size = $_FILES[$file_key]['size'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            // Allow only valid formats
            $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];

            if (in_array($file_ext, $allowed_ext)) {
                if ($file_size <= 2 * 1024 * 1024) { // 2MB Max
                    $new_name = 'hall_' . time() . '_' . $i . '.' . $file_ext;
                    $destination = $upload_dir . $new_name;

                    if (move_uploaded_file($file_tmp, $destination)) {
                        $uploaded_images[] = $new_name;
                    } else {
                        $error = "Failed to move uploaded file: " . $file_name;
                    }
                } else {
                    $error = "File too large (Max 2MB): " . $file_name;
                }
            } else {
                $error = "Invalid file type. Only JPG, PNG, WEBP allowed.";
            }
        }
        // Priority 2: Image URL
        elseif (!empty($_POST[$url_key])) {
            $url = clean_input($_POST[$url_key]);
            if (preg_match('/\.(jpg|jpeg|png|webp|gif)(\?.*)?$/i', $url)) {
                $uploaded_images[] = $url;
            } else {
                 $error = "Invalid Image URL! Links must end with .jpg, .png, or .webp";
            }
        }
    }

    // Store as JSON array
    $images_json = !empty($uploaded_images) ? json_encode($uploaded_images) : json_encode([]);

    // Basic Validation
    if (empty($owner_email) || empty($owner_password) || empty($hall_name)) {
        $error = "Please fill all required fields.";
    } elseif (empty($uploaded_images)) {
        $error = "Please provide at least one image (Upload or URL).";
    } else {
        // Start Transaction
        mysqli_begin_transaction($conn);

        try {
            // 1. Create Hall Owner
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
            $_POST = [];

        } catch (Exception $e) {
            mysqli_rollback($conn);
            $error = $e->getMessage();
        }
    }
}

$page_title = "Add Hall";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - HallEase Admin</title>
    <link href="../assets/css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600&display=swap"
        rel="stylesheet">
</head>

<body>

    <div class="dashboard-wrapper">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h2 style="color: var(--primary);">HallEase</h2>
            </div>
            <div class="sidebar-menu">
                <a href="index.php" class="menu-item"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                <a href="manage_halls.php" class="menu-item"><i class="fas fa-building"></i> Manage Halls</a>
                <a href="add_hall.php" class="menu-item active"><i class="fas fa-plus-circle"></i> Add New Hall</a>
                <a href="manage_owners.php" class="menu-item"><i class="fas fa-user-tie"></i> Hall Owners</a>
                <a href="manage_users.php" class="menu-item"><i class="fas fa-users"></i> Users</a>
                <a href="logout.php" class="menu-item"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="top-header">
                <h3>Add New Hall</h3>
                <div class="user-profile">
                    <span>Admin</span>
                    <div
                        style="width: 35px; height: 35px; background: var(--gray-200); border-radius: 50%; display: inline-block;">
                    </div>
                </div>
            </div>

            <div class="content-wrapper">
                <?php if ($message): ?>
                    <div class="alert alert-success"><?php echo $message; ?></div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <div class="hall-card" style="max-width: 800px; margin: 0 auto; padding: 0;">
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">

                            <h4 style="color: var(--primary); margin-bottom: 20px;">1. Create Owner Account</h4>

                            <div class="form-group">
                                <label class="form-label">Owner Name</label>
                                <input type="text" name="owner_name" class="form-control" required
                                    placeholder="Full Name">
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                                <div class="form-group">
                                    <label class="form-label">Email (Login ID)</label>
                                    <input type="email" name="owner_email" class="form-control" required
                                        placeholder="email@example.com">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Phone Number</label>
                                    <input type="text" name="owner_phone" class="form-control" required
                                        placeholder="+91 98765 43210">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Password</label>
                                <input type="password" name="owner_password" class="form-control" required
                                    placeholder="Secure Password">
                            </div>

                            <hr style="margin: 30px 0; border: 0; border-top: 1px solid var(--gray-200);">

                            <h4 style="color: var(--primary); margin-bottom: 20px;">2. Hall Information</h4>

                            <div class="form-group">
                                <label class="form-label">Hall Name</label>
                                <input type="text" name="hall_name" class="form-control" required
                                    placeholder="e.g. Grand Royal Palace">
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                                <div class="form-group">
                                    <label class="form-label">Location (Area)</label>
                                    <input type="text" name="location" class="form-control" required
                                        placeholder="e.g. MG Road">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">City</label>
                                    <input type="text" name="city" class="form-control" required
                                        placeholder="e.g. Mumbai">
                                </div>
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                                <div class="form-group">
                                    <label class="form-label">Capacity (Persons)</label>
                                    <input type="number" name="capacity" class="form-control" required
                                        placeholder="e.g. 500">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Price Per Day (₹)</label>
                                    <input type="number" name="price" class="form-control" step="0.01" required
                                        placeholder="e.g. 15000">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Facilities</label>
                                <input type="text" name="facilities" class="form-control"
                                    placeholder="e.g. AC, WiFi, Parking, Catering (Comma Separated)">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="4"
                                    placeholder="Describe the venue..."></textarea>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Images (Upload File OR Provide URL)</label>
                                <p style="font-size: 0.8rem; color: var(--danger); margin-bottom: 10px;">
                                    <i class="fas fa-exclamation-circle"></i> Note: Image URLs must be direct links
                                    ending in .jpg, .png, etc. Do not paste webpage addresses.
                                </p>
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                                    <!-- Image 1 -->
                                    <div
                                        style="border: 1px solid var(--gray-200); padding: 15px; border-radius: var(--radius-md);">
                                        <label
                                            style="font-size: 14px; font-weight: 600; margin-bottom: 5px; display: block;">Image
                                            1 (Main)</label>
                                        <input type="file" name="hall_image1" class="form-control" accept="image/*"
                                            style="margin-bottom: 10px;">
                                        <input type="text" name="hall_image_url1" class="form-control"
                                            placeholder="OR Paste Direct Image URL">
                                    </div>
                                    <!-- Image 2 -->
                                    <div
                                        style="border: 1px solid var(--gray-200); padding: 15px; border-radius: var(--radius-md);">
                                        <label
                                            style="font-size: 14px; font-weight: 600; margin-bottom: 5px; display: block;">Image
                                            2</label>
                                        <input type="file" name="hall_image2" class="form-control" accept="image/*"
                                            style="margin-bottom: 10px;">
                                        <input type="text" name="hall_image_url2" class="form-control"
                                            placeholder="OR Paste Direct Image URL">
                                    </div>
                                    <!-- Image 3 -->
                                    <div
                                        style="border: 1px solid var(--gray-200); padding: 15px; border-radius: var(--radius-md);">
                                        <label
                                            style="font-size: 14px; font-weight: 600; margin-bottom: 5px; display: block;">Image
                                            3</label>
                                        <input type="file" name="hall_image3" class="form-control" accept="image/*"
                                            style="margin-bottom: 10px;">
                                        <input type="text" name="hall_image_url3" class="form-control"
                                            placeholder="OR Paste Direct Image URL">
                                    </div>
                                    <!-- Image 4 -->
                                    <div
                                        style="border: 1px solid var(--gray-200); padding: 15px; border-radius: var(--radius-md);">
                                        <label
                                            style="font-size: 14px; font-weight: 600; margin-bottom: 5px; display: block;">Image
                                            4</label>
                                        <input type="file" name="hall_image4" class="form-control" accept="image/*"
                                            style="margin-bottom: 10px;">
                                        <input type="text" name="hall_image_url4" class="form-control"
                                            placeholder="OR Paste Direct Image URL">
                                    </div>
                                </div>
                            </div>

                            <button type="submit" name="add_hall" class="btn btn-primary btn-block"
                                style="margin-top: 20px;">
                                <i class="fas fa-check-circle"></i> Create Hall & Account
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>