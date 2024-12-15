<?php

// Start the session to check for user login
session_start();

if(isset($_SESSION["user"])){
    if(($_SESSION["user"])=="" or $_SESSION['usertype']!='a'){
        header("location: ../login.php");
    }
}else{
    header("location: ../login.php");
}

// Include the database connection file
include("../connection.php");  // Make sure this path is correct

// Assuming $adminEmail is fetched from the database
$query = "SELECT aemail FROM admin WHERE aemail = '" . $_SESSION["user"] . "'";  // Assuming you are storing email in session
$result = mysqli_query($database, $query);  // Use $database from the connection.php file

// Check if query was successful and fetch the email
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $adminEmail = $row['aemail'];
} else {
    // Handle error if query fails
    echo "Error fetching email: " . mysqli_error($database);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/animations.css">  
    <link rel="stylesheet" href="../css/main.css">  
    <link rel="stylesheet" href="../css/admin.css">
        
    <title>Dashboard</title>
    <style>
        .dashbord-tables{
            animation: transitionIn-Y-over 0.5s;
        }
        .filter-container{
            animation: transitionIn-Y-bottom  0.5s;
        }
        .sub-table{
            animation: transitionIn-Y-bottom 0.5s;
        }
    </style>
</head>
<body>
<?php

// Check if a session is already started to avoid the warning
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in and has the correct user type
if(isset($_SESSION["user"])){
    if(($_SESSION["user"])=="" or $_SESSION['usertype']!='a'){
        header("location: ../login.php");
    }

}else{
    header("location: ../login.php");
}

// Import the database connection
include("../connection.php");

?>

<div class="container">
        <!-- Hamburger Icon  -->
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
                                <p class="profile-title">Administrator</p>
                                <p class="profile-subtitle"><?php echo $adminEmail; ?></p> <!-- Display admin email here -->
                            </td>
                        </tr>

                            <tr>
                                <td colspan="2">
                                    <a href="../logout.php" ><input type="button" value="Log out" class="logout-btn btn-primary-soft btn"></a>
                                </td>
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

        <!-- Main Dashboard Content -->
        


        <div class="dash-body" style="margin-top: 15px">
            <table border="0" width="100%" style=" border-spacing: 0;margin:0;padding:0;" >
                        
            <tr >       
                <tr class="date-container">
                    <td width="100%">
                        <p style="font-size: 14px;color: rgb(119, 119, 119);padding: 0;margin: 0;">
                            Today's Date
                        </p>
                    <p class="heading-sub12" style="margin: 0;">
                
                <?php 
                    date_default_timezone_set('Asia/Kolkata');
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
                                    <!-- <button class="btn-label">
                                        <img src="../img/calendar.svg" width="100%">
                                    </button> -->
                </td>

            </tr>
                <td colspan="2" class="nav-bar" >
                                
                                <form action="doctors.php" method="post" class="header-search">
        
                                    <input type="search" name="search" class="input-text header-searchbar" placeholder="Search Doctor name or Email" list="doctors">&nbsp;&nbsp;
                                    
                                    <?php
                                        echo '<datalist id="doctors">';
                                        $list11 = $database->query("select  docname,docemail from  doctor;");
        
                                        for ($y=0;$y<$list11->num_rows;$y++){
                                            $row00=$list11->fetch_assoc();
                                            $d=$row00["docname"];
                                            $c=$row00["docemail"];
                                            echo "<option value='$d'><br/>";
                                            echo "<option value='$c'><br/>";
                                        };
        
                                    echo ' </datalist>';
                                    ?>
                                    
                               
                                    <input type="Submit" value="Search" class="login-btn btn-primary-soft btn" style="padding-left: 25px;padding-right: 25px;padding-top: 10px;padding-bottom: 10px;">
                                
                                </form>
                                
                            </td>
                
                <tr>
                        
                    <tr>
                        <td colspan="4">
                        
                        <center>
                        <table class="filter-container" style="border: none;" border="0">
                            <tr>
                                <td colspan="4">
                                    <p style="font-size: 20px;font-weight:600;padding-left: 12px;">Status</p>
                                </td>
                            </tr>

                            <tr class="status-report">
                                <td class="stats1">
                                    <a href="doctors.php" class="non-style-link">
                                        <div class="dashboard-items">
                                            <div>
                                                <div class="h1-dashboard">
                                                    <?php echo $doctorrow->num_rows; ?>
                                                </div><br>
                                                <div class="h3-dashboard">
                                                    Doctors &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                </div>
                                            </div>
                                            <div class="btn-icon-back dashboard-icons" style="background-image: url('../img/icons/doctors-hover.svg');"></div>
                                        </div>
                                    </a>
                            </td>

                                <td class="stats1">
                                    <a href="patient.php" class="non-style-link">
                                        <div class="dashboard-items">
                                            <div>
                                                <div class="h1-dashboard">
                                                    <?php echo $patientrow->num_rows; ?>
                                                </div><br>
                                                <div class="h3-dashboard">
                                                    Patients &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                </div>
                                            </div>
                                            <div class="btn-icon-back dashboard-icons" style="background-image: url('../img/icons/patients-hover.svg');"></div>
                                        </div>
                                    </a>
                            </td>


                                <td class="stats2">
                                    <a href="appointment.php" class="non-style-link">
                                        <div class="dashboard-items">
                                            <div>
                                                <div class="h1-dashboard">
                                                    <?php echo $appointmentrow->num_rows; ?>
                                                </div><br>
                                                <div class="h3-dashboard">
                                                    New Booking &nbsp;&nbsp;
                                                </div>
                                            </div>
                                            <div class="btn-icon-back dashboard-icons" style="background-image: url('../img/icons/book-hover.svg');"></div>
                                        </div>
                                    </a>
                            </td>

                                <td class="stats2">
                                    <a href="schedule.php" class="non-style-link">
                                        <div class="dashboard-items">
                                            <div>
                                                <div class="h1-dashboard">
                                                    <?php echo $schedulerow->num_rows; ?>
                                                </div><br>
                                                <div class="h3-dashboard">
                                                    All Sessions
                                                </div>
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
                        Upcoming Appointments until Next <?php echo date("l",strtotime("+1 week")); ?>
                    </p>
                    <p class="upcoming-appointments-description">
                        Here's Quick access to Upcoming Appointments until 7 days<br>
                        More details available in @Appointment section.
                    </p>
                   
                </td>

                <td class="responsive-td" width="50%">
                    <center>
                        <div class="abc scroll" style="height: 200px;">
                            <table width="85%" class="sub-table scrolldown" border="0">
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
                                    $sqlmain = "SELECT appointment.appoid, schedule.scheduleid, schedule.title, doctor.docname, patient.pname, schedule.scheduledate, schedule.scheduletime, appointment.apponum, appointment.appodate 
                                                FROM schedule 
                                                INNER JOIN appointment ON schedule.scheduleid = appointment.scheduleid 
                                                INNER JOIN patient ON patient.pid = appointment.pid 
                                                INNER JOIN doctor ON schedule.docid = doctor.docid  
                                                WHERE schedule.scheduledate >= '$today' AND schedule.scheduledate <= '$nextweek' 
                                                
                                                ORDER BY schedule.scheduledate DESC";
                                                
                                    $result = $database->query($sqlmain);

                                    if ($result->num_rows == 0) {
                                        echo '<tr><td colspan="4">
                                                  <center>
                                                  <img src="../img/notfound.svg" width="25%">
                                                  <p class="heading-main12" style="font-size:20px;color:rgb(49, 49, 49)">We couldn\'t find anything related to your keywords!</p>
                                                  <a class="non-style-link" href="appointment.php">
                                                      <button class="login-btn btn-primary-soft btn">Show all Appointments</button>
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
                                                  <td style="text-align:center;font-size:23px;font-weight:500;color:var(--btnnicetext);padding:20px;">' . $apponum . '</td>
                                                  <td style="font-weight:600;">' . substr($pname, 0, 25) . '</td>
                                                  <td style="font-weight:600;">' . substr($docname, 0, 25) . '</td>
                                                  <td>' . substr($title, 0, 15) . '</td>
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
            <tr>
                <td class="responsive-td">
                    <p class="upcoming-session-title">
                        Upcoming Sessions until Next <?php echo date("l",strtotime("+1 week")); ?>
                    </p>
                    <p class="upcoming-session-description">
                        Here's Quick access to Upcoming Sessions that are scheduled until 7 days
                        Add, Remove, and many features are available in the @Schedule section.
                    </p>
                </td>
                <td class="responsive-td" width="50%" style="padding: 0;">
                    <center>
                        <div class="abc scroll" style="height: 200px;padding: 0;margin: 0;">
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
                                                  <p class="heading-main12" style="font-size:20px;color:rgb(49, 49, 49)">We couldn\'t find anything related to your keywords!</p>
                                                  <a class="non-style-link" href="schedule.php">
                                                      <button class="login-btn btn-primary-soft btn">Show all Sessions</button>
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
                                                  <td style="padding:20px;">' . substr($title, 0, 30) . '
                                                  </td>
                                                  <td>' . substr($docname, 0, 20) . '
                                                  </td>
                                                  <td style="text-align:center;">' . 
                                                  substr($scheduledate, 0, 10) . ' 
                                                  ' . substr($scheduletime, 0, 5) . '
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
            
            

        </table>
    </td>
    
    
</tr>


                        </table>
                        </center>
                        </td>
                </tr>
            </table>
        </div>
    </div>
    </div>
    </div>
</body>
</html>