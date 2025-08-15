<?php
// Include necessary files and initiate database connection

// Start session to verify user permissions
session_start();
if(isset($_SESSION["user"])) {
    if ($_SESSION["user"] == "" || $_SESSION['usertype'] != 'a') {
        header("location: ../login.php");
        exit;
    }
} else {
    header("location: ../login.php");
    exit;
}

// Include the database connection
include("../connection.php");

// --- CSV Export with Logo and Dashboard Stats ---
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    // Path to logo image
    $logoPath = '../img/bg01.png';
    $logoData = '';
    if (file_exists($logoPath)) {
        $logoType = pathinfo($logoPath, PATHINFO_EXTENSION);
        $logoData = 'data:image/' . $logoType . ';base64,' . base64_encode(file_get_contents($logoPath));
    }
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=mabayuan_dashboard_export.csv');
    $output = fopen('php://output', 'w');
    if ($logoData) {
        fputcsv($output, ["Logo", $logoData], ',', '"', '\\');
    }
    fputcsv($output, ["Mabayuan Health Center"], ',', '"', '\\');
    fputcsv($output, [], ',', '"', '\\');
    // Filter info
    $filterType = isset($_GET['filter']) ? $_GET['filter'] : 'year';
    $filterLabel = 'This Year';
    switch($filterType) {
        case 'month': $filterLabel = 'This Month'; break;
        case 'week': $filterLabel = 'This Week'; break;
        case 'day': $filterLabel = 'Today'; break;
    }
    date_default_timezone_set('Asia/Manila');
    $filterDate = date('F j, Y g:i A');
    fputcsv($output, ["Filtered By", $filterLabel], ',', '"', '\\');
    fputcsv($output, ["Filtered Date", $filterDate], ',', '"', '\\');
    fputcsv($output, [], ',', '"', '\\');
    // Dashboard stats
    $totalPatients = $database->query("SELECT COUNT(*) as total FROM patient")->fetch_assoc()['total'];
    $totalAppointments = $database->query("SELECT COUNT(*) as total FROM appointment WHERE status = 'Approved'")->fetch_assoc()['total'];
    $totalDoctors = $database->query("SELECT COUNT(*) as total FROM doctor")->fetch_assoc()['total'];
    $totalSchedules = $database->query("SELECT COUNT(*) as total FROM schedule WHERE deleted_at IS NULL")->fetch_assoc()['total'];
    $pwdCount = $database->query("SELECT COUNT(*) as count FROM patient WHERE patient_category LIKE '%PWD%'")->fetch_assoc()['count'];
    $seniorCount = $database->query("SELECT COUNT(*) as count FROM patient WHERE patient_category LIKE '%SENIOR CITIZEN%'")->fetch_assoc()['count'];
    $ipCount = $database->query("SELECT COUNT(*) as count FROM patient WHERE patient_category LIKE '%IP%'")->fetch_assoc()['count'];
    $stats = [
        ["Total Patients", $totalPatients],
        ["Total Appointments", $totalAppointments],
        ["Total Doctors", $totalDoctors],
        ["Active Schedules", $totalSchedules],
        ["PWD Patients", $pwdCount],
        ["Senior Citizens", $seniorCount],
        ["Indigenous People", $ipCount],
    ];
    fputcsv($output, ["Statistic", "Value"], ',', '"', '\\');
    foreach ($stats as $row) {
        fputcsv($output, $row, ',', '"', '\\');
    }
    fputcsv($output, [], ',', '"', '\\');
    // Appointment status table
    $appointmentFilter = '';
    switch($filterType) {
        case 'month': $appointmentFilter = "AND MONTH(appodate) = MONTH('$filterDate') AND YEAR(appodate) = YEAR('$filterDate')"; break;
        case 'week': $appointmentFilter = "AND WEEK(appodate) = WEEK('$filterDate') AND YEAR(appodate) = YEAR('$filterDate')"; break;
        case 'day': $appointmentFilter = "AND DATE(appodate) = DATE('$filterDate')"; break;
        case 'year': default: $appointmentFilter = "AND YEAR(appodate) = YEAR('$filterDate')"; break;
    }
    // Scheduled/Done
    $statusTableQuery = "SELECT status, COUNT(*) as count FROM appointment WHERE (status = 'scheduled' OR status = 'done') $appointmentFilter GROUP BY status";
    $statusTableResult = $database->query($statusTableQuery);
    $statusCounts = ['scheduled' => 0, 'done' => 0];
    while($row = $statusTableResult->fetch_assoc()) {
        $statusCounts[$row['status']] = $row['count'];
    }
    fputcsv($output, ["Appointment Status (Filtered)"], ',', '"', '\\');
    fputcsv($output, ["Status", "Count"], ',', '"', '\\');
    fputcsv($output, ["Scheduled", $statusCounts['scheduled']], ',', '"', '\\');
    fputcsv($output, ["Done", $statusCounts['done']], ',', '"', '\\');
    fputcsv($output, [], ',', '"', '\\');
    // Approved/Pending/Declined (is_confirmed)
    $confirmationQuery = "SELECT is_confirmed, COUNT(*) as count FROM appointment WHERE 1=1 $appointmentFilter GROUP BY is_confirmed ORDER BY is_confirmed DESC";
    $confirmationResult = $database->query($confirmationQuery);
    $confirmationCounts = ['approved' => 0, 'pending' => 0, 'declined' => 0];
    while($row = $confirmationResult->fetch_assoc()) {
        if ($row['is_confirmed'] == 1) $confirmationCounts['approved'] = $row['count'];
        elseif ($row['is_confirmed'] == 0) $confirmationCounts['pending'] = $row['count'];
        elseif ($row['is_confirmed'] == -1) $confirmationCounts['declined'] = $row['count'];
    }
    fputcsv($output, ["Appointment Confirmation Status (Filtered)"], ',', '"', '\\');
    fputcsv($output, ["Status", "Count"], ',', '"', '\\');
    fputcsv($output, ["Approved", $confirmationCounts['approved']], ',', '"', '\\');
    fputcsv($output, ["Pending", $confirmationCounts['pending']], ',', '"', '\\');
    fputcsv($output, ["Declined", $confirmationCounts['declined']], ',', '"', '\\');
    fclose($output);
    exit();
}

