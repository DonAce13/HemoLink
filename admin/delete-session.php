<?php

session_start();

if (isset($_SESSION["user"])) {
    if (($_SESSION["user"]) == "" || $_SESSION['usertype'] != 'a') {
        header("location: ../login.php");
        exit; // Prevent further execution
    }
} else {
    header("location: ../login.php");
    exit; // Prevent further execution
}

if (isset($_GET["id"])) {
    // Import database
    include("../connection.php");
    $id = $_GET["id"];
    
    // Validate the ID (ensure it's an integer)
    if (filter_var($id, FILTER_VALIDATE_INT)) {
        // Retrieve the session title before deletion
        $result = $database->query("SELECT title FROM schedule WHERE scheduleid='$id'");
        $session = $result->fetch_assoc();
        $nameget = $session['title']; // Store session title
        
        // Perform the deletion
        $sql = $database->query("DELETE FROM schedule WHERE scheduleid='$id';");
        
        // Check if the deletion was successful
        if ($sql) {
            $_SESSION['delete_success'] = true; // Set success session variable
            $_SESSION['title'] = $nameget; // Store the session title for the alert
        } else {
            $_SESSION['delete_error'] = "Failed to delete the session."; // Handle the error
        }
    } else {
        $_SESSION['delete_error'] = "Invalid session ID."; // Handle invalid ID
    }
    
    // Redirect back to schedule.php
    header("location: schedule.php");
    exit; // Prevent further execution
}
?>