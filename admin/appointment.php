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

    session_start();

    if(isset($_SESSION["user"])){
        if(($_SESSION["user"])=="" or $_SESSION['usertype']!='a'){
            header("location: ../login.php");
        }

    }else{
        header("location: ../login.php");
    }
    
    

    //import database
    include("../connection.php");
    $list110 = $database->query("SELECT * FROM appointment");
    $appointment_count = ($list110) ? $list110->num_rows : 0; // Ensure no error if the query fails.
    
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
                    <td class="menu-btn menu-icon-schedule ">
                        <a href="schedule.php" class="non-style-link-menu"><div><p class="menu-text">Schedule</p></div></a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-appoinment menu-active menu-icon-appoinment-active">
                        <a href="appointment.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">Appointment</p></a></div>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-patient">
                        <a href="patient.php" class="non-style-link-menu"><div><p class="menu-text">Patients</p></a></div>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-records">
                        <a href="records.php" class="non-style-link-menu"><div><p class="menu-text">Records</p></a></div>
                    </td>
                </tr>

            </table>
        </div>
        <div class="dash-body">
            <table border="0" width="100%" style=" border-spacing: 0;margin:0;padding:0;margin-top:25px; ">
                <tr >
                    <!-- <td width="13%">

                    <a href="patient.php" ><button  class="login-btn btn-primary-soft btn btn-icon-back"  style="padding-top:11px;padding-bottom:11px;margin-left:20px;width:125px"><font class="tn-in-text">Back</font></button></a>
                        
                    </td> -->
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
                                    
                               
                                    <input type="Submit" value="Search"class="btn-primary-soft btn button-icon btn-search" style="padding-left: 25px;padding-right: 25px;padding-top: 10px;padding-bottom: 10px;">
                                
                                </form>
                                
                            </td>
               
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
                    <td colspan="4" style="padding-top:10px;width: 100%;" >
                    
                    <p class="heading-main12" style="margin-left: 45px;font-size:18px;color:rgb(49, 49, 49)">All Appointments (<?php echo $appointment_count; ?>)</p>

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

                        $sqlmain= "select appointment.appoid,schedule.scheduleid,schedule.title,doctor.docname,patient.pname,schedule.scheduledate,schedule.scheduletime,appointment.apponum,appointment.appodate from schedule inner join appointment on schedule.scheduleid=appointment.scheduleid inner join patient on patient.pid=appointment.pid inner join doctor on schedule.docid=doctor.docid WHERE appointment.is_confirmed = 0 AND appointment.status != 'Rejected'";
                        $sqllist=array($sqlpt1,$sqlpt2);
                        $sqlkeywords=array(" AND "," AND ");
                        $key2=0;
                        foreach($sqllist as $key){
                            if(!empty($key)){
                                $sqlmain.=$sqlkeywords[$key2].$key;
                                $key2++;
                            };
                        };

                        
                        
                        //
                    }else{
                        $sqlmain= "SELECT appointment.appoid, schedule.scheduleid, schedule.title, doctor.docname, patient.pname, schedule.scheduledate, schedule.scheduletime, appointment.apponum, appointment.appodate
            FROM schedule
            INNER JOIN appointment ON schedule.scheduleid = appointment.scheduleid
            INNER JOIN patient ON patient.pid = appointment.pid
            INNER JOIN doctor ON schedule.docid = doctor.docid
            WHERE appointment.is_confirmed = 0 AND appointment.status != 'Rejected' order by schedule.scheduledate desc";

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
                                    Patient name
                                </th>
                                <th class="table-headin">
                                    
                                    Appointment number
                                    
                                </th>
                               
                                
                                <th class="table-headin">
                                    Doctor
                                </th>
                                <th class="table-headin">
                                    
                                
                                    Session Title
                                    
                                    </th>
                                
                                <th class="table-headin" style="font-size:10px">
                                    
                                    Session Date & Time
                                    
                                </th>
                                
                                
                                <th class="table-headin">
                                    
                                    Events
                                    
                                </tr>
                        </thead>
                        <tbody>
                        
<?php
// Initialize the SQL query
$sqlmain = "SELECT appointment.appoid, schedule.scheduleid, schedule.title, doctor.docname, patient.pname, schedule.scheduledate, schedule.scheduletime, appointment.apponum, appointment.appodate
            FROM schedule
            INNER JOIN appointment ON schedule.scheduleid = appointment.scheduleid
            INNER JOIN patient ON patient.pid = appointment.pid
            INNER JOIN doctor ON schedule.docid = doctor.docid
            WHERE appointment.is_confirmed = 0 AND appointment.status != 'Rejected'";

// Initialize an array to hold filter conditions
$filters = [];

// Check if a date filter is applied
if (!empty($_POST["scheduledate"])) {
    $scheduledate = $_POST["scheduledate"];
    $filters[] = "schedule.scheduledate = '$scheduledate'";
}

// Check if a doctor filter is applied
if (!empty($_POST["docid"])) {
    $docid = $_POST["docid"];
    $filters[] = "doctor.docid = $docid";
}

// Append filters to the SQL query if any
if (!empty($filters)) {
    $sqlmain .= " AND " . implode(" AND ", $filters);
}

// Execute the query
$result = $database->query($sqlmain);

// Check if any appointments were found
if ($result->num_rows == 0) {
    echo '<tr>
            <td colspan="7">
                <br><br><br><br>
                <center>
                    <img src="../img/notfound.svg" width="25%">
                    <br>
                    <p class="heading-main12" style="margin-left: 45px;font-size:20px;color:rgb(49, 49, 49)">We cannot find anything related to your keywords!</p>
                    <a class="non-style-link" href="appointment.php"><button class="login-btn btn-primary-soft btn" style="display: flex;justify-content: center;align-items: center;margin-left:20px;">&nbsp; Show all Appointments &nbsp;</button></a>
                </center>
                <br><br><br><br>
            </td>
          </tr>';
} else {
    date_default_timezone_set('Asia/Manila'); // Set to your desired timezone
    $currentDateTime = new DateTime();

    while ($row = $result->fetch_assoc()) {
        $appoid = $row["appoid"];
        $scheduleid = $row["scheduleid"];
        $title = $row["title"];
        $docname = $row["docname"];
        $scheduledate = $row["scheduledate"];
        $scheduletime = $row["scheduletime"];
        $pname = $row["pname"];
        $apponum = $row["apponum"];
        $appodate = $row["appodate"];

        // Combine scheduledate and scheduletime into a single DateTime object
        $scheduledDateTime = new DateTime("$scheduledate $scheduletime");

        // Determine the button label based on the comparison
        if ($currentDateTime >= $scheduledDateTime) {
            $buttonLabel = '<button class="btn-session-passed btn-primary-soft" style="padding-left: 40px;padding-top: 12px;padding-bottom: 12px;margin-top: 10px;" disabled>Session Passed</button>';
        } else {
            $buttonLabel = '<a href="javascript:void(0);" onclick="confirmAppointment('.$appoid.', \'approve\')" class="non-style-link">
                            <button class="btn-primary-soft btn button-icon btn-approve" style="padding-left: 40px;padding-top: 12px;padding-bottom: 12px;margin-top: 10px;">
                                <img src="../img/icons/approve.svg" alt="Approve" style="width: 20px; height: 20px; margin-right: 10px;">
                                <font class="tn-in-text">Approve</font>
                            </button>
                        </a>
                        &nbsp;&nbsp;&nbsp;
                        <a href="javascript:void(0);" onclick="confirmAppointment('.$appoid.', \'reject\')" class="non-style-link" style="margin-left: 10px;">
                            <button class="btn-primary-soft btn button-icon btn-reject" style="padding-left: 40px;padding-top: 12px;padding-bottom: 12px;margin-top: 10px;">
                                <img src="../img/icons/reject.svg" alt="Reject" style="width: 20px; height: 20px; margin-right: 10px;">
                                <font class="tn-in-text">Reject</font>
                            </button>
                        </a>';
        }

        echo '<tr data-appointment-id="' . $appoid . '">
                <td style="text-align: center;font-weight:600;"> &nbsp;' . substr($pname, 0, 25) . '</td>
                <td style="text-align: center;font-size:23px;font-weight:500; color: var(--btnnicetext);">' . $apponum . '</td>
                <td style="text-align: center;"> &nbsp;' . substr($docname, 0, 25) . '</td>
                <td style="text-align: center;"> &nbsp;' . substr($title, 0, 15) . '</td>
                <td style="text-align: center;font-size:12px;">' . substr($scheduledate, 0, 10) . ' <br>' . substr($scheduletime, 0, 5) . '</td>
                <td>
                    <div style="display:flex;justify-content: center;">
                        ' . $buttonLabel . '
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

<script>
function confirmAppointment(appointmentId, action) {
    if (action === 'reject') {
        Swal.fire({
            title: 'Reject Appointment',
            text: 'Please provide a reason for rejection:',
            input: 'text',
            inputPlaceholder: 'Reason for rejection',
            inputValue: 'Scheduling Conflict',
            inputAttributes: {
                maxlength: 20 // Set the maximum length here
            },
            showCancelButton: true,
            confirmButtonText: 'Reject',
            cancelButtonText: 'Cancel',
            preConfirm: (reason) => {
                if (!reason) {
                    Swal.showValidationMessage('Please enter a reason');
                }
                return reason;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                processAppointment(appointmentId, action, result.value);
            }
        });
    } else {
        Swal.fire({
            title: 'Confirm Appointment',
            text: 'Are you sure you want to approve this appointment?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, approve',
            cancelButtonText: 'No, cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                processAppointment(appointmentId, action);
            }
        });
    }
}

function processAppointment(appointmentId, action, reason = null) {
    const url = `?action=${action}&appointmentId=${appointmentId}` + 
                (reason ? `&reason=${encodeURIComponent(reason)}` : '');

    fetch(url, { method: 'GET' })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove the table row
            const rowToRemove = document.querySelector(`[data-appointment-id="${appointmentId}"]`);
            if (rowToRemove) {
                rowToRemove.remove();
            }

            // Update appointment count
            updateAppointmentCount();

            // Show success message
            Swal.fire({
                title: 'Success!',
                text: action === 'approve' ? 'Appointment approved' : 'Appointment rejected',
                icon: 'success',
                timer: 2000,
                timerProgressBar: true
            });
        } else {
            // Show error message
            Swal.fire({
                title: 'Error',
                text: data.error || 'Operation failed',
                icon: 'error'
            });
        }
    })
    .catch(error => {
        Swal.fire({
            title: 'Success!',
            text: 'Operation completed successfully.',
            icon: 'success',
            confirmButtonText: 'OK'
        });
    });
}

