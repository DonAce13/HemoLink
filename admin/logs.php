<?php
// Start output buffering at the very beginning
ob_start();
session_start();

//import database
include("../connection.php");

// Handle all API endpoints first
if (isset($_GET['ajax']) || isset($_GET['action'])) {
    header('Content-Type: application/json');
    
    try {
        // --- Ajax request for pending visits ---
        if (isset($_GET['ajax']) && $_GET['ajax'] === 'pending' && isset($_GET['pid'])) {
            $pid = (int)$_GET['pid'];

            $stmt = $database->prepare("SELECT a.appoid, s.title, s.scheduledate, s.scheduletime, a.attended
                                         FROM appointment a
                                         JOIN schedule s ON a.scheduleid = s.scheduleid
                                         WHERE a.pid = ? AND a.is_confirmed=1 AND a.status NOT IN ('canceled','rejected')
                                         ORDER BY s.scheduledate, s.scheduletime");
            $stmt->bind_param("i", $pid);
            $stmt->execute();
            $result = $stmt->get_result();
            
            // Debug logging
            error_log("DEBUG: Fetching appointments for pid=$pid. Found rows: ".$result->num_rows);
            if ($result->num_rows === 0) {
                error_log("DEBUG: No appointments found with conditions: is_confirmed=1 AND status NOT IN ('canceled','rejected')");
            }
            
            $pending = [];
            while ($row = $result->fetch_assoc()) {
                $row['scheduletime'] = date('g:ia', strtotime($row['scheduletime']));
                $pending[] = $row;
            }

            ob_end_clean();
            echo json_encode($pending);
            exit();
        }

        // Mark visit handler
        if (isset($_GET['action']) && $_GET['action'] == 'markvisit_confirmed') {
            $appid = $_GET['appid'];
            $patientid = $_GET['id'];
            
            error_log("DEBUG: Starting markvisit_confirmed for appid=$appid, pid=$patientid");
            
            header('Content-Type: application/json');
            
            try {
                $database->autocommit(FALSE);
                
                // Validate appointment exists and belongs to patient
                $stmt = $database->prepare("SELECT attended, status, pid, is_confirmed, appodate, scheduletime FROM appointment WHERE appoid = ?");
                $stmt->bind_param("i", $appid);
                $stmt->execute();
                $appt = $stmt->get_result()->fetch_assoc();
                
                if (!$appt) {
                    error_log("ERROR: Appointment $appid not found");
                    throw new Exception("Appointment not found");
                } elseif ($appt['pid'] != $patientid) {
                    error_log("ERROR: Appointment $appid belongs to patient {$appt['pid']} but was requested for $patientid");
                    throw new Exception("Appointment does not belong to this patient");
                } elseif ($appt['is_confirmed'] != 1 || in_array(strtolower($appt['status']), ['canceled','rejected'])) {
                    error_log("ERROR: Appointment $appid has invalid status: is_confirmed={$appt['is_confirmed']}, status={$appt['status']}");
                    throw new Exception("Only approved appointments can be marked as visited");
                } elseif ($appt['attended']) {
                    error_log("ERROR: Appointment $appid is already marked as attended");
                    throw new Exception("Already marked as visited");
                } elseif (strtotime($appt['appodate'].' '.$appt['scheduletime']) > time()) {
                    error_log("ERROR: Appointment $appid is in the future");
                    throw new Exception("Cannot mark future appointments");
                }
                
                // Update appointment
                $update = $database->prepare("UPDATE appointment SET attended=1 WHERE appoid=?");
                $update->bind_param("i", $appid);
                $update->execute();
                
                if ($update->affected_rows === 0) {
                    error_log("ERROR: No rows updated for appointment $appid");
                    throw new Exception("No rows updated");
                }
                
                $database->commit();
                error_log("SUCCESS: Marked appointment $appid as visited");
                ob_end_clean();
                echo json_encode(['success' => true, 'message' => 'Marked as visited']);
            } catch (Exception $e) {
                $database->rollback();
                http_response_code(400);
                error_log("ERROR: Failed to mark visit - " . $e->getMessage());
                ob_end_clean();
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            exit();
        }

        // Mark as Not Visited Confirmed Action
        if (isset($_GET['action']) && $_GET['action'] == 'marknotvisited_confirmed' && isset($_GET['appid']) && isset($_GET['id'])) {
            $appid = (int)$_GET['appid'];
            $patientid = (int)$_GET['id'];
            
            error_log("DEBUG: Mark not visited request - appid: $appid, pid: $patientid");
            
            try {
                $database->autocommit(FALSE);
                
                // Validate appointment exists
                $stmt = $database->prepare("SELECT * FROM appointment WHERE appoid = ? AND pid = ?");
                $stmt->bind_param("ii", $appid, $patientid);
                $stmt->execute();
                $appt = $stmt->get_result()->fetch_assoc();
                
                if (!$appt) {
                    error_log("ERROR: Appointment not found - appid: $appid, pid: $patientid");
                    throw new Exception("Appointment not found");
                }
                
                // Check if already marked
                if ($appt['attended'] == -1) {
                    error_log("ERROR: Appointment already marked as not visited - appid: $appid");
                    throw new Exception("Already marked as not visited");
                }
                
                // Update status
                $update = $database->prepare("UPDATE appointment SET attended = -1 WHERE appoid = ?");
                $update->bind_param("i", $appid);
                $update->execute();
                
                if ($update->affected_rows === 0) {
                    error_log("ERROR: No rows updated - appid: $appid");
                    throw new Exception("Failed to update appointment");
                }
                
                $database->commit();
                error_log("SUCCESS: Marked as not visited - appid: $appid");
                echo json_encode(['success' => true, 'message' => 'Marked as not visited']);
            } catch (Exception $e) {
                $database->rollback();
                error_log("ERROR: " . $e->getMessage());
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            exit();
        }

        // Mark as Not Visited Action
        if (isset($_GET['action']) && $_GET['action'] == 'marknotvisited' && isset($_GET['appid']) && isset($_GET['confirm'])) {
            $appid = $_GET['appid'];
            $patientid = $_GET['id'];
            // Directly proceed to confirmation handler
            header("Location: ?action=marknotvisited_confirmed&appid=$appid&id=$patientid");
            exit();
        }
        if (isset($_GET['action']) && $_GET['action'] == 'markvisit' && isset($_GET['appid']) && isset($_GET['confirm'])) {
            $appid = $_GET['appid'];
            $patientid = $_GET['id'];
            // Directly proceed to confirmation handler
            header("Location: ?action=markvisit_confirmed&appid=$appid&id=$patientid");
            exit();
        }
        
    } catch (Exception $e) {
        if (isset($database)) $database->rollback();
        ob_end_clean();
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        exit();
    }
}

// Clear buffer before HTML output
ob_end_clean();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../img/bg01.png">
    <link rel="icon" type="image/png" href="../img/bg01.png">
    <link rel="shortcut icon" type="image/png" href="../img/bg01.png">
    <link rel="stylesheet" href="../css/animations.css">  
    <link rel="stylesheet" href="../css/main.css">  
    <link rel="stylesheet" href="../css/admin.css">
        
    <title>Patients</title>
    <style>
        .popup{
            animation: transitionIn-Y-bottom 0.5s;
        }
        .sub-table{
            animation: transitionIn-Y-bottom 0.5s;
        }
        
        /* SweetAlert Patient Profile Styling */
        .patient-profile-popup {
            width: 600px !important;
            border-radius: 15px !important;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important;
        }

        .patient-profile-container {
            padding: 0 !important;
            margin: 0 !important;
        }

        .patient-profile-container .patient-header {
            background-color: #007bff;
            color: white;
            display: flex;
            align-items: center;
            padding: 20px;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
        }

        .patient-header .patient-avatar {
            margin-right: 20px;
        }

        .patient-header .patient-avatar i {
            font-size: 80px;
            color: white;
        }

        .patient-header .patient-name-id h2 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }

        .patient-header .patient-name-id .patient-id-text {
            margin: 5px 0 0;
            opacity: 0.8;
            font-size: 14px;
        }

        .patient-details-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            padding: 20px;
            background-color: #f8f9fa;
        }

        .patient-detail-item {
            display: flex;
            align-items: center;
            background-color: white;
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            transition: transform 0.3s ease;
            flex-direction: column;
        }

        .patient-detail-item:hover {
            transform: translateY(-5px);
        }

        .patient-detail-item.full-width {
            grid-column: span 2;
        }

        .patient-detail-item i {
            font-size: 30px;
            color: #007bff;
            margin-right: 15px;
            width: 50px;
            text-align: center;
        }

        .patient-detail-item div h3 {
            margin: 0 0 5px;
            font-size: 14px;
            color: #6c757d;
            text-transform: uppercase;
            font-weight: 600;
        }

        .patient-detail-item div p {
            margin: 0;
            font-size: 16px;
            color: #212529;
            font-weight: 500;
        }

        /* Responsive adjustments */
        @media (max-width: 600px) {
            .patient-details-grid {
                grid-template-columns: 1fr;
            }

            .patient-detail-item.full-width {
                grid-column: span 1;
            }
        }
        
        /* SweetAlert Custom Styling */
        .patient-details-popup .swal2-html-container {
            margin: 0 !important;
            padding: 0 !important;
        }
        
        .patient-details-popup .swal2-html-container .swal-patient-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 10px;
        }
        
        .patient-details-popup .swal2-html-container .swal-patient-table tr {
            margin-bottom: 10px;
        }
        
        .patient-details-popup .swal2-html-container .swal-patient-table .label-td {
            font-weight: bold;
            text-align: right;
            padding-right: 15px;
            color: #555;
            width: 40%;
        }
        
        .patient-details-popup .swal2-html-container .swal-patient-table td {
            text-align: left;
            padding: 5px;
        }
        
        .visits-container {
            max-height: 400px;
            overflow-y: auto;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #eee;
            border-radius: 5px;
        }
        .visits-table {
            width: 100%;
            border-collapse: collapse;
        }
        .visits-table th {
            position: sticky;
            top: 0;
            background: white;
            z-index: 10;
        }
        .visits-table tr {
            border-bottom: 1px solid #eee;
        }
        .visits-table td, .visits-table th {
            padding: 8px 12px;
            text-align: left;
        }
    </style>
