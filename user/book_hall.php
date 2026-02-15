<?php
session_start();
require_once '../config/db.php';
require_once '../config/razorpay.php';
require_once '../includes/functions.php';

// Check if user is logged in
check_user_login();

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Auto-cleanup expired bookings
cleanup_expired_bookings();

$hall_id = isset($_GET['hall_id']) ? (int)$_GET['hall_id'] : null;
$hall = null;
$halls = [];

if ($hall_id) {
    // Single Hall Mode (Booking Form)
    $stmt = $pdo->prepare("SELECT * FROM halls WHERE hall_id = :hall_id AND status = 'available'");
    $stmt->execute([':hall_id' => $hall_id]);
    $hall = $stmt->fetch();

    if (!$hall) {
        redirect_with_message('book_hall.php', 'Hall not found or unavailable.', 'error');
    }
} else {
    // List Mode
    $stmt = $pdo->query("SELECT * FROM halls WHERE status = 'available' ORDER BY created_at DESC");
    $halls = $stmt->fetchAll();
}

// Handle booking submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_booking'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token. Please try again.';
    } else {
        $hall_id = (int) $_POST['hall_id'];
        $from_date = clean_input($_POST['from_date']);
        $to_date = clean_input($_POST['to_date']);

        // Validate...
        if (!validate_date($from_date) || !validate_date($to_date)) {
            $error = 'Invalid date format.';
        } elseif (is_past_date($from_date)) {
            $error = 'Cannot book halls for past dates.';
        } elseif (strtotime($to_date) < strtotime($from_date)) {
            $error = 'End date cannot be before start date.';
        } else {
            // Check overlapping
            if (check_date_overlap($hall_id, $from_date, $to_date)) {
                $error = 'This hall is already booked for the selected dates.';
            } else {
                // Re-fetch hall to be safe
                $stmt = $pdo->prepare("SELECT * FROM halls WHERE hall_id = :hall_id");
                $stmt->execute([':hall_id' => $hall_id]);
                $hall_data = $stmt->fetch();

                if ($hall_data) {
                     $total_days = calculate_days_difference($from_date, $to_date);
                     $price_per_day = $hall_data['price_per_day'];
                     $total_amount = $total_days * $price_per_day;

                     try {
                        $pdo->beginTransaction();

                        $stmt = $pdo->prepare("
                            INSERT INTO bookings 
                            (user_id, hall_id, booking_start_date, booking_end_date, total_days, price_per_day, total_amount, booking_status, payment_status, created_at)
                            VALUES 
                            (:user_id, :hall_id, :from_date, :to_date, :total_days, :price_per_day, :total_amount, 'pending_payment', 'pending', NOW())
                        ");

                        $stmt->execute([
                            ':user_id' => $user_id,
                            ':hall_id' => $hall_id,
                            ':from_date' => $from_date,
                            ':to_date' => $to_date,
                            ':total_days' => $total_days,
                            ':price_per_day' => $price_per_day,
                            ':total_amount' => $total_amount
                        ]);

                        $booking_id = $pdo->lastInsertId();

                        // Razorpay Order
                        $receipt = 'booking_' . $booking_id . '_' . time();
                        $notes = [
                            'booking_id' => $booking_id,
                            'hall_name' => $hall_data['hall_name'],
                            'user_id' => $user_id
                        ];

                        $razorpay_order = createRazorpayOrder($total_amount, $receipt, $notes);

                        if ($razorpay_order && isset($razorpay_order['id'])) {
                             $stmt = $pdo->prepare("UPDATE bookings SET razorpay_order_id = :order_id WHERE booking_id = :booking_id");
                             $stmt->execute([':order_id' => $razorpay_order['id'], ':booking_id' => $booking_id]);
                             
                             $pdo->commit();
                             
                             // Prepare session for payment page
                             $_SESSION['pending_booking'] = [
                                'booking_id' => $booking_id,
                                'order_id' => $razorpay_order['id'],
                                'amount' => $total_amount,
                                'hall_name' => $hall_data['hall_name'],
                                'from_date' => $from_date,
                                'to_date' => $to_date,
                                'total_days' => $total_days
                             ];
                             
                             redirect('process_payment.php');
                        } else {
                            throw new Exception("Failed to create Razorpay order");
                        }

                     } catch (Exception $e) {
                         $pdo->rollBack();
                         $error = "Booking failed: " . $e->getMessage();
                     }
                }
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
    <title>Book Hall - HallEase</title>
    <link href="../assets/css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <!-- Flatpickr for dates -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
</head>
<body>

<!-- Navbar -->
<nav class="navbar">
    <div class="container nav-container">
        <a href="dashboard.php" class="navbar-brand">HallEase.</a>
        <div class="nav-links">
            <a href="dashboard.php" class="nav-link">Home</a>
            <a href="book_hall.php" class="nav-link active">Halls</a>
            <a href="my_bookings.php" class="nav-link">My Bookings</a>
            <div class="nav-user">
                <span style="font-weight: 500; color: var(--dark);">Hi, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?></span>
                <a href="logout.php" class="btn btn-secondary btn-sm">Logout</a>
            </div>
        </div>
        <div class="mobile-menu-btn"><i class="fas fa-bars"></i></div>
    </div>
</nav>

<?php if ($hall): ?>
    <!-- BOOKING FORM MODE -->
    <div class="container section">
        <a href="book_hall.php" class="btn btn-secondary mb-20"><i class="fas fa-arrow-left"></i> Back to Halls</a>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 40px; align-items: start;" class="booking-grid">
            <!-- Left: Hall Details & Images -->
            <div>
                <div class="hall-card" style="border: none; box-shadow: none;">
                    <?php 
                        $images = json_decode($hall['images'], true);
                        if(empty($images) || !is_array($images)) $images = ['default_hall.jpg'];
                    ?>
                    
                    <!-- Image Gallery Grid -->
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; margin-bottom: 20px;">
                        <?php foreach($images as $index => $img): 
                             $img_src = (filter_var($img, FILTER_VALIDATE_URL)) ? $img : "../assets/images/halls/" . $img;
                             // First image takes full width if there are multiple, or just behaves nicely
                             $style = ($index == 0) ? "grid-column: span 2; height: 400px;" : "height: 200px;";
                             if(count($images) == 1) $style = "grid-column: span 2; height: 500px;";
                        ?>
                            <div style="<?php echo $style; ?> border-radius: var(--radius-lg); overflow: hidden; box-shadow: var(--shadow-md);">
                                <img src="<?php echo htmlspecialchars($img_src); ?>" alt="Hall Image" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'" onerror="this.src='https://via.placeholder.com/800x400?text=No+Image'">
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div style="margin-top: 30px;">
                        <h1 style="margin-bottom: 10px;"><?php echo htmlspecialchars($hall['hall_name']); ?></h1>
                        <div class="card-meta" style="font-size: 1.1rem;">
                            <span class="meta-item"><i class="fas fa-map-marker-alt text-primary"></i> <?php echo htmlspecialchars($hall['city'] . ', ' . $hall['location']); ?></span>
                            <span class="meta-item"><i class="fas fa-users text-primary"></i> <?php echo htmlspecialchars($hall['capacity']); ?> Guests</span>
                        </div>
                        
                        <div style="margin-top: 20px;">
                            <h3 style="margin-bottom: 10px;">About this venue</h3>
                            <p style="color: var(--text-secondary);"><?php echo nl2br(htmlspecialchars($hall['description'])); ?></p>
                        </div>

                        <div style="margin-top: 30px;">
                            <h3 style="margin-bottom: 10px;">Facilities</h3>
                            <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                                <?php 
                                $facs = explode(',', $hall['facilities']);
                                foreach($facs as $f): 
                                ?>
                                    <span class="badge badge-info" style="font-size: 0.9rem; padding: 8px 15px;">
                                        <i class="fas fa-check"></i> <?php echo trim($f); ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Booking Form -->
            <div style="background: var(--white); padding: 30px; border-radius: var(--radius-lg); box-shadow: var(--shadow-lg); position: sticky; top: 100px;">
                <h3 style="margin-bottom: 20px;">Book your dates</h3>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <span class="text-secondary">Price per day</span>
                    <span style="font-size: 1.5rem; font-weight: 700; color: var(--primary);">₹<?php echo number_format($hall['price_per_day']); ?></span>
                </div>

                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                    <input type="hidden" name="hall_id" value="<?php echo $hall['hall_id']; ?>">
                    <input type="hidden" id="pricePerDay" value="<?php echo $hall['price_per_day']; ?>">

                    <div class="form-group">
                        <label class="form-label">Check-in Date</label>
                        <input type="text" name="from_date" id="from_date" class="form-control" placeholder="Select date" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Check-out Date</label>
                        <input type="text" name="to_date" id="to_date" class="form-control" placeholder="Select date" required>
                    </div>

                    <div style="background: var(--gray-100); padding: 15px; border-radius: var(--radius-md); margin-bottom: 20px;">
                         <div class="d-flex justify-between mb-20">
                             <span>Total Days:</span>
                             <span id="totalDays">0</span>
                         </div>
                         <div class="d-flex justify-between" style="font-weight: 700; font-size: 1.2rem; color: var(--dark);">
                             <span>Total Total:</span>
                             <span id="totalPrice">₹0</span>
                         </div>
                    </div>

                    <button type="submit" name="create_booking" class="btn btn-primary btn-block">
                        Proceed to Payment
                    </button>
                    <p style="text-align: center; font-size: 0.8rem; color: var(--text-secondary); margin-top: 10px;">You won't be charged yet</p>
                </form>
            </div>
        </div>
    </div>

<?php else: ?>
    <!-- LISTING MODE -->
    
    <!-- Hero Section -->
    <div class="hero">
        <div class="hero-shapes">
             <div class="shape-1"></div>
             <div class="shape-2"></div>
             <div class="shape-3"></div>
        </div>
        <div class="container hero-content">
            <h1>Find Your Perfect Venue</h1>
            <p>Discover and book premium halls for weddings, corporate events, and parties.</p>
            
            <div class="search-box">
                <input type="text" class="search-input" placeholder="Search by location, name, or capacity...">
                <button class="search-btn"><i class="fas fa-search"></i></button>
            </div>
        </div>
    </div>

    <!-- Halls List -->
    <div class="container section">
        <div class="d-flex justify-between align-center mb-20">
            <h2>Available Halls</h2>
        </div>

        <div class="card-grid">
            <?php foreach ($halls as $h): ?>
                <?php 
                    $images = json_decode($h['images'], true);
                    $main_image = !empty($images) && is_array($images) ? $images[0] : 'default_hall.jpg';
                    if (!filter_var($main_image, FILTER_VALIDATE_URL)) {
                        $main_image = "../assets/images/halls/" . $main_image;
                    }
                ?>
                <div class="hall-card">
                    <div class="card-image" style="position: relative; overflow: hidden; height: 250px;">
                        <a href="book_hall.php?hall_id=<?php echo $h['hall_id']; ?>" style="display: block; width: 100%; height: 100%;">
                            <?php if (count($images) > 1): ?>
                                <!-- Multiple Images Slider -->
                                <div class="hall-slider" id="slider-<?php echo $h['hall_id']; ?>" style="width: 100%; height: 100%; position: relative;">
                                    <?php foreach ($images as $index => $img): 
                                        if (!filter_var($img, FILTER_VALIDATE_URL)) {
                                            $img = "../assets/images/halls/" . $img;
                                        }
                                    ?>
                                        <img src="<?php echo htmlspecialchars($img); ?>" class="slide-item <?php echo $index === 0 ? 'active' : ''; ?>" 
                                             style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; transition: opacity 1s ease-in-out; opacity: <?php echo $index === 0 ? '1' : '0'; ?>;" 
                                             onerror="this.src='https://via.placeholder.com/400x300?text=No+Image'">
                                    <?php endforeach; ?>
                                    
                                    <!-- Dots Indicator -->
                                    <div class="slider-dots" style="position: absolute; bottom: 10px; left: 50%; transform: translateX(-50%); display: flex; gap: 5px; z-index: 2;">
                                        <?php foreach ($images as $index => $img): ?>
                                            <span class="dot" data-target="slider-<?php echo $h['hall_id']; ?>" data-index="<?php echo $index; ?>"
                                                  style="width: 8px; height: 8px; background: <?php echo $index === 0 ? '#fff' : 'rgba(255,255,255,0.5)'; ?>; border-radius: 50%; display: block; transition: background 0.3s;"></span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php else: 
                                // Single Image Fallback
                                $single_img = !empty($images) ? $images[0] : 'default_hall.jpg';
                                if (!filter_var($single_img, FILTER_VALIDATE_URL)) {
                                    $single_img = "../assets/images/halls/" . $single_img;
                                }
                            ?>
                                <img src="<?php echo htmlspecialchars($single_img); ?>" alt="<?php echo htmlspecialchars($h['hall_name']); ?>" 
                                     style="width: 100%; height: 100%; object-fit: cover;"
                                     onerror="this.src='https://via.placeholder.com/400x300?text=No+Image'">
                            <?php endif; ?>
                        </a>
                        <div class="card-overlay" style="z-index: 5;">
                            <?php if($h['status'] == 'available'): ?>
                                <span class="badge badge-success">Available</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Booked</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-between align-center mb-20">
                            <span class="badge badge-info"><?php echo htmlspecialchars($h['city']); ?></span>
                            <div class="text-secondary" style="font-size: 0.8rem;">
                                <i class="fas fa-star text-warning"></i> 4.5
                            </div>
                        </div>
                        
                        <h3 class="card-title">
                            <a href="book_hall.php?hall_id=<?php echo $h['hall_id']; ?>" style="color: inherit;"><?php echo htmlspecialchars($h['hall_name']); ?></a>
                        </h3>
                        
                        <div class="card-meta">
                            <span class="meta-item"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($h['location']); ?></span>
                            <span class="meta-item"><i class="fas fa-user-friends"></i> <?php echo htmlspecialchars($h['capacity']); ?></span>
                        </div>
                        
                        <div class="card-footer">
                            <div class="price-block">
                                <span class="card-price">₹<?php echo number_format($h['price_per_day']); ?></span>
                                <span class="price-period">/ day</span>
                            </div>
                            <a href="book_hall.php?hall_id=<?php echo $h['hall_id']; ?>" class="btn btn-primary btn-sm">Book Now</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <?php if(empty($halls)): ?>
            <div class="text-center" style="padding: 50px;">
                <i class="fas fa-search" style="font-size: 3rem; color: var(--gray-300); margin-bottom: 20px;"></i>
                <h3>No halls found</h3>
                <p class="text-secondary">Try adjusting your search criteria.</p>
            </div>
        <?php endif; ?>
    </div>

<?php endif; ?>

<script>
    // Flatpickr Logic
    const fromInput = document.getElementById('from_date');
    const toInput = document.getElementById('to_date');
    const pricePerDay = parseFloat(document.getElementById('pricePerDay')?.value || 0);

    if (fromInput && toInput) {
        flatpickr(fromInput, {
            minDate: "today",
            dateFormat: "Y-m-d",
            onChange: function(selectedDates, dateStr, instance) {
                toInput._flatpickr.set('minDate', dateStr);
                calculateTotal();
            }
        });

        flatpickr(toInput, {
            minDate: "today",
            dateFormat: "Y-m-d",
            onChange: function(selectedDates, dateStr, instance) {
                calculateTotal();
            }
        });
    }

    function calculateTotal() {
        const fromDate = fromInput._flatpickr.selectedDates[0];
        const toDate = toInput._flatpickr.selectedDates[0];

        if (fromDate && toDate) {
            const diffTime = Math.abs(toDate - fromDate);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1; // Inclusive
            
            if (diffDays > 0) {
                document.getElementById('totalDays').innerText = diffDays;
                document.getElementById('totalPrice').innerText = '₹' + (diffDays * pricePerDay).toLocaleString();
            }
        }
    }

    // Hall Image Slider Logic
    document.addEventListener('DOMContentLoaded', function() {
        const sliders = document.querySelectorAll('.hall-slider');
        
        sliders.forEach(slider => {
            const slides = slider.querySelectorAll('.slide-item');
            const dots = slider.querySelectorAll('.dot');
            let currentIndex = 0;
            
            if(slides.length > 1) {
                setInterval(() => {
                    // Hide current
                    slides[currentIndex].style.opacity = '0';
                    dots[currentIndex].style.background = 'rgba(255,255,255,0.5)';
                    
                    // Update index
                    currentIndex = (currentIndex + 1) % slides.length;
                    
                    // Show next
                    slides[currentIndex].style.opacity = '1';
                    dots[currentIndex].style.background = '#fff';
                    
                }, 3000); // 3 seconds
            }
        });
    });
</script>

</body>
</html>