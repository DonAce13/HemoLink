<?php
session_start();

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Assuming the phone number is sent via POST request
    $telephone = $_POST['tele'] ?? '';

    // Simple validation for the telephone number
    if (preg_match('/^09\d{9}$/', $telephone)) {
        // Generate a random 6-digit OTP
        $otp = rand(100000, 999999);

        // Store the OTP in session for later verification
        $_SESSION['otp'] = $otp;
        $_SESSION['otp_expiry'] = time() + 300; // OTP expires in 5 minutes

        // Here you would integrate with an SMS gateway to send the OTP
        // For example, using a hypothetical SMS API
        /*
        $result = sendSms($telephone, "Your OTP is: $otp");
        if ($result['success']) {
            echo "OTP sent successfully.";
        } else {
            echo "Failed to send OTP.";
        }
        */

        // Simulating a successful send for demonstration purposes
        echo "OTP sent successfully to $telephone.";
    } else {
        echo "Invalid phone number.";
    }
} else {
    // Not a POST request
    http_response_code(405);
    echo "Method not allowed";
}

// Function to send SMS (hypothetical example)
function sendSms($to, $message) {
    // This function would interact with an SMS API
    return ['success' => true]; // Simulate a successful send
}
?>