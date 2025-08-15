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
        .chart-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            padding: 30px;
            width: 100%;
            max-width: 100%;
        }

        .chart-container.resizable {
            background-color: #f9f9f9;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            position: relative;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            width: 100%;
            height: 600px;  /* Increased height */
        }

        .chart-container.resizable:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.15);
            z-index: 10;
        }

        .chart-container.resizable h3 {
            text-align: center;
            color: #2d6a4f;
            margin-bottom: 10px;
            font-size: 1rem;
            flex-shrink: 0;
            transition: color 0.3s ease;
        }

        .chart-container.resizable:hover h3 {
            color: #1a4a33;  /* Slightly darker shade on hover */
        }

        .chart-container.resizable canvas {
            flex-grow: 1;
            max-height: calc(100% - 100px);  /* Leave space for legend */
        }

        @media (max-width: 1200px) {
            .chart-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .chart-grid {
                grid-template-columns: 1fr;
            }

            .chart-container.resizable {
                height: 300px;
            }
        }
        
        .chart-legend {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 10px;
            padding: 10px;
            background-color: #f9f9f9;
            border-radius: 5px;
            margin-top: 10px;
            width: 100%;
            box-sizing: border-box;
        }

        .chart-legend-item {
            display: flex;
            align-items: center;
            font-size: 0.8rem;
            margin: 0 5px;
        }

        .chart-legend-item .color-box {
            width: 12px;
            height: 12px;
            margin-right: 5px;
            border-radius: 50%;
            display: inline-block;
        }

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
                color-adjust: exact !important;
                print-color-adjust: exact !important;
                print-color-adjust: economy !important;
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
                page-break-inside: avoid;
                page-break-after: auto;
                width: 100% !important;
                height: 250px !important;
                max-height: 250px !important;
                margin-bottom: 10px;
                overflow: visible !important;
            }
            
            .chart-container.resizable canvas {
                width: 100% !important;
                height: 100% !important;
            }
            
            .statistics-section {
                page-break-inside: avoid;
                margin-top: 10px;
            }
            
            .statistics-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
            }
            
            .menu, .hamburger, .global-filter {
                display: none !important;
            }
            
            @page {
                size: A4 portrait;
                margin: 10mm;
                bleed: 5mm;
            }
        }
        
        .btn-primary-soft.btn:hover {
            background-color: #2d6a4f;
            color: white;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                        <a href="patient.php" class="non-style-link-menu"><div><p class="menu-text">Patients</p></a></div>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-records  menu-active menu-icon-records-active">
                        <a href="records.php" class="non-style-link-menu  non-style-link-menu-active"><div><p class="menu-text">Records</p></a></div>
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
                                $date = date('Y-m-d');
                                echo $date;
                            ?>
                        </p>
                    </td>
                </tr>
            </table>
            
            <div class="global-filter">
                <select id="globalFilter" onchange="updateCharts()">
                    <option value="year" <?php echo ($filterType == 'year' ? 'selected' : ''); ?>>This Year</option>
                    <option value="month" <?php echo ($filterType == 'month' ? 'selected' : ''); ?>>This Month</option>
                    <option value="week" <?php echo ($filterType == 'week' ? 'selected' : ''); ?>>This Week</option>
                    <option value="day" <?php echo ($filterType == 'day' ? 'selected' : ''); ?>>Today</option>
                </select>
                <button onclick="preparePrintView()" class="btn-primary-soft btn" style="margin-left: 10px;">
                    <i class="fas fa-print"></i> Print Report
                </button>
            </div>
            <h1 style="margin-left: 30px;">Informative Charts</h1>
            
            <div class="chart-grid">
                <div class="chart-container resizable" id="status-chart-container" style="flex: 1;">
                    <h3>Schedule Status</h3>
                    <canvas id="statusChart"></canvas>
                </div>
                <div class="chart-container resizable" id="age-group-chart-container" style="flex: 1;">
                    <h3>Patient Age Distribution</h3>
                    <canvas id="ageGroupChart"></canvas>
                </div>
                <div class="chart-container resizable full-width" id="booking-status-chart-container" style="flex: 1;">
                    <h3>Booking Approval/Rejection Statistics</h3>
                    <canvas id="bookingStatusChart"></canvas>
                </div>
            </div>
            <!-- Statistics Section -->
            <div class="statistics-section">
                <h2>Comprehensive Statistics</h2>
                <div class="statistics-grid">
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
            </div>
        </div>
    </div>

    <script>
        // Color palette with more accessible and visually pleasing colors
        const colorPalette = [
            '#3498db',  // Soft Blue
            '#2ecc71',  // Emerald Green
            '#e74c3c',  // Soft Red
            '#f39c12',  // Sunflower Yellow
            '#9b59b6',  // Amethyst Purple
            '#1abc9c',  // Turquoise
            '#34495e',  // Dark Blue Gray
            '#d35400',  // Pumpkin Orange
        ];

        // Enhanced chart options for better container fit
        const commonChartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            layout: {
                padding: {
                    top: 10,
                    bottom: 10,
                    left: 10,
                    right: 10
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    titleColor: 'white',
                    bodyColor: 'white',
                    borderColor: 'rgba(255,255,255,0.2)',
                    borderWidth: 1
                }
            }
        };

        // Schedule Status Chart
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        const statusData = {
            labels: <?php echo json_encode(array_column($statusData, 'status')); ?>,
            datasets: [{
                label: 'Schedule Status',
                data: <?php echo json_encode(array_column($statusData, 'count')); ?>,
                backgroundColor: colorPalette.slice(0, <?php echo count($statusData); ?>),
            }]
        };
        const statusChartOptions = {
            ...commonChartOptions,
            responsive: true,
            maintainAspectRatio: true,  // Changed to true to maintain aspect ratio
            aspectRatio: 1,  // Force 1:1 aspect ratio
            layout: {
                padding: {
                    top: 10,
                    bottom: 10,
                    left: 100,
                    right: 100
                }
            },
            plugins: {
                ...commonChartOptions.plugins,
                tooltip: {
                    ...commonChartOptions.plugins.tooltip,
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const value = context.parsed;
                            const percentage = ((value / total) * 100).toFixed(1);
                            return `${context.label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            }
        };
        const statusChart = new Chart(statusCtx, {
            type: 'pie',
            data: statusData,
            options: statusChartOptions
        });

        // Create custom legend for status chart
        function createStatusChartLegend() {
            // Remove any existing legend
            const existingLegend = document.querySelector('#status-chart-container .chart-legend');
            if (existingLegend) {
                existingLegend.remove();
            }

            const legendContainer = document.createElement('div');
            legendContainer.className = 'chart-legend';
            
            // Sort data by value in descending order
            const sortedData = statusData.labels.map((label, index) => ({
                label, 
                value: statusData.datasets[0].data[index],
                color: statusData.datasets[0].backgroundColor[index]
            })).sort((a, b) => b.value - a.value);

            // Create legend items
            sortedData.forEach(item => {
                const legendItem = document.createElement('div');
                legendItem.className = 'chart-legend-item';
                
                const colorSpan = document.createElement('span');
                colorSpan.className = 'color-box';
                colorSpan.style.backgroundColor = item.color;
                
                const labelSpan = document.createElement('span');
                labelSpan.textContent = `${item.label}: ${item.value}`;
                
                legendItem.appendChild(colorSpan);
                legendItem.appendChild(labelSpan);
                legendContainer.appendChild(legendItem);
            });

            // Append legend to the chart container
            document.getElementById('status-chart-container').appendChild(legendContainer);
        }

        // Age Group Chart
        const ageGroupCtx = document.getElementById('ageGroupChart').getContext('2d');
        const ageGroupData = {
            labels: <?php 
                $labels = array_column($ageGroupData, 'age_group');
                $labels = array_map(function($label) {
                    switch($label) {
                        case '18-30': return "'18-30 years'";
                        case '31-60': return "'31-60 years'";
                        case '61-90': return "'61-90 years'";
                        default: return $label;
                    }
                }, $labels);
                echo json_encode($labels);
            ?>,
            datasets: [{
                label: 'Patient Age Distribution',
                data: <?php echo json_encode(array_column($ageGroupData, 'count')); ?>,
                backgroundColor: colorPalette.slice(1, 4),
            }]
        };
        const ageGroupChart = new Chart(ageGroupCtx, {
            type: 'bar',
            data: ageGroupData,
            options: {
                ...commonChartOptions,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Number of Patients',
                            color: '#2d6a4f'
                        },
                        grid: {
                            color: 'rgba(0,0,0,0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // Booking Status Chart
        const bookingStatusCtx = document.getElementById('bookingStatusChart').getContext('2d');
        
        // Map numeric labels to descriptive status
        const statusMap = {
            '1': 'Approved',
            '0': 'Pending',
            '-1': 'Declined'
        };
        
        const bookingStatusData = {
            labels: <?php echo json_encode(array_column($bookingStatusData, 'is_confirmed')); ?>.map(status => statusMap[status] || status),
            datasets: [{
                label: 'Booking Status',
                data: <?php echo json_encode(array_column($bookingStatusData, 'count')); ?>,
                backgroundColor: [
                    colorPalette[2],   // Approved - Green
                    colorPalette[4],   // Pending - Purple
                    colorPalette[3]    // Rejected - Red
                ],
            }]
        };
        const bookingStatusChartOptions = {
            ...commonChartOptions,
            responsive: true,
            maintainAspectRatio: true,
            aspectRatio: 1,
            layout: {
                padding: {
                    top: 10,
                    bottom: 10,
                    left: 100,
                    right: 100
                }
            },
            plugins: {
                ...commonChartOptions.plugins,
                tooltip: {
                    ...commonChartOptions.plugins.tooltip,
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const value = context.parsed;
                            const percentage = ((value / total) * 100).toFixed(1);
                            return `${context.label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            }
        };
        const bookingStatusChart = new Chart(bookingStatusCtx, {
            type: 'pie',
            data: bookingStatusData,
            options: bookingStatusChartOptions
        });

        // Create custom legend for booking status chart
        function createBookingStatusChartLegend() {
            // Remove any existing legend
            const existingLegend = document.querySelector('#booking-status-chart-container .chart-legend');
            if (existingLegend) {
                existingLegend.remove();
            }

            const legendContainer = document.createElement('div');
            legendContainer.className = 'chart-legend';
            
            // Sort data by value in descending order
            const sortedData = bookingStatusData.labels.map((label, index) => ({
                label, 
                value: bookingStatusData.datasets[0].data[index],
                color: bookingStatusData.datasets[0].backgroundColor[index]
            })).sort((a, b) => b.value - a.value);

            // Create legend items
            sortedData.forEach(item => {
                const legendItem = document.createElement('div');
                legendItem.className = 'chart-legend-item';
                
                const colorSpan = document.createElement('span');
                colorSpan.className = 'color-box';
                colorSpan.style.backgroundColor = item.color;
                
                const labelSpan = document.createElement('span');
                labelSpan.textContent = `${item.label}: ${item.value}`;
                
                legendItem.appendChild(colorSpan);
                legendItem.appendChild(labelSpan);
                legendContainer.appendChild(legendItem);
            });

            // Append legend to the chart container
            const chartContainer = document.getElementById('booking-status-chart-container');
            chartContainer.appendChild(legendContainer);

            // Ensure legend doesn't overlap
            const canvas = chartContainer.querySelector('canvas');
            if (canvas) {
                canvas.style.maxHeight = 'calc(100% - 50px)';
            }
        }

        // Modify the existing updateCharts function to include new legend
        function updateCharts() {
            const filterSelect = document.getElementById('globalFilter');
            const selectedFilter = filterSelect.value;
            window.location.href = `records.php?filter=${selectedFilter}`;
        }

        // Call legend creation functions after charts are rendered
        createStatusChartLegend();
        createBookingStatusChartLegend();

        function preparePrintView() {
            // Remove any existing print-specific elements
            const existingPrintElements = document.querySelectorAll('.print-only');
            existingPrintElements.forEach(el => el.remove());

            // Temporarily resize charts for print
            const charts = [statusChart, ageGroupChart, bookingStatusChart];
            const containers = [
                document.getElementById('status-chart-container'),
                document.getElementById('age-group-chart-container'),
                document.getElementById('booking-status-chart-container')
            ];

            // Temporarily adjust chart configurations for print
            charts.forEach((chart, index) => {
                const container = containers[index];
                
                // Set fixed height and width
                container.style.height = '300px';
                container.style.width = '100%';
                
                // Temporarily modify chart options for print
                const originalOptions = {...chart.options};
                chart.options.responsive = true;
                chart.options.maintainAspectRatio = false;
                
                // Resize and update chart
                chart.resize();
                chart.update('none');
                
                // Store original options to restore later
                chart.originalOptions = originalOptions;
            });

            // Create a print-specific message
            const printInfo = document.createElement('div');
            printInfo.className = 'print-only';
            printInfo.style.textAlign = 'center';
            printInfo.style.marginBottom = '20px';
            printInfo.innerHTML = `
                <h1>Mabayuan Health Care Medical Records Report</h1>
                <p>Generated on: ${new Date().toLocaleString()}</p>
            `;
            
            // Insert print info at the top of the dash-body
            const dashBody = document.querySelector('.dash-body');
            dashBody.insertBefore(printInfo, dashBody.firstChild);

            // Small delay to ensure layout is ready
            setTimeout(() => {
                // Force full page width for better PDF rendering
                document.body.style.width = '100%';
                
                // Trigger print dialog
                window.print();
                
                // Restore original configurations
                charts.forEach((chart, index) => {
                    const container = containers[index];
                    
                    // Restore original container styles
                    container.style.height = '';
                    container.style.width = '';
                    
                    // Restore original chart options
                    chart.options = chart.originalOptions;
                    
                    // Resize back to original
                    chart.resize();
                    chart.update('none');
                });

                // Clean up
                printInfo.remove();
                document.body.style.width = '';
            }, 100);
        }

        function resizeCharts() {
            const charts = [
                { chart: statusChart, containerId: 'status-chart-container', legendFunc: createStatusChartLegend },
                { chart: ageGroupChart, containerId: 'age-group-chart-container' },
                { chart: bookingStatusChart, containerId: 'booking-status-chart-container', legendFunc: createBookingStatusChartLegend }
            ];

            charts.forEach(({ chart, containerId, legendFunc }) => {
                const container = document.getElementById(containerId);
                const canvas = container.querySelector('canvas');
                
                // Ensure canvas takes full container size with some padding
                canvas.style.width = '100%';
                canvas.style.height = '100%';
                
                // Resize and update chart without animation
                chart.resize();
                chart.update('none');

                // Recreate legend if function provided
                if (legendFunc) {
                    legendFunc();
                }
            });
        }

        // Resize charts on window resize with debounce
        let resizeTimer;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(resizeCharts, 250);
        });

        // Initial resize and legend creation after charts are created
        document.addEventListener('DOMContentLoaded', () => {
            resizeCharts();
            createStatusChartLegend();
            createBookingStatusChartLegend();
        });
    </script>
</body>
</html>