</head>
<body>
    <?php

    //learn from w3schools.com

    //import database
    include("../connection.php");

    
    ?>
    <div class="container">
        <div class="menu">
            <table class="menu-container" border="0">
                <tr>
                    <td style="padding:10px" colspan="2">
                        <table border="0" class="profile-container">
                            <tr>
                                <td width="30%" style="padding-left:20px" >
                                    <img src="../img/user.png" alt="" width="100%" style="border-radius:50%">
                                </td>
                                <td style="padding:0px;margin:0px;">
                                    <p class="profile-title">Administrator</p>
                                    <p class="profile-subtitle">admin@gmail.com</p>
                                </td>
                            </tr>
                            <tr>
                                 <tr>
                            <td colspan="2">
        <button onclick="confirmLogout()" class="logout-btn btn-primary-soft btn">Log out</button>
    </td>
</tr>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function showVisits(pid, pname) {
    const now = new Date();
    const options = { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric',
        hour: '2-digit', 
        minute: '2-digit',
        hour12: true 
    };
    const timestamp = now.toLocaleString('en-US', options);
    
    Swal.fire({
        title: 'Appointment History: ' + pname,
        html: `<div style="margin-bottom:15px;font-size:14px;color:#666;text-align:center;">
                Report generated: ${timestamp}
               </div>
               <div class="visits-container">
                <table class="visits-table">
                    <thead>
                        <tr>
                            <th>Service</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="visits-content"></tbody>
                </table>
            </div>
            <div style="margin-top:20px;text-align:center;">
                <button onclick="Swal.close()" class="btn-primary-soft btn" style="padding:10px 20px;">
                    Close
                </button>
            </div>`,
        width: '1050px',
        showConfirmButton: false,
        didOpen: () => {
            fetch('logs?ajax=pending&pid=' + pid)
                .then(res => {
                    if (!res.ok) {
                        throw new Error(`HTTP error! status: ${res.status}`);
                    }
                    const contentType = res.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) {
                        return res.text().then(text => {
                            throw new Error('Server returned non-JSON response');
                        });
                    }
                    return res.json();
                })
                .then(data => {
                    let rows = '';
                    if (!data || data.length === 0) {
                        rows = '<tr><td colspan="5" style="padding:12px 0;text-align:center;">No appointments found.</td></tr>';
                    } else {
                        data.forEach(appt => {
                            let statusBadge = '';
                            let statusText = '';
                            let actionButtons = '';

                            if (appt.attended === 1) {
                                statusBadge = '<span class="status-badge" style="background:#28a745;color:white;padding:6px 14px;border-radius:20px;font-weight:bold;">✓ Visited</span>';
                                statusText = 'Already marked';
                                actionButtons = '<td style="color:#666;font-style:italic;">' + statusText + '</td>';
                            } else if (appt.attended === -1) {
                                statusBadge = '<span class="status-badge" style="background:#dc3545;color:white;padding:6px 14px;border-radius:20px;font-weight:bold;">✗ Not Visited</span>';
                                statusText = 'Already marked';
                                actionButtons = '<td style="color:#666;font-style:italic;">' + statusText + '</td>';
                            } else {
                                statusBadge = '<span class="status-badge" style="background:#ffc107;color:black;padding:6px 14px;border-radius:20px;font-weight:bold;">Pending</span>';
                                actionButtons = '<td>'
                                    + '<button class="btn-primary-soft btn button-icon btn-approve" style="margin-right:6px;" onclick="confirmMarkVisit(' + appt.appoid + ', ' + pid + ')">Mark as Visited</button>'
                                    + '<button class="btn-primary-soft btn button-icon btn-reject" onclick="confirmMarkNotVisited(' + appt.appoid + ', ' + pid + ')">Mark as Not Visited</button>'
                                    + '</td>';
                            }

                            rows += `<tr>
                                <td>${appt.title}</td>
                                <td>${appt.scheduledate}</td>
                                <td>${appt.scheduletime}</td>
                                <td>${statusBadge}</td>
                                ${actionButtons}
                            </tr>`;
                        });
                    }
                    document.getElementById('visits-content').innerHTML = rows;
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('visits-content').innerHTML = 
                        `<tr><td colspan="5" style="padding:12px 0;text-align:center;color:red;">Error loading appointments: ${error.message}</td></tr>`;
                });
        }
    });
}

