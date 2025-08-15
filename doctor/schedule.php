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
            session_start();

            // Check if user is logged in and is a doctor
            if (!isset($_SESSION["user"]) || $_SESSION["user"] == "" || $_SESSION['usertype'] != 'd') {
                header("location: ../login.php");
                exit();
            }

            // Get user email from session
            $useremail = $_SESSION["user"]; // Assuming the email is stored here

            // Import database
            include("../connection.php");

            // Get doctor info from DB using the email
            $userrow = $database->query("SELECT * FROM doctor WHERE docemail = '$useremail'");
            $userfetch = $userrow->fetch_assoc();

            // Fallback in case doctor is not found (optional but recommended)
            if (!$userfetch) {
                header("location: ../login.php");
                exit();
            }

            $userid = $userfetch["docid"];
            $username = $userfetch["docname"];

            // Optional: Check for delete success message
            if (isset($_SESSION['delete_success'])) {
                $title = $_SESSION['title'];
                echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
                echo '<script type="text/javascript">
                Swal.fire({
                    icon: "success",
                    title: "Deleted!",
                    text: "' . $title . ' has been successfully deleted.",
                    confirmButtonText: "OK"
                }).then(() => {
                    window.location.href = "schedule.php";
                });
                </script>';
                unset($_SESSION['delete_success']);
                unset($_SESSION['title']);
            }
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
                    </table>
                    </td>
                
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-dashbord" >
                        <a href="index.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">Dashboard</p></a></div></a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-appoinment">
                        <a href="appointment.php" class="non-style-link-menu"><div><p class="menu-text">Appointments</p></a></div>
                    </td>
                </tr>
                
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-session menu-active menu-icon-session-active">
                        <a href="schedule.php" class="non-style-link-menu"><div><p class="menu-text">Sessions</p></div></a>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-patient">
                        <a href="patient.php" class="non-style-link-menu"><div><p class="menu-text">Patients</p></a></div>
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
                        $sqlmain= "select schedule.scheduleid,schedule.title,doctor.docname,schedule.scheduledate,schedule.scheduletime,schedule.nop,schedule.session_duration,schedule.end_time from schedule inner join doctor on schedule.docid=doctor.docid  order by schedule.scheduledate asc";

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
                                    
                                    Session Duration
                                    
                                </th>
                                <th class="table-headin">
                                    
                                    End Time
                                    
                                </th>
                                
                                <th class="table-headin">
                                    
                                    Actions
                                    
                                </tr>
                        </thead>
                        <tbody>
                        
                            <?php

                                // Define how many results you want per page
                                $results_per_page = 10;

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
                                $sqlmain = "SELECT schedule.scheduleid, schedule.title, doctor.docname, schedule.scheduledate, schedule.scheduletime, schedule.nop, schedule.session_duration, schedule.end_time \n            FROM schedule \n            INNER JOIN doctor ON schedule.docid = doctor.docid \n            ORDER BY schedule.scheduledate ASC \n            LIMIT " . $starting_limit . ", " . $results_per_page;

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
    
    if (isset($_GET["action"])) {
        $action = $_GET["action"];
        $id = isset($_GET["id"]) ? $_GET["id"] : null;
        if ($action == 'add-session') {
    echo '
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        Swal.fire({
            title: "Add New Session",
            html: `
                <form id="sessionForm" action="add-session.php" method="POST">
                    <label class="form-label">Session Title:</label><br>
                    <input type="text" name="title" class="input-text" placeholder="Name of this Session" required><br><br>

                    <label class="form-label">Select Doctor:</label><br>
                    <select name="docid" class="box" required>
                        <option value="" disabled selected hidden>Choose Doctor Name from the list</option>';
                        
                        $list11 = $database->query("SELECT * FROM doctor ORDER BY docname ASC;");
                        while ($row00 = $list11->fetch_assoc()) {
                            $sn = htmlspecialchars($row00["docname"]);
                            $id00 = htmlspecialchars($row00["docid"]);
                            echo "<option value='$id00'>$sn</option>";
                        }

    echo '      </select><br><br>

                    <label class="form-label">Number of Patients / Appointment Numbers:</label><br>
                    <input type="number" name="nop" class="input-text" min="0" required><br><br>

                    <label class="form-label">Session Date:</label><br>
                    <input type="date" name="date" class="input-text" min="' . date('Y-m-d') . '" required><br><br>

                    <label class="form-label">Schedule Time:</label><br>
                    <input type="time" name="time" id="startTime" class="input-text" required><br><br>

                    <label class="form-label">Session Duration:</label><br>
                    <select name="session_duration" id="duration" class="box" required>
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
                    </select><br><br>

                    <label class="form-label">End Time:</label><br>
                    <input type="time" name="end_time" id="endTime" class="input-text" readonly><br>
                </form>
            `,
            showCancelButton: true,
            confirmButtonText: "Place this Session",
            cancelButtonText: "Cancel",
            focusConfirm: false,
            willOpen: () => {
                document.getElementById("duration").addEventListener("change", updateEndTime);
                document.getElementById("startTime").addEventListener("input", updateEndTime);
                
                function updateEndTime() {
                    const start = document.getElementById("startTime").value;
                    const duration = parseInt(document.getElementById("duration").value);
                    if (start && duration) {
                        const [hour, minute] = start.split(":").map(Number);
                        const startDate = new Date();
                        startDate.setHours(hour, minute);
                        startDate.setMinutes(startDate.getMinutes() + duration);
                        const endH = String(startDate.getHours()).padStart(2, "0");
                        const endM = String(startDate.getMinutes()).padStart(2, "0");
                        document.getElementById("endTime").value = `${endH}:${endM}`;
                    }
                }
            },
            preConfirm: () => {
                const form = document.getElementById("sessionForm");
                if (!form.checkValidity()) {
                    Swal.showValidationMessage("Please complete all fields.");
                    return false;
                }
                form.submit(); // Submit form if valid
            }
        });
    </script>';

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
                Swal.fire({
                  icon: "success",
                  title: "Deleted!",
                  text: "' . $nameget . ' has been deleted.",
                  confirmButtonText: "OK"
                }).then(() => {
                    window.location.href = "delete-session.php?id=' . $id . '";
                });
              } else {
                window.location.href = "schedule.php";
              }
            });
            </script>';
        } elseif($action=='view'){
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
        
            // Enhanced logging for patient retrieval
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
            ini_set('log_errors', 1);
            $log_file = dirname(__FILE__) . '/schedule_patient_debug.log';
            ini_set('error_log', $log_file);

        

            // Log additional diagnostic information
            error_log("Debug Parameters: " . json_encode([
                'schedule_id' => $id,
                'current_datetime' => date('Y-m-d H:i:s')
            ]));

            $sqlmain12= "SELECT 
                patient.pid, 
                patient.pname, 
                appointment.apponum, 
                patient.phone_number,
                appointment.is_confirmed,
                appointment.status
            FROM appointment 
            INNER JOIN patient ON patient.pid=appointment.pid 
            INNER JOIN schedule ON schedule.scheduleid=appointment.scheduleid 
            WHERE schedule.scheduleid=$id 
                AND (appointment.is_confirmed=1 OR appointment.status='Approved')";

            $result12 = $database->query($sqlmain12);
            
            // Log the full SQL query with more context
            error_log("Patient Retrieval Query for Schedule ID $id: $sqlmain12");
            
            // Log query execution results with more details
            if ($result12 === false) {
                error_log("Query Execution Failed: " . $database->error);
            } else {
                error_log("Total Patients Found: " . $result12->num_rows);
                
                // Log individual query parameters for debugging
                error_log("Debug Parameters: " . json_encode([
                    'schedule_id' => $id,
                    'current_datetime' => date('Y-m-d H:i:s')
                ]));
            }

            // Prepare patient data for JavaScript
            $patients = [];
            while ($patient = $result12->fetch_assoc()) {
                // Log each patient's details
                error_log("Patient Details: " . json_encode([
                    'pid' => $patient['pid'],
                    'pname' => $patient['pname'],
                    'apponum' => $patient['apponum'],
                    'is_confirmed' => $patient['is_confirmed'],
                    'status' => $patient['status']
                ]));

                $patients[] = [
                    'pid' => htmlspecialchars(substr($patient['pid'], 0, 15)),
                    'pname' => htmlspecialchars(substr($patient['pname'], 0, 25)),
                    'apponum' => htmlspecialchars($patient['apponum']),
                    'phone' => htmlspecialchars(substr($patient['phone_number'], 0, 25))
                ];
            }

            echo '<style>
                .swal2-popup .session-details {
                    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
                }
                .session-header {
                    background-color: #f4f6f9;
                    padding: 15px;
                    border-bottom: 1px solid #e0e4e8;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
                .session-header div {
                    text-align: center;
                }
                .session-header h2 {
                    margin: 0;
                    color: #2c3e50;
                    font-size: 1.5em;
                    font-weight: 600;
                }
                .session-header p {
                    margin: 0;
                    color: #7f8c8d;
                    font-size: 0.9em;
                }
                .session-info-grid {
                    display: grid;
                    grid-template-columns: repeat(2, 1fr);
                    gap: 10px;
                    padding: 15px;
                    background-color: #f9fafb;
                    border-bottom: 1px solid #e0e4e8;
                }
                .session-info-grid div {
                    display: flex;
                    flex-direction: column;
                }
                .session-info-grid strong {
                    color: #34495e;
                    margin-bottom: 5px;
                    font-size: 0.9em;
                }
                .session-info-grid span {
                    color: #2c3e50;
                    font-weight: 500;
                }
                .patients-section {
                    padding: 15px;
                }
                .patients-section h3 {
                    margin: 0 0 15px 0;
                    color: #2c3e50;
                    font-size: 1.2em;
                    border-bottom: 2px solid #3498db;
                    padding-bottom: 10px;
                }
                .patients-table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 10px;
                }
                .patients-table thead {
                    background-color: #f1f3f5;
                }
                .patients-table th, .patients-table td {
                    border: 1px solid #e0e4e8;
                    padding: 10px;
                    text-align: left;
                    font-size: 0.9em;
                }
                .patients-table th {
                    background-color: #f4f6f9;
                    color: #2c3e50;
                    font-weight: 600;
                }
                .patients-table tr:nth-child(even) {
                    background-color: #f9fafb;
                }
                .patients-table tr:hover {
                    background-color: #f1f3f5;
                    transition: background-color 0.3s ease;
                }
                .no-patients {
                    text-align: center;
                    padding: 30px;
                    background-color: #f9fafb;
                }
                .no-patients img {
                    max-width: 150px;
                    margin-bottom: 15px;
                }
                .no-patients p {
                    color: #7f8c8d;
                    font-size: 1em;
                }
            </style>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <script>
            document.addEventListener("DOMContentLoaded", function() {
                const patients = ' . json_encode($patients) . ';
                
                Swal.fire({
                    title: null,
                    html: `
                        <div class="session-details">
                            <div class="session-header">
                                <div>
                                    <h2>' . htmlspecialchars($title) . '</h2>
                                    <p>Session with Dr. ' . htmlspecialchars($docname) . '</p>
                                </div>
                            </div>
                            
                            <div class="session-info-grid">
                                <div>
                                    <strong>Scheduled Date</strong>
                                    <span>' . htmlspecialchars($scheduledate) . '</span>
                                </div>
                                <div>
                                    <strong>Start Time</strong>
                                    <span>' . htmlspecialchars($scheduletime) . '</span>
                                </div>
                                <div>
                                    <strong>End Time</strong>
                                    <span>' . htmlspecialchars($end_time) . '</span>
                                </div>
                                <div>
                                    <strong>Session Duration</strong>
                                    <span>' . htmlspecialchars($session_duration) . ' minutes</span>
                                </div>
                            </div>
                            
                            <div class="patients-section">
                                <h3>Registered Patients (' . count($patients) . '/' . htmlspecialchars($nop) . ')</h3>
                                ${patients.length > 0 ? `
                                    <table class="patients-table">
                                        <thead>
                                            <tr>
                                                <th>Patient ID</th>
                                                <th>Patient Name</th>
                                                <th>Appointment No</th>
                                                <th>Telephone</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ${patients.map(patient => `
                                                <tr>
                                                    <td>${patient.pid}</td>
                                                    <td>${patient.pname}</td>
                                                    <td>${patient.apponum}</td>
                                                    <td>${patient.phone}</td>
                                                </tr>
                                            `).join("")}
                                        </tbody>
                                    </table>
                                ` : `
                                    <div class="no-patients">
                                        <img src="../img/notfound.svg" alt="No Patients">
                                        <p>No patients registered for this session</p>
                                    </div>
                                `}
                            </div>
                        </div>
                    `,
                    showConfirmButton: true,
                    width: "900px",
                    padding: "0",
                    confirmButtonText: "Close",
                    customClass: {
                        popup: "session-details-popup",
                        htmlContainer: "session-content",
                        confirmButton: "btn btn-primary"
                    },
                    buttonsStyling: false
                });
            });
            </script>';
            exit();
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