<?php 

session_start();

// Clear session data
$_SESSION = array();

// If there are cookies related to the session, remove them
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 86400, '/');
}

// Destroy the session
session_destroy();

// Redirect to the login page with the logout success message
header('Location: login.php?logout=success');
exit();

?>
