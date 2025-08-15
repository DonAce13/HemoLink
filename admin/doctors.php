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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

        
    <title>Doctors</title>
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
                                    <p class="profile-subtitle">administrator@gmail.com </p>
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
                    <td class="menu-btn menu-icon-doctor menu-active menu-icon-doctor-active ">
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
                    <td class="menu-btn menu-icon-logs">
                        <a href="logs.php" class="non-style-link-menu">
                            <div style="display:flex;align-items:center;gap:8px;">
                                <span class="menu-icon-svg menu-icon-logs-svg"></span>
                                <p class="menu-text">Logs</p>
                            </div>
                        </a>
                    </td>
                </tr>


            </table>
        </div>
        <div class="dash-body" style="margin-top: 15px;">
    <table border="0" width="100%" style="border-spacing: 0; margin: 0; padding: 0;">
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
                                    
                               
                                   
                                
                                </form>
                                
                            </td>

    <table border="0" width="100%" style="border-spacing: 0; margin: 0; padding: 0; margin-top: 25px;">
        <tr>
            <!-- Search Section -->
            
        </tr>



               
        <tr >
                    <td colspan="2" style="padding-top:30px;">
                        <p class="heading-main12" style="margin-left: 45px;font-size:20px;color:rgb(49, 49, 49)">Add New Doctor</p>
                    </td>
                    <td colspan="2">
                        <a href="?action=add&id=none&error=0" class="non-style-link"><button  class="login-btn btn-primary btn button-icon"  style="display: flex;justify-content: center;align-items: center;margin-right:700px;margin-top:25px;background-image: url('../img/icons/add.svg');">Add New</font></button>
                            </a></td>
                </tr>

                
                <tr>
                    <td colspan="4" style="padding-top:10px;">
                        <p class="heading-main12" style="margin-left: 45px;font-size:18px;color:rgb(49, 49, 49)">All Doctors (<?php echo $list11->num_rows; ?>)</p>
                    </td>
                    
                </tr>
                <?php
                    if($_POST){
                        $keyword=$_POST["search"];
                        
                        $sqlmain= "select * from doctor where docemail='$keyword' or docname='$keyword' or docname like '$keyword%' or docname like '%$keyword' or docname like '%$keyword%'";
                    }else{
                        $sqlmain= "select * from doctor order by docid desc";

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
                                    
                                
                                Doctor Name
                                
                                </th>
                                <th class="table-headin">
                                    Email
                                </th>
                                <th class="table-headin">
                                    
                                    Specialties
                                    
                                </th>
                                <th class="table-headin">
                                    
                                    Actions
                                    
                                </tr>
                        </thead>
                        <tbody>
                        
                            <?php

                                
                                $result= $database->query($sqlmain);

                                if($result->num_rows==0){
                                    echo '<tr>
                                    <td colspan="4">
                                    <br><br><br><br>
                                    <center>
                                    <img src="../img/notfound.svg" width="25%">
                                    
                                    <br>
                                    <p class="heading-main12" style="margin-left: 45px;font-size:20px;color:rgb(49, 49, 49)">We cannot find anything related to your keywords !</p>
                                    <a class="non-style-link" href="doctors.php"><button  class="login-btn btn-primary-soft btn"  style="display: flex;justify-content: center;align-items: center;margin-left:20px;">&nbsp; Show all Doctors &nbsp;</font></button>
                                    </a>
                                    </center>
                                    <br><br><br><br>
                                    </td>
                                    </tr>';
                                    
                                }
                                else{
                                for ( $x=0; $x<$result->num_rows;$x++){
                                    $row=$result->fetch_assoc();
                                    $docid=$row["docid"];
                                    $name=$row["docname"];
                                    $email=$row["docemail"];
                                    $spe=$row["specialties"];
                                    $spcil_res= $database->query("select sname from specialties where id='$spe'");
                                    $spcil_array= $spcil_res->fetch_assoc();
                                    $spcil_name=$spcil_array["sname"];
                                    echo '<tr>
                                        <td style="text-align: center;"> &nbsp;'.
                                        substr($name,0,30)
                                        .'</td>
                                        <td style="text-align: center;"> &nbsp;'
                                        .substr($email,0,20).
                                        '</td>
                                        <td style="text-align: center;"> &nbsp;'
                                        .substr($spcil_name,0,20).'
                                        </td>

                                        <td>
                                        <div style="display:flex;justify-content: center;">
                                        <a href="?action=edit&id='.$docid.'&error=0" class="non-style-link"><button  class="btn-primary-soft btn button-icon btn-edit"  style="padding-left: 40px;padding-top: 12px;padding-bottom: 12px;margin-top: 10px;"><font class="tn-in-text">Edit</font></button></a>
                                        &nbsp;&nbsp;&nbsp;
                                        <a href="?action=view&id='.$docid.'" class="non-style-link"><button  class="btn-primary-soft btn button-icon btn-view"  style="padding-left: 40px;padding-top: 12px;padding-bottom: 12px;margin-top: 10px;"><font class="tn-in-text">View</font></button></a>
                                       &nbsp;&nbsp;&nbsp;
                                       <a href="?action=drop&id='.$docid.'&name='.$name.'" class="non-style-link"><button  class="btn-primary-soft btn button-icon btn-delete"  style="padding-left: 40px;padding-top: 12px;padding-bottom: 12px;margin-top: 10px;"><font class="tn-in-text">Remove</font></button></a>
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
    if ($action == 'drop') {
        $nameget = $_GET["name"];
        
        // SweetAlert implementation
        echo '
        <script>
            Swal.fire({
                title: "Are you sure?",
                text: "You want to delete this record (' . substr($nameget, 0, 40) . ').",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "No, cancel!"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "delete-doctor.php?id=' . $id . '";
                } else {
                    window.location.href = "doctors.php"; // Redirect back to doctors page if canceled
                }
            });
        </script>
        ';
}elseif($action == 'view'){
    $sqlmain = "SELECT * FROM doctor WHERE docid='$id'";
    $result = $database->query($sqlmain);
    $row = $result->fetch_assoc();

    $name = htmlspecialchars($row["docname"]);
    $email = htmlspecialchars($row["docemail"]);
    $nic = empty($row["docnic"]) ? 'No' : htmlspecialchars($row["docnic"]);
    $phone_number = empty($row["doctel"]) ? 'No' : htmlspecialchars($row["doctel"]);

    $spe = $row["specialties"];
    $spcil_res = $database->query("SELECT sname FROM specialties WHERE id='$spe'");
    $spcil_array = $spcil_res->fetch_assoc();
    $spcil_name = htmlspecialchars($spcil_array["sname"]);

   echo '
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"></script>
<style>
    .doctor-profile-container {
        font-family: "Arial", sans-serif;
    }
    .doctor-header {
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
    }
    .doctor-avatar {
        font-size: 50px;
        color: #2d9c6e;
        margin-right: 15px;
    }
    .doctor-name-id h2 {
        margin: 0;
        font-size: 24px;
    }
    .doctor-id-text {
        font-size: 14px;
        color: gray;
    }
    .doctor-details-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
        margin-top: 20px;
    }
    .doctor-detail-item {
        background: #f9f9f9;
        padding: 15px;
        border-radius: 10px;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .doctor-detail-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    .doctor-detail-item i {
        font-size: 24px;
        color: #444;
        margin-bottom: 8px;
    }
    .doctor-detail-item h3 {
        margin: 0;
        font-size: 14px;
        color: #888;
    }
    .doctor-detail-item p {
        margin: 0;
        font-size: 16px;
        font-weight: 500;
    }
</style>
<script>
    Swal.fire({
        title: "Doctor Profile",
        html: `
            <div class="doctor-profile-container">
                <div class="doctor-header">
                    <div class="doctor-avatar">
                        <i class="fas fa-user-md"></i>
                    </div>
                    <div class="doctor-name-id">
                        <h2>' . $name . '</h2>
                        <p class="doctor-id-text">Doctor ID: D-' . $id . '</p>
                    </div>
                </div>
                <div class="doctor-details-grid">
                    <div class="doctor-detail-item">
                        <i class="fas fa-envelope"></i>
                        <h3>Email</h3>
                        <p>' . $email . '</p>
                    </div>
                    <div class="doctor-detail-item">
                        <i class="fas fa-phone"></i>
                        <h3>Telephone</h3>
                        <p>' . $phone_number . '</p>
                    </div>
                    <div class="doctor-detail-item">
                        <i class="fas fa-id-card"></i>
                        <h3>PhilHealth ID</h3>
                        <p>' . $nic . '</p>
                    </div>
                    <div class="doctor-detail-item">
                        <i class="fas fa-stethoscope"></i>
                        <h3>Specialty</h3>
                        <p>' . $spcil_name . '</p>
                    </div>
                </div>
            </div>
        `,
        icon: "info",
        confirmButtonText: "Close",
        showCloseButton: true,
        customClass: {
            popup: "doctor-profile-popup",
            htmlContainer: "doctor-profile-container",
            confirmButton: "btn btn-primary"
        }
    });
</script>';
    exit();



        }elseif($action=='add'){
                $error_1=$_GET["error"];
                $errorlist= array(
                    '1'=>'<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Already have an account for this Email address.</label>',
                    '2'=>'<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Password Conformation Error! Reconform Password</label>',
                    '3'=>'<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;"></label>',
                    '4'=>"",
                    '0'=>'',

                );
                if($error_1!='4'){
                echo '
            <div id="popup1" class="overlay">
                    <div class="popup">
                    <center>
                    
                        <a class="close" href="doctors.php">&times;</a> 
                        <div style="display: flex;justify-content: center;">
                        <div class="abc">
                        <table width="80%" class="sub-table scrolldown add-doc-form-container" border="0">
                        <tr>
                                <td class="label-td" colspan="2">'.
                                    $errorlist[$error_1]
                                .'</td>
                            </tr>
                            <tr>
                                <td>
                                    <p style="padding: 0;margin: 0;text-align: left;font-size: 25px;font-weight: 500;">Add New Doctor.</p><br><br>
                                </td>
                            </tr>
                            
                            <tr>
                                <form action="add-new.php" method="POST" class="add-new-form">
                                <td class="label-td" colspan="2">
                                    <label for="name" class="form-label">Name: </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <input type="text" name="name" class="input-text" placeholder="Doctor Name" required><br>
                                </td>
                                
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <label for="Email" class="form-label">Email: </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <input type="email" name="email" class="input-text" placeholder="Email Address" required><br>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <label for="nic" class="form-label">PhilHealth ID:</label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <input type="text" name="nic" class="input-text" placeholder="PhilHealth ID:" required><br>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <label for="Tele" class="form-label">Telephone: </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <input type="tel" name="Tele" class="input-text" placeholder="Telephone Number" required><br>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <label for="spec" class="form-label">Choose specialties: </label>
                                    
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <select name="spec" id="" class="box" >';
                                        
        
                                        $list11 = $database->query("select  * from  specialties order by sname asc;");
        
                                        for ($y=0;$y<$list11->num_rows;$y++){
                                            $row00=$list11->fetch_assoc();
                                            $sn=$row00["sname"];
                                            $id00=$row00["id"];
                                            echo "<option value=".$id00.">$sn</option><br/>";
                                        };
        
        
        
                                        
                        echo     '       </select><br>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <label for="password" class="form-label">Password: </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <input type="password" name="password" class="input-text" placeholder="New Password" required><br>
                                </td>
                            </tr><tr>
                                <td class="label-td" colspan="2">
                                    <label for="cpassword" class="form-label">Confirm Password: </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <input type="password" name="cpassword" class="input-text" placeholder="Confirm Password" required><br>
                                </td>
                            </tr>
                            
                
                            <tr>
                                <td colspan="2">
                                    <input type="reset" value="Reset" class="login-btn btn-primary-soft btn" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                
                                    <input type="submit" value="Add" class="login-btn btn-primary btn">
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
            </div>
            ';

            }else{
                echo '
                    <div id="popup1" class="overlay">
                            <div class="popup">
                            <center>
                            <br><br><br><br>
                                <h2>New Record Added Successfully!</h2>
                                <a class="close" href="doctors.php">&times;</a>
                                <div class="content">
                                    
                                    
                                </div>
                                <div style="display: flex;justify-content: center;">
                                
                                <a href="doctors.php" class="non-style-link"><button  class="btn-primary btn"  style="display: flex;justify-content: center;align-items: center;margin:10px;padding:10px;"><font class="tn-in-text">&nbsp;&nbsp;OK&nbsp;&nbsp;</font></button></a>

                                </div>
                                <br><br>
                            </center>
                    </div>
                    </div>
        ';
            }
        }elseif($action=='edit'){
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

            $error_1=$_GET["error"];
                $errorlist= array(
                    '1'=>'<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Already have an account for this Email address.</label>',
                    '2'=>'<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Password Conformation Error! Reconform Password</label>',
                    '3'=>'<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;"></label>',
                    '4'=>"",
                    '0'=>'',

                );

            if($error_1!='4'){
                    echo '
                        <style>
                            .form-grid {
                                display: grid;
                                grid-template-columns: repeat(2, 1fr);
                                gap: 20px;
                                margin-top: 20px;
                            }
                            .form-item {
                                background: #f9f9f9;
                                border-radius: 12px;
                                padding: 15px;
                                text-align: center;
                                display: flex;
                                flex-direction: column;
                                align-items: center;
                                transition: transform 0.2s ease, box-shadow 0.2s ease;
                            }
                            .form-item:hover {
                                transform: translateY(-5px);
                                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
                            }
                            .form-item i {
                                font-size: 24px;
                                margin-bottom: 8px;
                                color: #2d9c6e;
                            }
                            .form-item label {
                                font-weight: 600;
                                margin-bottom: 5px;
                            }
                            .form-item input, .form-item select {
                                width: 100%;
                                padding: 8px;
                                border-radius: 8px;
                                border: 1px solid #ccc;
                                font-size: 14px;
                            }
                        </style>

                        <div id="popup1" class="overlay">
                        <div class="popup">
                            <center>
                            <a class="close" href="doctors.php">&times;</a>
                            <div style="width: 90%;">
                                <p style="font-size: 25px; font-weight: 500;">Edit Doctor Details</p>
                                <form action="edit-doc.php" method="POST" class="add-new-form">
                                    <input type="hidden" value="'.$id.'" name="id00">
                                    <input type="hidden" name="oldemail" value="'.$email.'" >

                                    <div class="form-grid">
                                        <div class="form-item">
                                            <i class="fas fa-envelope"></i>
                                            <label>Email</label>
                                            <input type="email" name="email" placeholder="Email Address" value="'.$email.'" required>
                                        </div>
                                        <div class="form-item">
                                            <i class="fas fa-user"></i>
                                            <label>Name</label>
                                            <input type="text" name="name" placeholder="Doctor Name" value="'.$name.'" required>
                                        </div>
                                        <div class="form-item">
                                            <i class="fas fa-id-card"></i>
                                            <label>PhilHealth ID</label>
                                            <input type="text" name="nic" placeholder="PhilHealth ID" value="'.$nic.'" required>
                                        </div>
                                        <div class="form-item">
                                            <i class="fas fa-phone"></i>
                                            <label>Telephone</label>
                                            <input type="tel" name="Tele" placeholder="Telephone Number" value="'.$phone_number.'" required>
                                        </div>
                                        <div class="form-item">
                                            <i class="fas fa-user-md"></i>
                                            <label>Specialty (Current: '.$spcil_name.')</label>
                                            <select name="spec">';
                                                $list11 = $database->query("select * from specialties;");
                                                for ($y=0;$y<$list11->num_rows;$y++){
                                                    $row00=$list11->fetch_assoc();
                                                    $sn=$row00["sname"];
                                                    $id00=$row00["id"];
                                                    echo "<option value=".$id00.">$sn</option>";
                                                };
                        echo '              </select>
                                        </div>
                                        <div class="form-item">
                                            <i class="fas fa-lock"></i>
                                            <label>Password</label>
                                            <input type="password" name="password" placeholder="New Password" required>
                                        </div>
                                        <div class="form-item">
                                            <i class="fas fa-lock"></i>
                                            <label>Confirm Password</label>
                                            <input type="password" name="cpassword" placeholder="Confirm Password" required>
                                        </div>
                                    </div>
                                    <br>
                                    <div style="text-align:center;">
                                        <input type="reset" value="Reset" class="login-btn btn-primary-soft btn">
                                        &nbsp;&nbsp;&nbsp;
                                        <input type="submit" value="Save" class="login-btn btn-primary btn">
                                    </div>
                                </form>
                            </div>
                            </center>
                        </div>
                        </div>';

        }else{
            echo '
            <script>
                Swal.fire({
                    title: "Edit Successfully!",
                    text: "Your changes have been saved.",
                    icon: "success",
                    confirmButtonText: "OK"
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "doctors.php"; // Redirect to doctors page after confirmation
                    }
                });
            </script>
            ';



        }; };
    };

?>
</div>

</body>
</html>