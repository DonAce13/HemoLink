<?php
// Database connection
require_once('connection.php');

// Set timezone
date_default_timezone_set('Asia/Manila');

// Log function for debugging
function log_message($message) {
    $log_file = __DIR__ . '/cron_job.log';
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($log_file, "[$timestamp] $message" . PHP_EOL, FILE_APPEND);
}

try {
    // Start transaction
    $database->begin_transaction();
    
    // Log start of process
    log_message("Starting appointment status update process");
    
    // 1. Set appointments to 'ongoing' if current time matches schedule
    $sql1 = "UPDATE appointment 
             SET status = 'ongoing' 
             WHERE appodate = CURDATE() 
             AND scheduletime <= CURTIME()
             AND status = 'scheduled'";
    
    $database->query($sql1);
    $ongoing_updated = $database->affected_rows;
    
    // 2. Set appointments to 'done' if their time has passed
    $sql2 = "UPDATE appointment 
             SET status = 'done' 
             WHERE (appodate < CURDATE() 
             OR (appodate = CURDATE() AND scheduletime < CURTIME()))
             AND status = 'ongoing'";
    
    $database->query($sql2);
    $done_updated = $database->affected_rows;
    
    // 3. Set appointments to 'canceled' if they haven't been attended
    $sql3 = "UPDATE appointment 
             SET status = 'canceled' 
             WHERE (appodate < CURDATE() 
             OR (appodate = CURDATE() AND scheduletime < CURTIME()))
             AND status = 'scheduled'";
    
    $database->query($sql3);
    $canceled_updated = $database->affected_rows;
    
    // 4. Cleanup old appointments (runs once a week)
    if (date('N') == 1 && date('H') == 0) { // Run once a week on Monday at midnight
        $sql4 = "DELETE FROM appointment 
                 WHERE appodate < DATE_SUB(CURDATE(), INTERVAL 2 YEAR)";
        
        $database->query($sql4);
        $deleted_count = $database->affected_rows;
        log_message("Cleaned up $deleted_count old appointments");
    }
    
    // Commit transaction
    $database->commit();
    
    // Log results
    log_message("Status update complete. Updated: $ongoing_updated to ongoing, $done_updated to done, $canceled_updated to canceled");
    
    // Output for cron job logging
    echo "Appointment status update completed successfully\n";
    echo "Updated to ongoing: $ongoing_updated\n";
    echo "Updated to done: $done_updated\n";
    echo "Updated to canceled: $canceled_updated\n";
    
} catch (Exception $e) {
    // Rollback on error
    if (isset($database)) {
        $database->rollback();
    }
    
    // Log error
    $error_msg = "Error updating appointment statuses: " . $e->getMessage();
    log_message($error_msg);
    
    // Output error for cron job logging
    echo $error_msg . "\n";
    exit(1);
}

// Close connection
if (isset($database)) {
    $database->close();
}
?>
