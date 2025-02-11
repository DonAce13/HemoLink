<?php
use Twilio\Rest\Client;
use Twilio\Exceptions\RestException;
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
    die("Invalid phone number format. Please enter a valid 11-digit number starting with 09.");
}

// Convert to international format (+63)
$phone_number = '+63' . substr($phone_number, 1);

// Generate a 6-digit OTP
$otp = sprintf("%06d", rand(0, 999999));
$expires_at = date("Y-m-d H:i:s", strtotime("+5 minutes"));

// Initialize Twilio client
$twilio = new Client(TWILIO_SID, TWILIO_AUTH_TOKEN);

try {
    // Validate Twilio credentials
    if (empty(TWILIO_SID) || empty(TWILIO_AUTH_TOKEN) || empty(TWILIO_PHONE_NUMBER)) {
        throw new Exception("Missing Twilio configuration parameters");
    }

    // Send OTP via SMS
    $message = $twilio->messages->create(
        $phone_number,
        [
            'from' => TWILIO_PHONE_NUMBER,
            'body' => "Your OTP for Mabayuan Health is: $otp"
        ]
    );

    // Store OTP in database
    $stmt = $conn->prepare("INSERT INTO otp_verifications (phone_number, otp, expires_at) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE otp = ?, expires_at = ?");
    $stmt->bind_param("sssss", $phone_number, $otp, $expires_at, $otp, $expires_at);

    if (!$stmt->execute()) {
        throw new Exception("Error storing OTP: " . $stmt->error);
    }
    
    // Log successful OTP send
    error_log("OTP sent successfully to: $phone_number");
    echo "OTP verification initiated. Check your phone for the code.";

} catch (Twilio\Exceptions\RestException $e) {
    // Specific Twilio REST API error handling
    error_log("Twilio REST Error: " . $e->getMessage());
    error_log("Error Code: " . $e->getCode());
    echo "Twilio service error. Please contact support. Error: " . $e->getMessage();

} catch (Exception $e) {
    // General exception handling
    error_log("OTP Verification Error: " . $e->getMessage());
    echo "Failed to send OTP. Please try again or contact support.";
}

$stmt->close();
$conn->close();
?>