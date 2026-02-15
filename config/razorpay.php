<?php
// Razorpay API Configuration (TEST MODE)
define('RAZORPAY_KEY_ID', 'rzp_test_Ry4C57BA0Ny03W');
define('RAZORPAY_KEY_SECRET', 'L6eeFgBpCY62EYR0EyEJJWXn');

// Payment Configuration
define('CURRENCY', 'INR');
define('PAYMENT_TIMEOUT_MINUTES', 15); // Auto-cancel unpaid bookings after 15 minutes

// Function to create Razorpay order
function createRazorpayOrder($amount, $receipt, $notes = [])
{
    $url = 'https://api.razorpay.com/v1/orders';

    $data = [
        'amount' => $amount * 100, // Convert to paise
        'currency' => CURRENCY,
        'receipt' => $receipt,
        'notes' => $notes
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERPWD, RAZORPAY_KEY_ID . ':' . RAZORPAY_KEY_SECRET);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode == 200) {
        return json_decode($response, true);
    }

    return false;
}

// Function to verify Razorpay signature
function verifyRazorpaySignature($orderId, $paymentId, $signature)
{
    $generatedSignature = hash_hmac('sha256', $orderId . '|' . $paymentId, RAZORPAY_KEY_SECRET);
    return hash_equals($generatedSignature, $signature);
}

// Function to fetch payment details
function fetchPaymentDetails($paymentId)
{
    $url = "https://api.razorpay.com/v1/payments/" . $paymentId;

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERPWD, RAZORPAY_KEY_ID . ':' . RAZORPAY_KEY_SECRET);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode == 200) {
        return json_decode($response, true);
    }

    return false;
}
?>