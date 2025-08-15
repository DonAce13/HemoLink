<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set content type to JSON
header('Content-Type: application/json');

// Absolute path to connection file
$connection_path = __DIR__ . '/connection.php';

// Check if connection file exists
if (!file_exists($connection_path)) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Database connection file not found',
        'path' => $connection_path
    ]);
    exit;
}

// Include database connection
include($connection_path);

// Validate database connection
if (!$database) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Failed to establish database connection',
        'mysqli_error' => mysqli_connect_error()
    ]);
    exit;
}

// Validate and sanitize input
$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');
$month = isset($_GET['month']) ? intval($_GET['month']) : date('m');

// Construct date range
$start_date = date("Y-m-d", strtotime("$year-$month-01"));
$end_date = date("Y-m-d", strtotime("+1 month", strtotime($start_date)) - 1);

// Query to fetch available sessions for the month
$sqlSessions = "SELECT 
    schedule.scheduledate, 
    COUNT(schedule.scheduleid) as session_count,
    SUM(schedule.available_slots) as total_slots,
    GROUP_CONCAT(CONCAT(
        schedule.scheduleid, '|', 
        schedule.title, '|', 
        doctor.docname, '|', 
        schedule.scheduletime, '|', 
        schedule.available_slots, '|', 
        schedule.nop
    ) SEPARATOR ';;') as session_details
FROM 
    schedule
JOIN 
    doctor ON schedule.docid = doctor.docid
WHERE 
    schedule.scheduledate BETWEEN ? AND ?
    AND schedule.available_slots > 0
GROUP BY 
    schedule.scheduledate
ORDER BY 
    schedule.scheduledate";

// Prepare and execute query with error handling
$stmt = $database->prepare($sqlSessions);
if (!$stmt) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Failed to prepare SQL statement',
        'mysqli_error' => $database->error
    ]);
    exit;
}

$stmt->bind_param("ss", $start_date, $end_date);
$stmt->execute();

if ($stmt->errno) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Failed to execute SQL statement',
        'mysqli_error' => $stmt->error
    ]);
    exit;
}

$result = $stmt->get_result();
if (!$result) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Failed to get result set',
        'mysqli_error' => $database->error
    ]);
    exit;
}

$availableSessions = [];
while ($row = $result->fetch_assoc()) {
    $availableSessions[$row['scheduledate']] = [
        'session_count' => $row['session_count'],
        'total_slots' => $row['total_slots'],
        'session_details' => $row['session_details']
    ];
}

// Return empty array if no sessions found, not an error
echo json_encode($availableSessions);
exit;
?>