// --- PDF Export with Logo and Dashboard Stats ---
if (isset($_GET['export']) && $_GET['export'] === 'pdf') {
    require_once('../vendor/autoload.php');
    
    // Create new PDF instance
    $mpdf = new \Mpdf\Mpdf([
        'mode' => 'utf-8',
        'format' => 'A4',
        'margin_left' => 10,
        'margin_right' => 10,
        'margin_top' => 30,
        'margin_bottom' => 20,
        'margin_header' => 10,
        'margin_footer' => 10
    ]);
    
    // Add logo to header
    $logoPath = '../img/bg01.png';
    $logoHtml = '';
    if (file_exists($logoPath)) {
        $logoHtml = '<div style="text-align: center;"><img src="' . $logoPath . '" style="height: 50px;"></div>';
    }
    
    $mpdf->SetHTMLHeader($logoHtml . 
        '<div style="text-align: center; font-size: 16px; font-weight: bold; color: #2d6a4f; margin-top: 5px;">
            Mabayuan Health Center - Admin Dashboard Report
        </div>'
    );
    
    // Get current filter type
    $filterType = isset($_GET['filter']) ? $_GET['filter'] : 'year';
    $filterLabel = 'This Year';
    switch($filterType) {
        case 'month': $filterLabel = 'This Month'; break;
        case 'week': $filterLabel = 'This Week'; break;
        case 'day': $filterLabel = 'Today'; break;
    }
    
    // Generate HTML content
    $html = '<h1 style="text-align: center; color: #2d6a4f;">Admin Dashboard Report</h1>';
    date_default_timezone_set('Asia/Manila');
    $html .= '<p style="text-align: center;">Report generated on: ' . date('F j, Y g:i A') . '</p>';
    $html .= '<p style="text-align: center;">Filter: ' . $filterLabel . '</p>';
    
    // Include all statistics and charts
    ob_start();

    global $database;

    // Set up appointment filter based on current view
    $appointmentFilter = '';
    switch($filterType) {
        case 'year':
            $appointmentFilter = " AND YEAR(date) = YEAR(CURDATE())";
            break;
        case 'month':
            $appointmentFilter = " AND YEAR(date) = YEAR(CURDATE()) AND MONTH(date) = MONTH(CURDATE())";
            break;
        case 'week':
            $appointmentFilter = " AND YEARWEEK(date, 1) = YEARWEEK(CURDATE(), 1)";
            break;
        case 'day':
            $appointmentFilter = " AND date = CURDATE()";
            break;
    }

    // Include the PDF content template
    include('pdf_content.php');

    $html .= ob_get_clean();
    
    // Output PDF
    $mpdf->WriteHTML($html);
    $mpdf->Output('mabayuan_dashboard_report.pdf', 'D');
    exit();
}

