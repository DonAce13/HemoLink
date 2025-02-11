<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'sql_database_hemolink');

// Twilio Configuration
define('TWILIO_SID', 'ACe042a0f5dea2cb97ac2c45b1cbef4211');
define('TWILIO_AUTH_TOKEN', '162f2dcca26f8207e4481aff9e29af1c');
define('TWILIO_VERIFY_SID', 'VAd879fde2196ca8355bb3da60bd1c960c');
define('TWILIO_PHONE_NUMBER', '+18506000203');

// Additional Security Settings
ini_set('display_errors', 1);
error_reporting(E_ALL);
?>