function confirmLogout() {
    Swal.fire({
        title: "Are you sure?",
        text: "Do you really want to log out?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes, log out",
        cancelButtonText: "No, stay logged in",
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "../logout.php";
        }
    });
}
</script>
                            </tr>
                            </tr>
                    </table>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-dashbord" >
                        <a href="index.php" class="non-style-link-menu"><div><p class="menu-text">Dashboard</p></a></div></a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-doctor ">
                        <a href="doctors.php" class="non-style-link-menu "><div><p class="menu-text">Doctors</p></a></div>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-schedule">
                        <a href="schedule.php" class="non-style-link-menu"><div><p class="menu-text">Schedule</p></div></a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-appoinment">
                        <a href="appointment.php" class="non-style-link-menu"><div><p class="menu-text">Appointment</p></a></div>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-patient">
                        <a href="patient.php" class="non-style-link-menu "><div><p class="menu-text">Patients</p></a></div>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-logs">
                        <a href="logs.php" class="non-style-link-menu non-style-link-menu-active">
                            <div style="display:flex;align-items:center;gap:8px;">
                                <span class="menu-icon-svg menu-icon-logs-svg"></span>
                                <p class="menu-text">Logs</p>
                            </div>
                        </a>
                    </td>
                </tr>

            </table>
        </div>
        <div class="dash-body">
            <table border="0" width="100%" style=" border-spacing: 0;margin:0;padding:0;margin-top:25px; ">
                <tr >
                    <!-- <td width="13%">

                    <a href="patient.php" ><button  class="login-btn btn-primary-soft btn btn-icon-back"  style="padding-top:11px;padding-bottom:11px;margin-left:20px;width:125px"><font class="tn-in-text">Back</font></button></a>
                        
                    </td> -->
                    <tr class="date-container">
            <td width="100%">
            <p style="font-size: 14px;color: rgb(119, 119, 119);padding: 0;margin: 0;">
                    Today's Date
                </p>
                <p class="heading-sub12" style="padding: 0; margin: 0;">
                    <?php 
                        date_default_timezone_set('Asia/Manila');
                        $date = date('Y-m-d');
                        echo $date;

                        $patientrow = $database->query("SELECT * FROM patient;");
                        $doctorrow = $database->query("SELECT * FROM doctor;");
                        $appointmentrow = $database->query("SELECT * FROM appointment WHERE appodate >= '$date';");
                        $schedulerow = $database->query("SELECT * FROM schedule WHERE scheduledate = '$date';");
                    ?>
                </p>
            </td>
        </tr>
                    

                </tr>
                <td colspan="2" class="nav-bar" >
                                
                                <form action="patient.php" method="post" class="header-search">
        
                                    <input type="search" name="search" class="input-text header-searchbar" placeholder="Search Patient Name or Email" list="patients">&nbsp;&nbsp;
                                    
                                    <?php
                                        echo '<datalist id="patients">';
                                        $list11 = $database->query("select pname, pemail from patient;");
        
                                        for ($y=0;$y<$list11->num_rows;$y++){
                                            $row00=$list11->fetch_assoc();
                                            $p=$row00["pname"];
                                            $c=$row00["pemail"];
                                            echo "<option value='$p'><br/>";
                                            echo "<option value='$c'><br/>";
                                        };
        
                                    echo ' </datalist>';
                                    ?>
                                    
                               
                                    <input type="Submit" value="Search" class="btn-primary-soft btn button-icon btn-search" style="padding-left: 25px;padding-right: 25px;padding-top: 10px;padding-bottom: 10px;">
                                
                                </form>
                                
                            </td>
                    


                </tr>
               
                
                <tr>
    <td colspan="5" style="padding-top:10px;">
        <div style="margin-left: 45px; margin-bottom: 10px;">
            <strong>GUIDE:</strong>
            <span style="margin-left:12px; color:#007bff;">Reminder</span> <span style="color:#555;">(1 Not Visited)</span>,
            <span style="margin-left:12px; color:#ffc107;">Warning</span> <span style="color:#555;">(2 Not Visited)</span>,
            <span style="margin-left:12px; color:#e74c3c;">Penalty</span> <span style="color:#555;">(3 or more Not Visited)</span>
        </div>
        <p class="heading-main12" style="margin-left: 45px;font-size:18px;color:rgb(49, 49, 49)">All Patients </p>
    </td>
