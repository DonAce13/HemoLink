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
        .popup{
            animation: transitionIn-Y-bottom 0.5s;
        }
        .sub-table{
            animation: transitionIn-Y-bottom 0.5s;
        }
        .chart-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            padding: 20px;
        }

        .chart-container.resizable {
            background-color: #f9f9f9;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            resize: both;
            overflow: auto;
            min-width: 250px;
            min-height: 250px;
            max-width: 100%;
            max-height: 500px;
            position: relative;
        }

        .chart-container.resizable h3 {
            text-align: center;
            color: #2d6a4f;
            margin-bottom: 15px;
            font-size: 1rem;
        }

        .chart-container.resizable:hover {
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
            transform: translateY(-5px);
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

            <h1>Informative Charts</h1>
            <div class="chart-grid">
                <div class="chart-container resizable" id="status-chart-container">
                    <h3>Schedule Status</h3>
                    <canvas id="statusChart"></canvas>
                </div>
                <div class="chart-container resizable" id="age-group-chart-container">
                    <h3>Patient Age Distribution</h3>
                    <canvas id="ageGroupChart"></canvas>
                </div>
                <div class="chart-container resizable" id="appointment-chart-container">
                    <h3>Appointment Overview</h3>
                    <canvas id="appointmentChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Color palette for consistent and accessible colors
        const colorPalette = [
            '#1f77b4',  // Blue
            '#ff7f0e',  // Orange
            '#2ca02c',  // Green
            '#d62728',  // Red
            '#9467bd',  // Purple
            '#8c564b',  // Brown
            '#e377c2',  // Pink
            '#7f7f7f',  // Gray
            '#bcbd22',  // Olive
            '#17becf'   // Cyan
        ];

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
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        bottom: 50  // Space for legend
                    }
                },
                plugins: {
                    legend: {
                        display: false  // Hide default legend
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += context.formattedValue;
                                return label;
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
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Number of Patients'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
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
                borderWidth: 1,
                barThickness: 'flex',
                maxBarThickness: 50
            }]
        };
        const appointmentChart = new Chart(appointmentCtx, {
            type: 'bar',
            data: appointmentData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Number of Appointments'
                        },
                        ticks: {
                            precision: 0  // Whole numbers only
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Date'
                        },
                        ticks: {
                            autoSkip: true,
                            maxRotation: 45,
                            minRotation: 45
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            title: function(context) {
                                return 'Date: ' + context[0].label;
                            },
                            label: function(context) {
                                return 'Appointments: ' + context.formattedValue;
                            }
                        }
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
                
                // Adjust canvas size to container
                canvas.width = container.clientWidth - 40;
                canvas.height = container.clientHeight - 80;
                
                // Redraw the chart
                chart.resize();
            });
        }

        // Resize charts on window resize
        window.addEventListener('resize', resizeCharts);

        // Initial resize and legend creation after charts are created
        document.addEventListener('DOMContentLoaded', () => {
            resizeCharts();
            createStatusChartLegend();
        });
    </script>
</body>
</html>
