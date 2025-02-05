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
        if(($_SESSION["user"])=="" or $_SESSION['usertype']!='d'){
            header("location: ../login.php");
        }else{
            $useremail=$_SESSION["user"];
        }

    }else{
        header("location: ../login.php");
    }
    
    

       //import database
       include("../connection.php");
       $userrow = $database->query("select * from doctor where docemail='$useremail'");
       $userfetch=$userrow->fetch_assoc();
       $userid= $userfetch["docid"];
       $username=$userfetch["docname"];
    //echo $userid;
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
                                    <p class="profile-title"><?php echo substr($username,0,13)  ?>..</p>
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
                    <td class="menu-btn menu-icon-dashbord " >
                        <a href="index.php" class="non-style-link-menu "><div><p class="menu-text">Dashboard</p></a></div></a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-appoinment  menu-active menu-icon-appoinment-active">
                        <a href="appointment.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">My Appointments</p></a></div>
                    </td>
                </tr>
                
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-session">
                        <a href="schedule.php" class="non-style-link-menu"><div><p class="menu-text">My Sessions</p></div></a>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-patient">
                        <a href="patient.php" class="non-style-link-menu"><div><p class="menu-text">My Patients</p></a></div>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-settings">
                        <a href="settings.php" class="non-style-link-menu"><div><p class="menu-text">Settings</p></a></div>
                    </td>
                </tr>
                
            </table>
        </div>
 
    </div>
    <?php

if ($_GET) {
    $id = $_GET["id"];
    $action = $_GET["action"];
    if ($action == 'view') {
        // Fetch the appointment details from the database
        $sqlmain = "SELECT appointment.appoid, schedule.title, doctor.docname, patient.pname, 
                           schedule.scheduledate, schedule.scheduletime, appointment.apponum, 
                           appointment.appodate, appointment.is_self, appointment.other_patient_name, 
                           appointment.age, appointment.philhealth_id
                    FROM schedule 
                    INNER JOIN appointment ON schedule.scheduleid = appointment.scheduleid 
                    INNER JOIN patient ON patient.pid = appointment.pid 
                    INNER JOIN doctor ON schedule.docid = doctor.docid  
                    WHERE appointment.appoid = '$id'";

        $result = $database->query($sqlmain);
        $row = $result->fetch_assoc();

        // Extract the values from the query result
        $pname = $row["pname"];
        $title = $row["title"];
        $docname = $row["docname"];
        $scheduledate = $row["scheduledate"];
        $scheduletime = $row["scheduletime"];
        $apponum = $row["apponum"];
        $appodate = $row["appodate"];
        $is_self = $row["is_self"];
        $other_patient_name = $row["other_patient_name"];
        $age = $row["age"];
        $philhealth_id = $row["philhealth_id"];

        // Conditionally change the patient name and additional information
        if ($is_self == 0) {
            // If the appointment is for someone else
            $pname = isset($other_patient_name) ? $other_patient_name : 'N/A';
            $age = isset($age) ? $age : 'N/A';
            $philhealth_id = isset($philhealth_id) ? $philhealth_id : 'N/A';
        }

        // Generate the output
        echo '
        <div id="popup1" class="overlay">
            <div class="popup">
                <center>
                    <h2>Appointment Details</h2>
                    <a class="close" href="appointment.php">&times;</a>
                    <div class="content">
                        Details of the appointment are shown below:
                    </div>
                    <div style="display: flex;justify-content: center;">
                    <table width="80%" class="sub-table scrolldown add-doc-form-container" border="0">
                    
                        <tr>
                            <td class="label-td" colspan="2">
                                <label for="pname" class="form-label">Patient Name: </label>
                            </td>
                        </tr>
                        <tr>
                            <td class="label-td" colspan="2">
                                '.$pname.'<br><br>
                            </td>
                        </tr>

                        <tr>
                            <td class="label-td" colspan="2">
                                <label for="title" class="form-label">Session Title: </label>
                            </td>
                        </tr>
                        <tr>
                            <td class="label-td" colspan="2">
                                '.$title.'<br><br>
                            </td>
                        </tr>

                        <tr>
                            <td class="label-td" colspan="2">
                                <label for="docname" class="form-label">Doctor: </label>
                            </td>
                        </tr>
                        <tr>
                            <td class="label-td" colspan="2">
                                '.$docname.'<br><br>
                            </td>
                        </tr>

                        <tr>
                            <td class="label-td" colspan="2">
                                <label for="scheduledate" class="form-label">Session Date: </label>
                            </td>
                        </tr>
                        <tr>
                            <td class="label-td" colspan="2">
                                '.$scheduledate.'<br><br>
                            </td>
                        </tr>

                        <tr>
                            <td class="label-td" colspan="2">
                                <label for="scheduletime" class="form-label">Session Time: </label>
                            </td>
                        </tr>
                        <tr>
                            <td class="label-td" colspan="2">
                                '.$scheduletime.'<br><br>
                            </td>
                        </tr>

                        <tr>
                            <td class="label-td" colspan="2">
                                <label for="appodate" class="form-label">Appointment Date: </label>
                            </td>
                        </tr>
                        <tr>
                            <td class="label-td" colspan="2">
                                '.$appodate.'<br><br>
                            </td>
                        </tr>';

                        if ($is_self == 0) {
                            // Display additional info when it's for another patient
                            echo '
                            <tr>
                                <td class="label-td" colspan="2">
                                    <label for="age" class="form-label">Age: </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    '.$age.'<br><br>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <label for="philhealth_id" class="form-label">PhilHealth ID: </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    '.$philhealth_id.'<br><br>
                                </td>
                            </tr>';
                        }

        echo '
                        <tr>
                            <td colspan="2">
                                <a href="appointment.php"><input type="button" value="OK" class="login-btn btn-primary-soft btn" ></a>
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