</tr>
                <?php
                    // Pagination setup
                    $resultsPerPage = 10;
                    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                    $page = max(1, $page); // Ensure page is at least 1

                    // Modify search query to support pagination
                    if($_POST){
                        $keyword=$_POST["search"];
                        
                        $sqlmain= "select * from patient where pemail='$keyword' or pname='$keyword' or pname like '$keyword%' or pname like '%$keyword' or pname like '%$keyword%' ";
                    }else{
                        $sqlmain= "select * from patient order by pid desc";
                    }

                    // Count total results
                    $countResult = $database->query($sqlmain);
                    $totalResults = $countResult->num_rows;
                    $totalPages = ceil($totalResults / $resultsPerPage);

                    // Modify query to include LIMIT for pagination
                    $offset = ($page - 1) * $resultsPerPage;
                    $sqlmain .= " LIMIT $offset, $resultsPerPage";

                    $result = $database->query($sqlmain);
                ?>
                  
                <tr>
                   <td colspan="4">
                       <center>
                        <div class="abc scroll">
                        <table width="93%" class="sub-table scrolldown"  style="border-spacing:0;">
                        <thead>
<tr>
    <th class="table-headin">Name</th>
    <th class="table-headin">Visited</th>
    <th class="table-headin">Pending</th>
    <th class="table-headin">Not Visited</th>
    <th class="table-headin">Status</th>
    <th class="table-headin">Actions</th>
