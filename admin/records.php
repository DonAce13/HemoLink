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

// Fetch data for doctor specialties
$specialtyQuery = "SELECT sname, COUNT(*) as count FROM doctor JOIN specialties ON doctor.specialties = specialties.id GROUP BY sname";
$specialtyResult = $database->query($specialtyQuery);
$specialtyData = [];
while($row = $specialtyResult->fetch_assoc()) {
    $specialtyData[] = $row;
}

// Fetch data for appointment status
$statusQuery = "SELECT status, COUNT(*) as count FROM appointment GROUP BY status";
$statusResult = $database->query($statusQuery);
$statusData = [];
while($row = $statusResult->fetch_assoc()) {
    $statusData[] = $row;
}

// Check if the appointments table exists and fetch data
$appointmentData = [];
if ($database->query("SHOW TABLES LIKE 'appointments'")->num_rows == 1) {
    $appointmentQuery = "SELECT DATE(appodate) as date, COUNT(*) as count FROM appointment WHERE appodate >= CURDATE() GROUP BY DATE(appodate)";
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
            <h1>Informative Charts</h1>
            <div class="chart-container">
                <canvas id="specialtyChart"></canvas>
            </div>
            <div class="chart-container">
                <canvas id="statusChart"></canvas>
            </div>
            <div class="chart-container">
                <canvas id="appointmentChart"></canvas>
                <div class="filter-controls">
                    <label for="filter">Filter by:</label>
                    <select id="filter" onchange="updateAppointmentChart()">
                        <option value="all">All</option>
                        <option value="month">This Month</option>
                        <option value="week">This Week</option>
                        <option value="date">Specific Date</option>
                    </select>
                    <input type="date" id="specificDate" onchange="updateAppointmentChart()" style="display:none;">
                </div>
            </div>
        </div>
    </div>

    <script>
        // Doctor Specialties Chart
        const specialtyCtx = document.getElementById('specialtyChart').getContext('2d');
        const specialtyData = {
            labels: <?php echo json_encode(array_column($specialtyData, 'sname')); ?>,
            datasets: [{
                label: 'Doctor Specialties',
                data: <?php echo json_encode(array_column($specialtyData, 'count')); ?>,
                backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'],
            }]
        };
        new Chart(specialtyCtx, {
            type: 'doughnut',
            data: specialtyData,
        });

        // Appointment Status Chart
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        const statusData = {
            labels: <?php echo json_encode(array_column($statusData, 'status')); ?>,
            datasets: [{
                label: 'Appointment Status',
                data: <?php echo json_encode(array_column($statusData, 'count')); ?>,
                backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0'],
            }]
        };
        new Chart(statusCtx, {
            type: 'pie',
            data: statusData,
        });

        // Appointment Chart
        const appointmentCtx = document.getElementById('appointmentChart').getContext('2d');
        const appointmentData = {
            labels: <?php echo json_encode(array_column($appointmentData, 'date')); ?>,
            datasets: [{
                label: 'Appointments',
                data: <?php echo json_encode(array_column($appointmentData, 'count')); ?>,
                backgroundColor: '#36A2EB',
            }]
        };
        let appointmentChart = new Chart(appointmentCtx, {
            type: 'bar',
            data: appointmentData,
        });

        function updateAppointmentChart() {
            const filter = document.getElementById('filter').value;
            const specificDate = document.getElementById('specificDate').value;

            let query = "SELECT DATE(appodate) as date, COUNT(*) as count FROM appointment WHERE appodate >= CURDATE() ";

            if (filter === 'month') {
                query += "AND MONTH(appodate) = MONTH(CURDATE()) AND YEAR(appodate) = YEAR(CURDATE()) ";
            } else if (filter === 'week') {
                query += "AND WEEK(appodate) = WEEK(CURDATE()) AND YEAR(appodate) = YEAR(CURDATE()) ";
            } else if (filter === 'date' && specificDate) {
                query += "AND appodate = '" + specificDate + "' ";
            }

            query += "GROUP BY DATE(appodate)";

            // Fetch new data based on the query
            fetch('fetch_appointment_data.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ query: query }),
            })
            .then(response => response.json())
            .then(data => {
                const appointmentData = {
                    labels: data.map(item => item.date),
                    datasets: [{
                        label: 'Appointments',
                        data: data.map(item => item.count),
                        backgroundColor: '#36A2EB',
                    }]
                };

                // Update the chart
                appointmentChart.data = appointmentData;
                appointmentChart.update();
            });
        }
    </script>
</body>
</html>
