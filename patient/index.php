<?php
// Start the session to check for user login
session_start();

if (isset($_SESSION["user"])) {
    if ($_SESSION["user"] == "" || $_SESSION['usertype'] != 'p') {
        header("location: ../login.php");
        exit;
    }
} else {
    header("location: ../login.php");
    exit;
}

// Include the database connection file
include("../connection.php");  // Make sure this path is correct

// Assuming $patientEmail is fetched from the database
$query = "SELECT pemail, pname FROM patient WHERE pemail = ?";  // Use 'pemail' as per the patient table schema

// Prepare a secure query using prepared statements
$stmt = $database->prepare($query);
$stmt->bind_param("s", $_SESSION["user"]);
$stmt->execute();
$result = $stmt->get_result();

// Check if query was successful and fetch the email and name
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $patientEmail = $row['pemail']; // Retrieve the patient's email
    $patientName = $row['pname'];   // Retrieve the patient's name
} else {
    // Handle the case where no patient is found
    echo "Error: Patient email not found in the database.";
    exit;
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
        
    <title>Dashboard</title>
    <style>
        .dashbord-tables {
            animation: transitionIn-Y-over 0.5s;
        }
        .filter-container {
            animation: transitionIn-Y-bottom 0.5s;
        }
        .sub-table, .anime {
            animation: transitionIn-Y-bottom 0.5s;
        }

        /* New styles for sessions layout */
        .sessions-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
            padding: 20px;
        }

        .days-row {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 20px;
        }

        .day-sessions {
            flex: 1;
            min-width: 200px;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 6px 12px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: all 0.3s ease;
            border: 1px solid #e0e0e0;
        }

        .day-sessions:hover {
            transform: translateY(-10px);
            box-shadow: 0 12px 20px rgba(0,0,0,0.15);
            border-color: #2d6a4f;
        }

        .day-header {
            background-color: #2d6a4f;
            color: white;
            padding: 15px 20px;
            margin: 0;
            text-align: center;
            font-weight: bold;
            font-size: 1.1rem;
            letter-spacing: 0.5px;
        }

        .sessions-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .sessions-table thead {
            background-color: #f4f4f4;
        }

        .sessions-table th {
            padding: 12px;
            text-align: center;
            font-size: 0.9em;
            color: #2d6a4f;
            border-bottom: 2px solid #e0e0e0;
        }

        .sessions-table td {
            padding: 12px;
            border-bottom: 1px solid #e0e0e0;
            text-align: center;
            font-size: 0.9em;
            transition: background-color 0.3s ease;
        }

        .sessions-table tr:hover td {
            background-color: #f0f0f0;
        }

        .sessions-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .sessions-table .login-btn {
            width: 100%;
            padding: 8px 12px;
            font-size: 0.9em;
            transition: all 0.3s ease;
        }

        .sessions-table .login-btn.book-now {
            background-color: #2d6a4f;
            color: white;
        }

        .sessions-table .login-btn.book-now:hover {
            background-color: #1f4d37;
            transform: translateY(-2px);
        }

        .sessions-table .login-btn.session-full {
            background-color: #6c757d;
            color: white;
            cursor: not-allowed;
        }

        .sessions-container {
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 15px;
        }

        .days-row {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }

        @media (max-width: 1200px) {
            .days-row {
                flex-direction: column;
            }

            .day-sessions {
                width: 100%;
            }
        }

        @media (max-width: 768px) {
            .sessions-table {
                font-size: 0.8em;
            }

            .sessions-table th, 
            .sessions-table td {
                padding: 8px;
            }

            .day-header {
                padding: 10px 15px;
                font-size: 1rem;
            }
        }

        /* Next appointment section styles */
        .next-appointment-container {
            background-color: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-top: 20px;
            overflow: hidden;
            transition: transform 0.3s ease;
            width: 100%;
        }

        .next-appointment-container:hover {
            transform: scale(1.02);
        }

        .next-appointment-header {
            background-color: #2d6a4f;
            color: white;
            padding: 10px 20px;
            text-align: center;
        }

        .next-appointment-title {
            font-size: 1em;
            font-weight: bold;
            margin: 0;
        }

        .next-appointment-body {
            padding: 15px;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
        }

        .next-appointment-details {
            display: flex;
            align-items: center;
            width: 100%;
            gap: 15px;
        }

        .appointment-number {
            flex-shrink: 0;
        }

        .appointment-number .badge {
            background-color: #2d6a4f;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            font-weight: bold;
            font-size: 1.2em;
        }

        .appointment-info {
            flex-grow: 1;
            min-width: 0;
        }

        .appointment-info h4 {
            margin: 0 0 5px 0;
            color: #333;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .appointment-info p {
            margin: 0 0 5px 0;
            color: #666;
        }

        .appointment-info small {
            color: #888;
            font-size: 0.9em;
        }

        .no-appointments {
            text-align: center;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 10px;
            color: #666;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .next-appointment-details {
                flex-direction: column;
                text-align: center;
                gap: 10px;
            }

            .appointment-number {
                margin-bottom: 10px;
            }

            .appointment-number .badge {
                font-size: 1em;
                padding: 8px 12px;
            }

            .appointment-info h4 {
                font-size: 0.9em;
            }

            .appointment-info p,
            .appointment-info small {
                font-size: 0.8em;
            }
        }

        @media (max-width: 480px) {
            .next-appointment-body {
                padding: 10px;
            }

            .appointment-number .badge {
                font-size: 0.9em;
                padding: 6px 10px;
            }
        }

        /* Enhanced Responsive Upcoming Booking Table Styles */
        .abc.scroll {
            max-height: 600px;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 10px;
        }

        .sub-table.scrolldown {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-bottom: 20px;
        }

        .sub-table.scrolldown thead {
            position: sticky;
            top: 0;
            background-color: #f9f9f9;
            z-index: 10;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .sub-table.scrolldown .table-headin {
            background-color: #2d6a4f;
            color: white;
            padding: 12px;
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .sub-table.scrolldown tbody tr {
            transition: background-color 0.3s ease;
            border-bottom: 1px solid #e0e0e0;
        }

        .sub-table.scrolldown tbody tr:nth-child(even) {
            background-color: #f5f5f5;
        }

        .sub-table.scrolldown tbody tr:hover {
            background-color: #e0e0e0;
        }

        /* Responsive Table Design */
        @media (max-width: 768px) {
            .abc.scroll {
                max-height: 500px;
                padding: 5px;
            }

            .sub-table.scrolldown {
                font-size: 0.9em;
            }

            .sub-table.scrolldown thead {
                position: static;
            }

            .sub-table.scrolldown td, 
            .sub-table.scrolldown th {
                padding: 12px 8px;
                text-align: center;
            }

            /* Card-like Mobile Layout */
            .sub-table.scrolldown thead,
            .sub-table.scrolldown tbody,
            .sub-table.scrolldown tr,
            .sub-table.scrolldown td,
            .sub-table.scrolldown th {
                display: block;
                width: 100%;
            }

            .sub-table.scrolldown tr {
                background-color: #fff;
                border: 1px solid #e0e0e0;
                border-radius: 8px;
                margin-bottom: 15px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.05);
                overflow: hidden;
            }

            .sub-table.scrolldown thead tr {
                display: none;
            }

            .sub-table.scrolldown tr td {
                border: none;
                position: relative;
                padding: 10px 15px;
                text-align: right;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .sub-table.scrolldown tr td:before {
                content: attr(data-label);
                font-weight: bold;
                color: #2d6a4f;
                text-align: left;
                flex-grow: 1;
                padding-right: 10px;
            }

            .sub-table.scrolldown td:nth-of-type(1):before { content: "Appoint. Number"; }
            .sub-table.scrolldown td:nth-of-type(2):before { content: "Session Title"; }
            .sub-table.scrolldown td:nth-of-type(3):before { content: "Doctor"; }
            .sub-table.scrolldown td:nth-of-type(4):before { content: "Scheduled Date & Time"; }
            .sub-table.scrolldown td:nth-of-type(5):before { content: "Booked For"; }
        }

        @media (max-width: 480px) {
            .sub-table.scrolldown {
                font-size: 0.8em;
            }

            .sub-table.scrolldown tr {
                margin-bottom: 10px;
            }

            .sub-table.scrolldown td, 
            .sub-table.scrolldown th {
                padding: 8px 10px;
            }

            .sub-table.scrolldown tr td:before {
                font-size: 0.9em;
            }
        }

        /* No Bookings Enhanced Styles */
        .no-bookings-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
            text-align: center;
            background-color: #f9f9f9;
            border-radius: 10px;
            gap: 20px;
        }

        .no-bookings-container img {
            max-width: 200px;
            width: 50%;
            margin-bottom: 20px;
        }

        .no-bookings-container .heading-main12 {
            color: #2d6a4f;
            font-size: 1.2em;
            margin-bottom: 20px;
        }

        .no-bookings-container .login-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 12px 24px;
            background-color: #2d6a4f;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            transition: all 0.3s ease;
            font-size: 1em;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .no-bookings-container .login-btn:hover {
            background-color: #1f4d37;
            transform: translateY(-2px);
            box-shadow: 0 6px 8px rgba(0,0,0,0.15);
        }

        /* Appointment Status Styling */
        .appointment-status {
            display: inline-block;
            margin-top: 10px;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 0.8em;
            font-weight: bold;
        }

        .status-approved {
            background-color: #28a745;
            color: white;
        }

        .status-pending {
            background-color: #ffc107;
            color: #212529;
        }

        .status-unknown {
            background-color: #6c757d;
            color: white;
        }

        /* Added CSS for booking-full button state */
        .booking-full {
            background-color: #6c757d;
            color: white;
            cursor: not-allowed;
            opacity: 0.7;
        }

        .booking-full:hover {
            background-color: #6c757d;
            transform: none;
            box-shadow: none;
        }
    </style>
</head>
<body>
    <?php
    // Check if a session is already started to avoid the warning
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Check if the user is logged in and is a patient
    if (!isset($_SESSION["user"]) || empty($_SESSION["user"]) || $_SESSION['usertype'] != 'p') {
        header("location: ../login.php");
        exit;
    }

    $useremail = $_SESSION["user"];

    // Import the database connection
    include("../connection.php");

    // Fetch patient details securely using prepared statements
    $sqlmain = "SELECT * FROM patient WHERE pemail = ?";
    $stmt = $database->prepare($sqlmain);
    $stmt->bind_param("s", $useremail);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $userfetch = $result->fetch_assoc();
        $userid = $userfetch["pid"];
        $username = $userfetch["pname"];
    } else {
        // Handle the case where no patient is found
        $username = "Unknown User";
        $userid = 0;
    }


    if (isset($_SESSION["login_success"]) && !isset($_SESSION["alert_shown"])) {
        $userType = $_SESSION["user_type"] ?? "Patient";
        $userName = $_SESSION["user_name"] ?? $username;
        echo "
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Welcome to Mabayuan Health!',
                    text: 'You have successfully logged in as {$userType}',
                    icon: 'success',
                    confirmButtonText: 'Continue',
                    confirmButtonColor: '#2d6a4f'
                });
            });
        </script>";
        $_SESSION["alert_shown"] = true;
    }
    ?>


