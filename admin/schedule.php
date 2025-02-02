<?php
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

// Define the SQL query for fetching the schedules
$sqlmain = "SELECT schedule.scheduleid, schedule.title, doctor.docname, schedule.scheduledate, schedule.scheduletime, schedule.nop 
            FROM schedule 
            INNER JOIN doctor ON schedule.docid = doctor.docid";

// Initialize an array to hold the conditions for filtering
$sqlConditions = [];

// Check if the date filter is applied
if (!empty($_POST["scheduledate"])) {
    $scheduledate = $_POST["scheduledate"];
    $sqlConditions[] = "schedule.scheduledate = '$scheduledate'";
}

// Check if the doctor filter is applied
if (!empty($_POST["docid"])) {
    $docid = $_POST["docid"];
    $sqlConditions[] = "schedule.docid = $docid";
}

// Add conditions to the SQL query if they exist
if (count($sqlConditions) > 0) {
    $sqlmain .= " WHERE " . implode(" AND ", $sqlConditions);
}

// Add ordering to the query
$sqlmain .= " ORDER BY schedule.scheduledate DESC";

// Execute the query
$list110 = $database->query($sqlmain);
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
        
    <title>Schedule</title>
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
        if(($_SESSION["user"])=="" or $_SESSION['usertype']!='a'){
            header("location: ../login.php");
        }

    }else{
        header("location: ../login.php");
    }
    
    

    //import database
    include("../connection.php");

    
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
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-dashbord">
                        <a href="index.php" class="non-style-link-menu"><div><p class="menu-text">Dashboard</p></div></a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-doctor">
                        <a href="doctors.php" class="non-style-link-menu"><div><p class="menu-text">Doctors</p></div></a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-schedule menu-active menu-icon-schedule-active">
                        <a href="schedule.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">Schedule</p></div></a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-appoinment">
                        <a href="appointment.php" class="non-style-link-menu"><div><p class="menu-text">Appointment</p></div></a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-patient">
                        <a href="patient.php" class="non-style-link-menu"><div><p class="menu-text">Patients</p></div></a>
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

                        $patientrow = $database->query("SELECT * FROM patient;");
                        $doctorrow = $database->query("SELECT * FROM doctor;");
                        $appointmentrow = $database->query("SELECT * FROM appointment WHERE appodate >= '$date';");
                        $schedulerow = $database->query("SELECT * FROM schedule WHERE scheduledate = '$date';");
                    ?>
                </p>
            </td>
        </tr>
               
                <tr>
                    <td colspan="4" >
                        <div style="display: flex;margin-top: 40px;">
                        <div class="heading-main12" style="margin-left: 45px;font-size:20px;color:rgb(49, 49, 49);margin-top: 5px;">Schedule a Session</div>
                        <a href="?action=add-session&id=none&error=0" class="non-style-link"><button  class="login-btn btn-primary btn button-icon"  style="margin-left:25px;background-image: url('../img/icons/add.svg');">Add a Session</font></button>
                        </a>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" style="padding-top:10px;width: 100%;">
                        <p class="heading-main12" style="margin-left: 45px;font-size:18px;color:rgb(49, 49, 49)">
                            All Sessions
                            <?php
                                // Fetch the total number of sessions based on filter (if any)
                                $sql = "SELECT scheduleid FROM schedule";
                                $conditions = array();

                                if ($_POST) {
                                    // Add the selected filters to the query dynamically
                                    if (!empty($_POST["scheduledate"])) {
                                        $scheduledate = $_POST["scheduledate"];
                                        $conditions[] = "scheduledate = '$scheduledate'";
                                    }

                                    if (!empty($_POST["docid"])) {
                                        $docid = $_POST["docid"];
                                        $conditions[] = "docid = $docid";
                                    }

                                    // Append conditions if they exist
                                    if (count($conditions) > 0) {
                                        $sql .= " WHERE " . implode(" AND ", $conditions);
                                    }
                                }

                                $result = $database->query($sql);
                                echo($result->num_rows); // Output the filtered count
                                ?>
                        </p>
                    </td>
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
                        <td width="5%" style="text-align: center;">
                        Doctor:
                        </td>
                        <td width="30%">
                        <select name="docid" id="" class="box filter-container-items" style="width:90% ;height: 37px;margin: 0;" >
                            <option value="" disabled selected hidden>Choose Doctor Name from the list</option><br/>
                                
                            <?php 
                            
                                $list11 = $database->query("select  * from  doctor order by docname asc;");

                                for ($y=0;$y<$list11->num_rows;$y++){
                                    $row00=$list11->fetch_assoc();
                                    $sn=$row00["docname"];
                                    $id00=$row00["docid"];
                                    echo "<option value=".$id00.">$sn</option><br/>";
                                };


                                ?>

                        </select>
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
                
                <?php
                    if($_POST){
                        //print_r($_POST);
                        $sqlpt1="";
                        if(!empty($_POST["scheduledate"])){
                            $scheduledate=$_POST["scheduledate"];
                            $sqlpt1=" schedule.scheduledate='$scheduledate' ";
                        }


                        $sqlpt2="";
                        if(!empty($_POST["docid"])){
                            $docid=$_POST["docid"];
                            $sqlpt2=" doctor.docid=$docid ";
                        }
                        //echo $sqlpt2;
                        //echo $sqlpt1;
                        $sqlmain= "select schedule.scheduleid,schedule.title,doctor.docname,schedule.scheduledate,schedule.scheduletime,schedule.nop from schedule inner join doctor on schedule.docid=doctor.docid ";
                        $sqllist=array($sqlpt1,$sqlpt2);
                        $sqlkeywords=array(" where "," and ");
                        $key2=0;
                        foreach($sqllist as $key){

                            if(!empty($key)){
                                $sqlmain.=$sqlkeywords[$key2].$key;
                                $key2++;
                            };
                        };
                        //echo $sqlmain;

                        
                        
                        //
                    }else{
                        $sqlmain= "select schedule.scheduleid,schedule.title,doctor.docname,schedule.scheduledate,schedule.scheduletime,schedule.nop from schedule inner join doctor on schedule.docid=doctor.docid  order by schedule.scheduledate desc";

                    }



                ?>
                  
                <tr>
                   <td colspan="4">
                       <center>
                        <div class="abc scroll">
                        <table width="93%" class="sub-table scrolldown" border="0">
                        <thead>
                        <tr>
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
                                    
                                Max num that can be booked
                                    
                                </th>
                                
                                <th class="table-headin">
                                    
                                    Events
                                    
                                </tr>
                        </thead>
                        <tbody>
                        
                        <?php
