<?php
session_start();
require_once('../connection.php');

// Check if user is logged in
if (!isset($_SESSION['user']) || $_SESSION['usertype'] != 'p') {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['error' => 'Not authorized']);
    exit();
}

// Get date range parameters
$startDate = isset($_GET['start']) ? $_GET['start'] : '';
$endDate = isset($_GET['end']) ? $_GET['end'] : '';

if (empty($startDate) || empty($endDate)) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['error' => 'Missing date parameters']);
    exit();
}

try {
    // Query to get available sessions for the date range
    $sql = "SELECT 
                s.scheduledate as date,
                s.scheduleid,
                s.title,
                d.docname,
                s.scheduletime as time,
                s.nop as total_slots,
                (s.nop - IFNULL((
                    SELECT COUNT(*) 
                    FROM appointment a 
                    WHERE a.scheduleid = s.scheduleid 
                    AND a.status = 'Approved'
                ), 0)) as available_slots
            FROM 
                schedule s
            JOIN 
                doctor d ON s.docid = d.docid
            WHERE 
                s.scheduledate BETWEEN ? AND ?
                AND s.scheduledate >= CURDATE()
            ORDER BY 
                s.scheduledate, s.scheduletime";

    $stmt = $database->prepare($sql);
    $stmt->bind_param('ss', $startDate, $endDate);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $sessions = [];
    while ($row = $result->fetch_assoc()) {
        $date = $row['date'];
        if (!isset($sessions[$date])) {
            $sessions[$date] = [];
        }
        
        // Only include sessions with available slots
        if ($row['available_slots'] > 0) {
            $sessions[$date][] = [
                'scheduleid' => $row['scheduleid'],
                'title' => $row['title'],
                'docname' => $row['docname'],
                'time' => $row['time'],
                'available_slots' => (int)$row['available_slots'],
                'total_slots' => (int)$row['total_slots']
            ];
        }
    }
    
    header('Content-Type: application/json');
    echo json_encode($sessions);
    
} catch (Exception $e) {
    error_log("Error in get_month_sessions.php: " . $e->getMessage());
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['error' => 'Failed to fetch sessions. Please try again.']);
}
?>