// Default filter to current year
$filterType = isset($_GET['filter']) ? $_GET['filter'] : 'year';
$currentDate = date('Y-m-d');

// Function to get filter condition
function getFilterCondition($filterType, $currentDate) {
    switch($filterType) {
        case 'month':
            return "AND MONTH(appodate) = MONTH('$currentDate') AND YEAR(appodate) = YEAR('$currentDate')";
        case 'week':
            return "AND WEEK(appodate) = WEEK('$currentDate') AND YEAR(appodate) = YEAR('$currentDate')";
        case 'day':
            return "AND DATE(appodate) = DATE('$currentDate')";
        case 'year':
        default:
            return "AND YEAR(appodate) = YEAR('$currentDate')";
    }
}

// Fetch data for schedule status
$statusQuery = "";
$currentDateTime = date('Y-m-d H:i:s');
switch($filterType) {
    case 'year':
        $statusQuery = "SELECT 
            CASE 
                WHEN deleted_at IS NULL AND scheduledate < DATE('$currentDate') THEN 'Passed Sessions'
                WHEN deleted_at IS NULL AND scheduledate >= DATE('$currentDate') THEN 'Active Schedules'
                ELSE 'Inactive Schedules'
            END as status, 
            COUNT(*) as count 
        FROM schedule 
        WHERE YEAR(scheduledate) = YEAR('$currentDate')
        GROUP BY status";
        break;
    case 'month':
        $statusQuery = "SELECT 
            CASE 
                WHEN deleted_at IS NULL AND scheduledate < DATE('$currentDate') THEN 'Passed Sessions'
                WHEN deleted_at IS NULL AND scheduledate >= DATE('$currentDate') THEN 'Active Schedules'
                ELSE 'Inactive Schedules'
            END as status, 
            COUNT(*) as count 
        FROM schedule 
        WHERE YEAR(scheduledate) = YEAR('$currentDate') 
        AND MONTH(scheduledate) = MONTH('$currentDate')
        GROUP BY status";
        break;
    case 'week':
        $statusQuery = "SELECT 
            CASE 
                WHEN deleted_at IS NULL AND scheduledate < DATE('$currentDate') THEN 'Passed Sessions'
                WHEN deleted_at IS NULL AND scheduledate >= DATE('$currentDate') THEN 'Active Schedules'
                ELSE 'Inactive Schedules'
            END as status, 
            COUNT(*) as count 
        FROM schedule 
        WHERE YEAR(scheduledate) = YEAR('$currentDate') 
        AND WEEK(scheduledate) = WEEK('$currentDate')
        GROUP BY status";
        break;
    case 'day':
        $statusQuery = "SELECT 
            CASE 
                WHEN deleted_at IS NULL AND scheduledate < DATE('$currentDate') THEN 'Passed Sessions'
                WHEN deleted_at IS NULL AND scheduledate >= DATE('$currentDate') THEN 'Active Schedules'
                ELSE 'Inactive Schedules'
            END as status, 
            COUNT(*) as count 
        FROM schedule 
        WHERE DATE(scheduledate) = DATE('$currentDate')
        GROUP BY status";
        break;
}

$statusResult = $database->query($statusQuery);
$statusData = [];
while($row = $statusResult->fetch_assoc()) {
    $statusData[] = $row;
}

