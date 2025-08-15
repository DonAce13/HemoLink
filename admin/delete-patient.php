<?php
session_start();
include("../connection.php");

// Enhanced logging function
function logDeletionEvent($patient_id, $status, $patient_name) {
    $log_dir = "../logs";
    if (!file_exists($log_dir)) {
        mkdir($log_dir, 0755, true);
    }
    $log_file = $log_dir . "/patient_deletion.log";
    $timestamp = date('Y-m-d H:i:s');
    $log_message = "[{$timestamp}] Patient {$patient_id} - {$patient_name} - {$status}\n";
    file_put_contents($log_file, $log_message, FILE_APPEND);
}

if(isset($_SESSION["user"]) && $_SESSION['usertype'] == 'a') {
    if(isset($_GET['id'])) {
        $patient_id = $_GET['id'];

        // Verify patient exists before deletion
        $check_patient = $database->prepare("SELECT * FROM patient WHERE pid = ?");
        $check_patient->bind_param("s", $patient_id);
        $check_patient->execute();
        $result = $check_patient->get_result();

        if($result->num_rows == 0) {
            $_SESSION['delete_error'] = "Patient not found.";
            header("location: patient.php");
            exit();
        }

        $patient_details = $result->fetch_assoc();
        $patient_name = $patient_details['pname'];

        $database->begin_transaction();

        try {
            // Count related records before deletion
            $appointment_count = $database->prepare("SELECT COUNT(*) as count FROM appointment WHERE pid = ?");
            $appointment_count->bind_param("s", $patient_id);
            $appointment_count->execute();
            $count_result = $appointment_count->get_result()->fetch_assoc();
            $related_appointments = $count_result['count'];

            // Delete related appointments
            $delete_appointments = $database->prepare("DELETE FROM appointment WHERE pid = ?");
            $delete_appointments->bind_param("s", $patient_id);
            $delete_appointments->execute();

            // Delete patient record
            $delete_patient = $database->prepare("DELETE FROM patient WHERE pid = ?");
            $delete_patient->bind_param("s", $patient_id);
            $delete_patient->execute();

            $database->commit();

            // Log successful deletion with details
            logDeletionEvent($patient_id, "SUCCESS - Related Appointments: {$related_appointments}", $patient_name);

            $_SESSION['delete_success'] = true;
            $_SESSION['title'] = "Patient Record for {$patient_name}";
            header("location: patient.php");
            exit();

        } catch (Exception $e) {
            $database->rollback();
            
            // Log failed deletion
            logDeletionEvent($patient_id, "FAILED - Error: " . $e->getMessage(), $patient_name);

            $_SESSION['delete_error'] = "Error deleting patient record: " . $e->getMessage();
            header("location: patient.php");
            exit();
        }
    }
}
header("location: ../login.php");
exit();
?>