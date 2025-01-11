<?php
session_start();

if (isset($_SESSION["user"])) {
    if ($_SESSION["user"] == "" || $_SESSION['usertype'] != 'a') {
        header("location: ../login.php");
        exit();  // Always exit after header redirect
    }
} else {
    header("location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['shedulesubmit'])) { // Check for shedulesubmit
    // Import database connection
    include("../connection.php");

    // Retrieve POST data
    $title = $_POST["title"];
    $docid = $_POST["docid"];
    $nop = $_POST["nop"];
    $date = $_POST["date"];
    $time = $_POST["time"];
    $duration = $_POST["duration"];  // Session duration from form

    // Validate received data
    if (empty($title) || empty($docid) || empty($nop) || empty($date) || empty($time) || empty($duration)) {
        echo "All fields are required!";
        exit();
    }

    // Validate that 'nop' is a valid number
    if (!is_numeric($nop) || $nop < 1) {
        echo "The number of patients/appointments must be a positive number!";
        exit();
    }

    // Calculate end time by adding the session duration to the start time
    $start_time = strtotime($time); // Convert start time to timestamp
    $end_time = date('H:i', $start_time + $duration * 60);  // End time is calculated from start time + duration

    // Prepare the SQL statement to insert session data into the database
    $stmt = $database->prepare("INSERT INTO schedule (docid, title, scheduledate, scheduletime, nop, session_duration, end_time) 
                                VALUES (?, ?, ?, ?, ?, ?, ?)");

    // Bind parameters to the prepared statement (assuming correct data types: i - integer, s - string)
    $stmt->bind_param("isssiss", $docid, $title, $date, $time, $nop, $duration, $end_time);

    // Execute the query and check if it was successful
    if ($stmt->execute()) {
        // Redirect to schedule page with success message
        header("Location: schedule.php?action=session-added&title=" . urlencode($title));
        exit();
    } else {
        // Error handling: Show error if insert fails
        echo "Error: " . $stmt->error;
        exit();
    }

    $stmt->close();
} else {
    // Handle case when POST data is not received or shedulesubmit is not set
    echo "No data received.";
    exit();
}
?>