// Fetch data for age groups
$filterCondition = "";
switch($filterType) {
    case 'year':
        $filterCondition = "AND YEAR(created_at) = YEAR('$currentDate')";
        break;
    case 'month':
        $filterCondition = "AND YEAR(created_at) = YEAR('$currentDate') AND MONTH(created_at) = MONTH('$currentDate')";
        break;
    case 'week':
        $filterCondition = "AND YEAR(created_at) = YEAR('$currentDate') AND WEEK(created_at) = WEEK('$currentDate')";
        break;
    case 'day':
        $filterCondition = "AND DATE(created_at) = DATE('$currentDate')";
        break;
    default:
        $filterCondition = "";
}

$ageGroupQuery = "SELECT 
    CASE 
        WHEN TIMESTAMPDIFF(YEAR, pdob, CURDATE()) BETWEEN 18 AND 30 THEN '18-30'
        WHEN TIMESTAMPDIFF(YEAR, pdob, CURDATE()) BETWEEN 31 AND 60 THEN '31-60'
        WHEN TIMESTAMPDIFF(YEAR, pdob, CURDATE()) BETWEEN 61 AND 90 THEN '61-90'
    END AS age_group,
    COUNT(*) as count 
FROM patient 
WHERE pdob IS NOT NULL 
    $filterCondition
    AND TIMESTAMPDIFF(YEAR, pdob, CURDATE()) BETWEEN 18 AND 90
GROUP BY age_group";
$ageGroupResult = $database->query($ageGroupQuery);
$ageGroupData = [];
while($row = $ageGroupResult->fetch_assoc()) {
    $ageGroupData[] = $row;
}

// Fetch booking approval/rejection statistics
$bookingStatusQuery = "";
switch($filterType) {
    case 'year':
        $bookingStatusQuery = "SELECT 
            is_confirmed, 
            COUNT(*) as count 
        FROM appointment 
        WHERE YEAR(appodate) = YEAR('$currentDate')
        GROUP BY is_confirmed
        ORDER BY is_confirmed";
        break;
    case 'month':
        $bookingStatusQuery = "SELECT 
            is_confirmed, 
            COUNT(*) as count 
        FROM appointment 
        WHERE YEAR(appodate) = YEAR('$currentDate') 
        AND MONTH(appodate) = MONTH('$currentDate')
        GROUP BY is_confirmed
        ORDER BY is_confirmed";
        break;
    case 'week':
        $bookingStatusQuery = "SELECT 
            is_confirmed, 
            COUNT(*) as count 
        FROM appointment 
        WHERE YEAR(appodate) = YEAR('$currentDate') 
        AND WEEK(appodate) = WEEK('$currentDate')
        GROUP BY is_confirmed
        ORDER BY is_confirmed";
        break;
    case 'day':
        $bookingStatusQuery = "SELECT 
            is_confirmed, 
            COUNT(*) as count 
        FROM appointment 
        WHERE DATE(appodate) = DATE('$currentDate')
        GROUP BY is_confirmed
        ORDER BY is_confirmed";
        break;
}

$bookingStatusResult = $database->query($bookingStatusQuery);
$bookingStatusData = [];
while($row = $bookingStatusResult->fetch_assoc()) {
    $bookingStatusData[] = $row;
}