</tr>
</thead>
                        <tbody>
                        
                        <?php
if ($result->num_rows == 0) {
    echo '<tr>
        <td colspan="7">
        <br><br><br><br>
        <center>
        <img src="../img/notfound.svg" width="25%" alt="Not Found">
        <br>
        <p class="heading-main12" style="margin-left: 45px;font-size:20px;color:rgb(49, 49, 49)">
            We cannot find anything related to your keywords!
        </p>
        <a class="non-style-link" href="patient.php">
            <button class="login-btn btn-primary-soft btn" style="display: flex;justify-content: center;align-items: center;margin-left:20px;">
                &nbsp; Show all Patients &nbsp;
            </button>
        </a>
        </center>
        <br><br><br><br>
        </td>
    </tr>';
} else {
    while ($row = $result->fetch_assoc()) {
        $pid = $row["pid"];
        $name = $row["pname"];

        // Get visited, pending, not visited counts
        $visitedQuery = $database->query("SELECT COUNT(*) as cnt FROM appointment WHERE pid='$pid' AND attended=1 AND is_confirmed=1 AND status NOT IN ('canceled','rejected')");
        $visitedRow = $visitedQuery->fetch_assoc();
        $visited_count = $visitedRow['cnt'];

        $pendingQuery = $database->query("SELECT COUNT(*) as cnt FROM appointment WHERE pid='$pid' AND attended=0 AND is_confirmed=1 AND status NOT IN ('canceled','rejected')");
        $pendingRow = $pendingQuery->fetch_assoc();
        $pending_count = $pendingRow['cnt'];

        $notVisitedQuery = $database->query("SELECT COUNT(*) as cnt FROM appointment WHERE pid='$pid' AND attended=-1 AND is_confirmed=1 AND status NOT IN ('canceled','rejected')");
        $notVisitedRow = $notVisitedQuery->fetch_assoc();
        $not_visited_count = $notVisitedRow['cnt'];

        // Legend logic
        $legend = '';
        if ($not_visited_count == 1) {
            $legend = '<span style="color:#007bff;">Reminder</span>';
        } elseif ($not_visited_count == 2) {
            $legend = '<span style="color:#ffc107;">Warning</span>';
        } elseif ($not_visited_count >= 3) {
            $legend = '<span style="color:#e74c3c;">Penalty</span>';
        }

        // Output patient row with button
        echo '<tr>
            <td style="text-align: center;">&nbsp;' . htmlspecialchars(substr($name, 0, 35)) . '</td>
            <td style="text-align: center;">&nbsp;' . $visited_count . '</td>
            <td style="text-align: center;">&nbsp;' . $pending_count . '</td>
            <td style="text-align: center;">&nbsp;' . $not_visited_count . '</td>
            <td style="text-align: center;">' . $legend . '</td>
            <td>
                <div style="display:flex;justify-content: center;gap:4px;">
                    <button class="btn-primary-soft btn button-icon btn-edit"
                        style="padding-left:36px;padding-top:10px;padding-bottom:10px;margin-top:10px;background-color:#28a745;color:white;"
                        onclick="showVisits('.$pid.', \'' . htmlspecialchars(substr($name, 0, 35)) . '\')">
                        Visits
                    </button>
                </div>
            </td>
        </tr>';
    }
}
?>

 
                            </tbody>

                        </table>
                        </div>
                        </center>
                   </td> 
                </tr>
                
                <!-- Pagination -->
                <tr>
                    <td colspan="7" style="text-align: center; padding: 20px;">
                        <?php
                        // Pagination links
                        echo '<div class="pagination">';
                        
                        // Previous page link
                        if ($page > 1) {
                            echo '<a href="?page='.($page-1).'" class="btn btn-primary-soft" style="margin-right: 10px;">&laquo; Previous</a>';
                        }
                        
                        // Page numbers
                        for ($i = 1; $i <= $totalPages; $i++) {
                            if ($i == $page) {
                                echo '<span class="btn btn-primary" style="margin: 0 5px; background-color: #007bff; color: white;">'.$i.'</span>';
                            } else {
                                echo '<a href="?page='.$i.'" class="btn btn-primary-soft" style="margin: 0 5px;">'.$i.'</a>';
                            }
                        }
                        
                        // Next page link
                        if ($page < $totalPages) {
                            echo '<a href="?page='.($page+1).'" class="btn btn-primary-soft" style="margin-left: 10px;">Next &raquo;</a>';
                        }
                        
                        echo '</div>';
                        ?>
                    </td>
                </tr>
                </table>
            </div>
        </div>
        <?php 
        if(isset($_GET['action']) && $_GET['action'] == 'visits' && isset($_GET['id'])) {
            $patientid = $_GET['id'];
            $patientQuery = $database->query("SELECT * FROM patient WHERE pid='$patientid'");
            $name = $patientQuery ? $patientQuery->fetch_assoc()['pname'] : 'Unknown Patient';
            // Visited Appointments
            $visitedRows = '';
            $visitedQuery = $database->query("SELECT a.*, s.title, s.scheduledate, s.scheduletime FROM appointment a JOIN schedule s ON a.scheduleid = s.scheduleid WHERE a.pid='$patientid' AND a.attended=1 AND a.is_confirmed=1 AND a.status NOT IN ('canceled','rejected')");
            if ($visitedQuery && $visitedQuery->num_rows > 0) {
                while ($appt = $visitedQuery->fetch_assoc()) {
                    $visitedRows .= '<tr>'
                        . '<td>' . htmlspecialchars($appt['title']) . '</td>'
                        . '<td>' . htmlspecialchars($appt['scheduledate']) . '</td>'
                        . '<td>' . htmlspecialchars($appt['scheduletime']) . '</td>'
                        . '<td>Visited</td>'
                        . '</tr>';
                }
            } 
            // Pending Visits
            $pendingRows = '';
            $pendingQuery = $database->query("SELECT a.*, s.title, s.scheduledate, s.scheduletime FROM appointment a JOIN schedule s ON a.scheduleid = s.scheduleid WHERE a.pid='$patientid' AND a.attended=0 AND a.is_confirmed=1 AND a.status NOT IN ('canceled','rejected')");
            if ($pendingQuery && $pendingQuery->num_rows > 0) {
                while ($pending = $pendingQuery->fetch_assoc()) {
                    $pendingRows .= '<tr>'
                        . '<td>' . htmlspecialchars($pending['title']) . '</td>'
                        . '<td>' . htmlspecialchars($pending['scheduledate']) . '</td>'
                        . '<td>' . htmlspecialchars($pending['scheduletime']) . '</td>'
                        . '<td>'
                        . '<a href=\'?action=markvisit&appid=' . $pending['appoid'] . '&id=' . $patientid . '\' class=\'non-style-link markvisit-btn\'><button class=\'btn-primary-soft btn button-icon btn-approve\' style=\'margin-right:6px;\'>Mark as Visited</button></a>'
                        . '<a href=\'?action=marknotvisited&appid=' . $pending['appoid'] . '&id=' . $patientid . '\' class=\'non-style-link marknotvisited-btn\'><button class=\'btn-primary-soft btn button-icon btn-reject\'>Mark as Not Visited</button></a>'
                        . '</td>'
                        . '</tr>';
                }
            } 
// (Duplicate block removed by Cascade AI. Only the first instance of visited and pending rows logic is kept.)

       
        exit();
    }
    // Mark as Not Visited Action
        if (isset($_GET['action']) && $_GET['action'] == 'marknotvisited' && isset($_GET['appid']) && isset($_GET['confirm'])) {
            $appid = $_GET['appid'];
            $patientid = $_GET['id'];
            // Directly proceed to confirmation handler
            header("Location: ?action=marknotvisited_confirmed&appid=$appid&id=$patientid");
            exit();
        }
        if (isset($_GET['action']) && $_GET['action'] == 'markvisit' && isset($_GET['appid']) && isset($_GET['confirm'])) {
            $appid = $_GET['appid'];
            $patientid = $_GET['id'];
            // Directly proceed to confirmation handler
            header("Location: ?action=markvisit_confirmed&appid=$appid&id=$patientid");
            exit();
        }
        // Mark as Not Visited Confirmed Action
        if (isset($_GET['action']) && $_GET['action'] == 'marknotvisited_confirmed' && isset($_GET['appid']) && isset($_GET['id'])) {
            $appid = $_GET['appid'];
            $patientid = $_GET['id'];
            
            error_log("DEBUG: Starting marknotvisited_confirmed for appid=$appid, pid=$patientid");
            
            header('Content-Type: application/json');
            
            try {
                $database->autocommit(FALSE);
                
                // Log pre-validation state
                error_log("DEBUG: Validating appointment $appid for patient $patientid");
                
                $stmt = $database->prepare("SELECT attended, status, pid, is_confirmed, appodate, scheduletime FROM appointment WHERE appoid = ?");
                $stmt->bind_param("i", $appid);
                $stmt->execute();
                $appt = $stmt->get_result()->fetch_assoc();
                
                if (!$appt) {
                    error_log("ERROR: Appointment $appid not found");
                    throw new Exception("Appointment not found");
                } elseif ($appt['pid'] != $patientid) {
                    error_log("ERROR: Appointment $appid belongs to patient {$appt['pid']} but was requested for $patientid");
                    throw new Exception("Appointment does not belong to this patient");
                } elseif ($appt['is_confirmed'] != 1 || in_array(strtolower($appt['status']), ['canceled','rejected'])) {
                    error_log("ERROR: Appointment $appid has invalid status: is_confirmed={$appt['is_confirmed']}, status={$appt['status']}");
                    throw new Exception("Only approved appointments can be marked as not visited");
                } elseif ($appt['attended'] == -1) {
                    error_log("ERROR: Appointment $appid is already marked as not visited");
                    throw new Exception("Already marked as not visited");
                } elseif (strtotime($appt['appodate'].' '.$appt['scheduletime']) > time()) {
                    error_log("ERROR: Appointment $appid is in the future");
                    throw new Exception("Cannot mark future appointments");
                }
                
                error_log("DEBUG: Updating appointment $appid to attended=-1");
                
                $update = $database->prepare("UPDATE appointment SET attended=-1 WHERE appoid=?");
                $update->bind_param("i", $appid);
                $update->execute();
                
                if ($update->affected_rows === 0) {
                    error_log("ERROR: No rows updated for appointment $appid");
                    throw new Exception("No rows updated");
                }
                
                $database->commit();
                error_log("SUCCESS: Marked appointment $appid as not visited");
                ob_end_clean();
                echo json_encode(['success' => true, 'message' => 'Marked as not visited']);
            } catch (Exception $e) {
                $database->rollback();
                http_response_code(400);
                error_log("ERROR: " . $e->getMessage());
                ob_end_clean();
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            exit();
        }
        // Mark as Visited Confirmed Action
        if (isset($_GET['action']) && $_GET['action'] == 'markvisit_confirmed' && isset($_GET['appid']) && isset($_GET['id'])) {
            $appid = $_GET['appid'];
            $patientid = $_GET['id'];
            
            error_log("DEBUG: Starting markvisit_confirmed for appid=$appid, pid=$patientid");
            
            header('Content-Type: application/json');
            
            try {
                $database->autocommit(FALSE);
                
                // Log pre-validation state
                error_log("DEBUG: Validating appointment $appid for patient $patientid");
                
                $stmt = $database->prepare("SELECT attended, status, pid, is_confirmed, appodate, scheduletime FROM appointment WHERE appoid = ?");
                $stmt->bind_param("i", $appid);
                $stmt->execute();
                $appt = $stmt->get_result()->fetch_assoc();
                
                if (!$appt) {
                    error_log("ERROR: Appointment $appid not found");
                    throw new Exception("Appointment not found");
                } elseif ($appt['pid'] != $patientid) {
                    error_log("ERROR: Appointment $appid belongs to patient {$appt['pid']} but was requested for $patientid");
                    throw new Exception("Appointment does not belong to this patient");
                } elseif ($appt['is_confirmed'] != 1 || in_array(strtolower($appt['status']), ['canceled','rejected'])) {
                    error_log("ERROR: Appointment $appid has invalid status: is_confirmed={$appt['is_confirmed']}, status={$appt['status']}");
                    throw new Exception("Only approved appointments can be marked as visited");
                } elseif ($appt['attended']) {
                    error_log("ERROR: Appointment $appid is already marked as attended");
                    throw new Exception("Already marked as visited");
                } elseif (strtotime($appt['appodate'].' '.$appt['scheduletime']) > time()) {
                    error_log("ERROR: Appointment $appid is in the future");
                    throw new Exception("Cannot mark future appointments");
                }
                
                error_log("DEBUG: Updating appointment $appid to attended=1");
                
                $update = $database->prepare("UPDATE appointment SET attended=1 WHERE appoid=?");
                $update->bind_param("i", $appid);
                $update->execute();
                
                if ($update->affected_rows === 0) {
                    error_log("ERROR: No rows updated for appointment $appid");
                    throw new Exception("No rows updated");
                }
                
                $database->commit();
                error_log("SUCCESS: Marked appointment $appid as visited");
                ob_end_clean();
                echo json_encode(['success' => true, 'message' => 'Marked as visited']);
            } catch (Exception $e) {
                $database->rollback();
                http_response_code(400);
                error_log("ERROR: Failed to mark visit - " . $e->getMessage());
                ob_end_clean();
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            exit();
        }

        

    

?>
</div>

</body>
</html>
        </script>

<script>
function confirmMarkVisit(appoid, pid) {
    Swal.fire({
        title: 'Confirm Visit',
        text: 'Are you sure you want to mark this appointment as visited?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, mark as visited',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`logs?action=markvisit_confirmed&appid=${appoid}&id=${pid}`)
                .then(handleApiResponse)
                .then((data) => {
                    Swal.fire({
                        title: 'Success',
                        html: '<div style="display:flex;flex-direction:column;align-items:center">'
                            + '<i class="fas fa-check-circle" style="font-size:60px;color:#28a745;margin-bottom:20px"></i>'
                            + '<p>' + data.message + '</p>'
                            + '</div>',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        const pname = document.querySelector(`[onclick*="showVisits(${pid}"]`).textContent.trim();
                        showVisits(pid, pname);
                        location.reload();
                    });
                })
                .catch(handleApiError);
        }
    });
}

