<?php
session_start();

if (isset($_SESSION["user"])) {
    if (($_SESSION["user"]) == "" or $_SESSION['usertype'] != 'd') {
        header("location: ../login.php");
        exit();
    } else {
        $useremail = $_SESSION["user"];
    }
} else {
    header("location: ../login.php");
    exit();
}

// Import database
include("../connection.php");
$userrow = $database->query("SELECT * FROM doctor WHERE docemail = '$useremail'");
$userfetch = $userrow->fetch_assoc();
$userid = $userfetch["docid"];
$username = $userfetch["docname"];
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
        .dashbord-tables, .doctor-heade {
            animation: transitionIn-Y-over 0.5s;
        }
        .filter-container {
            animation: transitionIn-Y-bottom 0.5s;
        }
        .sub-table, #anim {
            animation: transitionIn-Y-bottom 0.5s;
        }
        .doctor-heade {
            animation: transitionIn-Y-over 0.5s;
        }
    </style>
</head>
<body>

<?php
// Check if login_success action is passed in the URL
if (isset($_GET['action']) && $_GET['action'] == 'login_success' && !isset($_SESSION['login_alert_shown'])) {
    // Set the session variable to indicate the alert has been shown
    $_SESSION['login_alert_shown'] = true;

    // Display SweetAlert for successful login
    echo "
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>
        setTimeout(function() {
            Swal.fire({
                title: 'Login Successful',
                text: 'Welcome Dr. " . $username . "!',
                icon: 'success',
                confirmButtonText: 'OK'
            });
        }, 250); // Delay for 250ms
    </script>
    ";
}
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
                                    <p class="profile-title"><?php echo substr($username,0,13)  ?></p>
                                    <p class="profile-subtitle"><?php echo substr($useremail,0,22)  ?></p>
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
                        <a href="index.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">Dashboard</p></a></div></a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-appoinment">
                        <a href="appointment.php" class="non-style-link-menu"><div><p class="menu-text">Appointments</p></a></div>
                    </td>
                </tr>
                
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-session">
                        <a href="schedule.php" class="non-style-link-menu"><div><p class="menu-text">Sessions</p></div></a>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-patient">
                        <a href="patient.php" class="non-style-link-menu"><div><p class="menu-text">Patients</p></a></div>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-settings">
                        <a href="settings.php" class="non-style-link-menu"><div><p class="menu-text">Settings</p></a></div>
                    </td>
                </tr>
                
            </table>
        </div>
        <div class="dash-body" style="margin-top: 15px">
            <table border="0" width="100%" style=" border-spacing: 0;margin:0;padding:0;" >
                        
                        <tr >
                            
                            <td colspan="1" class="nav-bar" >
                            <p style="font-size: 23px;padding-left:12px;font-weight: 600;margin-left:20px;">Dashboard</p>
                          
                            </td>
                            <td width="25%">

                            </td>
                            <td width="15%">
                                <p style="font-size: 14px;color: rgb(119, 119, 119);padding: 0;margin: 0;text-align: right;">
                                    Today's Date
                                </p>
                                <p class="heading-sub12" style="padding: 0;margin: 0;">
                                    <?php 
                                date_default_timezone_set('Asia/Manila');
        
                                $today = date('Y-m-d');
                                echo $today;


                                $patientrow = $database->query("select  * from  patient;");
                                $doctorrow = $database->query("select  * from  doctor;");
                                $appointmentrow = $database->query("select  * from  appointment where appodate>='$today';");
                                $schedulerow = $database->query("select  * from  schedule where scheduledate='$today';");


                                ?>
                                </p>
                            </td>
                            <td width="10%">
                                <button  class="btn-label"  style="display: flex;justify-content: center;align-items: center;"><img src="../img/calendar.svg" width="100%"></button>
                            </td>
        
        
                        </tr>
                <tr>
                    <td colspan="4" >
                        
                    <center>
                    <table class="filter-container doctor-header" style="border: none;width:95%" border="0" >
                    <tr>
                        <td >
                            <h3>Welcome!</h3>
                            <h1><?php echo $username  ?>.</h1>
                            <p>Thanks for joining with us. We are always trying to get you a complete service<br>
                            You can view your daily schedule, Reach Patients Appointment at home!<br><br>
                            </p>
                            <a href="appointment.php" class="non-style-link"><button class="btn-primary btn" style="width:30%">View Appointments</button></a>
                            <br>
                            <br>
                        </td>
                    </tr>
                    </table>
                    </center>
                    
                </td>
                </tr>
                <tr>
                    <td colspan="4">
                        <table border="0" width="100%"">
                            <tr>
                                <td width="50%">

                                    




                                    <center>
                                    <table class="filter-container" style="border: none;" border="0">
                            <tr>
                            <td colspan="4">
                    <center>
                        <table class="filter-container" style="border: none;" border="0">
                            <tr>
                                <td colspan="4">
                                    <p style="font-size: 20px; font-weight: 600; padding-left: 12px;">Status</p>
                                </td>
                            </tr>
                            <tr class="status-report">
                                <td class="stats1">
                                    <a href="doctors.php" class="non-style-link">
                                        <div class="dashboard-items">
                                            <div>
                                                <div class="h1-dashboard"><?php echo $doctorrow->num_rows; ?></div><br>
                                                <div class="h3-dashboard">Doctors</div>
                                            </div>
                                            <div class="btn-icon-back dashboard-icons" style="background-image: url('../img/icons/doctors-hover.svg');"></div>
                                        </div>
                                    </a>
                                </td>
                                <td class="stats1">
                                    <a href="patient.php" class="non-style-link">
                                        <div class="dashboard-items">
                                            <div>
                                                <div class="h1-dashboard"><?php echo $patientrow->num_rows; ?></div><br>
                                                <div class="h3-dashboard">Patients</div>
                                            </div>
                                            <div class="btn-icon-back dashboard-icons" style="background-image: url('../img/icons/patients-hover.svg');"></div>
                                        </div>
                                    </a>
                                </td>
                                <td class="stats2">
                                    <a href="appointment.php" class="non-style-link">
                                        <div class="dashboard-items">
                                            <div>
                                                <div class="h1-dashboard"><?php echo $appointmentrow->num_rows; ?></div><br>
                                                <div class="h3-dashboard">New Booking</div>
                                            </div>
                                            <div class="btn-icon-back dashboard-icons" style="background-image: url('../img/icons/book-hover.svg');"></div>
                                        </div>
                                    </a>
                                </td>
                                <td class="stats2">
                                    <a href="schedule.php" class="non-style-link">
                                        <div class="dashboard-items">
                                            <div>
                                                <div class="h1-dashboard"><?php echo $schedulerow->num_rows; ?></div><br>
                                                <div class="h3-dashboard">Current Session</div>
                                            </div>
                                            <div class="btn-icon-back dashboard-icons" style="background-image: url('../img/icons/session-iceblue.svg');"></div>
                                        </div>
                                    </a>
                                </td>
                            </tr>
                        </table>
                    </center>
                </td>
            </tr>
            <tr>
                <td colspan="4">
                    <table width="100%" border="0" class="dashbord-tables">
                        <tr>
                            <td class="responsive-td">
                                <p class="upcoming-appointments-title">
                                    Upcoming Appointments until Next <?php echo date("l", strtotime("+1 week")); ?>
                                </p>
                                <p class="upcoming-appointments-description">
                                    Here's Quick access to Upcoming Appointments until 7 days<br>
                                    More details available in @Appointment section.
                                </p>
                            </td>
                            <td class="responsive-td" width="50%">
                                <center>
                                    <div class="abc" style="max-height: 80vh; overflow-y: auto;">
                                        <table width="100%" class="sub-table scrolldown" border="0">
                                            <thead>
                                                <tr>
                                                    <th class="table-headin">Appointment number</th>
                                                    <th class="table-headin">Patient name</th>
                                                    <th class="table-headin">Doctor</th>
                                                    <th class="table-headin">Session</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $nextweek = date("Y-m-d", strtotime("+1 week"));
                                                $today = date("Y-m-d");
                                                $sqlmain = "SELECT appointment.appoid, schedule.scheduleid, schedule.title, doctor.docname, patient.pname, schedule.scheduledate, schedule.scheduletime, appointment.apponum, appointment.appodate 
                                                            FROM schedule 
                                                            INNER JOIN appointment ON schedule.scheduleid = appointment.scheduleid 
                                                            INNER JOIN patient ON patient.pid = appointment.pid 
                                                            INNER JOIN doctor ON schedule.docid = doctor.docid  
                                                            WHERE schedule.scheduledate >= '$today' AND schedule.scheduledate <= '$nextweek' 
                                                            AND appointment.is_confirmed = 1
                                                            AND (schedule.scheduledate > '$today' OR (schedule.scheduledate = '$today' AND schedule.scheduletime > CURRENT_TIME)) 
                                                            ORDER BY schedule.scheduledate DESC";
                                                    
                                                $result = $database->query($sqlmain);

                                                if ($result->num_rows == 0) {
                                                    echo '<tr><td colspan="4">
                                                              <center>
                                                              <img src="../img/notfound.svg" width="25%">
                                                              <p class="heading-main12" style="font-size:20px;color:rgb(49, 49, 49)">We couldn\'t find anything related to your keywords!</p>
                                                              <a class="non-style-link" href="appointment.php">
                                                                  <button class="login-btn btn-primary-soft btn">Find an Appointment Section</button>
                                                              </a>
                                                              </center>
                                                          </td></tr>';
                                                } else {
                                                    while ($row = $result->fetch_assoc()) {
                                                        $apponum = $row["apponum"];
                                                        $pname = $row["pname"];
                                                        $docname = $row["docname"];
                                                        $title = $row["title"];
                                                        echo '<tr>
                                                              <td style="text-align:center;font-size:23px;font-weight:500;color:var(--btnnicetext);padding:15px;">' . $apponum . '</td>
                                                              <td style="text-align:center;font-weight:600;">' . substr($pname, 0, 25) . '</td>
                                                              <td style="text-align:center;font-weight:600;">'. substr($docname, 0, 25) . '</td>
                                                              <td style="text-align:center;font-weight:600;">'. substr($title, 0, 15) . '</td>
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
                    </table>
                </td>
            </tr>
            <tr>
                <td class="responsive-td">
                    <p class="upcoming-session-title">
                        Upcoming Sessions until Next <?php echo date("l", strtotime("+1 week")); ?>
                    </p>
                    <p class="upcoming-session-description">
                        Here's Quick access to Upcoming Sessions that are scheduled until 7 days
                        Add, Remove, and many features are available in the @Schedule section.
                    </p>
                </td>
                <td class="responsive-td" width="50%" style="margin-top: 20px;">
                    <center>
                        <div class="abc scroll" style="height: 200px; padding: 0; margin: 0;">
                            <table width="85%" class="sub-table scrolldown" border="0">
                                <thead>
                                    <tr>
                                        <th class="table-headin">Session Title</th>
                                        <th class="table-headin">Doctor</th>
                                        <th class="table-headin">Scheduled Date & Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sqlmain = "SELECT schedule.scheduleid, schedule.title, doctor.docname, schedule.scheduledate, schedule.scheduletime 
                                                FROM schedule 
                                                INNER JOIN doctor ON schedule.docid = doctor.docid  
                                                WHERE schedule.scheduledate >= '$today' AND schedule.scheduledate <= '$nextweek' 
                                                ORDER BY schedule.scheduledate DESC"; 
                                    $result = $database->query($sqlmain);

                                    if ($result->num_rows == 0) {
                                        echo '<tr><td colspan="4">
                                                  <center>
                                                  <img src="../img/notfound.svg" width="25%">
                                                  <p class="heading-main12" style="font-size:20px;color:rgb(49, 49, 49)">We are unable find anything related to your keywords!</p>
                                                  <a class="non-style-link" href="schedule.php">
                                                      <button class="login-btn btn-primary-soft btn">Direct to Session Section</button>
                                                  </a>
                                                  </center>
                                              </td></tr>';
                                    } else {
                                        while ($row = $result->fetch_assoc()) {
                                            $title = $row["title"];
                                            $docname = $row["docname"];
                                            $scheduledate = $row["scheduledate"];
                                            $scheduletime = $row["scheduletime"];
                                            echo '<tr>
                                                  <td style="text-align:center;font-weight:600;padding:15px;">' . substr($title, 0, 30) . '</td>
                                                  <td style="text-align:center;font-weight:600;">'. substr($docname, 0, 20) . '</td>
                                                  <td style="text-align:center;">' . substr($scheduledate, 0, 10) . ' ' . substr($scheduletime, 0, 5) . '</td>
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
        </table>
    </div>
</div>
</body>
</html>