// Debug: Print out the actual booking status data
error_log('Booking Status Data: ' . print_r($bookingStatusData, true));

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../img/bg01.png">
    <link rel="shortcut icon" type="image/png" href="../img/bg01.png">
    <link rel="stylesheet" href="../css/animations.css">  
    <link rel="stylesheet" href="../css/main.css">  
    <link rel="stylesheet" href="../css/admin.css">
        
    <title>Admin Records</title>
    <style>
        
        
        .global-filter {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        .global-filter select {
            padding: 10px;
            margin-right: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
            color: #2d6a4f;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .global-filter select:hover {
            border-color: #2d6a4f;
            box-shadow: 0 0 5px rgba(45, 106, 79, 0.2);
        }

        .global-filter select:focus {
            outline: none;
            border-color: #2d6a4f;
            box-shadow: 0 0 8px rgba(45, 106, 79, 0.3);
        }

        .statistics-section {
            padding: 30px;
            background-color: #f4f4f4;
            border-radius: 10px;
            margin-top: 30px;
        }

        .statistics-section h2 {
            text-align: center;
            color: #2d6a4f;
            margin-bottom: 25px;
            font-size: 1.5rem;
        }

        .statistics-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }

        .statistic-card {
            display: flex;
            align-items: center;
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }

        .statistic-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
        }

        .statistic-icon {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
        }

        .statistic-icon i {
            color: white;
            font-size: 30px;
        }

        .statistic-content h3 {
            color: #2d6a4f;
            margin-bottom: 10px;
            font-size: 1rem;
        }

        .statistic-content p {
            font-size: 1.5rem;
            font-weight: bold;
            color: #333;
        }

        @media (max-width: 1200px) {
            .statistics-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .statistics-grid {
                grid-template-columns: 1fr;
            }
        }
        
        @media print {
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            html, body {
                width: 210mm;
                height: 297mm;
                margin: 0 !important;
                padding: 0 !important;
                overflow: visible !important;
            }
            
            .container {
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .dash-body {
                width: 100% !important;
                margin: 0 !important;
                padding: 10mm !important;
                page-break-before: always;
            }
            
            body * {
                visibility: hidden;
            }
            
            .dash-body, .dash-body * {
                visibility: visible;
            }
            
            .chart-grid {
                display: grid;
                grid-template-columns: 1fr 1fr 1fr;
                gap: 10px;
                padding: 10px;
                width: 100%;
                page-break-inside: avoid;
            }
            
            .chart-container.resizable {
                display: flex;
                flex-direction: column;
                align-items: stretch;
                page-break-inside: avoid;
                page-break-after: auto;
                width: 100% !important;
                height: 300px !important;
                max-height: 300px !important;
                margin-bottom: 20px;
                overflow: visible !important;
            }

            .chart-container.resizable canvas {
                flex-grow: 1;
                max-height: calc(100% - 80px) !important;
                width: 100% !important;
                height: auto !important;
            }

            .chart-legend {
                order: 2;
                width: 100%;
                display: flex;
                justify-content: center;
                flex-wrap: wrap;
                gap: 10px;
                padding: 10px 0;
                margin-top: 10px;
                page-break-inside: avoid;
            }

            .chart-legend-item {
                display: flex;
                align-items: center;
                font-size: 0.8rem;
                margin: 0 5px;
                page-break-inside: avoid;
            }

            .menu, .hamburger, .global-filter {
                display: none !important;
            }
            
            .statistics-section {
                margin-top: 10px;
                padding: 10px;
                page-break-inside: avoid;
            }

            .statistics-section h2 {
                font-size: 1rem;
                margin-bottom: 10px;
            }

            .statistics-grid {
                grid-template-columns: repeat(4, 1fr);
                gap: 10px;
                page-break-inside: avoid;
            }

            .statistic-card {
                padding: 10px;
                flex-direction: column;
                align-items: center;
                text-align: center;
            }

            .statistic-icon {
                width: 50px;
                height: 50px;
                margin-right: 0;
                margin-bottom: 5px;
            }

            .statistic-icon i {
                font-size: 24px;
            }

            .statistic-content h3 {
                font-size: 0.8rem;
                margin-bottom: 5px;
            }

            .statistic-content p {
                font-size: 1.2rem;
            }

            @page {
                size: A4 portrait;
                margin: 5mm;
                bleed: 2mm;
            }
        }
        
        .btn-primary-soft.btn:hover {
            background-color:rgb(3, 59, 33);
            color: green;
        }
    </style>
    <style>
        .pie-charts-row {
            display: flex;
            flex-wrap: nowrap;
            justify-content: center;
            gap: 30px;
            margin: 40px auto 0;
            overflow-x: auto;
        }
        .pie-chart-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            padding: 30px;
            max-width: 350px;
            min-width: 320px;
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 0 0 350px;
        }
        @media (max-width: 900px) {
            .pie-charts-row {
                flex-wrap: wrap;
            }
        }
    </style>
    <!-- Removed Chart.js import, not needed for numbers only -->
