<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/animations.css">  
    <link rel="stylesheet" href="../css/main.css">  
    <link rel="stylesheet" href="../css/admin.css">
        
    <title>Appointments</title>
    <style>
        a.login-btn.btn-primary-soft.btn.non-style-link:hover {
            color: #fff !important;
        }
        /* --- Mobile Sticky Hamburger Header --- */
        @media (max-width: 992px) {
            #mobile-hamburger-header {
                display: block !important;
                position: sticky;
                top: 0;
                left: 0;
                width: 100vw;
                height: 54px;
                background: #2d6a4f;
                z-index: 2000;
                box-shadow: 0 2px 8px rgba(0,0,0,0.09);
            }
            .hamburger {
                position: fixed;
                top: 8px;
                left: 18px;
                z-index: 2100;
                background: #fff;
                border-radius: 8px;
                box-shadow: 0 2px 8px rgba(0,0,0,0.09);
                padding: 8px 10px;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                width: 46px;
                height: 38px;
                transition: box-shadow 0.2s;
            }
            .hamburger .bar {
                background: #2d6a4f;
            }
        }
        /* --- End Mobile Sticky Hamburger Header --- */
        .popup{
            animation: transitionIn-Y-bottom 0.5s;
        }
        .sub-table {
            animation: transitionIn-Y-bottom 0.5s;
            margin-top: 0px !important;
        }
        .responsive-td {
            width: 25%;
            padding: 10px;
            box-sizing: border-box;
        }

        @media (max-width: 1200px) {
            .responsive-td {
                width: 50%;
            }
        }

        @media (max-width: 768px) {
            .responsive-td {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <?php

    //learn from w3schools.com

    session_start();
    date_default_timezone_set('Asia/Manila');

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
    $userrow = $stmt->get_result();
    $userfetch=$userrow->fetch_assoc();
    $userid= $userfetch["pid"];
    $username=$userfetch["pname"];


    //echo $userid;
    //echo $username;


    // Get the start and end of the current week
    $current_date = new DateTime();
    $week_start = clone $current_date;
    $week_start->modify('this week monday');
    $week_end = clone $current_date;
    $week_end->modify('this week sunday');

    // Check if a specific date is provided in the filter
    $filtered_date = isset($_POST["scheduledate"]) ? $_POST["scheduledate"] : null;

    // If a date is selected, use it to calculate the week
    if ($filtered_date) {
        $selected_date = new DateTime($filtered_date);
        $week_start = clone $selected_date;
        $week_start->modify('this week monday');
        $week_end = clone $selected_date;
        $week_end->modify('this week sunday');
    }

    $sqlmain = "SELECT 
        appointment.appoid,
        schedule.scheduleid,
        schedule.title,
        doctor.docname,
        patient.pname,
        schedule.scheduledate,
        schedule.scheduletime,
        appointment.apponum,
        appointment.appodate,
        appointment.is_confirmed,
        appointment.status,
        appointment.rejection_timestamp,
        appointment.rejection_reason,
        appointment.is_self,
        appointment.other_patient_name
    FROM schedule 
    INNER JOIN appointment ON schedule.scheduleid = appointment.scheduleid 
    INNER JOIN patient ON patient.pid = appointment.pid 
    INNER JOIN doctor ON schedule.docid = doctor.docid  
    WHERE patient.pid = $userid 
    AND schedule.scheduledate BETWEEN ? AND ?";

    // Prepare the statement
    $stmt = $database->prepare($sqlmain);
    $week_start_formatted = $week_start->format('Y-m-d');
    $week_end_formatted = $week_end->format('Y-m-d');

    // If a specific date filter is applied, modify the query
    if ($filtered_date) {
        $stmt->bind_param("ss", $week_start_formatted, $week_end_formatted);
    } else {
        $stmt->bind_param("ss", $week_start_formatted, $week_end_formatted);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    ?>
    <!-- Sticky Mobile Hamburger Header -->
    <div id="mobile-hamburger-header" style="display:none; align-items:center; justify-content:center;">
        <div style="display:flex;align-items:center;justify-content:center;height:54px;width:100vw;">
            <span style="color:#fff;font-size:1.25em;font-weight:bold;letter-spacing:1px;line-height:1;">Mabayuan Health Care</span>
        </div>
    </div>
    <div class="container">
        <div class="hamburger" id="hamburger">
            <div class="bar"></div>
            <div class="bar"></div>
            <div class="bar"></div>
        </div>
        <div class="menu" id="menu">
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
                            
                    </table>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-home" >
                        <a href="index.php" class="non-style-link-menu "><div><p class="menu-text">Home</p></a></div></a>
                    </td>
                </tr>
                
                <!-- <tr class="menu-row" >
                    <td class="menu-btn menu-icon-session">
                        <a href="schedule.php" class="non-style-link-menu"><div><p class="menu-text">Scheduled Sessions</p></div></a>
                    </td>
                </tr> -->
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-appoinment  menu-active menu-icon-appoinment-active">
                        <a href="appointment.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">Booking History</p></a></div>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-settings">
                        <a href="settings.php" class="non-style-link-menu"><div><p class="menu-text">Settings</p></a></div>
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
                        <p class="heading-sub12" style="margin: 0;">
                            <?php 
                                $today = date('Y-m-d');
                                echo $today;
                            ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" style="padding-top:0px;width: 100%;">
                        <center>
                            <table class="filter-container" border="0">
                                <tr>
                                    <td width="10%"></td>
                                    <td width="5%" style="text-align: center;">Date:</td>
                                    <td width="30%">
                                        <form action="" method="post">
                                            <input type="date" name="scheduledate" id="date" 
                                                   class="input-text filter-container-items" 
                                                   style="margin: 0;width: 95%;"
                                                   value="<?php echo $filtered_date ?? $current_date->format('Y-m-d'); ?>">
                                    </td>
                                   
                                </tr>
                            </table>
                        </center>
                    </td>
                </tr>
                <tr>
                <td>
                        <p style="font-size: 23px;padding-left:25px;font-weight: 600;">My Bookings History</p>
                        <p style="font-size: 16px;padding-left:25px;color: #666;">
                            <?php 
                            echo "Appointments for the Week: " . 
                                 $week_start->format('M d') . " - " . $week_end->format('M d'); 
                            ?>
                        </p>
                                           
                    </td>
    
               
                <tr>


                            </table>

                        </center>
                    </td>
                    
                </tr>
                
               
                  
                <tr>
                   <td colspan="4">
                       <center>
                        <div class="abc scroll">
                        <table border="0" class="scrolldown" style="width:93%; border:none; margin-top:0px; animation: transitionIn-Y-bottom 0.5s;">
                        
                        <tbody>
                        
                            <?php

                                if($result->num_rows==0){
                                    echo '<tr>
                                    <td colspan="7">
                                    <br><br><br><br>
                                    <center>
                                    <img src="../img/notfound.svg" width="25%">
                                    
                                    <br>
                                    <p class="heading-main12" style="margin-left: 45px;font-size:20px;color:rgb(49, 49, 49)">We  couldn\'t find anything related to your keywords !</p>
                                    <a class="login-btn btn-primary-soft btn non-style-link" href="index.php" style="display: flex;justify-content: center;align-items: center;margin-left:20px;">&nbsp; Go back to home page &nbsp;</a>
                                    </center>
                                    <br><br><br><br>
                                    </td>
                                    </tr>';
                                } else {
                                    // Display the current month
                                    echo "<tr><td colspan='7' class='day-header'><h3 style='margin-left: 20px;'>". date('F Y') . "</h3></td></tr>";

                                    // Initialize an array to store appointments by day
                                    $appointments_by_day = [];

                                    // Fetch all appointments and group them by day name
                                    while ($row = $result->fetch_assoc()) {
                                        $day_name = date('l', strtotime($row['scheduledate']));
                                        $appointments_by_day[$day_name][] = $row;
                                    }

                                    // Iterate over each day and display appointments
                                    foreach ($appointments_by_day as $day => $appointments) {
                                        echo "<tr><td colspan='7' class='day-header'><h3 style='margin-left: 20px;'>$day</h3></td></tr>";
                                        echo "<tr class='appointment-row'>";
                                        $counter = 0;
                                        foreach ($appointments as $appointment) {
                                            $scheduleid = $appointment["scheduleid"];
                                            $title = $appointment["title"];
                                            $docname = $appointment["docname"];
                                            $scheduledate = $appointment["scheduledate"];
                                            $scheduletime = $appointment["scheduletime"];
                                            $apponum = $appointment["apponum"];
                                            $appodate = $appointment["appodate"];
                                            $appoid = $appointment["appoid"];
                                            $is_confirmed = $appointment["is_confirmed"];
                                            $status = $appointment["status"];
                                            $is_self = $appointment["is_self"];
                                            $other_patient_name = $appointment["other_patient_name"];

                                            $currentDateTime = new DateTime();
                                            $scheduledDateTime = new DateTime("$scheduledate $scheduletime");
                                            
                                            // Fetch the end time from the schedule table for this specific appointment
                                            $schedule_query = "SELECT end_time FROM schedule WHERE scheduleid = ?";
                                            $schedule_stmt = $database->prepare($schedule_query);
                                            $schedule_stmt->bind_param("i", $scheduleid);
                                            $schedule_stmt->execute();
                                            $schedule_result = $schedule_stmt->get_result();
                                            $schedule_row = $schedule_result->fetch_assoc();
                                            
                                            $endDateTime = new DateTime("$scheduledate {$schedule_row['end_time']}");
                                            $oneDayBefore = clone $scheduledDateTime;
                                            $oneDayBefore->modify('-1 day');

                                            $isPast = $currentDateTime >= $endDateTime;
                                            $isOneDayBefore = $currentDateTime >= $oneDayBefore && $currentDateTime < $scheduledDateTime;

                                            // Automatically reject pending appointments that reach the scheduled date
                                            if ($is_confirmed == 0 && $currentDateTime >= $scheduledDateTime) {
                                                // Update the appointment status to rejected
                                                $update_query = "UPDATE appointment SET is_confirmed = -1 WHERE appoid = ?";
                                                $update_stmt = $database->prepare($update_query);
                                                $update_stmt->bind_param("i", $appoid);
                                                $update_stmt->execute();

                                                // Update the local variable to reflect the change
                                                $is_confirmed = -1;
                                            }

                                            $cancelButtonText = $isPast ? 
                                                '<button class="btn-cancellation-not-allowed" style="width:100%" disabled><font class="tn-in-text">Session Passed</font></button>' : 
                                                ($isOneDayBefore ? 
                                                '<button class="btn-cancellation-not-allowed" style="width:100%" onclick="showCancellationAlert()"><font class="tn-in-text">Cannot Cancel 1 Day Before Session</font></button>' : 
                                                '<a href="?action=drop&id='.$appoid.'&title='.$title.'&doc='.$docname.'"><button class="login-btn btn-primary-soft btn" style="padding-top:11px;padding-bottom:11px;width:100%"><font class="tn-in-text">Cancel Booking</font></button></a>');

                                            $statusBadge = $is_confirmed == 1 ? 
                                                '<span class="status-badge" style="background-color: #28a745; color: white;">Approved</span>' : 
                                                ($is_confirmed == -1 ? 
                                                '<span class="status-badge" style="background-color: #dc3545; color: white;">Declined</span>' : 
                                                '<span class="status-badge" style="background-color: #ffc107; color: black;">Pending</span>');

                                            // Check for rejection reason from URL
                                            $rejectionReasonFromUrl = isset($_GET['reason']) ? urldecode($_GET['reason']) : null;

                                            $rejectionDetails = $is_confirmed == -1 ? 
                                                "<div class='h4-search' style='color: black; margin-top: 5px;'>" .
                                                "Rejected on: " . htmlspecialchars(DateTime::createFromFormat('Y-m-d H:i:s', $appointment['rejection_timestamp'] ?? date('Y-m-d H:i:s'))->format('F j, Y h:i A') ?? 'Automatic Rejection') . 
                                                "<br>Reason: " . htmlspecialchars($appointment['rejection_reason'] ?? $rejectionReasonFromUrl ?? 'No specific reason provided') . 
                                                "</div>" : '';

                                            // Start a new row every 4 appointments
                                            if ($counter > 0 && $counter % 4 == 0) {
                                                echo "</tr><tr class='appointment-row'>";
                                            }

                                            echo '
                                            <td class="responsive-td">
                                                    <div  class="dashboard-items search-items"  >
                                                    <div style="width:100%">
                                                            <div class="h1-search" style="text-align:center;font-size: 24px;font-weight: 600;color: #2d6a4f">
                                                                '.substr($title,0,21).'
                                                            </div>
                                                            <div class="h3-search" style="font-size: 34px; text-align:center;color: #2d6a4f;margin-top: 5px; margin-bottom: 15px">
                                                                Appointment Number:'.$apponum.'
                                                            </div>

                                                            <div class="h4-search" style="text-align:left;font-size: 16px">
                                                                Scheduled Date: '.DateTime::createFromFormat('Y-m-d', $scheduledate)->format('F j, Y').'  <b>'.DateTime::createFromFormat("H:i", substr($scheduletime, 0, 5))->format("h:i A").'</b>
                                                            </div>

                                                            <div class="h4-search" style="text-align:left;font-size: 16px">Booked For: ' . ($is_self == 0 ? 'Myself' : htmlspecialchars($other_patient_name) ) . '</div>
                                                            <div class="h3-search" style="text-align:left;font-size: 16px">
                                                                '.substr($docname,0,30).'
                                                            </div>
                                                            
                                                            <div class="h4-search">
                                                                Cancellation Deadline: '.$oneDayBefore->format('F j').', 2025
                                                            </div>
                                                            <div class="h4-search">
                                                                Confirmation Status: '.$statusBadge.'
                                                            </div>
                                                        ' . ($is_confirmed == 1 ? 
                                                            '<div class="h4-search" style="background-color: #d4edda; border-left: 4px solid #28a745; padding: 10px; margin-top: 10px; border-radius: 4px;">' .
                                                            '<div style="display: flex; align-items: center; margin-bottom: 5px;">' .
                                                            '<img src="../img/icons/approve.svg" alt="Approved" style="width: 20px; height: 20px; margin-right: 10px;">' .
                                                            '<strong style="color: #155724;">Appointment Confirmed</strong>' .
                                                            '</div>' .
                                                            '<div style="text-align: right; margin-top: 5px; font-size: 0.8em; color: #6c757d;">' .
                                                            "Approved on: " . htmlspecialchars(DateTime::createFromFormat('Y-m-d H:i:s', $appointment['approval_timestamp'] ?? date('Y-m-d H:i:s'))->format('F j, h:i A') ?? 'Automatic Approval') . 
                                                            '</div>' .
                                                            '</div>' : '') . 
                                                            ($is_confirmed == -1 ? 
                                                            '<div class="h4-search" style="background-color: #f8d7da; border-left: 4px solid #dc3545; padding: 10px; margin-top: 10px; border-radius: 4px;">' .
                                                            '<div style="display: flex; align-items: center; margin-bottom: 5px;">' .
                                                            '<img src="../img/icons/reject.svg" alt="Declined" style="width: 20px; height: 20px; margin-right: 10px;">' .
                                                            '<strong style="color: #721c24;">Scheduling Conflict: ' . 
                                                            htmlspecialchars($appointment['rejection_reason'] ?? $rejectionReasonFromUrl ?? 'Booking Declined') . 
                                                            '</strong>' .
                                                            '</div>' .
                                                            '<div style="text-align: right; margin-top: 5px; font-size: 0.8em; color: #6c757d;">' .
                                                            "Was Declined on: " . htmlspecialchars(DateTime::createFromFormat('Y-m-d H:i:s', $appointment['rejection_timestamp'] ?? date('Y-m-d H:i:s'))->format('F j, Y h:i A') ?? 'Automatic Rejection') . 
                                                            '</div>' .
                                                            '</div>' : '') . 
                                                            ($is_confirmed == 0 ? 
                                                            '<div class="h4-search" style="background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 10px; margin-top: 10px; border-radius: 4px;">' .
                                                            '<div style="display: flex; align-items: center; margin-bottom: 5px;">' .
                                                            '<img src="../img/icons/pending.svg" alt="Pending" style="width: 20px; height: 20px; margin-right: 10px;">' .
                                                            '<strong style="color: #856404;">Appointment Pending</strong>' .
                                                            '</div>' .
                                                            '<div style="text-align: right; margin-top: 5px; font-size: 0.8em; color: #6c757d;">' .
                                                            "Booked on: " . htmlspecialchars(date('F j, h:i A', strtotime($appointment['booking_attempt_timestamp'] ?? date('Y-m-d H:i:s')))) . 
                                                            '</div>' .
                                                            '</div>' : '') . '
                                            <br>
                                                            <div class="h3-search"  style="text-align:right ">
                                                                Booking Date: '.substr($appodate,0,30).'<br>
                                                                Reference Number: OC-000-'.$appoid.'
                                                            </div>'
                                            ;

                                            
                                            echo $cancelButtonText;

                                            echo '</div>
                                                    </div>
                                                </td>';
                                            
                                            $counter++;
                                        }
                                        echo "</tr>";
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
    
    if(!empty($_GET)){
        $id = isset($_GET["id"]) ? $_GET["id"] : null;
        $action = isset($_GET["action"]) ? $_GET["action"] : null;
        
        if($action=='booking-added'){
            echo "
            <script>
                Swal.fire({
                    title: 'Booking Submitted!',
                    text: 'Your appointment request has been submitted and is pending approval. Your Appointment number is $id.',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'appointment.php';
                    }
                });
            </script>
            ";
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
        }elseif($action=='view'){
            $sqlmain= "select * from doctor where docid=?";
            $stmt = $database->prepare($sqlmain);
            $stmt->bind_param("i",$id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row=$result->fetch_assoc();
            $name=$row["docname"];
            $email=$row["docemail"];
            $spe=$row["specialties"];
            
            $sqlmain= "select sname from specialties where id=?";
            $stmt = $database->prepare($sqlmain);
            $stmt->bind_param("s",$spe);
            $stmt->execute();
            $spcil_res = $stmt->get_result();
            $spcil_array= $spcil_res->fetch_assoc();
            $spcil_name=$spcil_array["sname"];
            $nic=$row['docnic'];
            $tele=$row['doctel'];
            echo '
            <div id="popup1" class="overlay">
                    <div class="popup">
                    <center>
                        <h2></h2>
                        <a class="close" href="doctors.php">&times;</a>
                        <div class="content">
                            eDoc Web App<br>
                            
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
                                    <label for="nic" class="form-label">NIC: </label>
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
    }

    
    // Add SweetAlert script
    $sweetalert = isset($_GET['sweetalert']) ? $_GET['sweetalert'] : null;
    
    if ($sweetalert == 'success') {
        echo '
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Check if this is a direct booking or a page refresh
            const isDirectBooking = performance.navigation.type !== performance.navigation.TYPE_RELOAD;
            
            // Create a unique key for this booking session
            const bookingKey = "pendingApprovalAlertShown_" + new Date().toISOString().split("T")[0];
            
            // Check if the alert should be shown
            if (isDirectBooking) {
                Swal.fire({
                    icon: "info",
                    title: "Pending Approval",
                    text: "Your booking is currently pending approval. You will be notified once it is confirmed.",
                    confirmButtonText: "OK"
                }).then((result) => {
                    // Mark the alert as shown for today only after user confirms
                    if (result.isConfirmed) {
                        sessionStorage.setItem(bookingKey, "true");
                    }
                });
            }
        });
        </script>';
    } elseif ($sweetalert == 'error') {
        echo '
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                icon: "error",
                title: "Booking Failed",
                text: "You have reached the maximum attempt to book on this session",
                confirmButtonText: "OK"
            });
        });
        </script>';
    } elseif ($sweetalert == 'declined') {
        echo '
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                icon: "warning",
                title: "Booking Declined",
                text: "Your booking has been declined by the admin.",
                confirmButtonText: "OK"
            });
        });
        </script>';
    }
    ?>
    <script>
        function showCancellationAlert() {
            Swal.fire({
                icon: "error",
                title: "Cancellation Not Allowed",
                text: "You can only cancel bookings up to one day before the scheduled date.",
                confirmButtonText: "OK"
            });
        }
    </script>
    <script>
        const hamburger = document.getElementById('hamburger');
        const menu = document.getElementById('menu');
        hamburger.addEventListener('click', () => {
            console.log("Hamburger clicked!");
            menu.classList.toggle('show');
            // Prevent body scroll when menu is open (mobile only)
            if (window.innerWidth <= 992) {
                if (menu.classList.contains('show')) {
                    document.body.style.overflow = 'hidden';
                } else {
                    document.body.style.overflow = '';
                }
            }
        });
        // Also restore scroll if menu is closed by resizing window
        window.addEventListener('resize', function() {
            if (window.innerWidth > 992) {
                document.body.style.overflow = '';
            } else if (!menu.classList.contains('show')) {
                document.body.style.overflow = '';
            }
        });
    </script>
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
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var dateInput = document.getElementById('date');
        if (dateInput) {
            dateInput.addEventListener('change', function() {
                this.form.submit();
            });
        }
    });
    </script>

</body>
</html>