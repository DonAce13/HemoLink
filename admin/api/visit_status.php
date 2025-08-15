<?php
// Start output buffering and clean any existing output
while (ob_get_level()) ob_end_clean();
ob_start();

// Suppress warnings for require_once - use correct path to project root
@require_once(__DIR__.'/../../connection.php');

// Set headers
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

$response = ['success' => false, 'message' => 'Invalid request'];

try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'], $_GET['appid'], $_GET['id'])) {
        if (!isset($database) || !($database instanceof mysqli)) {
            throw new Exception('Database connection failed');
        }

        $database->autocommit(FALSE);
        $appid = (int)$_GET['appid'];
        $patientid = (int)$_GET['id'];

        // Validate appointment
        $stmt = $database->prepare("SELECT * FROM appointment WHERE appoid = ? AND pid = ?");
        $stmt->bind_param("ii", $appid, $patientid);
        $stmt->execute();
        
        if (!$stmt->get_result()->num_rows) {
            throw new Exception("Appointment not found");
        }

        // Determine status to set
        $status = ($_GET['action'] === 'markvisit_confirmed') ? 1 : -1;
        
        $update = $database->prepare("UPDATE appointment SET attended=? WHERE appoid=?");
        $update->bind_param("ii", $status, $appid);
        $update->execute();
        
        $database->commit();
        $response = ['success' => true, 'message' => 'Status updated successfully'];
    }
} catch (Exception $e) {
    if (isset($database)) $database->rollback();
    http_response_code(400);
    $response['message'] = $e->getMessage();
}

// Clean all output and send JSON
ob_end_clean();
echo json_encode($response);
exit();
