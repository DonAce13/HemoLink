<?php
session_start();
include("../connection.php");

// Check if user is logged in
if (!isset($_SESSION["user"]) || $_SESSION["user"] == "" || $_SESSION['usertype'] != 'p') {
    header("location: ../login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get user ID from session email
    $useremail = $_SESSION["user"];
    
    // First verify the user exists
    $sqlmain = "SELECT pid, ppassword FROM patient WHERE pemail=? AND is_deleted=0";
    $stmt = $database->prepare($sqlmain);
    $stmt->bind_param("s", $useremail);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        header("Location: settings.php?action=edit&error=6");
        exit;
    }
    
    $userfetch = $result->fetch_assoc();
    $userid = $userfetch["pid"];
    $current_password = trim($_POST["current_password"] ?? '');
    $password = trim($_POST["password"] ?? '');
    $cpassword = trim($_POST["cpassword"] ?? '');
    
    // Validate inputs
    if (empty($current_password)) {
        header("Location: settings.php?action=edit&id=" . $userid . "&error=3");
        exit;
    }
    
    // Verify current password
    if ($current_password !== $userfetch["ppassword"]) {
        header("Location: settings.php?action=edit&id=" . $userid . "&error=5");
        exit;
    }
    
    // If new password is provided, validate it
    if (!empty($password)) {
        if ($password !== $cpassword) {
            header("Location: settings.php?action=edit&id=" . $userid . "&error=2");
        exit;
    }
    
        // Update password
        $sql = "UPDATE patient SET ppassword=? WHERE pid=? AND is_deleted=0";
        $stmt = $database->prepare($sql);
        $stmt->bind_param("si", $password, $userid);
        
        if ($stmt->execute()) {
            header("Location: settings.php?action=edit&id=" . $userid . "&error=4");
            exit;
        } else {
            header("Location: settings.php?action=edit&id=" . $userid . "&error=3");
            exit;
        }
    } else {
        // No new password provided
        header("Location: settings.php?action=edit&id=" . $userid . "&error=3");
                        exit;
    }
} else {
    // Not a POST request
    header("Location: settings.php");
    exit;
}
