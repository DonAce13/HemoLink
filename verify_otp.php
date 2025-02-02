<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $otp = $_POST['otp'];

    $database = new mysqli("localhost", "root", "", "hemolink_database");

    if ($database->connect_error) {
        die("Connection failed: " . $database->connect_error);
    }

    $email = $_SESSION["user"];

    // Verify OTP
    $stmt = $database->prepare("SELECT otp FROM patient WHERE pemail = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($row['otp'] == $otp) {
            // Update OTP verification status
            $stmt = $database->prepare("UPDATE patient SET otp_verified = 1 WHERE pemail = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();

            // Redirect to success page
            header("Location: patient.php");
            exit();
        } else {
            echo "Invalid OTP. Please try again.";
        }
    } else {
        echo "OTP verification failed. Please try again.";
    }

    $database->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Verify OTP</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <h2>Verify OTP</h2>
    <form action="" method="post">
        <label for="otp">Enter OTP:</label>
        <input type="text" id="otp" name="otp" required>
        <button type="submit">Verify OTP</button>
    </form>
</body>
</html>