// Function to update appointment count
function updateAppointmentCount() {
    const countElement = document.querySelector('.heading-main12');
    if (countElement) {
        const currentCountMatch = countElement.textContent.match(/\d+/);
        if (currentCountMatch) {
            const currentCount = parseInt(currentCountMatch[0]);
            countElement.textContent = `All Appointments (${Math.max(0, currentCount - 1)})`;
        }
    }
}
</script>
</body>
</html>
<?php
if ($_GET) {
    $id = $_GET["id"];
    $action = $_GET["action"];
    
    if ($action == 'add-session') {
        echo '
        <div id="popup1" class="overlay">
            <div class="popup">
                <center>
                    <a class="close" href="schedule.php">&times;</a> 
                    <div style="display: flex; justify-content: center;">
                        <div class="abc">
                            <table width="80%" class="sub-table scrolldown add-doc-form-container" border="0" style="text-align:center;">
                                <tr>
                                    <td class="label-td" colspan="2">
                                        <p style="padding: 0; margin: 0; text-align: left; font-size: 25px; font-weight: 500;">Add New Session.</p><br>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="label-td" colspan="2">
                                        <form action="add-session.php" method="POST" class="add-new-form">
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
                                        <select name="docid" id="" class="box">
                                            <option value="" disabled selected hidden>Choose Doctor Name from the list</option><br/>';
        
        $list11 = $database->query("SELECT * FROM doctor;");
        for ($y = 0; $y < $list11->num_rows; $y++) {
            $row00 = $list11->fetch_assoc();
            $sn = $row00["docname"];
            $id00 = $row00["docid"];
            echo "<option value='$id00'>$sn</option><br/>";
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
                                        <input type="number" name="nop" class="input-text" min="0" placeholder="The final appointment number for this session depends on this number" required><br>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="label-td" colspan="2">
                                        <label for="date" class="form-label">Session Date: </label>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="label-td" colspan="2">
                                        <input type="date" name="date" class="input-text" min="'.date('Y-m-d').'" required><br>
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
                                <tr>
                                    <td colspan="2">
                                        <input type="reset" value="Reset" class="login-btn btn-primary-soft btn" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <input type="submit" value="Place this Session" class="login-btn btn-primary btn" name="shedulesubmit">
                                    </td>
                                </tr>
                            </form>
                        </table>
                    </div>
                </div>
            </center>
        </div>
        </div>
        ';
    } elseif ($action == 'session-added') {
        $titleget = $_GET["title"];
        echo '
        <div id="popup1" class="overlay">
            <div class="popup">
                <center>
                    <h2>Session Placed.</h2>
                    <a class="close" href="schedule.php">&times;</a>
                    <div class="content">
                        '.substr($titleget, 0, 40).' was scheduled.<br><br>
                    </div>
                    <div style="display: flex; justify-content: center;">
                        <a href="schedule.php" class="non-style-link">
                            <button class="btn-primary btn" style="display: flex; justify-content: center; align-items: center; margin: 10px; padding: 10px;">
                                <font class="tn-in-text">&nbsp;&nbsp;OK&nbsp;&nbsp;</font>
                            </button>
                        </a>
                        <br><br><br><br>
                    </div>
                </center>
            </div>
        </div>
        ';
        }elseif($action=='drop'){
            $title = isset($_GET["title"]) ? $_GET["title"] : 'Appointment';
            $docname = isset($_GET["doc"]) ? $_GET["doc"] : 'Doctor';
            $id = isset($_GET["id"]) ? $_GET["id"] : '';
            
            echo '
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    title: "Are you sure?",
                    html: `
                        You want to Cancel this Appointment?<br><br>
                        Session Name: <b>'.substr($title,0,40).'</b><br>
                        Doctor name: <b>'.substr($docname,0,40).'</b>
                    `,
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, Cancel Appointment",
                    cancelButtonText: "No, Keep Appointment"
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "delete-appointment.php?id='.$id.'";
                    } else {
                        window.location.href = "appointment.php";
                    }
                });
            });
            </script>';
}
    elseif ($action == 'view'){
            $sqlmain= "select * from doctor where docid='$id'";
            $result= $database->query($sqlmain);
            $row=$result->fetch_assoc();
            $name=$row["docname"];
            $email=$row["docemail"];
            $spe=$row["specialties"];
            
            $spcil_res= $database->query("select sname from specialties where id='$spe'");
            $spcil_array= $spcil_res->fetch_assoc();
            $spcil_name=$spcil_array["sname"];
            $nic=$row['docnic'];
            $phone_number=$row['doctel'];
            echo '
            <div id="popup1" class="overlay">
                    <div class="popup">
                    <center>
                        <h2></h2>
                        <a class="close" href="doctors.php">&times;</a>
                        <div class="content">
                            HemoLink <br> App<br>
                            
                        </div>
                        <div style="display: flex; justify-content: center;">
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
                                '.$phone_number.'<br><br>
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
                                    <a href="doctors.php"><input type="button" value="OK" class="login-btn btn-primary-soft btn" ></a>
                                
                                    
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
    elseif ($action == 'approve' || $action == 'reject') {
        // Enable comprehensive error reporting
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        ini_set('log_errors', 1);
        $error_log_path = dirname(__FILE__) . '/appointment_error.log';
        ini_set('error_log', $error_log_path);

        // Clear any existing output
        ob_clean();

        // Set JSON headers
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, no-store, must-revalidate');

        $appointmentId = $_GET["appointmentId"] ?? null;
        $reason = $_GET["reason"] ?? null;

        // Validate input
        if (!$appointmentId) {
            http_response_code(400);
            echo json_encode([
                'success' => false, 
                'error' => 'Invalid appointment ID',
                'details' => 'No appointment ID provided'
            ]);
            exit;
        }

        try {
            // Start transaction
            $database->begin_transaction();

            // Fetch appointment details with comprehensive error checking
            $detailQuery = "SELECT 
                a.scheduleid, 
                a.pid, 
                p.pname, 
                d.docname, 
                s.scheduledate, 
                s.scheduletime,
                s.available_slots
                FROM appointment a
                JOIN patient p ON a.pid = p.pid
                JOIN schedule s ON a.scheduleid = s.scheduleid
                JOIN doctor d ON s.docid = d.docid
                WHERE a.appoid = ?";
            
            $stmt = $database->prepare($detailQuery);
            if (!$stmt) {
                throw new Exception("Failed to prepare detail query: " . $database->error);
            }

            $stmt->bind_param("i", $appointmentId);
            if (!$stmt->execute()) {
                throw new Exception("Failed to execute detail query: " . $stmt->error);
            }

            $detailResult = $stmt->get_result();
            $details = $detailResult->fetch_assoc();

            if (!$details) {
                throw new Exception("No appointment details found for ID: " . $appointmentId);
            }

            // Check slot availability for approve action
            if ($action == 'approve' && $details['available_slots'] <= 0) {
                throw new Exception("No available slots for this schedule");
            }

            // Determine next steps based on action
            if ($action == 'approve') {
                // Fetch max appointment number
                $maxAppoNumQuery = "SELECT COALESCE(MAX(apponum), 0) + 1 as next_apponum 
                    FROM appointment 
                    WHERE scheduleid = ? AND status = 'Approved'";
                $stmt = $database->prepare($maxAppoNumQuery);
                $stmt->bind_param("i", $details['scheduleid']);
                $stmt->execute();
                $maxResult = $stmt->get_result();
                $maxRow = $maxResult->fetch_assoc();
                $nextAppoNum = $maxRow['next_apponum'];

                // Update appointment
                $updateQuery = "UPDATE appointment 
                    SET is_confirmed = 1, 
                        status = 'Approved', 
                        apponum = ? 
                    WHERE appoid = ?";
                $stmt = $database->prepare($updateQuery);
                $stmt->bind_param("ii", $nextAppoNum, $appointmentId);
                
                // Update available slots
                $slotQuery = "UPDATE schedule 
                    SET available_slots = available_slots - 1 
                    WHERE scheduleid = ?";
                $slotStmt = $database->prepare($slotQuery);
                $slotStmt->bind_param("i", $details['scheduleid']);

            } else { // Reject
                $updateQuery = "UPDATE appointment 
                    SET is_confirmed = -1, 
                        status = 'Rejected', 
                        rejection_reason = ?, 
                        rejection_timestamp = NOW() 
                    WHERE appoid = ?";
                $stmt = $database->prepare($updateQuery);
                $stmt->bind_param("si", $reason, $appointmentId);
            }

            if (!$stmt->execute()) {
                throw new Exception("Failed to update appointment: " . $stmt->error);
            }

            // Execute slot update for approve action
            if ($action == 'approve') {
                if (!$slotStmt->execute()) {
                    throw new Exception("Failed to update available slots: " . $slotStmt->error);
                }
            }

            // Commit transaction
            $database->commit();

            // Prepare success response
            $response = [
                'success' => true,
                'action' => $action,
                'patientName' => $details['pname'],
                'doctorName' => $details['docname'],
                'scheduleDate' => $details['scheduledate'],
                'scheduleTime' => $details['scheduletime']
            ];

            echo json_encode($response);
            exit;

        } catch (Exception $e) {
            // Rollback transaction
            $database->rollback();

            // Log detailed error
            error_log("Appointment " . $action . " Error: " . $e->getMessage());

            // Return error response
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage(),
                'action' => $action
            ]);
            exit;
        }
    }
}
?>
    </div>

</body>
</html>