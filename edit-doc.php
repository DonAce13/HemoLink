<?php
session_start();
include("../connection.php");

$id = $_POST['id00'];
$oldemail = $_POST['oldemail'];
$email = $_POST['email'];
$name = $_POST['name'];
$doctel = $_POST['Tele'];
$docnic = $_POST['nic'];
$specialties = $_POST['spec'];
$current_password = trim($_POST['password']);
$new_password = trim($_POST['newpassword']);
$confirm_password = trim($_POST['cpassword']);

// Fetch current password from DB
$sql = "SELECT docpassword FROM doctor WHERE docid='$id'";
$result = $database->query($sql);
$row = $result->fetch_assoc();
$db_password = $row['docpassword'];

// 1. Always check current password
if ($current_password !== $db_password) {
    header("Location: settings.php?action=edit&id=$id&error=1"); // Incorrect current password
    exit();
}

// 2. If user is trying to change password (either new or confirm is filled)
if (!empty($new_password) || !empty($confirm_password)) {
    // Both must be filled and match
    if (empty($new_password) || empty($confirm_password) || $new_password !== $confirm_password) {
        header("Location: settings.php?action=edit&id=$id&error=2"); // Passwords do not match
        exit();
    }
    // If all three are the same (current, new, confirm), allow update (user wants to keep the same password)
    $update_password = ", docpassword='$new_password'";
} else {
    // No password change
    $update_password = "";
}

// 3. Update other fields
$sql_update = "UPDATE doctor SET docemail='$email', docname='$name', doctel='$doctel', docnic='$docnic', specialties='$specialties' $update_password WHERE docid='$id'";
$database->query($sql_update);

header("Location: settings.php?action=edit&id=$id&error=4"); // Success
exit();
?> 