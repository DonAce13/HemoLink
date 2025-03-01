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
switch($filterType) {
    case 'year':
        $statusQuery = "SELECT 
            CASE 
                WHEN deleted_at IS NULL THEN 'Active Schedules'
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
                WHEN deleted_at IS NULL THEN 'Active Schedules'
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
                WHEN deleted_at IS NULL THEN 'Active Schedules'
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
                WHEN deleted_at IS NULL THEN 'Active Schedules'
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

// Fetch appointment overview data from schedule and appointment tables
$appointmentData = [];
$appointmentQuery = "";

switch($filterType) {
    case 'year':
        $appointmentQuery = "SELECT 
            s.scheduledate as date, 
            COUNT(a.scheduleid) as count 
        FROM schedule s
        LEFT JOIN appointment a ON s.scheduleid = a.scheduleid
        WHERE YEAR(s.scheduledate) = YEAR('$currentDate')
        GROUP BY s.scheduledate
        ORDER BY s.scheduledate";
        break;
    case 'month':
        $appointmentQuery = "SELECT 
            s.scheduledate as date, 
            COUNT(a.scheduleid) as count 
        FROM schedule s
        LEFT JOIN appointment a ON s.scheduleid = a.scheduleid
        WHERE YEAR(s.scheduledate) = YEAR('$currentDate') 
        AND MONTH(s.scheduledate) = MONTH('$currentDate')
        GROUP BY s.scheduledate
        ORDER BY s.scheduledate";
        break;
    case 'week':
        $appointmentQuery = "SELECT 
            s.scheduledate as date, 
            COUNT(a.scheduleid) as count 
        FROM schedule s
        LEFT JOIN appointment a ON s.scheduleid = a.scheduleid
        WHERE YEAR(s.scheduledate) = YEAR('$currentDate') 
        AND WEEK(s.scheduledate) = WEEK('$currentDate')
        GROUP BY s.scheduledate
        ORDER BY s.scheduledate";
        break;
    case 'day':
        $appointmentQuery = "SELECT 
            s.scheduledate as date, 
            COUNT(a.scheduleid) as count 
        FROM schedule s
        LEFT JOIN appointment a ON s.scheduleid = a.scheduleid
        WHERE DATE(s.scheduledate) = DATE('$currentDate')
        GROUP BY s.scheduledate
        ORDER BY s.scheduledate";
        break;
}

if (!empty($appointmentQuery)) {
    $appointmentResult = $database->query($appointmentQuery);
    while($row = $appointmentResult->fetch_assoc()) {
        $appointmentData[] = $row;
    }
}
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
            height: 350px;  /* Fixed height to prevent resize */
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
            flex: 1;
            width: 100% !important;
            height: 100% !important;
            max-width: 100%;
            max-height: 100%;
            overflow: hidden;
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
            position: absolute;
            bottom: 10px;
            left: 10px;
            right: 10px;
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        .chart-legend-item {
            display: flex;
            align-items: center;
            font-size: 0.8rem;
        }

        .chart-legend-item span {
            width: 12px;
            height: 12px;
            margin-right: 5px;
            border-radius: 50%;
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
                <div class="chart-container resizable full-width" id="appointment-chart-container" style="flex: 1;">
                    <h3>Appointment Overview</h3>
                    <canvas id="appointmentChart"></canvas>
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
        const statusChart = new Chart(statusCtx, {
            type: 'pie',
            data: statusData,
            options: {
                ...commonChartOptions,
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
            }
        });

        // Create custom legend for status chart
        function createStatusChartLegend() {
            const legendContainer = document.createElement('div');
            legendContainer.className = 'chart-legend';
            
            statusData.labels.forEach((label, index) => {
                const legendItem = document.createElement('div');
                legendItem.className = 'chart-legend-item';
                
                const colorSpan = document.createElement('span');
                colorSpan.className = 'color-box';
                colorSpan.style.backgroundColor = statusData.datasets[0].backgroundColor[index];
                
                const labelSpan = document.createElement('span');
                labelSpan.textContent = `${label}: ${statusData.datasets[0].data[index]}`;
                
                legendItem.appendChild(colorSpan);
                legendItem.appendChild(labelSpan);
                legendContainer.appendChild(legendItem);
            });

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

        // Appointment Chart
        const appointmentCtx = document.getElementById('appointmentChart').getContext('2d');
        const appointmentData = {
            labels: <?php echo json_encode(array_column($appointmentData, 'date')); ?>,
            datasets: [{
                label: 'Appointments',
                data: <?php echo json_encode(array_column($appointmentData, 'count')); ?>,
                backgroundColor: colorPalette[5],
                borderColor: colorPalette[5],
                borderWidth: 3,
                fill: true,
                tension: 0.4
            }]
        };
        const appointmentChart = new Chart(appointmentCtx, {
            type: 'line',
            data: appointmentData,
            options: {
                ...commonChartOptions,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Number of Appointments',
                            color: '#2d6a4f'
                        },
                        grid: {
                            color: 'rgba(0,0,0,0.05)'
                        },
                        ticks: {
                            precision: 0
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Date',
                            color: '#2d6a4f'
                        },
                        grid: {
                            display: false
                        },
                        ticks: {
                            autoSkip: true,
                            maxRotation: 45,
                            minRotation: 45
                        }
                    }
                },
                elements: {
                    point: {
                        radius: 5,
                        hoverRadius: 7,
                        backgroundColor: 'white',
                        borderColor: colorPalette[5],
                        borderWidth: 2
                    }
                }
            }
        });

        function updateCharts() {
            const filter = document.getElementById('globalFilter').value;
            window.location.href = 'records.php?filter=' + filter;
        }

        function resizeCharts() {
            const charts = [
                { chart: statusChart, containerId: 'status-chart-container' },
                { chart: ageGroupChart, containerId: 'age-group-chart-container' },
                { chart: appointmentChart, containerId: 'appointment-chart-container' }
            ];

            charts.forEach(({ chart, containerId }) => {
                const container = document.getElementById(containerId);
                const canvas = container.querySelector('canvas');
                
                // Ensure canvas takes full container size with some padding
                canvas.style.width = '100%';
                canvas.style.height = '100%';
                
                // Resize and update chart without animation
                chart.resize();
                chart.update('none');
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
        });
    </script>
</body>
</html>
