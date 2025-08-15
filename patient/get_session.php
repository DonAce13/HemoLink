<?php
// Include database connection
include("../connection.php");

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set headers for JSON response
header('Content-Type: application/json');

try {
    // Check if a specific date is provided
    if (isset($_GET['date'])) {
        $selected_date = $_GET['date'];

        // Prepare SQL to fetch sessions for the specific date
        $query = "SELECT 
                    schedule.scheduleid, 
                    schedule.title, 
                    schedule.scheduledate, 
                    schedule.scheduletime, 
                    schedule.available_slots,
                    schedule.nop,
                    doctor.docname
                  FROM schedule 
                  JOIN doctor ON schedule.docid = doctor.docid
                  WHERE schedule.scheduledate = ? 
                  AND schedule.available_slots > 0
                  ORDER BY schedule.scheduletime";

        // Prepare and execute the statement
        $stmt = $database->prepare($query);
        $stmt->bind_param("s", $selected_date);
        $stmt->execute();
        $result = $stmt->get_result();

        // Fetch sessions
        $sessions = [];
        while ($row = $result->fetch_assoc()) {
            $sessions[] = $row;
        }

        // Return JSON response
        echo json_encode($sessions);
    } else {
        // Invalid or missing date
        echo json_encode(['error' => 'No date provided']);
    }
} catch (Exception $e) {
    // Handle any errors
    echo json_encode(['error' => $e->getMessage()]);
}
?>