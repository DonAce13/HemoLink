<?php
// Start session and check user authentication
session_start();

if (isset($_SESSION["user"])) {
    if ($_SESSION["user"] == "" || $_SESSION['usertype'] != 'p') {
        header("Location: ../login.php");
        exit;
    } else {
        $useremail = $_SESSION["user"];
    }
} else {
    header("Location: ../login.php");
    exit;
}

// Import database connection
include("../connection.php");

// Query to get patient details
$sqlmain = "SELECT * FROM patient WHERE pemail=?";
$stmt = $database->prepare($sqlmain);
$stmt->bind_param("s", $useremail);
$stmt->execute();
$userrow = $stmt->get_result();



if ($userrow->num_rows > 0) {
    $userfetch = $userrow->fetch_assoc();
    $userid = $userfetch["pid"];
    $username = $userfetch["pname"];
    $patientEmail = $userfetch["pemail"]; // Assign patient email here
} else {
    echo "Error: Patient data not found.";
    exit;
}

// PHP handling for appointment cancellation
if (isset($_GET['action']) && $_GET['action'] == 'drop') {
    $appoid = $_GET['id'];

    // Delete the booking from the schedule
    $sql = "DELETE FROM appointment WHERE appoid=?";
    $stmt = $database->prepare($sql);
    $stmt->bind_param('i', $appoid);
    $stmt->execute();

    // Redirect to show confirmation
    header("Location: appointment?action=canceled&id=$appoid");
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
    <link rel="stylesheet" href="../css/main.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../css/admin.css">
        
    <title>Appointments</title>
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



    //echo $userid;
    //echo $username;


    //TODO
    $sqlmain= "select appointment.appoid,schedule.scheduleid,schedule.title,doctor.docname,patient.pname,schedule.scheduledate,schedule.scheduletime,appointment.apponum,appointment.appodate from schedule inner join appointment on schedule.scheduleid=appointment.scheduleid inner join patient on patient.pid=appointment.pid inner join doctor on schedule.docid=doctor.docid  where  patient.pid=$userid ";

    if($_POST){
        //print_r($_POST);
        


        
        if(!empty($_POST["scheduledate"])){
            $scheduledate=$_POST["scheduledate"];
            $sqlmain.=" and schedule.scheduledate='$scheduledate' ";
        };

    

        //echo $sqlmain;

    }

    $sqlmain.="order by appointment.appodate  asc";
    $result= $database->query($sqlmain);
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
                    <td class="menu-btn menu-icon-settings <?php if ($currentPage == 'settings.php') echo 'menu-active menu-icon-appoinment-active'; ?>">
                        <a href="settings.php" class="non-style-link-menu <?php if ($currentPage == 'settings.php') echo 'non-style-link-menu-active'; ?>">
                            <div><p class="menu-text">Settings</p></div>
                        </a>
                    </td>
                </tr>
                
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
        <div class="dash-body">
            <table border="0" width="100%" style=" border-spacing: 0;margin:0;padding:0;margin-top:25px; ">
                <tr >
                    <!-- <td width="13%" >
                    <a href="appointment.php" ><button  class="login-btn btn-primary-soft btn btn-icon-back"  style="padding-top:11px;padding-bottom:11px;margin-left:20px;width:125px"><font class="tn-in-text">Back</font></button></a>
                    </td> -->
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
                        $appointmentrow = $database->query("select  * from  appointment where appodate>='$today';");
                        $schedulerow = $database->query("select  * from  schedule where scheduledate='$today';");
                    ?>

                    </p>
                </td>


                </tr>
               
                <!-- <tr>
                    <td colspan="4" >
                        <div style="display: flex;margin-top: 40px;">
                        <div class="heading-main12" style="margin-left: 45px;font-size:20px;color:rgb(49, 49, 49);margin-top: 5px;">Schedule a Session</div>
                        <a href="?action=add-session&id=none&error=0" class="non-style-link"><button  class="login-btn btn-primary btn button-icon"  style="margin-left:25px;background-image: url('../img/icons/add.svg');">Add a Session</font></button>
                        </a>
                        </div>
                    </td>
                </tr> -->
                <tr>
                    
                </tr>
                <tr>
                    <td colspan="4" style="padding-top:0px;width: 100%;" >
                        <center>
                        <table class="filter-container" border="0" >
                        <tr>
                           <td width="10%">

                           </td> 
                        <td width="5%" style="text-align: center;">
                        Date:
                        </td>
                        <td width="30%">
                        <form action="" method="post">
                            
                            <input type="date" name="scheduledate" id="date" class="input-text filter-container-items" style="margin: 0;width: 95%;">

                        </td>
                        
                    <td width="12%">
                        <input type="submit"  name="filter" value=" Filter" class=" btn-primary-soft btn button-icon btn-filter"  style="padding: 15px; margin :0;width:100%">
                        </form>
                    </td>

                    </tr>
                    
                            </table>

                        </center>
                    </td>
                    
                </tr>
                
                <td colspan="4" style="padding-top:10px;width: 100%;" >
                    
                    <p class="heading-main12" style="margin-left: 45px;font-size:18px;color:rgb(49, 49, 49)">My Bookings (<?php echo $result->num_rows; ?>)</p>
                    
                </td>
                  
                <tr>
                   <td colspan="4">
                       <center>
                        <div class="abc scroll">
                        <table width="93%" class="sub-table scrolldown" border="0" style="border:none">
                        
                        <tbody>
                        
                        <tbody>
                        <?php
// Set the correct time zone to Philippine Time (PHT)
date_default_timezone_set('Asia/Manila');

// Set the number of records per page
$records_per_page = 3;

// Get the current page number from the query string, defaulting to 1 if not set
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Calculate the offset for the SQL query
$offset = ($current_page - 1) * $records_per_page;

// Modify the SQL query to apply pagination
$sqlmain = "SELECT appointment.appoid, schedule.scheduleid, schedule.title, doctor.docname, patient.pname, 
            schedule.scheduledate, schedule.scheduletime, appointment.apponum, appointment.appodate 
            FROM schedule 
            INNER JOIN appointment ON schedule.scheduleid = appointment.scheduleid 
            INNER JOIN patient ON patient.pid = appointment.pid 
            INNER JOIN doctor ON schedule.docid = doctor.docid 
            WHERE patient.pid = $userid";

// Apply additional filters if POST data is present
if ($_POST) {
    if (!empty($_POST["scheduledate"])) {
        $scheduledate = $_POST["scheduledate"];
        $sqlmain .= " AND schedule.scheduledate = '$scheduledate' ";
    }
}

// Add LIMIT and OFFSET for pagination
$sqlmain .= " ORDER BY appointment.appodate ASC LIMIT $records_per_page OFFSET $offset";

// Execute the query
$result = $database->query($sqlmain);

// Get the current time
$current_datetime = date('Y-m-d H:i'); // Current time in Philippine time zone

// Fetch total number of appointments for pagination
$sqlcount = "SELECT COUNT(*) AS total_records FROM schedule 
             INNER JOIN appointment ON schedule.scheduleid = appointment.scheduleid 
             INNER JOIN patient ON patient.pid = appointment.pid 
             WHERE patient.pid = $userid";
$total_result = $database->query($sqlcount);
$total_row = $total_result->fetch_assoc();
$total_records = $total_row['total_records'];

// Calculate total number of pages
$total_pages = ceil($total_records / $records_per_page);

// Check if no records are found
if ($result->num_rows == 0) {
    echo '<tr>
    <td colspan="7">
        <br><br><br><br>
        <center>
            <img src="../img/notfound.svg" width="25%">
            <br>
            <p class="heading-main12" style="margin-left: 45px;font-size:20px;color:rgb(49, 49, 49)">
                We couldn\'t find anything related to your keywords!
            </p>
            <a class="non-style-link" href="appointment.php">
                <button class="login-btn btn-primary-soft btn" 
                        style="display: flex;justify-content: center;align-items: center;margin-left:20px;">
                    &nbsp; Show all Appointments &nbsp;
                </button>
            </a>
        </center>
        <br><br><br><br>
    </td>
    </tr>';
} else {
    // Display records
    while ($row = $result->fetch_assoc()) {
        $scheduleid = $row["scheduleid"];
        $title = $row["title"];
        $docname = $row["docname"];
        $scheduledate = $row["scheduledate"];
        $scheduletime = $row["scheduletime"];
        $apponum = $row["apponum"];
        $appodate = $row["appodate"];
        $appoid = $row["appoid"];

        // Session duration logic
        $session_duration = isset($row["session_duration"]) && !is_null($row["session_duration"]) ? (int)$row["session_duration"] : 60;
        if ($session_duration <= 0) {
            $session_duration = 60;
        }

        $schedule_datetime = $scheduledate . ' ' . $scheduletime;

        $start_datetime = new DateTime($schedule_datetime);
        $end_datetime = clone $start_datetime;
        $end_datetime->modify('+' . $session_duration . ' minutes');
        $end_time = $end_datetime->format('Y-m-d H:i');

        $button_disabled = '';

        // Comparison logic to disable button
        if ($current_datetime >= $end_time) {
            // If current time is after the end time, session has passed
            $button_disabled = '<button class="cancel-booking-btn btn-primary-soft btn btn-session-passed" style="width:100%;" disabled>Session Passed</button>';
        } elseif ($current_datetime >= $schedule_datetime && $current_datetime < $end_time) {
            // If current time is between the start and end time, session is ongoing
            $button_disabled = '<button class="cancel-booking-btn btn-primary-soft btn btn-session-ongoing" style="width:100%;" disabled>Session Ongoing</button>';
        } else {
            // If current time is before the scheduled time, button is enabled
            $button_disabled = '<button class="cancel-booking-btn btn-primary-soft btn" style="padding-top:11px;padding-bottom:11px;width:100%" data-id="' . $appoid . '" data-title="' . $title . '" data-doc="' . $docname . '" onclick="cancelBooking(' . $appoid . ')">
                                <font class="tn-in-text">Cancel Booking</font>
                            </button>';
        }

        echo '
        <tr>
            <td style="width: 25%;">
                <div class="dashboard-items search-items">
                    <div style="width:100%;">
                        <div class="h3-search">
                            Booking Date: ' . substr($appodate, 0, 30) . '<br>
                            Reference Number: HemoLink ' . $appoid . '
                        </div>
                        <div class="h1-search">
                            ' . substr($title, 0, 21) . '<br>
                        </div>
                        <div class="h3-search">
                            Appointment Number: <div class="h1-search">0' . $apponum . '</div>
                        </div>
                        <div class="h3-search">
                            ' . substr($docname, 0, 30) . '
                        </div>
                        <div class="h4-search">
                            Scheduled Date: ' . $scheduledate . '<br>Starts: <b>@' . substr($scheduletime, 0, 5) . '</b> (24h)<br>
                            Ends: <b>@' . substr($end_time, 11, 5) . '</b> (24h)
                        </div>
                        <br>
                        <div>' . $button_disabled . '</div>
                    </div>
                </div>
            </td>
        </tr>';
    }
}
?>

<!-- Add the JavaScript function for canceling the booking -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function cancelBooking(appoid) {
        // Use SweetAlert2 to display the confirmation modal
        Swal.fire({
            title: 'Are you sure?',
            text: "You want to cancel this booking?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, cancel it!',
            cancelButtonText: 'No, go back'
        }).then((result) => {
            if (result.isConfirmed) {
                // Redirect to the cancellation page with the appropriate ID
                window.location.href = "delete-appointment?id=" + appoid;
            }
        });
    }
</script>




</tbody>


            </table>
            <?php
// Display pagination controls
echo '<div class="pagination">';

// Previous button
if ($current_page > 1) {
    echo '<a href="?page=' . ($current_page - 1) . '" class="pagination-btn">&laquo; Previous</a>';
}

// Page numbers
for ($page = 1; $page <= $total_pages; $page++) {
    if ($page == $current_page) {
        echo '<span class="pagination-active">' . $page . '</span>';
    } else {
        echo '<a href="?page=' . $page . '" class="pagination-btn">' . $page . '</a>';
    }
}

// Next button
if ($current_page < $total_pages) {
    echo '<a href="?page=' . ($current_page + 1) . '" class="pagination-btn">Next &raquo;</a>';
}

echo '</div>';
?>

            

        </div>
        
    </div>
    
    <?php
    
    if (isset($_GET['id']) && isset($_GET['action'])) {
        $id = $_GET['id'];
        $action = $_GET['action'];
    
        if ($action == 'booking-added') {
            echo '
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        title: "Booking Successful!",
                        html: "Your Appointment number is <strong>' . $id . '</strong>.",
                        icon: "success",
                        confirmButtonText: "OK",
                        allowOutsideClick: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = "appointment.php";
                        }
                    });
                });
            </script>
            ';
        } elseif($action=='drop'){
            $title=$_GET["title"];
            $docname=$_GET["doc"];
            
            echo '
            <div id="popup1" class="overlay">
                    <div class="popup">
                    <center>
                        <h2>Are you sure?</h2>
                        <a class="close" href="appointment.php">&times;</a>
                        <div class="content">
                            You want to Cancel this Appointment?<br><br>
                            Session Name: &nbsp;<b>'.substr($title,0,40).'</b><br>
                            Doctor name&nbsp; : <b>'.substr($docname,0,40).'</b><br><br>
                            
                        </div>
                        <div style="display: flex;justify-content: center;">
                        <a href="delete-appointment.php?id='.$id.'" class="non-style-link"><button  class="btn-primary btn"  style="display: flex;justify-content: center;align-items: center;margin:10px;padding:10px;"<font class="tn-in-text">&nbsp;Yes&nbsp;</font></button></a>&nbsp;&nbsp;&nbsp;
                        <a href="appointment.php" class="non-style-link"><button  class="btn-primary btn"  style="display: flex;justify-content: center;align-items: center;margin:10px;padding:10px;"><font class="tn-in-text">&nbsp;&nbsp;No&nbsp;&nbsp;</font></button></a>

                        </div>
                    </center>
            </div>
            </div>
            '; 
        } elseif($action == 'view') {
            $sqlmain = "SELECT * FROM doctor WHERE docid=?";
            $stmt = $database->prepare($sqlmain);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $name = $row["docname"];
            $email = $row["docemail"];
            $spe = $row["specialties"];
            
            $sqlmain = "SELECT sname FROM specialties WHERE id=?";
            $stmt = $database->prepare($sqlmain);
            $stmt->bind_param("s", $spe);
            $stmt->execute();
            $spcil_res = $stmt->get_result();
            $spcil_array = $spcil_res->fetch_assoc();
            $spcil_name = $spcil_array["sname"];
            $nic = $row['docnic'];
            $tele = $row['doctel'];

            echo '
            <div id="popup1" class="overlay">
                    <div class="popup">
                    <center>
                        <h2></h2>
                        <a class="close" href="doctors">&times;</a>
                        <div class="content">
                            HemoLink <br> App<br>
                        </div>
                        <div style="display: flex;justify-content: center;">
                        <table width="80%" class="sub-table scrolldown add-doc-form-container" border="0">
                        
                            <tr>
                                <td>
                                    <p style="padding: 0;margin: 0;text-align: left;font-size: 25px;font-weight: 500;">View Details.</p><br><br>
                                </td>
                            </tr>
                             <tr>
                                 <td class="label-td" colspan="2">
                                    <label for="name" class="form-label">Name: </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    '.$name.'<br><br>
                                </td>
                             </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <label for="Email" class="form-label">Email: </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                '.$email.'<br><br>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <label for="nic" class="form-label">PhilHealth ID: </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                '.$nic.'<br><br>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <label for="Tele" class="form-label">Telephone: </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                '.$tele.'<br><br>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <label for="spec" class="form-label">Specialties: </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                '.$spcil_name.'<br><br>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <a href="doctors"><input type="button" value="OK" class="login-btn btn-primary-soft btn"></a>
                                </td>
                            </tr>
                        </table>
                        </div>
                    </center>
                    <br><br>
            </div>
            </div>
            ';  
        }
    }
?>


    
    </div>
    

</body>
</html>