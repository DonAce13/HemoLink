<?php
ob_start(); // Start output buffering
session_start();

if(isset($_SESSION["user"])){
    if(($_SESSION["user"])=="" or $_SESSION['usertype']!='a'){
        header("location: ../login.php");
        exit();
    }
}else{
    header("location: ../login.php");
    exit();
}

//import database
include("../connection.php");
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
        
    <title>Patients</title>
    <style>
        .popup{
            animation: transitionIn-Y-bottom 0.5s;
        }
        .sub-table{
            animation: transitionIn-Y-bottom 0.5s;
        }
        
        /* SweetAlert Patient Profile Styling */
        .patient-profile-popup {
            width: 600px !important;
            border-radius: 15px !important;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important;
        }

        .patient-profile-container {
            padding: 0 !important;
            margin: 0 !important;
        }

        .patient-profile-container .patient-header {
            background-color: #007bff;
            color: white;
            display: flex;
            align-items: center;
            padding: 20px;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
        }

        .patient-header .patient-avatar {
            margin-right: 20px;
        }

        .patient-header .patient-avatar i {
            font-size: 80px;
            color: white;
        }

        .patient-header .patient-name-id h2 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }

        .patient-header .patient-name-id .patient-id-text {
            margin: 5px 0 0;
            opacity: 0.8;
            font-size: 14px;
        }

        .patient-details-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            padding: 20px;
            background-color: #f8f9fa;
        }

        .patient-detail-item {
            display: flex;
            align-items: center;
            background-color: white;
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            transition: transform 0.3s ease;
            flex-direction: column;
        }

        .patient-detail-item:hover {
            transform: translateY(-5px);
        }

        .patient-detail-item.full-width {
            grid-column: span 2;
        }

        .patient-detail-item i {
            font-size: 30px;
            color: #007bff;
            margin-right: 15px;
            width: 50px;
            text-align: center;
        }

        .patient-detail-item div h3 {
            margin: 0 0 5px;
            font-size: 14px;
            color: #6c757d;
            text-transform: uppercase;
            font-weight: 600;
        }

        .patient-detail-item div p {
            margin: 0;
            font-size: 16px;
            color: #212529;
            font-weight: 500;
        }

        /* Responsive adjustments */
        @media (max-width: 600px) {
            .patient-details-grid {
                grid-template-columns: 1fr;
            }

            .patient-detail-item.full-width {
                grid-column: span 1;
            }
        }
        
        /* SweetAlert Custom Styling */
        .patient-details-popup .swal2-html-container {
            margin: 0 !important;
            padding: 0 !important;
        }
        
        .patient-details-popup .swal2-html-container .swal-patient-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 10px;
        }
        
        .patient-details-popup .swal2-html-container .swal-patient-table tr {
            margin-bottom: 10px;
        }
        
        .patient-details-popup .swal2-html-container .swal-patient-table .label-td {
            font-weight: bold;
            text-align: right;
            padding-right: 15px;
            color: #555;
            width: 40%;
        }
        
        .patient-details-popup .swal2-html-container .swal-patient-table td {
            text-align: left;
            padding: 5px;
        }
        
        .appointment-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .appointment-table th, .appointment-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        
        .appointment-table th {
            background-color: #f0f0f0;
        }
        
        .status-badge {
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
        }
        
        .status-badge.visited {
            background-color: #dff0d8;
            color: #3c763d;
        }
        
        .status-badge.not-visited {
            background-color: #f2dede;
            color: #a94442;
        }
        
        .status-badge.pending {
            background-color: #fcf8e3;
            color: #8a6d3b;
        }
        
        .counter-box {
            flex: 1;
            text-align: center;
            padding: 15px;
            border-radius: 8px;
            margin: 0 5px;
        }
        
        .counter-box.visited {
            background-color: #dff0d8;
            border: 1px solid #d6e9c6;
        }
        
        .counter-box.not-visited {
            background-color: #f2dede;
            border: 1px solid #ebccd1;
        }
        
        .counter-box.pending {
            background-color: #fcf8e3;
            border: 1px solid #faebcc;
        }
        
        .counter-value {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .counter-label {
            font-size: 14px;
            color: #555;
        }
        
        .scrollable-table-container {
            max-height: 300px;
            overflow-y: auto;
            margin-top: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        
        .scrollable-table-container table {
            margin-bottom: 0;
        }
    </style>
</head>
<body>
    <?php

    //learn from w3schools.com

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
                    <td class="menu-btn menu-icon-patient  menu-active menu-icon-patient-active">
                        <a href="patient.php" class="non-style-link-menu  non-style-link-menu-active"><div><p class="menu-text">Patients</p></a></div>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-logs">
                        <a href="logs.php" class="non-style-link-menu non-style-link-menu-active">
                            <div style="display:flex;align-items:center;gap:8px;">
                                <span class="menu-icon-svg menu-icon-logs-svg"></span>
                                <p class="menu-text">Logs</p>
                            </div>
                        </a>
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
                                
                                <form action="patient.php" method="post" class="header-search">
        
                                    <input type="search" name="search" class="input-text header-searchbar" placeholder="Search Patient Name or Email" list="patients">&nbsp;&nbsp;
                                    
                                    <?php
                                        echo '<datalist id="patients">';
                                        $list11 = $database->query("select pname, pemail from patient;");
        
                                        for ($y=0;$y<$list11->num_rows;$y++){
                                            $row00=$list11->fetch_assoc();
                                            $p=$row00["pname"];
                                            $c=$row00["pemail"];
                                            echo "<option value='$p'><br/>";
                                            echo "<option value='$c'><br/>";
                                        };
        
                                    echo ' </datalist>';
                                    ?>
                                    
                               
                                    <input type="Submit" value="Search" class="btn-primary-soft btn button-icon btn-search" style="padding-left: 25px;padding-right: 25px;padding-top: 10px;padding-bottom: 10px;">
                                
                                </form>
                                
                            </td>
                    


                </tr>
               
                
                <tr>
                    <td colspan="4" style="padding-top:10px;">
                        <p class="heading-main12" style="margin-left: 45px;font-size:18px;color:rgb(49, 49, 49)">All Patients </p>
                    </td>
                    
                </tr>
                <?php
                    // Pagination setup
                    $resultsPerPage = 10;
                    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                    $page = max(1, $page); // Ensure page is at least 1

                    // Modify search query to support pagination
                    if($_POST){
                        $keyword=$_POST["search"];
                        
                        $sqlmain= "select * from patient where pemail='$keyword' or pname='$keyword' or pname like '$keyword%' or pname like '%$keyword' or pname like '%$keyword%' ";
                    }else{
                        $sqlmain= "select * from patient order by pid desc";
                    }

                    // Count total results
                    $countResult = $database->query($sqlmain);
                    $totalResults = $countResult->num_rows;
                    $totalPages = ceil($totalResults / $resultsPerPage);

                    // Modify query to include LIMIT for pagination
                    $offset = ($page - 1) * $resultsPerPage;
                    $sqlmain .= " LIMIT $offset, $resultsPerPage";

                    $result = $database->query($sqlmain);
                ?>
                  
                <tr>
                   <td colspan="4">
                       <center>
                        <div class="abc scroll">
                        <table width="93%" class="sub-table scrolldown"  style="border-spacing:0;">
                        <thead>
                        <tr>
                                <th class="table-headin">
                                Name
                                </th>
                                <th class="table-headin">   
                                Philhealth Id:
                                </th>
                                <th class="table-headin">
                                Patient Category
                                </th>
                                <th class="table-headin">
                                Email
                                </th>
                                <th class="table-headin">
                                Date of Birth
                                </th>
                                <th class="table-headin">
                                Age
                                </th>
                                <th class="table-headin">
                                Actions
                                </tr>
                        </thead>
                        <tbody>
                        
                            <?php
                                if($result->num_rows==0){
                                    echo '<tr>
                                    <td colspan="7">
                                    <br><br><br><br>
                                    <center>
                                    <img src="../img/notfound.svg" width="25%">
                                    
                                    <br>
                                    <p class="heading-main12" style="margin-left: 45px;font-size:20px;color:rgb(49, 49, 49)">We cannot find anything related to your keywords !</p>
                                    <a class="non-style-link" href="patient.php"><button  class="login-btn btn-primary-soft btn"  style="display: flex;justify-content: center;align-items: center;margin-left:20px;">&nbsp; Show all Patients &nbsp;</font></button>
                                    </a>
                                    </center>
                                    <br><br><br><br>
                                    </td>
                                    </tr>';
                                    
                                }
                                else{
                                for ( $x=0; $x<$result->num_rows;$x++){
                                    $row=$result->fetch_assoc();
                                    $pid=$row["pid"];
                                    $name=$row["pname"];
                                    $email=$row["pemail"];
                                    $nic = strtolower($row["hasPhilhealth"]) === 'yes' ? 'Yes' : 'No';
                                    $dob=$row["pdob"];
                                    $category = $row["patient_category"] ?? 'Non-Priority';
                                    
                                    // Calculate age
                                    $birthDate = new DateTime($dob);
                                    $currentDate = new DateTime();
                                    $age = $currentDate->diff($birthDate)->y;
                                    
                                    echo '<tr>
                                        <td style="text-align: center;"> &nbsp;'.
                                        substr($name,0,35)
                                        .'</td>
                                        <td style="text-align: center;"> &nbsp;'.
                                        substr($nic,0,12).'
                                        </td>
                                        <td style="text-align: center;"> &nbsp;'.
                                        $category.'
                                        </td>
                                        <td style="text-align: center;"> &nbsp;'.
                                        substr($email,0,25).'
                                         </td>
                                        <td style="text-align: center;"> &nbsp;'.
                                        substr($dob,0,10).'
                                        </td>
                                        <td style="text-align: center;"> &nbsp;'.
                                        $age.'
                                        </td>
                                        <td >
                                        <div style="display:flex;justify-content: center;">
                                        
                                        <a href="?action=view&id='.$pid.'" class="non-style-link"><button  class="btn-primary-soft btn button-icon btn-view"  style="padding-left: 40px;padding-top: 12px;padding-bottom: 12px;margin-top: 10px;"><font class="tn-in-text">View</font></button></a>
                                        
                                        <a href="?action=drop&id='.$pid.'&name='.urlencode($name).'" class="non-style-link"><button  class="btn-primary-soft btn button-icon btn-delete"  style="padding-left: 40px;padding-top: 12px;padding-bottom: 12px;margin-top: 10px;"><font class="tn-in-text">Remove</font></button></a>
                                       
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
                
                <!-- Pagination -->
                <tr>
                    <td colspan="7" style="text-align: center; padding: 20px;">
                        <?php
                        // Pagination links
                        echo '<div class="pagination">';
                        
                        // Previous page link
                        if ($page > 1) {
                            echo '<a href="?page='.($page-1).'" class="btn btn-primary-soft" style="margin-right: 10px;">&laquo; Previous</a>';
                        }
                        
                        // Page numbers
                        for ($i = 1; $i <= $totalPages; $i++) {
                            if ($i == $page) {
                                echo '<span class="btn btn-primary" style="margin: 0 5px; background-color: #007bff; color: white;">'.$i.'</span>';
                            } else {
                                echo '<a href="?page='.$i.'" class="btn btn-primary-soft" style="margin: 0 5px;">'.$i.'</a>';
                            }
                        }
                        
                        // Next page link
                        if ($page < $totalPages) {
                            echo '<a href="?page='.($page+1).'" class="btn btn-primary-soft" style="margin-left: 10px;">Next &raquo;</a>';
                        }
                        
                        echo '</div>';
                        ?>
                    </td>
                </tr>
                       
                        
                        
            </table>
        </div>
    </div>
    <?php 
    if($_GET){
        $id=$_GET["id"];
        $action=$_GET["action"];

        // Drop (Delete) Action
        if ($action == 'drop') {
            $nameget = $_GET["name"];
            echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
            echo '<script type="text/javascript">
            Swal.fire({
              title: "Are you sure?",
              text: "You want to delete this patient record for ' . htmlspecialchars($nameget) . '.",
              icon: "warning",
              showCancelButton: true,
              confirmButtonText: "Yes, delete it",
              cancelButtonText: "No, cancel",
              reverseButtons: true
            }).then((result) => {
              if (result.isConfirmed) {
                window.location.href = "delete-patient.php?id=' . $id . '&name=' . urlencode($nameget) . '";
              }
            });
            </script>';
            exit(); // Prevent further execution
        }
        
        // View Action
        if ($action == 'view'){
            $sqlmain= "select * from patient where pid='$id'";
            $result= $database->query($sqlmain);
            $row=$result->fetch_assoc();
            $name=$row["pname"];
            $email=$row["pemail"];
            $nic = strtolower($row["hasPhilhealth"]) === 'yes' ? 'Yes' : 'No';
            $dob=$row["pdob"];
            $address=$row["paddress"];
            $category = $row["patient_category"] ?? 'Non-Priority';
            
            // Detailed age calculation
            $birthDate = new DateTime($dob);
            $currentDate = new DateTime();
            $age = $currentDate->diff($birthDate);
            
            // Format age string
            $ageString = $age->y . ' years';
            if ($age->m > 0) {
                $ageString .= ', ' . $age->m . ' months';
            }
            
            // Get SVG icons based on category
            $categoryIcon = '';
            $categoryClass = '';
            
            switch(strtolower($category)) {
                case 'emergency':
                    $categoryIcon = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" class="bi bi-exclamation-triangle-fill text-danger">
                        <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                    </svg>';
                    $categoryClass = 'text-danger';
                    break;
                case 'priority':
                    $categoryIcon = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" class="bi bi-exclamation-circle-fill text-warning">
                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 4a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 1-1.1 0l-.35-3.507A.905.905 0 0 0 8 4zm.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2z"/>
                    </svg>';
                    $categoryClass = 'text-warning';
                    break;
                default: // Non-Priority
                    $categoryIcon = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" class="bi bi-check-circle-fill text-success">
                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                    </svg>';
                    $categoryClass = 'text-success';
            }
            
            // Get appointment history
            $appointments = [];
            $apptQuery = $database->query("SELECT a.appoid, s.title, s.scheduledate, s.scheduletime, a.attended, a.status 
                                   FROM appointment a 
                                   JOIN schedule s ON a.scheduleid = s.scheduleid 
                                   WHERE a.pid = '$id' AND a.status NOT IN ('canceled','rejected')
                                   ORDER BY s.scheduledate DESC");
    
            while ($row = $apptQuery->fetch_assoc()) {
                $appointments[] = $row;
            }
            
            // Count appointment statuses
            $visitedCount = 0;
            $notVisitedCount = 0;
            $pendingCount = 0;
            
            foreach ($appointments as $appt) {
                if ($appt['attended'] == 1) $visitedCount++;
                elseif ($appt['attended'] == -1) $notVisitedCount++;
                else $pendingCount++;
            }
            
            // Build counters HTML
            $countersHtml = '<div style="display: flex; justify-content: space-between; margin-bottom: 15px;">'.
                '<div class="counter-box visited">'.
                    '<div class="counter-value">'.$visitedCount.'</div>'.
                    '<div class="counter-label">Visited</div>'.
                '</div>'.
                '<div class="counter-box not-visited">'.
                    '<div class="counter-value">'.$notVisitedCount.'</div>'.
                    '<div class="counter-label">Not Visited</div>'.
                '</div>'.
                '<div class="counter-box pending">'.
                    '<div class="counter-value">'.$pendingCount.'</div>'.
                    '<div class="counter-label">Pending</div>'.
                '</div>'.
            '</div>';
            
            // Build appointment history HTML
            $appointmentRows = '';
            foreach ($appointments as $appt) {
                $statusBadge = '';
                if ($appt['attended'] == 1) {
                    $statusBadge = '<span class="status-badge visited">✓ Visited</span>';
                } elseif ($appt['attended'] == -1) {
                    $statusBadge = '<span class="status-badge not-visited">✗ Not Visited</span>';
                } else {
                    $statusBadge = '<span class="status-badge pending">Pending</span>';
                }
                
                $appointmentRows .= '<tr>'.
                    '<td>'.htmlspecialchars($appt['title']).'</td>'.
                    '<td>'.htmlspecialchars($appt['scheduledate']).'</td>'.
                    '<td>'.date('g:i A', strtotime($appt['scheduletime'])).'</td>'.
                    '<td>'.$statusBadge.'</td>'.
                '</tr>';
            }
            
            echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
            <script type="text/javascript">
            const { jsPDF } = window.jspdf;
            
            function generatePDF() {
                const element = document.getElementById("patient-profile-pdf");
                html2canvas(element).then(canvas => {
                    const imgData = canvas.toDataURL("image/png");
                    const pdf = new jsPDF({
                        orientation: "portrait",
                        unit: "mm"
                    });
                    
                    // Add logo
                    const logo = new Image();
                    logo.src = "../img/bg01.png";
                    pdf.addImage(logo, "PNG", 10, 10, 30, 30);
                    
                    // Add content
                    pdf.setFontSize(16);
                    pdf.text("Patient Profile Report", 105, 20, { align: "center" });
                    
                    // Add timestamp
                    const now = new Date();
                    const options = {
                        year: "numeric",
                        month: "long",
                        day: "numeric",
                        hour: "2-digit",
                        minute: "2-digit",
                        hour12: true,
                        timeZone: "Asia/Manila"
                    };
                    const timestamp = now.toLocaleString("en-US", options);
                    pdf.setFontSize(10);
                    pdf.text(`Report generated: ${timestamp}`, 105, 30, { align: "center" });
                    
                    // Add patient profile image
                    pdf.addImage(imgData, "PNG", 15, 45, 180, 0);
                    
                    pdf.save("patient-profile-" + Date.now() + ".pdf");
                });
            }
            
            Swal.fire({
                title: "Patient Profile",
                html: `
                    <div id="patient-profile-pdf">
                        <div class="patient-profile-container">
                            <div class="patient-header">
                                <div class="patient-avatar">
                                    <i class="fas fa-user-circle"></i>
                                </div>
                                <div class="patient-name-id">
                                    <h2>' . htmlspecialchars($name) . '</h2>
                                    <p class="patient-id-text">Patient ID: P-' . $id . '</p>
                                </div>
                            </div>
                            <div class="patient-details-grid">
                                <div class="patient-detail-item">
                                    <i class="fas fa-envelope"></i>
                                    <div>
                                        <h3>Email</h3>
                                        <p>' . htmlspecialchars($email) . '</p>
                                    </div>
                                </div>
                                <div class="patient-detail-item">
                                    <i class="fas fa-tag"></i>
                                    <div>
                                        <h3>Patient Category</h3>
                                        <p class="' . $categoryClass . '">' . $categoryIcon . ' ' . htmlspecialchars($category) . '</p>
                                    </div>
                                </div>
                                <div class="patient-detail-item">
                                    <i class="fas fa-calendar-alt"></i>
                                    <div>
                                        <h3>Date of Birth</h3>
                                        <p>' . htmlspecialchars($dob) . ' (Age: ' . htmlspecialchars($ageString) . ')</p>
                                    </div>
                                </div>
                                <div class="patient-detail-item">
                                    <i class="fas fa-id-card"></i>
                                    <div>
                                        <h3>PhilHealth ID</h3>
                                        <p>' . htmlspecialchars($nic) . '</p>
                                    </div>
                                </div>
                                <div class="patient-detail-item full-width">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <div>
                                        <h3>Address</h3>
                                        <p>' . htmlspecialchars($address) . '</p>
                                    </div>
                                </div>
                                
                                <div class="patient-detail-item full-width">
                                    <h3>Appointment History</h3>
                                    '.$countersHtml.'
                                    <div class="scrollable-table-container">
                                        <table class="appointment-table">
                                            <thead>
                                                <tr>
                                                    <th>Service</th>
                                                    <th>Date</th>
                                                    <th>Time</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>'.($appointments ? $appointmentRows : '<tr><td colspan="4" style="text-align:center;">No appointment history</td></tr>').'</tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div style="text-align:center;margin-top:20px;">
                        <button onclick="generatePDF()" class="btn btn-primary">
                            <i class="fas fa-file-pdf"></i> Export to PDF
                        </button>
                    </div>
                `,
                icon: "info",
                confirmButtonText: "Close",
                showCloseButton: true,
                width: "1100px",
                customClass: {
                    popup: "patient-profile-popup",
                    htmlContainer: "patient-profile-container",
                    confirmButton: "btn btn-primary"
                },
                didOpen: () => {
                    if (!document.querySelector("link[href*=\"fontawesome\"]")) {
                        const link = document.createElement("link");
                        link.rel = "stylesheet";
                        link.href = "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css";
                        document.head.appendChild(link);
                    }
                }
            });
            </script>';
            exit(); // Prevent further execution
        }
        
        // Rest of the code remains the same
    };

    // Check for delete success message
    if (isset($_SESSION['delete_success']) && $_SESSION['delete_success'] === true) {
        $deleted_title = isset($_SESSION['title']) ? $_SESSION['title'] : 'Patient';
        echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
        echo '<script type="text/javascript">
        Swal.fire({
            icon: "success",
            title: "Patient Deleted",
            text: "' . htmlspecialchars($deleted_title, ENT_QUOTES) . ' has been successfully removed.",
            confirmButtonText: "OK"
        });
        </script>';

        // Unset the session variables to prevent repeated alerts
        unset($_SESSION['delete_success']);
        unset($_SESSION['title']);
    }

    // Existing error handling can remain the same
    if (isset($_SESSION['delete_error'])) {
        $error_message = $_SESSION['delete_error'];
        echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
        echo '<script type="text/javascript">
        Swal.fire({
            icon: "error",
            title: "Deletion Error",
            text: "' . htmlspecialchars($error_message, ENT_QUOTES) . '",
            confirmButtonText: "OK"
        });
        </script>';

        // Unset error session variable
        unset($_SESSION['delete_error']);
    }
?>
</div>

</body>
</html>