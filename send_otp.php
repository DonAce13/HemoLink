<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';
require 'connection.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log function for debugging
function logError($message) {
    error_log(date('[Y-m-d H:i:s] ') . $message . "\n", 3, __DIR__ . '/otp_errors.log');
}

$pemail = trim($_POST['email'] ?? '');

if (!$pemail || !filter_var($pemail, FILTER_VALIDATE_EMAIL)) {
    echo "Invalid or missing email.";
    exit;
}

// Check for existing, unexpired OTP
$stmt = $database->prepare("SELECT expires_at FROM otp_verifications WHERE pemail = ? AND expires_at > NOW() ORDER BY created_at DESC LIMIT 1");
$stmt->bind_param("s", $pemail);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo '<div style="text-align:center;">
        <img src="logo.png" alt="Logo" style="width:90px;margin-bottom:20px;">
        <h2 style="color:#22543d;">We have already sent you an email.</h2>
        <p style="font-size:1.1rem;">Wait for 5 minutes to resend on this email again.</p>
        <div style="margin-top:30px;">
            <a href="https://www.facebook.com/profile.php?id=61576816975344" target="_blank" style="color:#22543d;text-decoration:none;font-weight:bold;">
                <img src="logo.png" alt="Logo" style="width:30px;vertical-align:middle;margin-right:8px;">
                Need help? Visit our Facebook Page
            </a>
        </div>
    </div>';
    exit;
}
$stmt->close();

// Generate and insert OTP
$otp = sprintf("%06d", rand(0, 999999));
$expires_at = date("Y-m-d H:i:s", strtotime("+5 minutes"));
$created_at = date("Y-m-d H:i:s");

$stmt = $database->prepare("INSERT INTO otp_verifications (pemail, otp, expires_at, created_at) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $pemail, $otp, $expires_at, $created_at);

if (!$stmt->execute()) {
    logError("Database error: " . $stmt->error);
    echo "Failed to generate OTP. Please try again.";
    exit;
}
$stmt->close();

// Send email
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.hostinger.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'administrator@mabayuanhealthcare.online';
    $mail->Password   = 'Administrator@011303'; // <-- Replace with your Hostinger email password
    $mail->SMTPSecure = 'ssl';
    $mail->Port       = 465;
    $mail->setFrom('administrator@mabayuanhealthcare.online', 'Mabayuan Health');
    $mail->addAddress($pemail);
    $mail->isHTML(true);
    $mail->Subject = 'Your OTP for Mabayuan Health';

    $logoUrl = 'https://mabayuanhealthcare.online/logo.png';
    $facebookUrl = 'https://www.facebook.com/profile.php?id=61576816975344';

    $mail->Body = '
    <div style="background:#f8f9fa;padding:40px 0;text-align:center;font-family:Arial,sans-serif;">
        <img src="' . $logoUrl . '" alt="Logo" style="width:90px;margin-bottom:30px;">
        <h2 style="color:#22543d;font-size:1.6rem;font-weight:700;margin-bottom:18px;">Your OTP for Mabayuan Health</h2>
        <div style="font-size:1.3rem;color:#222;margin-bottom:10px;">Your OTP is: <span style="font-size:2.2rem;font-weight:700;color:#22543d;">' . $otp . '</span></div>
        <div style="margin-top:30px;font-size:1rem;color:#555;">
            <a href="' . $facebookUrl . '" style="color:#1b4332;text-decoration:underline;font-weight:bold;">
                <img src="' . $logoUrl . '" alt="Logo" style="width:30px;vertical-align:middle;margin-right:8px;">
                Follow us on Facebook
            </a>
        </div>
    </div>';
    $mail->AltBody = "Your OTP for Mabayuan Health is: $otp\nThis code will expire in 5 minutes.";

    $mail->send();
    echo "OTP has been sent to your email. Please check your inbox.";
} catch (Exception $e) {
    logError("Mail Error: " . $e->getMessage());
    echo "Failed to send OTP. Please try again.";
}

$database->close();
?>