</head>
<body>
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
                    <td class="menu-btn menu-icon-dashbord menu-active menu-icon-dashbord-active" >
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
                        <a href="patient.php" class="non-style-link-menu"><div><p class="menu-text">Patients</p></a></div>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-logs">
                        <a href="logs.php" class="non-style-link-menu">
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
                <tr class="date-container">
                    <td width="100%">
                        <p style="font-size: 14px;color: rgb(119, 119, 119);padding: 0;margin: 0;">
                            Today's Date
                        </p>
                        <p class="heading-sub12" style="padding: 0; margin: 0;">
                            <?php 
                                date_default_timezone_set('Asia/Manila');
                                $date = date('F j, Y');
                                $time = date('g:i A');
                                echo "$date at $time";
                            ?>
                        </p>
                    </td>
                </tr>
            </table>
            
            <div class="global-filter">
                <select id="globalFilter" onchange="updateFilter()">
                    <option value="year" <?php echo ($filterType == 'year' ? 'selected' : ''); ?>>This Year</option>
                    <option value="month" <?php echo ($filterType == 'month' ? 'selected' : ''); ?>>This Month</option>
                    <option value="week" <?php echo ($filterType == 'week' ? 'selected' : ''); ?>>This Week</option>
                    <option value="day" <?php echo ($filterType == 'day' ? 'selected' : ''); ?>>Today</option>
                </select>
                <button onclick="exportToExcel()" class="btn-primary-soft btn" style="margin-left: 10px;">
    <i class="fas fa-file-excel"></i> Export to Excel
