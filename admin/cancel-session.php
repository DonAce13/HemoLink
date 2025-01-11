<?php

session_start();

// Enable error reporting for troubleshooting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Check if the user is logged in and has admin rights
if (isset($_SESSION["user"])) {
    if ($_SESSION["user"] == "" || $_SESSION['usertype'] != 'a') {
        header("location: ../login.php");
        exit;  // Always exit after header redirect
    }
} else {
    header("location: ../login.php");
    exit;
}

// Check if 'id' parameter is passed in the URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    // Get the schedule ID from URL
    $id = $_GET['id'];

    // Include database connection
    include("../connection.php");

    // Prepare the SQL query to fetch session details using a prepared statement
    $stmt = $database->prepare("SELECT * FROM schedule WHERE scheduleid = ?");
    $stmt->bind_param("i", $id); // "i" means the parameter is an integer
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the session exists in the database
    if ($result->num_rows > 0) {
        // Fetch session data
        $row = $result->fetch_assoc();
        $scheduleid = $row['scheduleid'];
        $docid = $row['docid'];
        $title = $row['title'];
        $scheduledate = $row['scheduledate'];
        $scheduletime = $row['scheduletime'];
        $nop = $row['nop'];

        // Prepare the SQL query to insert session data into 'archived_schedule' table (soft delete)
        $archive_stmt = $database->prepare("INSERT INTO archived_schedule (scheduleid, docid, title, scheduledate, scheduletime, nop, deleted_at) 
                                            VALUES (?, ?, ?, ?, ?, ?, NOW())");
        $archive_stmt->bind_param("iisssi", $scheduleid, $docid, $title, $scheduledate, $scheduletime, $nop);

        if ($archive_stmt->execute()) {
            // Prepare the SQL query to delete the session from the 'schedule' table
            $delete_stmt = $database->prepare("DELETE FROM schedule WHERE scheduleid = ?");
            $delete_stmt->bind_param("i", $id);

            if ($delete_stmt->execute()) {
                // Redirect to the schedule page after successful cancellation
                header("location: schedule.php");
                exit;
            } else {
                // Error while deleting the session from the schedule table
                echo "Error deleting session: " . $delete_stmt->error;
            }
        } else {
            // Error while archiving the session
            echo "Error archiving session: " . $archive_stmt->error;
        }
    } else {
        // If no session is found in the schedule table
        echo "Session with ID $id not found!";
    }

    // Close the prepared statements
    $stmt->close();
    $archive_stmt->close();
    $delete_stmt->close();
} else {
    // If 'id' is not passed in the URL
    echo "Invalid request. No session ID provided.";
}

?>
