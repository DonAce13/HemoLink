<?php
// Establish connection to MySQL on localhost
$database = new mysqli("localhost", "root", "", "hemolink_database");

// Check if connection is successful
if ($database->connect_error) {
    die("Connection failed: " . $database->connect_error);
}
?>
