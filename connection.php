<?php
// Establish connection to MySQL on localhost (XAMPP)
$database = new mysqli("localhost", "root", "", "hemolink_database");

// Check if the connection is successful
if ($database->connect_error) {
    die("Connection failed: " . $database->connect_error);
}
?>
