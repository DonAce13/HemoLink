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
if (!isset($_POST['phone_number']) || empty($_POST['phone_number'])) {
    die("Phone number is required.");
}

$phone_number = $_POST['phone_number'];

// Validate phone number (must be 11 digits starting with 09)
if (!preg_match('/^09\d{9}$/', $phone_number)) {
    die("Invalid phone number formdefine('TWILIO_VERIFY_SID', 'your_twilio_verify_service_sid');at. Please enter a valid 11-digit number starting with 09.");
}

// Convert to international format (+63)
$phone_number = '+63' . substr($phone_number, 1);

// Generate a 6-digit OTP
$otp = rand(100000, 999999);
$expires_at = date("Y-m-d H:i:s", strtotime("+5 minutes"));

// Store OTP in database
$stmt = $conn->prepare("INSERT INTO otp_verifications (phone_number, otp, expires_at) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE otp = ?, expires_at = ?");
$stmt->bind_param("sssss", $phone_number, $otp, $expires_at, $otp, $expires_at);

if (!$stmt->execute()) {
    die("Error storing OTP: " . $stmt->error);
}

// Initialize Twilio Verify client
$twilio = new Client(TWILIO_SID, TWILIO_AUTH_TOKEN);

try {
    $verification = $twilio->verify->v2->services(TWILIO_VERIFY_SID)
                                       ->verifications
                                       ->create($phone_number, "sms");
    echo "OTP verification initiated. Check your phone for the code.";
} catch (Exception $e) {
    echo "Error sending OTP: " . $e->getMessage();
    error_log("Twilio Error: " . $e->getMessage());
}

$stmt->close();
$conn->close();
?>