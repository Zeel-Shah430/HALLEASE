<?php
session_start();
include '../config/db.php';
include '../includes/functions.php';

check_admin_login();

$message = "";
$error = "";
$hall_id = isset($_GET['id']) ? clean_input($_GET['id']) : '';

if (!$hall_id) {
    header("Location: manage_halls.php");
    exit();
}

// Fetch Hall Details
$query = "SELECT * FROM halls WHERE hall_id = '$hall_id'";
$result = mysqli_query($conn, $query);
$hall = mysqli_fetch_assoc($result);

if (!$hall) {
    header("Location: manage_halls.php");
    exit();
}

if (isset($_POST['update_hall'])) {
    $hall_name = clean_input($_POST['hall_name']);
    $location = clean_input($_POST['location']);
    $city = clean_input($_POST['city']);
    $capacity = clean_input($_POST['capacity']);
    $price = clean_input($_POST['price']);
    $facilities = clean_input($_POST['facilities']);
    $description = clean_input($_POST['description']);
    $status = clean_input($_POST['status']);

    // Image Handling
    $current_images = json_decode($hall['images'], true) ?? [];
    
    // Check if "Clear Images" is checked
    if (isset($_POST['clear_images'])) {
        $current_images = [];
    }

    $upload_dir = '../assets/images/halls/';

    if (!file_exists($upload_dir))
        mkdir($upload_dir, 0777, true);

    // Check for images (File or URL)
    for ($i = 1; $i <= 4; $i++) {
        $file_key = 'hall_image' . $i;
        $url_key = 'hall_image_url' . $i;

        // 1. File Upload
        if (isset($_FILES[$file_key]) && $_FILES[$file_key]['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES[$file_key]['tmp_name'];
            $file_name = $_FILES[$file_key]['name'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];

            if (in_array($file_ext, $allowed_ext) && $_FILES[$file_key]['size'] <= 2 * 1024 * 1024) {
                $new_name = 'hall_' . time() . '_' . $i . '.' . $file_ext;
                if (move_uploaded_file($file_tmp, $upload_dir . $new_name)) {
                    // Replace or Append? For now, append to options
                    $current_images[] = $new_name;
                }
            }
        }
        // 2. URL Input
        elseif (!empty($_POST[$url_key])) {
             $url = clean_input($_POST[$url_key]);
             // Basic validation for image extension
             if (preg_match('/\.(jpg|jpeg|png|webp|gif)$/i', $url) || filter_var($url, FILTER_VALIDATE_URL)) {
                 // We allow any valid URL but warn user in UI. 
                 // Stricter check:
                 if (preg_match('/\.(jpg|jpeg|png|webp|gif)(\?.*)?$/i', $url)) {
                    $current_images[] = $url;
                 } else {
                    // Try to be lenient but if it fails it fails. 
                    // Let's enforce extension for this user to avoid the "webpage" mistake.
                    $error = "Invalid Image URL! Links must end with .jpg, .png, or .webp";
                 }
             }
        }
    }

    // Remove duplicates and re-index
    $current_images = array_values(array_unique($current_images));
    $images_json = json_encode($current_images);

    $update_query = "UPDATE halls SET 
                     hall_name = '$hall_name',
                     location = '$location',
                     city = '$city',
                     capacity = '$capacity',
                     price_per_day = '$price',
                     facilities = '$facilities',
                     description = '$description',
                     status = '$status',
                     images = '$images_json'
                     WHERE hall_id = '$hall_id'";

    if (mysqli_query($conn, $update_query)) {
        $message = "Hall updated successfully!";
        // Refresh data
        $hall = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM halls WHERE hall_id = '$hall_id'"));
    } else {
        $error = "Error updating hall: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Hall - HallEase Admin</title>
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
                <a href="manage_halls.php" class="menu-item active"><i class="fas fa-building"></i> Manage Halls</a>
                <a href="add_hall.php" class="menu-item"><i class="fas fa-plus-circle"></i> Add New Hall</a>
                <a href="manage_owners.php" class="menu-item"><i class="fas fa-user-tie"></i> Hall Owners</a>
                <a href="manage_users.php" class="menu-item"><i class="fas fa-users"></i> Users</a>
                <a href="logout.php" class="menu-item"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="top-header">
                <h3>Edit Hall</h3>
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

                <div class="card" style="max-width: 800px; margin: 0 auto; padding: 20px;">
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">

                            <div class="form-group">
                                <label class="form-label">Hall Name</label>
                                <input type="text" name="hall_name" class="form-control"
                                    value="<?php echo htmlspecialchars($hall['hall_name']); ?>" required>
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                                <div class="form-group">
                                    <label class="form-label">Location (Area)</label>
                                    <input type="text" name="location" class="form-control"
                                        value="<?php echo htmlspecialchars($hall['location']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">City</label>
                                    <input type="text" name="city" class="form-control"
                                        value="<?php echo htmlspecialchars($hall['city']); ?>" required>
                                </div>
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
                                <div class="form-group">
                                    <label class="form-label">Capacity</label>
                                    <input type="number" name="capacity" class="form-control"
                                        value="<?php echo htmlspecialchars($hall['capacity']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Price Per Day (₹)</label>
                                    <input type="number" name="price" class="form-control" step="0.01"
                                        value="<?php echo htmlspecialchars($hall['price_per_day']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-control">
                                        <option value="available" <?php echo $hall['status'] == 'available' ? 'selected' : ''; ?>>Available</option>
                                        <option value="booked" <?php echo $hall['status'] == 'booked' ? 'selected' : ''; ?>>Booked/Maintenance</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Facilities</label>
                                <input type="text" name="facilities" class="form-control"
                                    value="<?php echo htmlspecialchars($hall['facilities']); ?>">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control"
                                    rows="4"><?php echo htmlspecialchars($hall['description']); ?></textarea>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Existing Images</label>
                                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                                    <?php
                                    $imgs = json_decode($hall['images'], true);
                                    if (!empty($imgs) && is_array($imgs)) {
                                        foreach ($imgs as $img) {
                                            $src = (filter_var($img, FILTER_VALIDATE_URL)) ? $img : "../assets/images/halls/" . $img;
                                            echo "<div style='position: relative; width: 100px; height: 100px;'>
                                                <img src='{$src}' style='width: 100%; height: 100%; object-fit: cover; border-radius: 5px; border: 1px solid var(--gray-200);' onerror=\"this.src='https://via.placeholder.com/100?text=Error'\">
                                              </div>";
                                        }
                                    } else {
                                        echo "<p class='text-secondary'>No images uploaded.</p>";
                                    }
                                ?>
                            </div>
                            <?php if (!empty($imgs)): ?>
                                <div style="margin-top: 10px;">
                                    <input type="checkbox" name="clear_images" id="clear_images">
                                    <label for="clear_images" style="color: var(--danger); font-size: 0.9rem;">Delete all existing images and replace with new ones</label>
                                </div>
                            <?php endif; ?>
                        </div>

                            <div class="form-group">
                                <label class="form-label">Add New Images</label>
                                <p style="font-size: 0.8rem; color: var(--danger); margin-bottom: 10px;">
                                    <i class="fas fa-exclamation-circle"></i> Note: specific image links must end with
                                    .jpg, .png, .jpeg, or .webp (e.g. https://example.com/image.jpg). Do not paste
                                    webpage links.
                                </p>
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                                    <!-- New Image 1 -->
                                    <div
                                        style="border: 1px solid var(--gray-200); padding: 15px; border-radius: var(--radius-md);">
                                        <label style="font-size: 14px; margin-bottom: 5px; display: block;">Upload or
                                            Link</label>
                                        <input type="file" name="hall_image1" class="form-control" accept="image/*"
                                            style="margin-bottom: 10px;">
                                        <input type="text" name="hall_image_url1" class="form-control"
                                            placeholder="OR Paste Direct Image URL">
                                    </div>
                                    <div
                                        style="border: 1px solid var(--gray-200); padding: 15px; border-radius: var(--radius-md);">
                                        <label style="font-size: 14px; margin-bottom: 5px; display: block;">Upload or
                                            Link</label>
                                        <input type="file" name="hall_image2" class="form-control" accept="image/*"
                                            style="margin-bottom: 10px;">
                                        <input type="text" name="hall_image_url2" class="form-control"
                                            placeholder="OR Paste Direct Image URL">
                                    </div>
                                </div>
                            </div>

                            <div style="display: flex; gap: 15px; margin-top: 30px;">
                                <button type="submit" name="update_hall" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Save Changes
                                </button>
                                <a href="manage_halls.php" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>