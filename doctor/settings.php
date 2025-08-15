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
        


    <title>Settings</title>
    <style>
        .dashbord-tables{
            animation: transitionIn-Y-over 0.5s;
        }
        .filter-container{
            animation: transitionIn-X  0.5s;
        }
        .sub-table{
            animation: transitionIn-Y-bottom 0.5s;
        }
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
            margin-top: 20px;
        }

        .form-card {
            background: #f7f7f7;
            border-radius: 12px;
            padding: 16px 14px 8px 14px;
            display: flex;
            flex-direction: column;
            box-sizing: border-box;
        }

        .form-header {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 18px;
        }

        .icon-circle {
            background: #4c9f70;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 70px;
            height: 70px;
            border-radius: 50%;
            margin-bottom: 10px;
        }

        .form-actions {
            display: flex;
            justify-content: center;
            gap: 16px;
            margin-top: 24px;
        }

        .btn.primary {
            background: #4c9f70;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 10px 28px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
        }

        .btn.secondary {
            background: #e0e0e0;
            color: #333;
            border: none;
            border-radius: 8px;
            padding: 10px 28px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
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
    //echo $username;
    
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
                    <td class="menu-btn menu-icon-dashbord" >
                        <a href="index.php" class="non-style-link-menu "><div><p class="menu-text">Dashboard</p></a></div></a>
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
                    <td class="menu-btn menu-icon-settings  menu-active menu-icon-settings-active">
                        <a href="settings.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">Settings</p></a></div>
                    </td>
                </tr>
                
            </table>
        </div>
        <div class="dash-body" style="margin-top: 15px">
            <table border="0" width="100%" style=" border-spacing: 0;margin:0;padding:0;" >
                        
                        <tr >
                            
                    <!-- <td width="13%" >
                    <a href="settings.php" ><button  class="login-btn btn-primary-soft btn btn-icon-back"  style="padding-top:11px;padding-bottom:11px;margin-left:20px;width:125px"><font class="tn-in-text">Back</font></button></a>
                    </td> -->
                    <td>
                        <p style="font-size: 23px;padding-left:12px;font-weight: 600;">Settings</p>
                                           
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
                    <td colspan="4">
                        
                        <center>
                        <table class="filter-container" style="border: none;" border="0">
                            <tr>
                                <td colspan="4">
                                    <p style="font-size: 20px">&nbsp;</p>
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 25%;">
                                    <a href="?action=edit&id=<?php echo $userid ?>&error=0" class="non-style-link">
                                    <div  class="dashboard-items setting-tabs"  style="padding:20px;margin:auto;width:95%;display: flex">
                                        <div class="btn-icon-back dashboard-icons-setting" style="background-image: url('../img/icons/doctors-hover.svg');"></div>
                                        <div>
                                                <div class="h1-dashboard">
                                                    Account Settings  &nbsp;

                                                </div><br>
                                                <div class="h3-dashboard" style="font-size: 15px;">
                                                    Edit your Account Details & Change Password
                                                </div>
                                        </div>
                                                
                                    </div>
                                    </a>
                                </td>
                                
                                
                            </tr>
                            <tr>
                                <td colspan="4">
                                    <p style="font-size: 5px">&nbsp;</p>
                                </td>
                            </tr>
                            <tr>
                            <td style="width: 25%;">
                                    <a href="?action=view&id=<?php echo $userid ?>" class="non-style-link">
                                    <div  class="dashboard-items setting-tabs"  style="padding:20px;margin:auto;width:95%;display: flex;">
                                        <div class="btn-icon-back dashboard-icons-setting " style="background-image: url('../img/icons/view-iceblue.svg');"></div>
                                        <div>
                                                <div class="h1-dashboard" >
                                                    View Account Details
                                                    
                                                </div><br>
                                                <div class="h3-dashboard"  style="font-size: 15px;">
                                                    View Personal information About Your Account
                                                </div>
                                        </div>
                                                
                                    </div>
                                    </a>
                                </td>
                                
                            </tr>
                            <tr>
                                <td colspan="4">
                                    <p style="font-size: 5px">&nbsp;</p>
                                </td>
                            </tr>
                            <tr>
                            <!-- <td style="width: 25%;">
                                    <a href="?action=drop&id=<?php echo $userid.'&name='.$username ?>" class="non-style-link">
                                    <div  class="dashboard-items setting-tabs"  style="padding:20px;margin:auto;width:95%;display: flex;">
                                        <div class="btn-icon-back dashboard-icons-setting" style="background-image: url('../img/icons/patients-hover.svg');"></div>
                                        <div>
                                                <div class="h1-dashboard" style="color: #ff5050;">
                                                    Delete Account
                                                    
                                                </div><br>
                                                <div class="h3-dashboard"  style="font-size: 15px;">
                                                    Will Permanently Remove your Account
                                                </div>
                                        </div>
                                                
                                    </div>
                                    </a>
                                </td> -->
                                
                            </tr>
                        </table>
                    </center>
                    </td>
                </tr>
            
            </table>
        </div>
    </div>
    <?php 
        if($_GET){
            $id=$_GET["id"];
            $action=$_GET["action"];
            if($action=='drop'){
                $nameget=$_GET["name"];
                echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
                echo '<script type="text/javascript">
                Swal.fire({
                title: "Are you sure?",
                text: "You want to delete this record.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, delete it",
                cancelButtonText: "No, cancel",
                reverseButtons: true
                }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "delete-doctor.php?id='.$id.'";
                } else {
                    window.location.href = "settings.php";
                }
                });
                </script>';
        }elseif($action=='view'){
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
                        <a class="close" href="settings.php">&times;</a>
                        <div class="content">
                            Mabayuan Health <br> App<br>
                            
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

                           

                        </table>
                        </div>
                    </center>
                    <br><br>
            </div>
            </div>
            ';
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
if ($error_1 != '4') {
    // Generate specialties dropdown
    $specialtyOptions = '';
    $list11 = $database->query("SELECT * FROM specialties;");
    while ($row00 = $list11->fetch_assoc()) {
        $sn = $row00["sname"];
        $id00 = $row00["id"];
        $selected = ($spcil_name == $sn) ? "selected" : "";
        $specialtyOptions .= "<option value='$id00' $selected>$sn</option>";
    }

    // SVG icons for inputs
    $emailSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#4c9f70" viewBox="0 0 16 16"><path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v.217l-8 4.8-8-4.8V4z"/><path d="M0 6.383v5.617a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V6.383l-8 4.8-8-4.8z"/></svg>';
    $nameSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#4c9f70" viewBox="0 0 16 16"><path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/></svg>';
    $phoneSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#4c9f70" viewBox="0 0 16 16"><path d="M3.654 1.328a.678.678 0 0 1 1.015-.063l2.29 2.29a.678.678 0 0 1-.063 1.015l-1.232.87a.678.678 0 0 0-.168.739c.257.667.62 1.283 1.077 1.757.454.47 1.002.832 1.674 1.09a.678.678 0 0 0 .739-.168l.87-1.232a.678.678 0 0 1 1.015-.063l2.29 2.29a.678.678 0 0 1-.063 1.015l-2.507 1.9a1.745 1.745 0 0 1-1.962-.288 16.627 16.627 0 0 1-5.07-5.07 1.745 1.745 0 0 1-.288-1.962l1.9-2.507z"/></svg>';
    $philhealthSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#4c9f70" viewBox="0 0 16 16"><path d="M2 2h12v12H2z" fill="none"/><path d="M4 4h8v8H4z"/></svg>';
    $specialtySvg = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#4c9f70" viewBox="0 0 16 16"><path d="M8 0a8 8 0 1 0 0 16A8 8 0 0 0 8 0zM4.5 7.5a.5.5 0 0 1 0 1H3.25a.25.25 0 0 1-.25-.25v-1.5a.5.5 0 0 1 1 0v1.5h1.5zm7.5 0a.5.5 0 0 1 1 0v1.5h1.5a.5.5 0 0 1 0 1h-1.5v1.5a.5.5 0 0 1-1 0v-1.5h-1.5a.5.5 0 0 1 0-1h1.5v-1.5a.5.5 0 0 1 .5-.5z"/></svg>';
    $currentPassSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#4c9f70" viewBox="0 0 16 16"><path d="M8 1a4 4 0 0 0-4 4v2H3a1 1 0 0 0-1 1v5a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1v-5a1 1 0 0 0-1-1h-1V5a4 4 0 0 0-4-4zm-2 6V5a2 2 0 1 1 4 0v2H6z"/></svg>';
    $newPassSvg = $currentPassSvg;
    $confirmPassSvg = $currentPassSvg;

    echo '
    <div id="popup1" class="overlay">
        <div class="popup refined-edit-form">
            <a class="close" href="settings.php">&times;</a>
            <div class="form-header">
                <span class="icon-circle">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="#fff" viewBox="0 0 16 16">
                        <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm4.285 2.433A5.978 5.978 0 0 0 8 10c-1.306 0-2.518.418-3.285 1.433C4.105 12.07 5.522 13 8 13s3.895-.93 4.285-2.567z"/>
                    </svg>
                </span>
                <h2>Edit Doctor Profile</h2>
            </div>
            <form action="edit-doc.php" method="POST" autocomplete="off">
                <input type="hidden" name="id00" value="'.htmlspecialchars($id).'">
                <input type="hidden" name="oldemail" value="'.htmlspecialchars($email).'">
                <div class="form-grid">
                    <div class="form-card">
                        <label>'.$emailSvg.' Email</label>
                        <input type="email" name="email" value="'.htmlspecialchars($email).'" autocomplete="email" required>
                    </div>
                    <div class="form-card">
                        <label>'.$nameSvg.' Name</label>
                        <input type="text" name="name" value="'.htmlspecialchars($name).'" autocomplete="name" required>
                    </div>
                    <div class="form-card">
                        <label>'.$phoneSvg.' Telephone</label>
                        <input type="tel" name="Tele" value="'.htmlspecialchars($phone_number).'" autocomplete="tel" required>
                    </div>
                    <div class="form-card">
                        <label>'.$specialtySvg.' Specialty</label>
                        <select name="spec" required>'.$specialtyOptions.'</select>
                    </div>
                    <div class="form-card">
                        <label>'.$philhealthSvg.' PhilHealth ID</label>
                        <input type="text" name="nic" value="'.htmlspecialchars($nic).'" autocomplete="off" required>
                    </div>
                    <div class="form-card">
                        <label>'.$currentPassSvg.' Current Password</label>
                        <input type="password" name="password" autocomplete="current-password" required>
                    </div>
                    <div class="form-card">
                        <label>'.$newPassSvg.' New Password</label>
                        <input type="password" name="newpassword" autocomplete="new-password">
                    </div>
                    <div class="form-card">
                        <label>'.$confirmPassSvg.' Confirm Password</label>
                        <input type="password" name="cpassword" autocomplete="new-password">
                    </div>
                </div>
                <div class="form-actions">
                    <button type="reset" class="btn secondary">Reset</button>
                    <button type="submit" class="btn primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>';
}

        }
    }
        ?>

    <?php
    // Place this after your modal HTML
    if (isset($_GET['error'])) {
        echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
        $error = $_GET['error'];
        if ($error == '1') {
            echo "
            <script>
            Swal.fire({
                icon: 'error',
                title: 'Incorrect Current Password',
                text: 'The current password you entered is incorrect.',
            });
            </script>
            ";
        } elseif ($error == '2') {
            echo "
            <script>
            Swal.fire({
                icon: 'error',
                title: 'Password Mismatch',
                text: 'The new password and confirm password do not match.',
            });
            </script>
            ";
            
        }
        elseif ($error == '3') {
            echo "
            <script>
            Swal.fire({
                icon: 'info',
                title: 'No changes made',
                text: 'No changes were made to your profile.',
            });
            </script>
            ";
        } elseif ($error == '4') {
            echo "
            <script>
            Swal.fire({
                icon: 'success',
                title: 'Profile Updated',
                text: 'Your profile and password have been updated successfully!',
            });
            </script>
            ";
        }

    }
    ?>

</body>
</html>