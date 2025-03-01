<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

    session_start();

    if(isset($_SESSION["user"])){
        if(($_SESSION["user"])=="" or $_SESSION['usertype']!='a'){
            header("location: ../login.php");
        }

    }else{
        header("location: ../login.php");
    }
    
    // Check for delete success message
    if (isset($_SESSION['delete_success'])) {
        $title = $_SESSION['title']; // Get the session title from the session variable
        echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
        echo '<script type="text/javascript">
        Swal.fire({
        icon: "success",
        title: "Deleted!",
        text: "' . $title . ' has been successfully deleted.",
        confirmButtonText: "OK"
        }).then(() => {
            window.location.href = "schedule.php"; // Redirect after user acknowledges the success alert
        });
        </script>';

        // Unset the session variables
        unset($_SESSION['delete_success']);
        unset($_SESSION['title']); // Unset the session title
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
                                    <p class="profile-subtitle">admin@edoc.com</p>
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
                    <td class="menu-btn menu-icon-schedule menu-active menu-icon-schedule-active">
                        <a href="schedule.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">Schedule</p></div></a>
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
                    <td class="menu-btn menu-icon-records">
                        <a href="records.php" class="non-style-link-menu"><div><p class="menu-text">Records</p></a></div>
                    </td>
                </tr>

            </table>
        </div>
        <div class="dash-body">
            <table border="0" width="100%" style=" border-spacing: 0;margin:0;padding:0;margin-top:25px; ">

                    <td width="100%">
                        <p style="font-size: 14px;color: rgb(119, 119, 119);padding: 0;margin: 0;text-align: center;">
                            Today's Date
                        </p>
                        <p class="heading-sub12" style="padding: 0;margin: 0;text-align: center;">
                            <?php 

                        date_default_timezone_set('Asia/Manila');

                        $today = date('Y-m-d');
                        echo $today;

                        $list110 = $database->query("select  * from  schedule;");

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
                    <td colspan="4" style="padding-top:10px;width: 100%;" >
                    
                        <p class="heading-main12" style="margin-left: 45px;font-size:18px;color:rgb(49, 49, 49)">All Sessions (<?php echo $list110->num_rows; ?>)</p>
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
                            
                            <input type="date" name="sheduledate" id="date" class="input-text filter-container-items" style="margin: 0;width: 95%;">

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
                        if(!empty($_POST["sheduledate"])){
                            $sheduledate=$_POST["sheduledate"];
                            $sqlpt1=" schedule.scheduledate='$sheduledate' ";
                        }


                        $sqlpt2="";
                        if(!empty($_POST["docid"])){
                            $docid=$_POST["docid"];
                            $sqlpt2=" doctor.docid=$docid ";
                        }
                        //echo $sqlpt2;
                        //echo $sqlpt1;
                        $sqlmain= "select schedule.scheduleid,schedule.title,doctor.docname,schedule.scheduledate,schedule.scheduletime,schedule.nop,schedule.session_duration,schedule.end_time from schedule inner join doctor on schedule.docid=doctor.docid ";
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
                        $sqlmain= "select schedule.scheduleid,schedule.title,doctor.docname,schedule.scheduledate,schedule.scheduletime,schedule.nop,schedule.session_duration,schedule.end_time from schedule inner join doctor on schedule.docid=doctor.docid  order by schedule.scheduledate desc";

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
                                    
                                    Sheduled Date & Time
                                    
                                </th>
                                <th class="table-headin">
                                    
                                Max num that can be booked
                                    
                                </th>

                                <th class="table-headin">
                                    
                                    Session Duration
                                    
                                </th>
                                <th class="table-headin">
                                    
                                    End Time
                                    
                                </th>
                                
                                <th class="table-headin">
                                    
                                    Events
                                    
                                </tr>
                        </thead>
                        <tbody>
                        
                            <?php

                                // Define how many results you want per page
                                $results_per_page = 9;

                                // Find out the number of results stored in database
                                $result = $database->query("SELECT * FROM schedule");
                                $number_of_results = $result->num_rows;

                                // Determine number of pages needed
                                $number_of_pages = ceil($number_of_results / $results_per_page);

                                // Determine which page number visitor is currently on
                                if (!isset($_GET['page'])) {
                                    $page = 1; // Default to first page
                                } else {
                                    $page = $_GET['page'];
                                }

                                // Determine the starting limit number for the results on the displaying page
                                $starting_limit = ($page - 1) * $results_per_page;

                                // Retrieve the relevant results from the database
                                $sqlmain = "SELECT schedule.scheduleid, schedule.title, doctor.docname, schedule.scheduledate, schedule.scheduletime, schedule.nop, schedule.session_duration, schedule.end_time \n            FROM schedule \n            INNER JOIN doctor ON schedule.docid = doctor.docid \n            ORDER BY schedule.scheduledate DESC \n            LIMIT " . $starting_limit . ", " . $results_per_page;

                                $result = $database->query($sqlmain);

                                if($result->num_rows==0){
                                    echo '<tr>
                                    <td colspan="7">
                                    <br><br><br><br>
                                    <center>
                                    <img src="../img/notfound.svg" width="25%">
                                    
                                    <br>
                                    <p class="heading-main12" style="margin-left: 45px;font-size:20px;color:rgb(49, 49, 49)">We  couldnt find anything related to your keywords !</p>
                                    <a class="non-style-link" href="schedule.php"><button  class="login-btn btn-primary-soft btn"  style="display: flex;justify-content: center;align-items: center;margin-left:20px;">&nbsp; Show all Sessions &nbsp;</font></button>
                                    </a>
                                    </center>
                                    <br><br><br><br>
                                    </td>
                                    </tr>';
                                    
                                }
                                else{
                                while ($row = $result->fetch_assoc()) {
                                    $scheduleid=$row["scheduleid"];
                                    $title=$row["title"];
                                    $docname=$row["docname"];
                                    $scheduledate=$row["scheduledate"];
                                    $scheduletime=$row["scheduletime"];
                                    $nop=$row["nop"];
                                    $session_duration=$row["session_duration"];
                                    $end_time=$row["end_time"];

                                    $current_datetime = date('Y-m-d H:i:s');
                                    $session_datetime = $scheduledate . ' ' . $end_time;

                                    // Determine if the session has passed
                                    $isSessionPassed = $current_datetime > $session_datetime;

                                    // Always show the view button
                                    $session_status = '<div style="display:flex;justify-content: center;">
                                    <a href="?action=view&id='.$scheduleid.'" class="non-style-link">
                                        <button class="btn-primary-soft btn button-icon btn-view" style="padding-left: 40px;padding-top: 12px;padding-bottom: 12px;margin-top: 10px;">
                                            <font class="tn-in-text">View</font>
                                        </button>
                                    </a>
                                    &nbsp;&nbsp;&nbsp;';

                                    // Disable the remove button if the session has passed
                                    if ($isSessionPassed) {
                                        $session_status .= '<button class="btn-primary-soft btn button-icon btn-done" style="padding-left: 40px;padding-top: 12px;padding-bottom: 12px;margin-top: 10px;">
                                                <font class="tn-in-text">Passed</font>
                                            </button>';
                                    } else {
                                        $session_status .= '<a href="?action=drop&id='.$scheduleid.'&name='.$title.'" class="non-style-link">
                                            <button class="btn-primary-soft btn button-icon btn-delete" style="padding-left: 40px;padding-top: 12px;padding-bottom: 12px;margin-top: 10px;">
                                                <font class="tn-in-text">Remove</font>
                                            </button>
                                        </a>';
                                    }

                                    $session_status .= '</div>';

                                    echo '<tr>
                                        <td> &nbsp;'.
                                        substr($title,0,30)
                                        .'</td>
                                        <td>
                                        '.substr($docname,0,20).'
                                        </td>
                                        <td style="text-align:center;">
                                            '.substr($scheduledate,0,10).' '.substr($scheduletime,0,5).'
                                        </td>
                                        <td style="text-align:center;">
                                            '.$nop.'
                                        </td>
                                        <td style="text-align:center;">
                                            '.$session_duration.'
                                        </td>
                                        <td style="text-align:center;">
                                            '.$end_time.'
                                        </td>

                                        <td>
                                        '.$session_status.'
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
                <tr>
                    <td colspan="4">
                        <center>
                            <div class="pagination">
                                <?php
                                // Previous Page Button
                                if ($page > 1) {
                                    echo '<a href="schedule.php?page=' . ($page - 1) . '">Previous</a>';
                                }

                                // Page Number Links
                                for ($i = 1; $i <= $number_of_pages; $i++) {
                                    echo '<a href="schedule.php?page=' . $i . '">' . $i . '</a>';
                                }

                                // Next Page Button
                                if ($page < $number_of_pages) {
                                    echo '<a href="schedule.php?page=' . ($page + 1) . '">Next</a>';
                                }
                                ?>
                            </div>
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
        if($action=='add-session'){

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
                                    <select name="docid" id="" class="box" >
                                    <option value="" disabled selected hidden>Choose Doctor Name from the list</option><br/>';
                                        
        
                                        $list11 = $database->query("select  * from  doctor order by docname asc;");
        
                                        for ($y=0;$y<$list11->num_rows;$y++){
                                            $row00=$list11->fetch_assoc();
                                            $sn=$row00["docname"];
                                            $id00=$row00["docid"];
                                            echo "<option value=".$id00.">$sn</option><br/>";
                                        };
        
        
        
                                        
                        echo     '       </select><br><br>
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
                                <td class="label-td" colspan="2">
                                    <label for="session_duration" class="form-label">Session Duration: </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <select name="session_duration" class="box" required>
                                        <option value="" disabled selected hidden>Select Session Duration</option>
                                        <option value="15">15 minutes</option>
                                        <option value="30">30 minutes</option>
                                        <option value="45">45 minutes</option>
                                        <option value="60">1 hour</option>
                                        <option value="90">1 hour 30 minutes</option>
                                        <option value="120">2 hours</option>
                                        <option value="150">2 hours 30 minutes</option>
                                        <option value="180">3 hours</option>
                                        <option value="210">3 hours 30 minutes</option>
                                        <option value="240">4 hours</option>
                                        <option value="270">4 hours 30 minutes</option>
                                        <option value="300">5 hours</option>

                                    </select><br>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <label for="end_time" class="form-label">End Time: </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <input type="time" name="end_time" class="input-text" readonly><br>
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
            </div>
            ';
        }elseif($action=='session-added'){
            $titleget=$_GET["title"];
            echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
            echo '<script type="text/javascript">
            Swal.fire({
              icon: "success",
              title: "Session Created",
              text: "'.substr($titleget,0,40).' was successfully scheduled.",
              confirmButtonText: "OK"
            }).then((result) => {
              if (result.isConfirmed) {
                window.location.href = "schedule.php";
              }
            });
            </script>';
        } elseif ($action == 'drop') {
            $nameget = $_GET["name"];
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
                // Perform deletion and show success alert
                window.location.href = "delete-session.php?id=' . $id . '";
              } else {
                window.location.href = "schedule.php";
              }
            }).then(() => {
                // This part will only execute after the deletion is confirmed
                Swal.fire({
                  icon: "success",
                  title: "Deleted!",
                  text: "' . $nameget . ' has been deleted.",
                  confirmButtonText: "OK"
                }).then(() => {
                    window.location.href = "schedule.php"; // Redirect after user acknowledges the success alert
                });
            });
            </script>';
        }elseif($action=='view'){
            $sqlmain= "SELECT schedule.scheduleid, schedule.title, doctor.docname, schedule.scheduledate, schedule.scheduletime, schedule.nop, schedule.session_duration, schedule.end_time 
                        FROM schedule 
                        INNER JOIN doctor ON schedule.docid=doctor.docid  
                        WHERE schedule.scheduleid=$id";
            
            $result= $database->query($sqlmain);
            $row=$result->fetch_assoc();
            $docname=$row["docname"];
            $scheduleid=$row["scheduleid"];
            $title=$row["title"];
            $scheduledate=$row["scheduledate"];
            $scheduletime=$row["scheduletime"];
            $nop=$row['nop'];
            $session_duration=$row['session_duration'];
            $end_time=$row['end_time'];
        
            // Updated SQL to fetch only approved patients
            $sqlmain12= "SELECT * FROM appointment 
                          INNER JOIN patient ON patient.pid=appointment.pid 
                          INNER JOIN schedule ON schedule.scheduleid=appointment.scheduleid 
                          WHERE schedule.scheduleid=$id AND appointment.is_confirmed=1;"; // Filter for approved patients
            
            $result12= $database->query($sqlmain12);
            echo '
            <div id="popup1" class="overlay">
                <div class="popup" style="width: 70%;">
                    <center>
                        <h2></h2>
                        <a class="close" href="schedule.php">&times;</a>
                        <div class="content">
                            <div class="abc" style="max-height: 80vh; overflow-y: auto;"> <!-- Set max-height and overflow -->
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
                                            '.$title.'<br><br>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <label for="Email" class="form-label">Doctor of this session: </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            '.$docname.'<br><br>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <label for="nic" class="form-label">Scheduled Date: </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            '.$scheduledate.'<br><br>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <label for="Tele" class="form-label">Scheduled Time: </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            '.$scheduletime.'<br><br>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <label for="spec" class="form-label">Session Duration: </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            '.$session_duration.'<br><br>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <label for="spec" class="form-label">End Time: </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            '.$end_time.'<br><br>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <label for="spec" class="form-label"><b>Patients that Already registered for this session:</b> ('.$result12->num_rows."/".$nop.')</label>
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
                
                        if ($result12->num_rows > 0) {
                            while ($row = $result12->fetch_assoc()) {
                                echo '<tr style="text-align:center;">
                                    <td>'.substr($row["pid"], 0, 15).'</td>
                                    <td style="font-weight:600;padding:25px">'.substr($row["pname"], 0, 25).'</td>
                                    <td style="text-align:center;font-size:23px;font-weight:500; color: var(--btnnicetext);">'.$row["apponum"].'</td>
                                    <td>'.substr($row["phone_number"], 0, 25).'</td>
                                </tr>';
                            }
                        } else {
                            echo '<tr>
                            <td colspan="4">
                                <br><br><br><br>
                                <center>
                                    <img src="../img/notfound.svg" width="25%">
                                    <br>
                                    <p class="heading-main12" style="margin-left: 45px;font-size:20px;color:rgb(49, 49, 49)">We cannot find anything related to your keywords!</p>
                                    <a class="non-style-link" href="schedule.php"><button class="login-btn btn-primary-soft btn" style="display: flex;justify-content: center;align-items: center;margin-left:20px;">&nbsp; Show all Sessions &nbsp;</button></a>
                                </center>
                                <br><br><br><br>
                            </td>
                        </tr>';
                        }
                
                        echo '</tbody>
                                                    </table>
                                                </div>
                                            </center>
                                        </td> 
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </center>
                    <br><br>
                </div>
            </div>
        ';
        
                        if ($result12->num_rows > 0) {
                            while ($row = $result12->fetch_assoc()) {
                                echo '<tr style="text-align:center;">
                                    <td>'.substr($row["pid"], 0, 15).'</td>
                                    <td style="font-weight:600;padding:25px">'.substr($row["pname"], 0, 25).'</td>
                                    <td style="text-align:center;font-size:23px;font-weight:500; color: var(--btnnicetext);">'.$row["apponum"].'</td>
                                    <td>'.substr($row["phone_number"], 0, 25).'</td>
                                </tr>';
                            }
                        } else {
                            echo '<tr>
                                <td colspan="4" style="text-align:center;">No patients registered for this session.</td>
                            </tr>';
                        }
        
                        echo '</tbody>
                                                    </table>
                                                </div>
                                            </center>
                                        </td> 
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </center>
                    <br><br>
                </div>
            </div>
        ';
                                 
                
                
                                         
                                         $result= $database->query($sqlmain12);
                
                                         if($result->num_rows==0){
                                             echo '<tr>
                                             <td colspan="7">
                                             <br><br><br><br>
                                             <center>
                                             <img src="../img/notfound.svg" width="25%">
                                             
                                             <br>
                                             <p class="heading-main12" style="margin-left: 45px;font-size:20px;color:rgb(49, 49, 49)">We  couldnt find anything related to your keywords !</p>
                                             <a class="non-style-link" href="appointment.php"><button  class="login-btn btn-primary-soft btn"  style="display: flex;justify-content: center;align-items: center;margin-left:20px;">&nbsp; Show all Appointments &nbsp;</font></button>
                                             </a>
                                             </center>
                                             <br><br><br><br>
                                             </td>
                                             </tr>';
                                             
                                         }
                                         else{
                                         while ($row = $result->fetch_assoc()) {
                                             $apponum=$row["apponum"];
                                             $pid=$row["pid"];
                                             $pname=$row["pname"];
                                             $phone_number=$row["phone_number"];
                                             
                                             echo '<tr style="text-align:center;">
                                                <td>
                                                '.substr($pid,0,15).'
                                                </td>
                                                 <td style="font-weight:600;padding:25px">'.
                                                 
                                                 substr($pname,0,25)
                                                 .'</td >
                                                 <td style="text-align:center;font-size:23px;font-weight:500; color: var(--btnnicetext);">
                                                 '.$apponum.'
                                                 
                                                 </td>
                                                 <td>
                                                 '.substr($phone_number,0,25).'
                                                 </td>
                                                 
                                                 
                
                                                 
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
            </div>
            ';  
    }
}
        
    ?>
    </div>

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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const timeInput = document.querySelector('input[name="time"]');
            const durationSelect = document.querySelector('select[name="session_duration"]');
            const endTimeInput = document.querySelector('input[name="end_time"]');

            function calculateEndTime() {
                if (!timeInput.value || !durationSelect.value) return;

                // Parse start time
                const [startHours, startMinutes] = timeInput.value.split(':').map(Number);

                // Calculate end time
                let endHours = startHours;
                let endMinutes = startMinutes + parseInt(durationSelect.value);

                // Handle hour overflow
                endHours += Math.floor(endMinutes / 60);
                endMinutes %= 60;

                // Ensure hours stay within 24-hour format
                endHours %= 24;

                // Format hours and minutes with leading zeros
                const formattedEndHours = String(endHours).padStart(2, '0');
                const formattedEndMinutes = String(endMinutes).padStart(2, '0');

                // Set end time
                endTimeInput.value = `${formattedEndHours}:${formattedEndMinutes}`;
            }

            // Calculate end time when start time or duration changes
            timeInput.addEventListener('change', calculateEndTime);
            durationSelect.addEventListener('change', calculateEndTime);

            // Optional: Validate form submission
            const form = document.querySelector('.add-new-form');
            form.addEventListener('submit', function(e) {
                if (!timeInput.value || !durationSelect.value) {
                    e.preventDefault();
                    alert('Please select start time and session duration');
                }
            });
        });
    </script>

</body>
</html>