// Initialize base query
$sqlmain = "SELECT scheduleid, title, docid, scheduledate, scheduletime, nop FROM schedule";

// Add date filter if specified
if ($_POST) {
    if (!empty($_POST["scheduledate"])) {
        $scheduledate = $_POST["scheduledate"];
        $sqlmain .= " WHERE scheduledate = '$scheduledate'";
    }
} else {
    // Default: Fetch all sessions ordered by scheduled date
    $sqlmain .= " ORDER BY scheduledate DESC";
}

// Execute the query
$result = $database->query($sqlmain);

if ($result->num_rows == 0) {
    echo '<tr>
        <td colspan="4">
        <br><br><br><br>
        <center>
        <img src="../img/notfound.svg" width="25%">
        <br>
        <p class="heading-main12" style="margin-left: 45px;font-size:20px;color:rgb(49, 49, 49)">We cannot find anything related to your keywords !</p>
        <a class="non-style-link" href="schedule.php"><button class="login-btn btn-primary-soft btn" style="display: flex;justify-content: center;align-items: center;margin-left:20px;">&nbsp; Show all Sessions &nbsp;</button></a>
        </center>
        <br><br><br><br>
        </td>
    </tr>';
} else {
    $current_datetime = date("Y-m-d H:i:s");  // Get the current date and time
    while ($row = $result->fetch_assoc()) {
        $scheduleid = $row["scheduleid"];
        $title = $row["title"];
        $docid = $row["docid"];  // Use docid here instead of docname
        $scheduledate = $row["scheduledate"];
        $scheduletime = $row["scheduletime"];
        $nop = $row["nop"];

        // Combine scheduled date and time into a single datetime variable
        $schedule_datetime = $scheduledate . ' ' . $scheduletime;

        // Compare logic to disable the button
        if ($current_datetime >= $schedule_datetime) {
            // If current time is after or equal to the session time, session has passed
            $cancel_button = '<button class="btn-session-passed btn-primary-soft" style="padding-left: 40px;padding-top: 12px;padding-bottom: 12px;margin-top: 10px;" disabled>Session Passed</button>';
        } else {
            // If current time is before the scheduled time, button is enabled
            $cancel_button = '<a href="?action=drop&id=' . $scheduleid . '&name=' . $title . '" class="non-style-link">
                <button class="btn-primary-soft btn button-icon btn-delete" style="padding-left: 40px;padding-top: 12px;padding-bottom: 12px;margin-top: 10px;">
                    <font class="tn-in-text">Remove</font>
                </button>
            </a>';
        }

        // Display sessions normally (soft-deletes removed)
        echo '<tr>
            <td style="text-align: center;"> &nbsp;'
            . substr($title, 0, 30) 
            . '</td>
            <td style="text-align: center;"> &nbsp;' 
            . substr($docid, 0, 20) 
            . '</td>  
            <td style="text-align:center;">' 
            . substr($scheduledate, 0, 10) . ' ' 
            . substr($scheduletime, 0, 5) . '</td>
            <td style="text-align:center;">' . $nop . '</td>
            <td>
                <div style="display:flex;justify-content: center;">
                    <a href="?action=view&id=' . $scheduleid . '" class="non-style-link">
                        <button class="btn-primary-soft btn button-icon btn-view" style="padding-left: 40px;padding-top: 12px;padding-bottom: 12px;margin-top: 10px;">
                            <font class="tn-in-text">View</font>
                        </button>
                    </a>
                    &nbsp;&nbsp;&nbsp;
                    ' . $cancel_button . '
                </div>
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
        </div>
    </div>
    <?php

if ($_GET) {
    $id = $_GET["id"];
    $action = $_GET["action"];

    // Add session
    if ($action == 'add-session') {
        echo '
        <div id="popup1" class="overlay">
            <div class="popup">
                <center>
                    <a class="close" href="schedule.php">&times;</a> 
                    <div style="display: flex;justify-content: center;">
                    <div class="abc">
                    <table width="80%" class="sub-table scrolldown add-doc-form-container" border="0">
                        <tr>
                            <td class="label-td" colspan="2">'.
                               ""
                            .'</td>
                        </tr>

                        <tr>
                            <td>
                                <p style="padding: 0;margin: 0;text-align: left;font-size: 25px;font-weight: 500;">Add New Session.</p><br>
                            </td>
                        </tr>
                        <tr>
                            <td class="label-td" colspan="2">
                            <form action="add-session" method="POST" class="add-new-form">
                                <label for="title" class="form-label">Session Title : </label>
                            </td>
                        </tr>
                        <tr>
                            <td class="label-td" colspan="2">
                                <input type="text" name="title" class="input-text" placeholder="Name of this Session" required><br>
                            </td>
                        </tr>
                        <tr>
                            <td class="label-td" colspan="2">
                                <label for="docid" class="form-label">Select Doctor: </label>
                            </td>
                        </tr>
                        <tr>
                            <td class="label-td" colspan="2">
                                <select name="docid" id="" class="box" >
                                <option value="" disabled selected hidden>Choose Doctor Name from the list</option><br/>';
        
        $list11 = $database->query("select  * from  doctor order by docname asc;");
        
        for ($y = 0; $y < $list11->num_rows; $y++) {
            $row00 = $list11->fetch_assoc();
            $sn = $row00["docname"];
            $id00 = $row00["docid"];
            echo "<option value=" . $id00 . ">$sn</option><br/>";
        }

        echo '       
                        </select><br><br>
                            </td>
                        </tr>
                        <tr>
                            <td class="label-td" colspan="2">
                                <label for="nop" class="form-label">Number of Patients/Appointment Numbers : </label>
                            </td>
                        </tr>
                        <tr>
                            <td class="label-td" colspan="2">
                                <input type="number" name="nop" class="input-text" min="0"  placeholder="The final appointment number for this session depends on this number" required><br>
                            </td>
                        </tr>
                        <tr>
                            <td class="label-td" colspan="2">
                                <label for="date" class="form-label">Session Date: </label>
                            </td>
                        </tr>
                        <tr>
                            <td class="label-td" colspan="2">
                                <input type="date" name="date" class="input-text" min="' . date('Y-m-d') . '" required><br>
                            </td>
                        </tr>
                        <tr>
                            <td class="label-td" colspan="2">
                                <label for="time" class="form-label">Schedule Time: </label>
                            </td>
                        </tr>
                        <tr>
                            <td class="label-td" colspan="2">
                                <input type="time" name="time" class="input-text" placeholder="Time" required><br>
                            </td>
                        </tr>

                        <!-- New field for session duration -->
                        <tr>
                            <td class="label-td" colspan="2">
                                <label for="duration" class="form-label">Session Duration (in minutes): </label>
                            </td>
                        </tr>
                        <tr>
                            <td class="label-td" colspan="2">
                                <input type="number" name="duration" class="input-text" min="1" placeholder="Session Duration in Minutes" required><br>
                            </td>
                        </tr>

                        <!-- End Time (will be calculated from duration and start time) -->
                        <tr>
                            <td class="label-td" colspan="2">
                                <label for="end_time" class="form-label">End Time (Calculated): </label>
                            </td>
                        </tr>
                        <tr>
                            <td class="label-td" colspan="2">
                                <input type="text" id="end_time" class="input-text" readonly><br>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="2">
                                <input type="reset" value="Reset" class="login-btn btn-primary-soft btn" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <input type="submit" value="Place this Session" class="login-btn btn-primary btn" name="shedulesubmit">
                            </td>
                        </tr>
                    </form>
                    </tr>
                </table>
                </div>
                </div>
            </center>
            <br><br>
        </div>
        </div>';

        // Add JavaScript to calculate end time dynamically based on start time and duration
        echo '
        <script>
        document.querySelector("input[name=\'time\']").addEventListener("change", calculateEndTime);
        document.querySelector("input[name=\'duration\']").addEventListener("change", calculateEndTime);

        function calculateEndTime() {
            var startTime = document.querySelector("input[name=\'time\']").value;
            var duration = document.querySelector("input[name=\'duration\']").value;

            if (startTime && duration) {
                var startDate = new Date("1970-01-01T" + startTime + "Z");  // Use a dummy date
                startDate.setMinutes(startDate.getMinutes() + parseInt(duration));

                var hours = startDate.getUTCHours().toString().padStart(2, "0");
                var minutes = startDate.getUTCMinutes().toString().padStart(2, "0");

                document.getElementById("end_time").value = hours + ":" + minutes;
            }
        }
        </script>';} elseif ($action == 'session-added') {
            $titleget = $_GET["title"];
            echo "
            <script>
                Swal.fire({
                    title: 'Session Placed.',
                    text: '" . substr($titleget, 0, 40) . " was scheduled.',
                    icon: 'success',
                    confirmButtonText: 'OK',
                    willClose: () => {
                        window.location.href = 'schedule.php'; // Redirect after closing the popup
                    }
                });
            </script>";
            
    } elseif ($action == 'drop') {
        // Get the session details passed through GET
        $nameget = isset($_GET["name"]) ? $_GET["name"] : '';
        $id = isset($_GET["id"]) ? $_GET["id"] : '';
    
        // Ensure id is valid and not empty
        if (empty($id)) {
            echo "Invalid session ID!";
            exit();
        }
    
        echo '
        <div id="popup1" class="overlay">
            <div class="popup">
                <center>
                    <h2>Are you sure?</h2>
                    <a class="close" href="schedule.php">&times;</a>
                    <div class="content">
                        You want to cancel or delete this session<br>(' . substr($nameget, 0, 40) . ').
                    </div>
                    <div style="display: flex;justify-content: center;">
                        <!-- Cancel Session Button -->
                        <a href="cancel-session?id=' . $id . '" class="non-style-link">
                            <button class="btn-primary btn" style="display: flex;justify-content: center;align-items: center;margin:10px;padding:10px;">
                                <font class="tn-in-text">&nbsp;Cancel Session&nbsp;</font>
                            </button>
                        </a>&nbsp;&nbsp;&nbsp;
                        <!-- Delete Session Button -->
                        <a href="delete-session.php?id=' . $id . '" class="non-style-link">
                            <button class="btn-primary btn" style="display: flex;justify-content: center;align-items: center;margin:10px;padding:10px;">
                                <font class="tn-in-text">&nbsp;Delete Session&nbsp;</font>
                            </button>
                        </a>
                    </div>
                </center>
            </div>
        </div>';
    
    } elseif ($action == 'view') {
        $sqlmain = "select schedule.scheduleid,schedule.title,doctor.docname,schedule.scheduledate,schedule.scheduletime,schedule.nop from schedule inner join doctor on schedule.docid=doctor.docid where schedule.scheduleid=$id";
        $result = $database->query($sqlmain);
        $row = $result->fetch_assoc();
        $docname = $row["docname"];
        $scheduleid = $row["scheduleid"];
        $title = $row["title"];
        $scheduledate = $row["scheduledate"];
        $scheduletime = $row["scheduletime"];
        $nop = $row['nop'];

        $sqlmain12 = "select * from appointment inner join patient on patient.pid=appointment.pid inner join schedule on schedule.scheduleid=appointment.scheduleid where schedule.scheduleid=$id;";
        $result12 = $database->query($sqlmain12);
        echo '
        <div id="popup1" class="overlay">
            <div class="popup" style="width: 70%;">
                <center>
                    <a class="close" href="schedule.php">&times;</a>
                    <div class="content">
                    </div>
                    <div class="abc scroll" style="display: flex;justify-content: center;">
                    <table width="80%" class="sub-table scrolldown add-doc-form-container" border="0">
                        <tr>
                            <td>
                                <p style="padding: 0;margin: 0;text-align: left;font-size: 25px;font-weight: 500;">View Details.</p><br><br>
                            </td>
                        </tr>
                        <tr>
                            <td class="label-td" colspan="2">
                                <label for="name" class="form-label">Session Title: </label>
                            </td>
                        </tr>
                        <tr>
                            <td class="label-td" colspan="2">
                                ' . $title . '<br><br>
                            </td>
                        </tr>
                        <tr>
                            <td class="label-td" colspan="2">
                                <label for="Email" class="form-label">Doctor of this session: </label>
                            </td>
                        </tr>
                        <tr>
                            <td class="label-td" colspan="2">
                                ' . $docname . '<br><br>
                            </td>
                        </tr>
                        <tr>
                            <td class="label-td" colspan="2">
                                <label for="nic" class="form-label">Scheduled Date: </label>
                            </td>
                        </tr>
                        <tr>
                            <td class="label-td" colspan="2">
                                ' . $scheduledate . '<br><br>
                            </td>
                        </tr>
                        <tr>
                            <td class="label-td" colspan="2">
                                <label for="Tele" class="form-label">Scheduled Time: </label>
                            </td>
                        </tr>
                        <tr>
                            <td class="label-td" colspan="2">
                                ' . $scheduletime . '<br><br>
                            </td>
                        </tr>
                        <tr>
                            <td class="label-td" colspan="2">
                                <label for="spec" class="form-label"><b>Patients that Already registered for this session:</b> (' . $result12->num_rows . "/" . $nop . ')</label>
                                <br><br>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4">
                                <center>
                                    <div class="abc scroll">
                                        <table width="100%" class="sub-table scrolldown" border="0">
                                            <thead>
                                                <tr>   
                                                    <th class="table-headin">Patient ID</th>
                                                    <th class="table-headin">Patient name</th>
                                                    <th class="table-headin">Appointment number</th> 
                                                    <th class="table-headin">Patient Telephone</th>
                                                </tr>
                                            </thead>
                                            <tbody>';

        // Fetching patient details
        $result = $database->query($sqlmain12);

        if ($result->num_rows == 0) {
            echo '<tr>
                    <td colspan="7">
                    <br><br><br><br>
                    <center>
                    <img src="../img/notfound.svg" width="25%">
                    <br>
                    <p class="heading-main12" style="margin-left: 45px;font-size:20px;color:rgb(49, 49, 49)">We couldn\'t find anything related to your keywords!</p>
                    <a class="non-style-link" href="appointment.php"><button class="login-btn btn-primary-soft btn" style="display: flex;justify-content: center;align-items: center;margin-left:20px;">&nbsp; Show all Appointments &nbsp;</button></a>
                    </center>
                    <br><br><br><br>
                    </td>
                </tr>';
        } else {
            // Loop through results and display
            for ($x = 0; $x < $result->num_rows; $x++) {
                $row = $result->fetch_assoc();
                $apponum = $row["apponum"];
                $pid = $row["pid"];
                $pname = $row["pname"];
                $ptel = $row["ptel"];
                echo '<tr style="text-align:center;">
                        <td>' . substr($pid, 0, 15) . '</td>
                        <td style="font-weight:600;padding:25px">' . substr($pname, 0, 25) . '</td>
                        <td style="text-align:center;font-size:23px;font-weight:500; color: var(--btnnicetext);">' . $apponum . '</td>
                        <td>' . substr($ptel, 0, 25) . '</td>
                      </tr>';
            }
        }
        echo '</tbody>
            </table>
        </div>
        </center>
        </td> 
        </tr>
        </table>
        </div>
    </center>
    <br><br>
</div>
</div>';
    }
}

        
    ?>
    </div>

</body>
</html>