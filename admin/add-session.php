<?php
session_start();

if (isset($_SESSION["user"])) {
    if ($_SESSION["user"] == "" || $_SESSION['usertype'] != 'a') {
        header("location: ../login.php");
    }
} else {
    header("location: ../login.php");
}

if ($_POST) {
    // Import database
    include("../connection.php");

    // Retrieve POST data
    $title = $_POST["title"];
    $docid = $_POST["docid"];
    $nop = $_POST["nop"];
    $date = $_POST["date"];
    $time = $_POST["time"];
    $duration = $_POST["duration"];  // New: retrieve session duration from the form

    // Calculate the end time by adding the session duration to the start time
    $end_time = date('H:i', strtotime($time) + $duration * 60);  // End time calculated from start time + duration

    // Use prepared statements to avoid SQL injection
    $stmt = $database->prepare("INSERT INTO schedule (docid, title, scheduledate, scheduletime, nop, session_duration, end_time) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssiss", $docid, $title, $date, $time, $nop, $duration, $end_time);  // Bind all parameters

    if ($stmt->execute()) {
        header("location: schedule.php?action=session-added&title=$title");
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

?>
