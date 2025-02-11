<?php
// Ensure session is started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Debugging session data to check if session variables are set correctly
echo "Session ID: " . session_id() . "<br>";
echo "User: " . (isset($_SESSION["user"]) ? $_SESSION["user"] : "Not set") . "<br>";
echo "User Type: " . (isset($_SESSION["usertype"]) ? $_SESSION["usertype"] : "Not set") . "<br>";

// Check if user is logged in and if they are a patient ('p') or admin ('a')
if (!isset($_SESSION["user"]) || ($_SESSION['usertype'] != 'p' && $_SESSION['usertype'] != 'a')) {
    header("Location: ../login.php");
    exit;
}

// Get the user email (for patient) or ID (for admin)
$useremail = $_SESSION["user"];
$userType = $_SESSION['usertype'];

// Import database connection
include("../connection.php");

// If the user is a patient, we need to check that they are cancelling their own appointment
if ($userType == 'p') {
    // Get the patient's ID from the database
    $sql = "SELECT pid FROM patient WHERE pemail = ?";
    $stmt = $database->prepare($sql);
    $stmt->bind_param("s", $useremail);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $patient = $result->fetch_assoc();
        $patient_id = $patient['pid'];
    } else {
        echo "Error: Patient data not found.";
        exit;
    }
}

// Check if ID is present in the GET request
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    // If the user is a patient, ensure they are cancelling their own appointment
    if ($userType == 'p') {
        // Query to check if the appointment belongs to the logged-in patient
        $sql = "SELECT appoid FROM appointment WHERE appoid = ? AND pid = ?";
        $stmt = $database->prepare($sql);
        $stmt->bind_param('ii', $id, $patient_id);
        $stmt->execute();
        $result = $stmt->get_result();

        // If the appointment exists and belongs to the patient
        if ($result->num_rows > 0) {
            // Proceed with deletion
            $sql = "DELETE FROM appointment WHERE appoid = ?";
            $stmt = $database->prepare($sql);
            $stmt->bind_param('i', $id);

            if ($stmt->execute()) {
                // Successful deletion, redirect to appointment page with a status message
                header("Location: appointment.php?status=canceled");
                exit;
            } else {
                echo "Error deleting appointment.";
                exit;
            }
        } else {
            echo "You are not allowed to cancel this appointment.";
            exit;
        }
    } else {
        // If the user is an admin, allow cancellation of any appointment
        $sql = "DELETE FROM appointment WHERE appoid = ?";
        $stmt = $database->prepare($sql);
        $stmt->bind_param('i', $id);

        if ($stmt->execute()) {
            // Successful deletion, redirect to appointment page with a status message
            header("Location: appointment.php?status=canceled");
            exit;
        } else {
            echo "Error deleting appointment.";
            exit;
        }
    }
} else {
    echo "Invalid or missing ID.";
    exit;
}
?>