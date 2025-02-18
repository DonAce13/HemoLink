<?php
// Establish connection to MySQL on localhost (XAMPP)
$database = new mysqli("localhost", "root", "", "sql_database_hemolink");

// Enhanced Connection Error Handling
if ($database->connect_error) {
    // Log connection error
    error_log("Database Connection Failed: " . $database->connect_error, 3, __DIR__ . '/db_connection_errors.log');
    
    // Prevent detailed error exposure
    die("System Error: Unable to establish database connection");
}

// Include security utilities
require_once 'security_utils.php';

// Optional: Set database connection parameters for additional security
$database->set_charset("utf8mb4");  // Use UTF-8 with full Unicode support
?>
