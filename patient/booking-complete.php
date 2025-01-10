<?php
session_start();

// Ensure the user is logged in and has the correct user type
if (isset($_SESSION["user"])) {
    if ($_SESSION["user"] == "" || $_SESSION['usertype'] != 'p') {
        header("location: ../login.php");
        exit();
    } else {
        $useremail = $_SESSION["user"];
    }
} else {
    header("location: ../login.php");
    exit();
}

// Import database connection
include("../connection.php");

// Retrieve patient details based on logged-in user
$sqlmain = "SELECT * FROM patient WHERE pemail = ?";
$stmt = $database->prepare($sqlmain);
$stmt->bind_param("s", $useremail);
$stmt->execute();
$userrow = $stmt->get_result();
$userfetch = $userrow->fetch_assoc();
$userid = $userfetch["pid"];
$username = $userfetch["pname"];

if ($_POST) {
    if (isset($_POST["booknow"])) {
        // Retrieve form inputs
        $apponum = $_POST["apponum"];
        $scheduleid = $_POST["scheduleid"];
        $date = $_POST["date"];
        $is_self = $_POST["is_self"]; // 0 for self, 1 for others
        $scheduletime = $_POST["scheduletime"]; // Assuming this is passed from the form

        // Common fields
        $sql2 = "INSERT INTO appointment 
            (pid, apponum, scheduleid, appodate, scheduletime, is_self, other_patient_name, description, philhealth_id, age, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'scheduled')";

        // Default values for other patient fields
        $other_patient_name = null;
        $description = null;
        $philhealth_id = null;
        $age = null;

        // If booking for others, retrieve additional fields
        if ($is_self == "1") {
            $other_patient_name = $_POST["other_patient_name"];
            $philhealth_id = $_POST["philhealth_id"];
            $age = $_POST["age"];
            $description = $_POST["description"];
        }

        // Prepare the query
        $stmt = $database->prepare($sql2);
        $stmt->bind_param(
            "iiississsi",
            $userid,        // Patient ID
            $apponum,       // Appointment number
            $scheduleid,    // Schedule ID
            $date,          // Appointment date
            $scheduletime,  // Schedule time
            $is_self,       // Is self or others
            $other_patient_name, // Name of other patient (if applicable)
            $description,   // Description (if applicable)
            $philhealth_id, // PhilHealth ID (if applicable)
            $age            // Age (if applicable)
        );

        // Execute and redirect
        if ($stmt->execute()) {
            // Redirect to the appointment page with success message
            header("location: appointment.php?action=booking-added&id=" . $apponum . "&titleget=none");
            exit();
        } else {
            echo "Error: Could not save the appointment. Please try again later.";
        }
    }
} else {
    echo "Invalid request.";
}
?>
