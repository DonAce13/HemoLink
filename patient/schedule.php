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
        @media (max-width: 768px) {
            .sub-table {
                display: block;
                width: 100%;
                overflow-x: auto;
                padding: 0;
            }
            .sub-table tr {
                display: block;
                width: 100%;
            }
            .sub-table td {
                display: block;
                width: 100%;
                box-sizing: border-box;
                margin-bottom: 10px;
            }
            .dashboard-items {
                flex-direction: column;
                align-items: center;
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
    $result = $stmt->get_result();
    $userfetch=$result->fetch_assoc();
    $userid= $userfetch["pid"];
    $username=$userfetch["pname"];


    //echo $userid;
    //echo $username;
    
    $today = date('Y-m-d');
    $current_time = date('H:i:s');

    

 //echo $userid;
 ?>
 <div class="container">
     <div class="hamburger" id="hamburger">
        <div class="bar"></div>
        <div class="bar"></div>
        <div class="bar"></div>
    </div>
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
                                 <a href="../logout.php" ><input type="button" value="Log out" class="logout-btn btn-primary-soft btn"></a>
                             </td>
                         </tr>
                 </table>
                 </td>
             </tr>
             <tr class="menu-row" >
                    <td class="menu-btn menu-icon-home " >
                        <a href="index.php" class="non-style-link-menu "><div><p class="menu-text">Home</p></a></div></a>
                    </td>
                </tr>
                
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-session menu-active menu-icon-session-active">
                        <a href="schedule.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">My Bookings</p></div></a>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-settings">
                        <a href="settings.php" class="non-style-link-menu"><div><p class="menu-text">Settings</p></a></div>
                    </td>
                </tr>
                
            </table>
        </div>
        <script>
            const hamburger = document.getElementById('hamburger');
            const menu = document.querySelector('.menu');
            hamburger.addEventListener('click', () => {
                menu.classList.toggle('show');
            });
        </script>
        <?php

                $sqlmain= "SELECT *, DAYNAME(scheduledate) as day_name FROM schedule INNER JOIN doctor ON schedule.docid = doctor.docid 
                                    LEFT JOIN (
                                        SELECT scheduleid, 
                                        COUNT(CASE WHEN status = 'Approved' THEN 1 END) as approved_bookings,
                                        COUNT(*) as total_bookings 
                                        FROM appointment 
                                        GROUP BY scheduleid
                                    ) as booking_counts ON schedule.scheduleid = booking_counts.scheduleid
                                    WHERE schedule.scheduledate >= ? 
                                    ORDER BY DAYOFWEEK(scheduledate), scheduletime";
                $stmt = $database->prepare($sqlmain);
                $stmt->bind_param("s", $today);

                $insertkey = ""; // Initialize variable
                $q = ""; // Initialize variable
                if ($_POST) {
                    if (!empty($_POST["scheduledate"])) {
                        $scheduledate = $_POST["scheduledate"];
                        $sqlmain = "SELECT *, DAYNAME(scheduledate) as day_name FROM schedule INNER JOIN doctor ON schedule.docid = doctor.docid 
                                    LEFT JOIN (
                                        SELECT scheduleid, 
                                        COUNT(CASE WHEN status = 'Approved' THEN 1 END) as approved_bookings,
                                        COUNT(*) as total_bookings 
                                        FROM appointment 
                                        GROUP BY scheduleid
                                    ) as booking_counts ON schedule.scheduleid = booking_counts.scheduleid
                                    WHERE schedule.scheduledate = ? 
                                    ORDER BY DAYOFWEEK(scheduledate), scheduletime";
                        $stmt = $database->prepare($sqlmain);
                        $stmt->bind_param("s", $scheduledate);
                    }
                    if (!empty($_POST["search"])) {
                        $keyword = $_POST["search"];
                        $sqlmain = "SELECT *, DAYNAME(scheduledate) as day_name FROM schedule INNER JOIN doctor ON schedule.docid = doctor.docid 
                                    LEFT JOIN (
                                        SELECT scheduleid, 
                                        COUNT(CASE WHEN status = 'Approved' THEN 1 END) as approved_bookings,
                                        COUNT(*) as total_bookings 
                                        FROM appointment 
                                        GROUP BY scheduleid
                                    ) as booking_counts ON schedule.scheduleid = booking_counts.scheduleid
                                    WHERE schedule.scheduledate >= ? AND (doctor.docname LIKE ? OR schedule.title LIKE ?) 
                                    ORDER BY DAYOFWEEK(scheduledate), scheduletime";
                        $stmt = $database->prepare($sqlmain);
                        $likeKeyword = "%" . $keyword . "%";
                        $stmt->bind_param("sss", $today, $likeKeyword, $likeKeyword);
                        $insertkey = $keyword;
                        $searchtype = "Search Result : ";
                        $q = '"';
                    }
                }

                $stmt->execute();
                $result = $stmt->get_result();
                $searchtype="All";

                // Initialize an array to store sessions by day
                $sessions_by_day = [];

                // Fetch all sessions and group them by day name
                while ($row = $result->fetch_assoc()) {
                    $day_name = $row['day_name'];
                    $sessions_by_day[$day_name][] = $row;
                }
                ?>
                  
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
                                            <input type="date" name="scheduledate" id="date" class="input-text filter-container-items" style="margin: 0;width: 95%;">
                                    </td>
                                    <td width="12%">
                                        <input type="submit" name="filter" value=" Filter" class="btn-primary-soft btn button-icon btn-filter" style="padding: 15px; margin:0;width:100%">
                                        </form>
                                    </td>
                                </tr>
                            </table>
                        </center>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" style="padding-top:10px;width: 100%;" >
                        <p class="heading-main12" style="margin-left: 45px;font-size:18px;color:rgb(49, 49, 49)"><?php echo $searchtype." Sessions"."(".$result->num_rows.")"; ?> </p>
                        <p class="heading-main12" style="margin-left: 45px;font-size:22px;color:rgb(49, 49, 49)"><?php echo $q.$insertkey.$q ; ?> </p>
                    </td>
                    
                </tr>
                <tr>
                   <td colspan="4">
                       <center>
                        <div class="abc scroll">
                        <table width="100%" class="sub-table scrolldown" border="0" style="padding: 50px;border:none">
                            
                        <tbody>
                        
                            <?php

                                if(count($sessions_by_day) == 0){
                                    echo '<tr>
                                    <td colspan="4">
                                    <br><br><br><br>
                                    <center>
                                    <img src="../img/notfound.svg" width="25%">
                                    <br>
                                    <p class="heading-main12" style="margin-left: 45px;font-size:20px;color:rgb(49, 49, 49)">We couldn\'t find anything related to your keywords !</p>
                                    <a class="non-style-link" href="schedule.php"><button  class="login-btn btn-primary-soft btn"  style="display: flex;justify-content: center;align-items: center;margin-left:20px;">&nbsp; Show all Sessions &nbsp;</font></button>
                                    </a>
                                    </center>
                                    <br><br><br><br>
                                    </td>
                                    </tr>';
                                } else {
                                    $lastMonth = "";
                                    foreach ($sessions_by_day as $day => $sessions) {
                                        $currentMonth = date('F Y', strtotime($sessions[0]['scheduledate']));
                                        if ($currentMonth !== $lastMonth) {
                                            echo "<tr><td colspan='7'><h2>$currentMonth</h2></td></tr>";
                                            $lastMonth = $currentMonth;
                                        }
                                        echo "<tr><td colspan='4'><h3>$day</h3></td></tr>";
                                        echo "<tr>";
                                        foreach ($sessions as $session) {
                                            $scheduleid = $session["scheduleid"];
                                            $title = $session["title"];
                                            $docname = $session["docname"];
                                            $scheduledate = $session["scheduledate"];
                                            $scheduletime = $session["scheduletime"];

                                            $sql_get_nop = "SELECT nop FROM schedule WHERE scheduleid = ?";
                                            $stmt_get_nop = $database->prepare($sql_get_nop);
                                            $stmt_get_nop->bind_param("i", $scheduleid);
                                            $stmt_get_nop->execute();
                                            $result_get_nop = $stmt_get_nop->get_result();
                                            $row_get_nop = $result_get_nop->fetch_assoc();
                                            $max_participants = $row_get_nop['nop'];

                                            $approved_bookings = $session['approved_bookings'];

                                            $is_full = $approved_bookings >= $max_participants;

                                            $button_state = "book-now"; // Default state
                                            if ($is_full) {
                                                $button_state = "session-full";
                                            } elseif ($today > $scheduledate || ($today == $scheduledate && $current_time > $session['end_time'])) {
                                                $button_state = "session-passed";
                                            } elseif ($today == $scheduledate && $current_time >= $scheduletime) {
                                                $button_state = "session-ongoing";
                                            }

                                            echo '
                                            <td class="responsive-td">
                                                    <div  class="dashboard-items search-items"  >
                                                    <div style="width:100%">
                                                            <div class="h1-search">
                                                                '.substr($title,0,21).'
                                                            </div><br>
                                                            <div class="h3-search">
                                                                '.substr($docname,0,30).'
                                                            </div>
                                                            <div class="h4-search">
                                                                '.$scheduledate.'<br>Starts: <b>@'.substr($scheduletime,0,5).'</b> (24h)
                                                            </div>
                                                            <br>';
                                                            if ($button_state == "session-full") {
                                                                echo '<button class="btn-session-full" disabled>Session Full</button>';
                                                            } elseif ($button_state == "session-passed") {
                                                                echo '<button class="btn-session-passed" disabled>Session Passed</button>';
                                                            } elseif ($button_state == "session-ongoing") {
                                                                echo '<button class="btn-schedule-ongoing" disabled>Session Ongoing</button>';
                                                            } else {
                                                                echo '<a href="booking.php?id='.$scheduleid.'" ><button  class="login-btn btn-primary-soft btn "  style="padding-top:11px;padding-bottom:11px;width:100%"><font class="tn-in-text">Book Now</font></button></a>';
                                                            }
                                                        echo '
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
                        </center>
                   </td> 
                </tr>
                       
                        
                        
            </table>


    </div>

</body>
</html>