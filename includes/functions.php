<?php

function clean_input($data) {
    global $conn;
    return mysqli_real_escape_string($conn, trim($data));
}


function redirect($url) {
    header("Location: $url");
    exit();
}

function check_admin_login() {
    if (!isset($_SESSION['admin_id'])) {
        redirect('../admin/login.php');
    }
}

function check_owner_login() {
    if (!isset($_SESSION['owner_id'])) {
        redirect('../owner/login.php');
    }
}

function check_user_login() {
    if (!isset($_SESSION['user_id'])) {
        redirect('../login.php');
    }
}

function format_currency($amount) {
    return '$' . number_format($amount, 2);
}
?>
