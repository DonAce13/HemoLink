<?php
// Establish connection to MySQL on localhost (XAMPP)
$database = new mysqli("localhost", "u667890873_Ace", "BarkForMeDog011303", "u667890873_hemolink_data");
    // Check if connection is successful
    if ($database->connect_error) {
        die("Connection failed: " . $database->connect_error);
    }
?>