<div class="container">
<div class="hamburger" id="hamburger">
            <div class="bar"></div>
            <div class="bar"></div>
            <div class="bar"></div>
        </div>
        
        <!-- Menu Container -->
        <div class="menu" id="menu">
            <table class="menu-container" border="0">
                <tr>
                    <td style="padding:10px" colspan="2">
                        <table border="0" class="profile-container">
                        <tr>
                            <td width="30%" style="padding-left:20px">
                                <img src="../img/user.png" alt="" width="100%" style="border-radius:50%">
                            </td>
                            <td style="padding:0px;margin:0px;">
                                <p class="profile-title"><?php echo $username  ?></p>
                                <p class="profile-subtitle"><?php echo $patientEmail; ?></p> <!-- Display admin email here -->
                            </td>
                        </tr>

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
                        </table>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-dashbord menu-active menu-icon-dashbord-active" >
                        <a href="index.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">Home</p></a></div></a>
                    </td>
                </tr>
                <!-- <tr class="menu-row" >
                    <td class="menu-btn menu-icon-schedule">
                        <a href="schedule.php" class="non-style-link-menu"><div><p class="menu-text">Schedule</p></div></a>
                    </td>
                </tr> -->
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-appoinment">
                        <a href="appointment.php" class="non-style-link-menu"><div><p class="menu-text">Booking  History</p></a></div>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-settings">
                        <a href="settings.php" class="non-style-link-menu"><div><p class="menu-text">Settings</p></a></div>
                    </td>
                </tr>
                <!-- <tr class="menu-row" >
                    <td class="menu-btn menu-icon-patient">
                        <a href="patient.php" class="non-style-link-menu"><div><p class="menu-text">Patients</p></a></div>
                    </td>
                </tr> -->
            </table>
        </div>
        <script>
    const hamburger = document.getElementById('hamburger');
    const menu = document.getElementById('menu');
    hamburger.addEventListener('click', () => {
    console.log("Hamburger clicked!"); // Debugging line
    menu.classList.toggle('show');
    });

    </script>







        <div class="dash-body" style="margin-top: 15px">
            <table border="0" width="100%" style=" border-spacing: 0;margin:0;padding:0;" >
                        
            <tr class="date-container">
                                <td width="100%">
                                    <p style="font-size: 14px;color: rgb(119, 119, 119);padding: 0;margin: 0;">
                                    Today's Date
                                    </p>
                                    <p class="heading-sub12" style="margin: 0;">
                                        <?php 
                                            date_default_timezone_set('Asia/Manila');
                                            $today = date('Y-m-d');
                                            echo $today;

                                            $patientrow = $database->query("select  * from  patient;");
                                            $doctorrow = $database->query("select  * from  doctor;");
                                            $appointmentrow = $database->query("SELECT * FROM appointment WHERE appodate >= '$today' AND status = 'Approved' AND is_confirmed = 1");
                                            $schedulerow = $database->query("select  * from  schedule where scheduledate='$today';");
                                        ?>
                                    </p>
                                </td>
                            </tr>
                            
                <tr>
                    <td colspan="4" >
                        
                    <center>
                    <form action="" method="get" class="header-search">
                                    <input type="date" id="session_date" name="session_date" class="input-text header-searchbar" 
                                           value="<?php echo isset($_GET['session_date']) ? htmlspecialchars($_GET['session_date']) : ''; ?>">
                                    
                                    <input type="submit" value="Filter Sessions" class="btn-primary-soft btn button-icon btn-search" 
                                           style="padding-left: 25px;padding-right: 25px;padding-top: 10px;padding-bottom: 10px;">
                                    
                                    <?php if(isset($_GET['session_date'])): ?>
                                        <a href="index.php" class="btn-primary-soft btn button-icon btn-search" 
                                           style="padding-left: 15px;padding-right: 15px;padding-top: 10px;padding-bottom: 10px; margin-left: 10px;">
                                            Reset Filter
                                        </a>
                                    <?php endif; ?>
                                </form>
                    <table class="filter-container doctor-header patient-header" style="border: none;width:95%" border="0" >
                    <tr>
                        <td >
                            <h3>Welcome!</h3>
                            <h1><?php echo $username  ?>.</h1>
                    </tr>
                    <td class="nav-bar" >
                                


                                <?php
                                // Define current time and date with timezone
                                date_default_timezone_set('Asia/Manila');
                                $today = date('Y-m-d');
                                $current_datetime = date('Y-m-d H:i:s');
                                $current_time = date('H:i:s');

                                // Enhanced next appointment query
                                $next_appointment_query = "
                                    SELECT 
                                        schedule.scheduleid, 
                                        schedule.title, 
                                        schedule.scheduledate, 
                                        schedule.scheduletime, 
                                        doctor.docname,
                                        appointment.apponum,
                                        appointment.appoid
                                    FROM 
                                        schedule 
                                    JOIN 
                                        appointment ON schedule.scheduleid = appointment.scheduleid 
                                    JOIN 
                                        doctor ON schedule.docid = doctor.docid
                                    WHERE 
                                        appointment.pid = ? 
                                        AND appointment.is_confirmed = 1 
                                        AND schedule.scheduledate >= CURDATE()
                                    ORDER BY 
                                        schedule.scheduledate ASC, 
                                        schedule.scheduletime ASC 
                                    LIMIT 1
                                ";
                                $next_stmt = $database->prepare($next_appointment_query);
                                $next_stmt->bind_param("i", $userid);
                                $next_stmt->execute();
                                $next_result = $next_stmt->get_result();

                                if ($next_result->num_rows > 0) {
                                    $next_appointment = $next_result->fetch_assoc();
                                    
                                    $next_appo_title = htmlspecialchars($next_appointment['title']);
                                    $next_appo_number = intval($next_appointment['apponum']);
                                    $next_appo_date = date('F d, Y', strtotime($next_appointment['scheduledate']));
                                    $next_appo_time = date('h:i A', strtotime($next_appointment['scheduletime']));
                                    $next_appo_doctor = htmlspecialchars($next_appointment['docname']);

                                    echo "<div class='next-appointment-container'>
                                            <div class='next-appointment-header'>
                                                <h3 class='next-appointment-title'>Your Next Appointment</h3>
                                            </div>
                                            <div class='next-appointment-body'>
                                                <div class='next-appointment-details'>
                                                    <div class='appointment-number'>
                                                        <span class='badge'>#{$next_appo_number}</span>
                                                    </div>
                                                    <div class='appointment-info'>
                                                        <h4>{$next_appo_title}</h4>
                                                        <p>with Dr. {$next_appo_doctor}</p>
                                                        <small>{$next_appo_date} at {$next_appo_time}</small>
                                                    </div>
                                                </div>
                                              </div>
                                          </div>";
                                } else {
                                    echo "<div class='next-appointment-container no-appointments'>
                                            <div class='next-appointment-header'>
                                                <h3 class='next-appointment-title'>Your Next Appointment</h3>
                                            </div>
                                            <div class='next-appointment-body'>
                                                <div class='next-appointment-details'>
                                                    <p>No confirmed upcoming appointments.</p>
                                                </div>
                                            </div>
                                          </div>";
                                }
                                ?>
                    
                    </table>
                    </center>
                    
                </td>
                </tr>
                <tr>

                                    </center)







                                </td>
                                
                            </tr>
                        </table>
                    </td>
                    <td>


                            
                                    <p style="font-size: 20px;font-weight:600;padding-left: 40px;" class="anime">Available Sessions</p>
                                    <center>
                                        <div class="abc scroll" style="height: 500px;padding: 0;margin: 0;">
                                        <?php
                                        // Get current month and year
                                        $current_month = date('F Y');
                                        
                                        // Check if a specific date is selected
                                        $filter_date = isset($_GET['session_date']) ? $_GET['session_date'] : null;
                                        
                                        // Display month or filtered date
                                        if ($filter_date) {
                                            $formatted_date = date('F d, Y', strtotime($filter_date));
                                            echo "<h2 style='text-align: center; color: #2d6a4f; margin-bottom: 20px;'>Sessions on $formatted_date</h2>";
                                        } else {
                                            echo "<h2 style='text-align: center; color: #2d6a4f; margin-bottom: 20px;'>$current_month</h2>";
                                        }

                                        // Define days of the week in order
                                        $days_order = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
                                        ?>
                                        
                                        <?php
                                        $today = date('Y-m-d');
                                        $current_time = date('H:i:s'); // Define current_time variable before using it in the query
                                        $nextweek = date("Y-m-d", strtotime("+1 week"));

                                        // Check if a specific date is selected for filtering
                                        $filter_date = isset($_GET['session_date']) ? $_GET['session_date'] : null;

                                        // Prepare the base SQL query
                                        $sqlmain = "SELECT schedule.*, doctor.docname, 
                                                    (SELECT COUNT(*) FROM appointment WHERE scheduleid = schedule.scheduleid AND status = 'Approved' AND is_confirmed = 1) as booked_count,
                                                    DAYOFWEEK(schedule.scheduledate) as day_of_week,
                                                    DAYNAME(schedule.scheduledate) as day_name
                                                    FROM schedule 
                                                    INNER JOIN doctor ON schedule.docid = doctor.docid
                                                    WHERE 1=1";

                                        // Add date filtering conditions
                                        if ($filter_date) {
                                            // If a specific date is selected, show only sessions for that date
                                            $sqlmain .= " AND schedule.scheduledate = '$filter_date'";
                                        } else {
                                            // Default behavior: show sessions from today to next week
                                            $sqlmain .= " AND schedule.scheduledate >= '$today' 
                                                          AND schedule.scheduledate <= '$nextweek'
                                                          AND DAYOFWEEK(schedule.scheduledate) BETWEEN 2 AND 6
                                                          AND (schedule.scheduledate > '$today' OR 
                                                               (schedule.scheduledate = '$today' AND schedule.scheduletime > '$current_time'))";
                                        }

                                        // Complete the query
                                        $sqlmain .= " ORDER BY 
                                                        CASE day_name 
                                                            WHEN 'Monday' THEN 1 
                                                            WHEN 'Tuesday' THEN 2 
                                                            WHEN 'Wednesday' THEN 3 
                                                            WHEN 'Thursday' THEN 4 
                                                            WHEN 'Friday' THEN 5 
                                                        END,
                                                        schedule.scheduletime";

                                        $result = $database->query($sqlmain);

                                        if ($result->num_rows == 0) {
                                            echo '<div class="no-sessions">
                                                <img src="../img/notfound.svg" width="25%">
                                                <p class="heading-main12">No available sessions at the moment!</p>
                                                </div>';
                                        } else {
                                            // Group sessions by day of the week
                                            $sessions_by_day = [];
                                            
                                            while ($row = $result->fetch_assoc()) {
                                                $day_name = $row['day_name'];
                                                $sessions_by_day[$day_name][] = $row;
                                            }

                                            // Create a container for all days
                                            echo '<div class="sessions-container">';
                                            
                                            // Start days row
                                            echo '<div class="days-row">';
                                            
                                            // Iterate through days in order
                                            foreach ($days_order as $day_name) {
                                                if (isset($sessions_by_day[$day_name])) {
                                                    // Start day container
                                                    echo "<div class='day-sessions' style='flex: 1 0 30%; margin: 10px;'>";
                                                    echo "<h3 class='day-header'>$day_name</h3>";
                                                    
                                                    // Create table for sessions
                                                    echo "<table class='sessions-table'>";
                                                    echo "<thead>
                                                            <tr>
                                                                <th>Session Title</th>
                                                                <th>Doctor</th>
                                                                <th>Time</th>
                                                                <th>Slots</th>
                                                                <th>Action</th>
                                                            </tr>
                                                          </thead>";
                                                    echo "<tbody>";

                                                    // Sort sessions for this day by time
                                                    usort($sessions_by_day[$day_name], function($a, $b) {
                                                        return strtotime($a['scheduletime']) - strtotime($b['scheduletime']);
                                                    });

                                                    // Display sessions for this day
                                                    foreach ($sessions_by_day[$day_name] as $row) {
                                                        $scheduleid = $row["scheduleid"];
                                                        $title = $row["title"];
                                                        $docname = $row["docname"];
                                                        $scheduletime = $row["scheduletime"];
                                                        $nop = $row["available_slots"]; // Use available_slots instead of nop
                                                        $total_nop = $row["nop"]; // Keep total slots for display
                                                        $remaining_slots = $nop; // Directly use available_slots

                                                        // Determine button state
                                                        $button_state = "book-now";
                                                        $button_text = "Book Now";
                                                        $button_disabled = "";

                                                        if ($remaining_slots <= 0) {
                                                            $button_state = "session-full";
                                                            $button_text = "Session Full";
                                                            $button_disabled = "disabled";
                                                        }

                                                        // Check if session has reached 5 approved bookings
                                                        if ($row["booked_count"] >= 5) {
                                                            $button_state = "session-full";
                                                            $button_text = "Session Full";
                                                            $button_disabled = "disabled";
                                                        }

                                                        echo '<tr>
                                                        <td>' . substr($title, 0, 20) . '</td>
                                                        <td>' . substr($docname, 0, 24) . '</td>
                                                        <td>' . substr($scheduletime, 0, 5) . '</td>
                                                        <td>' . $remaining_slots . '/' . $total_nop . '</td>
                                                        <td>
                                                            <a href="booking.php?id=' . $scheduleid . '">
                                                                <button class="login-btn btn-primary-soft btn ' . $button_state . '" ' . $button_disabled . ' style="width:100%">
                                                                    ' . $button_text . '
                                                                </button>
                                                            </a>
                                                        </td>
                                                        </tr>';
                                                    }

                                                    echo "</tbody>";
                                                    echo "</table>";
                                                    echo "</div>"; // End day-sessions div
                                                }
                                            }

                                            echo '</div>'; // End days-row
                                            echo '</div>'; // End sessions-container
                                        }
                                        ?>
                                        </div>
                                    </center>

                                    <p style="font-size: 20px;font-weight:600;padding-left: 40px;" class="anime">Your Upcoming Booking</p>
                                    <center>
                                        <div class="abc scroll" style="height: 1000px;padding: 0;margin: 0;">
                                        <table width="85%" class="sub-table scrolldown" border="0" >
                                        <thead>
                                            
                                        <tr>
                                        <th class="table-headin">
                                                    
                                                
                                                    Appoint. Number
                                                    
                                                    </th>
                                                <th class="table-headin">
                                                    
                                                
                                                Session Title
                                                
                                                </th>
                                                
                                                <th class="table-headin">
                                                    Doctor
                                                </th>
                                                <th class="table-headin">
                                                    
                                                Scheduled Date & Time
                                                    
                                                </th>

                                                <th class="table-headin">
                                                    
                                                Booked For
                                                        
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        
                                            <?php
                                            $nextweek = date("Y-m-d", strtotime("+1 week"));
                                            $sqlmain = "SELECT *, IF(is_self = 0, 'For Myself', other_patient_name) AS patient_display FROM schedule 
                                                        INNER JOIN appointment ON schedule.scheduleid = appointment.scheduleid 
                                                        INNER JOIN patient ON patient.pid = appointment.pid 
                                                        INNER JOIN doctor ON schedule.docid = doctor.docid 
                                                        WHERE patient.pid = $userid 
                                                        AND schedule.scheduledate >= '$today' 
                                                        AND appointment.is_confirmed = 1
                                                        ORDER BY schedule.scheduledate ASC";
                                            $result = $database->query($sqlmain);

                                            if ($result->num_rows == 0) {
                                                echo '<tr>
                                                <td colspan="6" class="no-bookings-container">
                                                <img src="../img/notfound.svg" width="25%">
                                                
                                                <br>
                                                <p class="heading-main12">Nothing to show here!</p>
                                                <a href="schedule.php" class="login-btn btn-primary-soft btn">
                                                    Find a Schedule
                                                </a>
                                                </td>
                                                </tr>';
                                            } else {
                                                for ($x = 0; $x < $result->num_rows; $x++) {
                                                    $row = $result->fetch_assoc();
                                                    $scheduleid = $row["scheduleid"];
                                                    $title = $row["title"];
                                                    $apponum = $row["apponum"];
                                                    $docname = $row["docname"];
                                                    $scheduledate = $row["scheduledate"];
                                                    $scheduletime = $row["scheduletime"];
                                                    $patient_display = $row["patient_display"];

                                                    echo '<tr>
                                                    <td data-label="Appoint. Number" style="text-align:center;font-size:25px;font-weight:700;">&nbsp;' . $apponum . '</td>
                                                    <td data-label="Session Title" style="text-align:center;">&nbsp;' . substr($title, 0, 30) . '</td>
                                                    <td data-label="Doctor" style="text-align:center;">' . substr($docname, 0, 20) . '</td>
                                                    <td data-label="Scheduled Date & Time" style="text-align:center;">' . substr($scheduledate, 0, 10) . ' ' . substr($scheduletime, 0, 5) . '</td>
                                                    <td data-label="Booked For" style="text-align:center;">' . $patient_display . '</td>
                                                    </tr>';
                                                }
                                            }
                                            ?>
                 
                                            </tbody>
                
                                        </table>
                                        </div>
                                        </center>







                                </td>
                <tr>
            </table>
        </div>
    </div>


</body>
</html>