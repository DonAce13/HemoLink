<?php
require 'vendor/autoload.php'; // Include Twilio SDK

use Twilio\Rest\Client;

// Database connection
$conn = new mysqli("localhost", "root", "", "hemolink_database");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get phone number from the form
$tele = $_POST['tele'];

// Convert phone number to international format (replace 09 with +63)
if (substr($tele, 0, 2) === '09') {
    $tele = '+63' . substr($tele, 1);
}

// Generate a 6-digit OTP
$otp = rand(100000, 999999);

// Insert the phone number and OTP into the database
$stmt = $conn->prepare("INSERT INTO patient (ptel, otp) VALUES (?, ?)");
$stmt->bind_param("ss", $tele, $otp);

if ($stmt->execute()) {
    // Twilio credentials
    $account_sid = 'ACbf56b173b4f3c4b0cef7f2497d30d6a5';
    $auth_token = 'd95e0606e4591b5f251cba67d54f7628';
    $twilio_number = '+14055432932';

    // Initialize Twilio client
    $client = new Client($account_sid, $auth_token);

    // Send SMS
    try {
        $client->messages->create(
            $tele, // Phone number to send the SMS to
            [
                'from' => $twilio_number,
                'body' => "Your OTP is: $otp"
            ]
        );
        echo "OTP sent to your phone number.";
    } catch (Exception $e) {
        echo "Error sending OTP: " . $e->getMessage();
    }
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
