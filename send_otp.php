<?php
use Twilio\Rest\Client;
require __DIR__ . '/vendor/autoload.php';
require 'config.php'; // Separate file for database & Twilio credentials

// Database connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get phone number from the form
if (!isset($_POST['tele']) || empty($_POST['tele'])) {
    die("Phone number is required.");
}

$tele = $_POST['tele'];

// Validate phone number (must be 11 digits starting with 09)
if (!preg_match('/^09\d{9}$/', $tele)) {
    die("Invalid phone number format.");
}

// Convert to international format (+63)
$tele = '+63' . substr($tele, 1);

// Generate a 6-digit OTP
$otp = rand(100000, 999999);
$expires_at = date("Y-m-d H:i:s", strtotime("+5 minutes"));

// Check if the phone number exists in `otp_verifications`
$stmt = $conn->prepare("SELECT id FROM otp_verifications WHERE phone_number = ?");
$stmt->bind_param("s", $tele);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Update existing OTP
    $stmt = $conn->prepare("UPDATE otp_verifications SET otp = ?, expires_at = ? WHERE phone_number = ?");
    $stmt->bind_param("sss", $otp, $expires_at, $tele);
} else {
    // Insert new OTP entry
    $stmt = $conn->prepare("INSERT INTO otp_verifications (phone_number, otp, expires_at) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $tele, $otp, $expires_at);
}

// Execute query
if (!$stmt->execute()) {
    die("Error storing OTP: " . $stmt->error);
}

// Twilio credentials from config.php

$client = new Client(TWILIO_SID, TWILIO_AUTH_TOKEN);

try {
    $client->messages->create(
        $tele,
        [
            'from' => TWILIO_NUMBER,
            'body' => "Your OTP is: $otp. It expires in 5 minutes."
        ]
    );
    echo "OTP sent successfully.";
} catch (Exception $e) {
    echo "Error sending OTP: " . $e->getMessage();
}

$stmt->close();
$conn->close();
?>