function confirmMarkNotVisited(appid, pid) {
    Swal.fire({
        title: 'Confirm Mark as Not Visited',
        text: "Are you sure you want to mark this appointment as not visited?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, mark as not visited'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`logs?action=marknotvisited_confirmed&appid=${appid}&id=${pid}`)
                .then(res => {
                    if (!res.ok) {
                        return res.text().then(text => {
                            throw new Error(text || `HTTP error! status: ${res.status}`);
                        });
                    }
                    const contentType = res.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) {
                        return res.text().then(text => {
                            throw new Error('Server returned non-JSON response');
                        });
                    }
                    return res.json();
                })
                .then(data => {
                    if (data.success) {
                        Swal.fire(
                            'Marked!',
                            'Appointment has been marked as not visited.',
                            'success'
                        ).then(() => {
                            location.reload();
                        });
                    } else {
                        throw new Error(data.message || 'Failed to mark as not visited');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire(
                        'Error!',
                        error.message || 'Failed to mark appointment as not visited',
                        'error'
                    );
                });
        }
    });
}

function handleApiResponse(res) {
    if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);
    return res.json().then(data => {
        if (!data.success) throw new Error(data.message);
        return data;
    });
}

function handleApiError(error) {
    console.error('API Error:', error);
    
    // Try to get the raw response if available
    let details = error.message;
    if (error.response) {
        error.response.text().then(text => {
            details += `\n\nRaw response: ${text.substring(0, 200)}`;
            Swal.fire('Error', details, 'error');
        });
        return;
    }
    
    Swal.fire('Error', details, 'error');
}
</script>
</html>