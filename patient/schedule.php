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
$query = "SELECT pemail FROM patient WHERE pemail = ?";  // Use 'pemail' as per the patient table schema

// Prepare a secure query using prepared statements
$stmt = $database->prepare($query);
$stmt->bind_param("s", $_SESSION["user"]);
$stmt->execute();
$result = $stmt->get_result();

// Check if query was successful and fetch the email
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $patientEmail = $row['pemail']; // Retrieve the patient's email
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
    <link rel="stylesheet" href="../css/animations.css">  
    <link rel="stylesheet" href="../css/main.css">  
    <link rel="stylesheet" href="../css/admin.css">
        
    <title>Sessions</title>
    <style>
        .popup{
            animation: transitionIn-Y-bottom 0.5s;
        }
        .sub-table{
            animation: transitionIn-Y-bottom 0.5s;
        }
</style>
</head>
<body>
    <?php

    //learn from w3schools.com


    if(isset($_SESSION["user"])){
        if(($_SESSION["user"])=="" or $_SESSION['usertype']!='p'){
            header("location: ../login.php");
        }else{
            $useremail=$_SESSION["user"];
        }

    }else{
        header("location: ../login.php");
    }
    

    //import database
    include("../connection.php");
    $sqlmain= "select * from patient where pemail=?";
    $stmt = $database->prepare($sqlmain);
    $stmt->bind_param("s",$useremail);
    $stmt->execute();
    $result = $stmt->get_result();
    $userfetch=$result->fetch_assoc();
    $userid= $userfetch["pid"];
    $username=$userfetch["pname"];


    //echo $userid;
    //echo $username;
    
    date_default_timezone_set('Asia/Kolkata');

    $today = date('Y-m-d');


 //echo $userid;
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
                                    <a href="../logout.php" ><input type="button" value="Log out" class="logout-btn btn-primary-soft btn"></a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            <?php
                // Get the current script name
                $currentPage = basename($_SERVER['PHP_SELF']);
                ?>

                <tr class="menu-row">
                    <td class="menu-btn menu-icon-dashbord <?php if ($currentPage == 'index.php') echo 'menu-active menu-icon-dashbord-active'; ?>">
                        <a href="index.php" class="non-style-link-menu <?php if ($currentPage == 'index.php') echo 'non-style-link-menu-active'; ?>">
                            <div><p class="menu-text">Dashboard</p></div>
                        </a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-doctor <?php if ($currentPage == 'doctors.php') echo 'menu-active menu-icon-doctor-active'; ?>">
                        <a href="doctors.php" class="non-style-link-menu <?php if ($currentPage == 'doctors.php') echo 'non-style-link-menu-active'; ?>">
                            <div><p class="menu-text">Doctors</p></div>
                        </a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-schedule <?php if ($currentPage == 'schedule.php') echo 'menu-active menu-icon-schedule-active'; ?>">
                        <a href="schedule.php" class="non-style-link-menu <?php if ($currentPage == 'schedule.php') echo 'non-style-link-menu-active'; ?>">
                            <div><p class="menu-text">Schedule</p></div>
                        </a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-appoinment <?php if ($currentPage == 'appointment.php') echo 'menu-active menu-icon-appoinment-active'; ?>">
                        <a href="appointment.php" class="non-style-link-menu <?php if ($currentPage == 'appointment.php') echo 'non-style-link-menu-active'; ?>">
                            <div><p class="menu-text">Appointment</p></div>
                        </a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-appoinment <?php if ($currentPage == 'setttings.php') echo 'menu-active menu-icon-appoinment-active'; ?>">
                        <a href="settings.php" class="non-style-link-menu <?php if ($currentPage == 'settings.php') echo 'non-style-link-menu-active'; ?>">
                            <div><p class="menu-text">Settings</p></div>
                        </a>
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
        <?php

                $sqlmain= "select * from schedule inner join doctor on schedule.docid=doctor.docid where schedule.scheduledate>='$today'  order by schedule.scheduledate asc";
                $sqlpt1="";
                $insertkey="";
                $q='';
                $searchtype="All";
                        if($_POST){
                        //print_r($_POST);
                        
                        if(!empty($_POST["search"])){
                            /*TODO: make and understand */
                            $keyword=$_POST["search"];
                            $sqlmain= "select * from schedule inner join doctor on schedule.docid=doctor.docid where schedule.scheduledate>='$today' and (doctor.docname='$keyword' or doctor.docname like '$keyword%' or doctor.docname like '%$keyword' or doctor.docname like '%$keyword%' or schedule.title='$keyword' or schedule.title like '$keyword%' or schedule.title like '%$keyword' or schedule.title like '%$keyword%' or schedule.scheduledate like '$keyword%' or schedule.scheduledate like '%$keyword' or schedule.scheduledate like '%$keyword%' or schedule.scheduledate='$keyword' )  order by schedule.scheduledate asc";
                            //echo $sqlmain;
                            $insertkey=$keyword;
                            $searchtype="Search Result : ";
                            $q='"';
                        }

                    }


                $result= $database->query($sqlmain)


                ?>
                  












                  
        <div class="dash-body">
            <table border="0" width="100%" style=" border-spacing: 0;margin:0;padding:0;margin-top:25px; ">
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

                        $patientrow = $database->query("select * from patient;");
                        $doctorrow = $database->query("select * from doctor;");
                        $appointmentrow = $database->query("select * from appointment where appodate>='$today';");

                        // Updated query with JOIN to fetch doctor details with schedule
                        $schedulerow = $database->query("SELECT schedule.scheduleid, schedule.title, schedule.scheduledate, schedule.scheduletime, doctor.docname 
                        FROM schedule 
                        JOIN doctor ON schedule.docid = doctor.docid 
                        WHERE schedule.scheduledate = '$today'");

?>

<tr>
    <td colspan="2" class="nav-bar">
        <form action="doctors.php" method="post" class="header-search">
            <input type="search" name="search" class="input-text header-searchbar" placeholder="Search Doctor name or Email" list="doctors">&nbsp;&nbsp;

            <?php
            echo '<datalist id="doctors">';
            $list11 = $database->query("select docname, docemail from doctor;");
            for ($y = 0; $y < $list11->num_rows; $y++) {
                $row00 = $list11->fetch_assoc();
                $d = $row00["docname"];
                $c = $row00["docemail"];
                echo "<option value='$d'><br/>";
                echo "<option value='$c'><br/>";
            }
            echo ' </datalist>';
            ?>

            <input type="Submit" value="Search" class="login-btn btn-primary-soft btn" style="padding-left: 25px;padding-right: 25px;padding-top: 10px;padding-bottom: 10px;">
        </form>
    </td>
</tr>

<tr>
    <td colspan="4">
        <center>
        <div class="abc scroll">
    <table width="100%" class="sub-table scrolldown" border="0" style="padding: 50px;border:none">
        <tbody>
            <?php
            // Check if there are any schedules
            if ($schedulerow->num_rows == 0) {
                echo '<tr>
                <td colspan="4">
                <br><br><br><br>
                <center>
                <img src="../img/notfound.svg" width="25%">
                <br>
                <p class="heading-main12" style="margin-left: 45px;font-size:20px;color:rgb(49, 49, 49)">We couldn\'t find anything related to your keywords !</p>
                <a class="non-style-link" href="schedule.php"><button class="login-btn btn-primary-soft btn" style="display: flex;justify-content: center;align-items: center;margin-left:20px;">&nbsp; Show all Sessions &nbsp;</button></a>
                </center>
                <br><br><br><br>
                </td>
                </tr>';
            } else {
                // Iterate through the schedules
                for ($x = 0; $x < ($schedulerow->num_rows); $x++) {
                    echo "<tr>";
                    for ($q = 0; $q < 3; $q++) {
                        $row = $schedulerow->fetch_assoc();
                        if (!isset($row)) {
                            break;
                        }
                        $scheduleid = $row["scheduleid"];
                        $title = $row["title"];
                        $docname = $row["docname"];
                        $scheduledate = $row["scheduledate"];
                        $scheduletime = $row["scheduletime"];

                        if ($scheduleid == "") {
                            break;
                        }

                        // Assuming the user is logged in and the email is in session
                        $user_email = $_SESSION['user'];

                        // Check if the user has already booked this schedule
                        $booking_check = $database->query("SELECT * FROM appointment WHERE pid = (SELECT pid FROM patient WHERE pemail = '$user_email') AND scheduleid = '$scheduleid'");

                        // Get the number of patients already booked for this session
                        $patient_count = $database->query("SELECT COUNT(*) AS patient_count FROM appointment WHERE scheduleid = '$scheduleid'")->fetch_assoc();
                        $max_patients = 10; // Set the max patients limit per session (you can adjust this value)

                        // Check if the session has reached the max number of patients
                        if ($patient_count['patient_count'] >= $max_patients) {
                            $button_disabled = '<button class="login-btn btn-primary-soft btn" style="padding-top:11px;padding-bottom:11px;width:100%" disabled><font class="tn-in-text">Session Full</font></button>';
                            $popup_message = "Booking has reached its maximum number of patients.";
                            echo "<script type='text/javascript'>alert('$popup_message');</script>";
                        } else {
                            // If the user has already booked, disable the button
                            if ($booking_check->num_rows > 0) {
                                $button_disabled = '<button class="login-btn btn-primary-soft btn" style="padding-top:11px;padding-bottom:11px;width:100%" disabled><font class="tn-in-text">Already Booked</font></button>';
                            } else {
                                $button_disabled = '<a href="booking.php?id=' . $scheduleid . '"><button class="login-btn btn-primary-soft btn" style="padding-top:11px;padding-bottom:11px;width:100%"><font class="tn-in-text">Book Now</font></button></a>';
                            }
                        }

                        // Display schedule and booking button
                        echo '
                        <td style="width: 25%;">
                            <div class="dashboard-items search-items">
                                <div style="width:100%">
                                    <div class="h1-search">
                                        ' . substr($title, 0, 21) . '
                                    </div><br>
                                    <div class="h3-search">
                                        ' . substr($docname, 0, 30) . '
                                    </div>
                                    <div class="h4-search">
                                        ' . $scheduledate . '<br>Starts: <b>@' . substr($scheduletime, 0, 5) . '</b>
                                    </div>
                                    <br>
                                    ' . $button_disabled . '
                                </div>
                            </div>
                        </td>';
                    }
                    echo "</tr>";
                }
            }
            ?>
        </tbody>
    </table>
</div>

    </div>

    </div>

</body>
</html>