</button>
                <button onclick="exportToPDF()" class="btn-primary-soft btn" style="margin-left: 10px;">
        <i class="fas fa-file-pdf"></i> Export to PDF
    </button>
            </div>
            <h1 style="margin-left: 30px;">Dashboard Overview</h1>
            <div class="statistics-section">
                <h2>Comprehensive Statistics</h2>
                <div class="statistics-grid" id="statisticsGrid">
                    <?php
                    // Fetch total counts for various entities
                    $totalPatientsQuery = "SELECT COUNT(*) as total_patients FROM patient";
                    $totalAppointmentsQuery = "SELECT COUNT(*) as total_appointments FROM appointment WHERE status = 'Approved'";
                    $totalDoctorsQuery = "SELECT COUNT(*) as total_doctors FROM doctor";
                    $totalSchedulesQuery = "SELECT COUNT(*) as total_schedules FROM schedule WHERE deleted_at IS NULL";

                    $totalPatientsResult = $database->query($totalPatientsQuery);
                    $totalAppointmentsResult = $database->query($totalAppointmentsQuery);
                    $totalDoctorsResult = $database->query($totalDoctorsQuery);
                    $totalSchedulesResult = $database->query($totalSchedulesQuery);

                    $totalPatients = $totalPatientsResult->fetch_assoc()['total_patients'];
                    $totalAppointments = $totalAppointmentsResult->fetch_assoc()['total_appointments'];
                    $totalDoctors = $totalDoctorsResult->fetch_assoc()['total_doctors'];
                    $totalSchedules = $totalSchedulesResult->fetch_assoc()['total_schedules'];

                    // Patient category statistics
                    $pwdQuery = "SELECT COUNT(*) as count FROM patient WHERE patient_category LIKE '%PWD%'";
                    $seniorQuery = "SELECT COUNT(*) as count FROM patient WHERE patient_category LIKE '%SENIOR CITIZEN%'";
                    $ipQuery = "SELECT COUNT(*) as count FROM patient WHERE patient_category LIKE '%IP%'";

                    $pwdCount = $database->query($pwdQuery)->fetch_assoc()['count'];
                    $seniorCount = $database->query($seniorQuery)->fetch_assoc()['count'];
                    $ipCount = $database->query($ipQuery)->fetch_assoc()['count'];

                    $statisticsCards = [
                        [
                            'icon' => 'fa-users',
                            'title' => 'Total Patients',
                            'count' => $totalPatients,
                            'color' => '#3498db'
                        ],
                        [
                            'icon' => 'fa-calendar-check',
                            'title' => 'Total Appointments',
                            'count' => $totalAppointments,
                            'color' => '#2ecc71'
                        ],
                        [
                            'icon' => 'fa-user-md',
                            'title' => 'Total Doctors',
                            'count' => $totalDoctors,
                            'color' => '#e74c3c'
                        ],
                        [
                            'icon' => 'fa-calendar',
                            'title' => 'Active Schedules',
                            'count' => $totalSchedules,
                            'color' => '#f39c12'
                        ],
                        [
                            'icon' => 'fa-wheelchair',
                            'title' => 'PWD Patients',
                            'count' => $pwdCount,
                            'color' => '#8e44ad'
                        ],
                        [
                            'icon' => 'fa-blind',
                            'title' => 'Senior Citizens',
                            'count' => $seniorCount,
                            'color' => '#16a085'
                        ],
                        [
                            'icon' => 'fa-people-carry',
                            'title' => 'Indigenous People',
                            'count' => $ipCount,
                            'color' => '#b9770e'
                        ]
                    ];

                    foreach ($statisticsCards as $card):
                    ?>
                    <div class="statistic-card">
                        <div class="statistic-icon" style="background-color: <?php echo $card['color']; ?>">
                            <i class="fas <?php echo $card['icon']; ?>"></i>
                        </div>
                        <div class="statistic-content">
                            <h3><?php echo $card['title']; ?></h3>
                            <p><?php echo number_format($card['count']); ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pie Charts Row -->
                <div class="pie-charts-row">
                    <div class="pie-chart-card">
                        <h3 style="text-align:center; margin-bottom: 20px;">Patient Category Distribution</h3>
                        <canvas id="categoryPieChart" width="400" height="400"></canvas>
                    </div>
                    <div class="pie-chart-card">
                        <h3 style="text-align:center; margin-bottom: 20px;">Appointment Status Distribution</h3>
                        <canvas id="statusPieChart" width="400" height="400"></canvas>
                    </div>
                    <div class="pie-chart-card">
                        <h3 style="text-align:center; margin-bottom: 20px;">Appointment Confirmation Status</h3>
                        <canvas id="confirmationPieChart" width="400" height="400"></canvas>
                    </div>
                </div>

                <!-- Appointment Status Table -->



                <?php
                // Build filter condition for appointments
                $appointmentFilter = '';
                switch($filterType) {
                    case 'month':
                        $appointmentFilter = "AND MONTH(appodate) = MONTH('$currentDate') AND YEAR(appodate) = YEAR('$currentDate')";
                        break;
                    case 'week':
                        $appointmentFilter = "AND WEEK(appodate) = WEEK('$currentDate') AND YEAR(appodate) = YEAR('$currentDate')";
                        break;
                    case 'day':
                        $appointmentFilter = "AND DATE(appodate) = DATE('$currentDate')";
                        break;
                    case 'year':
                    default:
                        $appointmentFilter = "AND YEAR(appodate) = YEAR('$currentDate')";
                        break;
                }
                $statusTableQuery = "SELECT status, COUNT(*) as count FROM appointment WHERE (status = 'scheduled' OR status = 'done') $appointmentFilter GROUP BY status";
                $statusTableResult = $database->query($statusTableQuery);
                $statusCounts = ['scheduled' => 0, 'done' => 0];
                while($row = $statusTableResult->fetch_assoc()) {
                    $statusCounts[$row['status']] = $row['count'];
                }
                ?>
                <div style="margin-top:30px;">
                    <?php
                    $filterLabel = 'This Year';
                    switch($filterType) {
                        case 'month':
                            $filterLabel = 'This Month';
                            break;
                        case 'week':
                            $filterLabel = 'This Week';
                            break;
                        case 'day':
                            $filterLabel = 'Today';
                            break;
                        case 'year':
                        default:
                            $filterLabel = 'This Year';
                            break;
                    }
                    ?>
                    <h3>Appointment Status (<?php echo $filterLabel; ?>)</h3>
                    <table id="statusTable" style="width:100%;max-width:400px;border-collapse:collapse;background:#fff;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,0.04);">
                        <thead>
                            <tr style="background:#f4f4f4;">
                                <th style="padding:8px 12px;text-align:left;">Status</th>
                                <th style="padding:8px 12px;text-align:right;">Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="padding:8px 12px;">Scheduled</td>
                                <td style="padding:8px 12px;text-align:right;"><?php echo number_format($statusCounts['scheduled']); ?></td>
                            </tr>
                            <tr>
                                <td style="padding:8px 12px;">Done</td>
                                <td style="padding:8px 12px;text-align:right;"><?php echo number_format($statusCounts['done']); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
    function updateFilter() {
        const filterSelect = document.getElementById('globalFilter');
        const selectedFilter = filterSelect.value;
        window.location.href = `index.php?filter=${selectedFilter}`;
    }

    function exportToExcel() {
        window.location.href = 'index.php?export=csv&filter=<?php echo $filterType; ?>';
    }
    function exportToPDF() {
        window.location.href = 'index.php?export=pdf&filter=<?php echo $filterType; ?>';
    }
    </script>
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Patient Category Pie Chart
        const ctx = document.getElementById('categoryPieChart').getContext('2d');
        const categoryPieChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['PWD', 'Senior Citizen', 'Indigenous People'],
                datasets: [{
                    data: [
                        <?php echo $pwdCount; ?>,
                        <?php echo $seniorCount; ?>,
                        <?php echo $ipCount; ?>
                    ],
                    backgroundColor: [
                        '#8e44ad', // PWD
                        '#16a085', // Senior Citizen
                        '#b9770e'  // IP
                    ],
                    borderColor: '#fff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            font: { size: 16 }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                let value = context.parsed;
                                let total = context.chart._metasets[0].total;
                                let percent = total ? ((value / total) * 100).toFixed(1) : 0;
                                return `${label}: ${value} (${percent}%)`;
                            }
                        }
                    }
                }
            }
        });

        // Appointment Status Pie Chart
        const ctxStatus = document.getElementById('statusPieChart').getContext('2d');
        const statusPieChart = new Chart(ctxStatus, {
            type: 'pie',
            data: {
                labels: ['Scheduled', 'Done'],
                datasets: [{
                    data: [
                        <?php echo isset($statusCounts['scheduled']) ? $statusCounts['scheduled'] : 0; ?>,
                        <?php echo isset($statusCounts['done']) ? $statusCounts['done'] : 0; ?>
                    ],
                    backgroundColor: [
                        '#2980b9', // Scheduled
                        '#27ae60'  // Done
                    ],
                    borderColor: '#fff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            font: { size: 16 }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                let value = context.parsed;
                                let total = context.chart._metasets[0].total;
                                let percent = total ? ((value / total) * 100).toFixed(1) : 0;
                                return `${label}: ${value} (${percent}%)`;
                            }
                        }
                    }
                }
            }
        });
    // Appointment Confirmation Pie Chart (is_confirmed)
    const confirmationCtx = document.getElementById('confirmationPieChart').getContext('2d');
    // Prepare data from PHP
    const confirmationData = {
        approved: 0,
        pending: 0,
        declined: 0
    };
    <?php
    // Map is_confirmed values to labels
    foreach ($bookingStatusData as $row) {
        if ($row['is_confirmed'] == 1) {
            echo "confirmationData.approved = {$row['count']};\n";
        } elseif ($row['is_confirmed'] == 0) {
            echo "confirmationData.pending = {$row['count']};\n";
        } elseif ($row['is_confirmed'] == -1) {
            echo "confirmationData.declined = {$row['count']};\n";
        }
    }
    ?>
    const confirmationPieChart = new Chart(confirmationCtx, {
        type: 'pie',
        data: {
            labels: ['Approved', 'Pending', 'Declined'],
            datasets: [{
                data: [confirmationData.approved, confirmationData.pending, confirmationData.declined],
                backgroundColor: [
                    '#27ae60', // Approved
                    '#f39c12', // Pending
                    '#e74c3c'  // Declined
                ],
                borderColor: '#fff',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: {
                        font: { size: 16 }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            let value = context.parsed;
                            let total = context.chart._metasets[0].total;
                            let percent = total ? ((value / total) * 100).toFixed(1) : 0;
                            return `${label}: ${value} (${percent}%)`;
                        }
                    }
                }
            }
        }
    });
    </script>
</body>
</html>
