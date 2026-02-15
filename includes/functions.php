<?php
// ==================================================
// HallEase - Security & Utility Functions
// ==================================================

// ============ INPUT VALIDATION ============

function clean_input($data)
{
    if (is_array($data)) {
        return array_map('clean_input', $data);
    }
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

function validate_email($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validate_phone($phone)
{
    $phone = preg_replace('/[^0-9]/', '', $phone);
    return (strlen($phone) >= 10 && strlen($phone) <= 15) ? $phone : false;
}

function validate_date($date, $format = 'Y-m-d')
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

// ============ SECURITY ============

function generate_csrf_token()
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token($token)
{
    if (!isset($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

function hash_password($password)
{
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
}

function verify_password($password, $hash)
{
    return password_verify($password, $hash);
}

function sanitize_filename($filename)
{
    $filename = preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);
    return substr($filename, 0, 255);
}

// ============ SESSION & AUTHENTICATION ============

function check_admin_login()
{
    if (!isset($_SESSION['admin_id'])) {
        redirect('../admin/login.php');
    }
}

function check_owner_login()
{
    if (!isset($_SESSION['owner_id'])) {
        redirect('../owner/login.php');
    }
}

function check_user_login()
{
    if (!isset($_SESSION['user_id'])) {
        redirect('../user/login.php');
    }
}

function is_logged_in()
{
    return isset($_SESSION['user_id']) || isset($_SESSION['admin_id']) || isset($_SESSION['owner_id']);
}

function get_user_type()
{
    if (isset($_SESSION['admin_id']))
        return 'admin';
    if (isset($_SESSION['owner_id']))
        return 'owner';
    if (isset($_SESSION['user_id']))
        return 'user';
    return 'guest';
}

function get_user_id()
{
    if (isset($_SESSION['user_id']))
        return $_SESSION['user_id'];
    if (isset($_SESSION['admin_id']))
        return $_SESSION['admin_id'];
    if (isset($_SESSION['owner_id']))
        return $_SESSION['owner_id'];
    return null;
}

// ============ REDIRECT & NAVIGATION ============

function redirect($url)
{
    header("Location: $url");
    exit();
}

function redirect_with_message($url, $message, $type = 'success')
{
    $_SESSION['message'] = $message;
    $_SESSION['message_type'] = $type;
    redirect($url);
}

function get_flash_message()
{
    if (isset($_SESSION['message'])) {
        $message = $_SESSION['message'];
        $type = $_SESSION['message_type'] ?? 'info';
        unset($_SESSION['message'], $_SESSION['message_type']);
        return ['message' => $message, 'type' => $type];
    }
    return null;
}

// ============ DATE & TIME UTILITIES ============

function format_date($date, $format = 'd M Y')
{
    return date($format, strtotime($date));
}

function calculate_days_difference($start_date, $end_date)
{
    $start = new DateTime($start_date);
    $end = new DateTime($end_date);
    $diff = $start->diff($end);
    return $diff->days + 1; // Including both start and end date
}

function is_past_date($date)
{
    return strtotime($date) < strtotime(date('Y-m-d'));
}

function is_future_date($date)
{
    return strtotime($date) >= strtotime(date('Y-m-d'));
}

// ============ CURRENCY & NUMBER FORMATTING ============

function format_currency($amount)
{
    return '₹' . number_format($amount, 2);
}

function format_number($number)
{
    return number_format($number);
}

// ============ BOOKING VALIDATION ============

function check_date_overlap($hall_id, $start_date, $end_date, $exclude_booking_id = null)
{
    global $pdo;

    $sql = "SELECT booking_id FROM bookings 
            WHERE hall_id = :hall_id 
            AND booking_status NOT IN ('cancelled', 'payment_failed')
            AND (
                (booking_start_date <= :end_date AND booking_end_date >= :start_date)
            )";

    if ($exclude_booking_id) {
        $sql .= " AND booking_id != :exclude_booking_id";
    }

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':hall_id', $hall_id, PDO::PARAM_INT);
    $stmt->bindParam(':start_date', $start_date);
    $stmt->bindParam(':end_date', $end_date);

    if ($exclude_booking_id) {
        $stmt->bindParam(':exclude_booking_id', $exclude_booking_id, PDO::PARAM_INT);
    }

    $stmt->execute();
    return $stmt->fetch() !== false;
}

// ============ AUTO-CLEANUP FUNCTIONS ============

function cleanup_expired_bookings()
{
    global $pdo;

    $timeout_minutes = PAYMENT_TIMEOUT_MINUTES ?? 15;

    $sql = "UPDATE bookings 
            SET booking_status = 'payment_failed',
                payment_status = 'failed'
            WHERE booking_status = 'pending_payment'
            AND payment_status = 'pending'
            AND TIMESTAMPDIFF(MINUTE, created_at, NOW()) > :timeout";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':timeout', $timeout_minutes, PDO::PARAM_INT);
    return $stmt->execute();
}

// ============ LOGGING & AUDIT ============

function log_audit($action, $table_name = null, $record_id = null, $description = null)
{
    global $pdo;

    try {
        $user_id = get_user_id();
        $user_type = get_user_type();
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';

        $sql = "INSERT INTO audit_log (user_id, user_type, action, table_name, record_id, description, ip_address)
                VALUES (:user_id, :user_type, :action, :table_name, :record_id, :description, :ip_address)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':user_id' => $user_id,
            ':user_type' => $user_type,
            ':action' => $action,
            ':table_name' => $table_name,
            ':record_id' => $record_id,
            ':description' => $description,
            ':ip_address' => $ip_address
        ]);
    } catch (Exception $e) {
        error_log("Audit log failed: " . $e->getMessage());
    }
}

// ============ ERROR HANDLING ============

function show_error($message)
{
    $_SESSION['error'] = $message;
}

function get_error()
{
    if (isset($_SESSION['error'])) {
        $error = $_SESSION['error'];
        unset($_SESSION['error']);
        return $error;
    }
    return null;
}

// ============ FILE UPLOAD VALIDATION ============

function validate_image($file, $max_size = 5242880)
{ // 5MB default
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];

    if (!in_array($file['type'], $allowed_types)) {
        return ['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, GIF allowed.'];
    }

    if ($file['size'] > $max_size) {
        return ['success' => false, 'message' => 'File size exceeds limit (5MB maximum).'];
    }

    return ['success' => true];
}

// ============ STATUS BADGE HELPERS ============

function get_status_badge($status)
{
    $badges = [
        'confirmed' => '<span class="badge badge-success">Confirmed</span>',
        'pending_payment' => '<span class="badge badge-warning">Pending Payment</span>',
        'pending' => '<span class="badge badge-warning">Pending</span>',
        'cancelled' => '<span class="badge badge-danger">Cancelled</span>',
        'payment_failed' => '<span class="badge badge-danger">Payment Failed</span>',
        'completed' => '<span class="badge badge-info">Completed</span>',
        'paid' => '<span class="badge badge-success">Paid</span>',
        'failed' => '<span class="badge badge-danger">Failed</span>',
    ];

    return $badges[$status] ?? '<span class="badge badge-secondary">' . ucfirst($status) . '</span>';
}
?>