<?php
session_start();

if (isset($_SESSION["user"])) {
    if (($_SESSION["user"]) == "" or $_SESSION['usertype'] != 'd') {
        header("location: ../login.php");
        exit();
    } else {
        $useremail = $_SESSION["user"];
    }
} else {
    header("location: ../login.php");
    exit();
}

// Import database
include("../connection.php");
$userrow = $database->query("SELECT * FROM doctor WHERE docemail = '$useremail'");
$userfetch = $userrow->fetch_assoc();
$userid = $userfetch["docid"];
$username = $userfetch["docname"];
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
        
        .patient-details-popup .swal-patient-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 10px;
        }
        
        .patient-details-popup .swal-patient-table tr {
            margin-bottom: 10px;
        }
        
        .patient-details-popup .swal-patient-table .label-td {
            font-weight: bold;
            text-align: right;
            padding-right: 15px;
            color: #555;
            width: 40%;
        }
        
        .patient-details-popup .swal-patient-table td {
            text-align: left;
            padding: 5px;
        }
    </style>
</head>
<body>
<?php
// Check if login_success action is passed in the URL
if (isset($_GET['action']) && $_GET['action'] == 'login_success' && !isset($_SESSION['login_alert_shown'])) {
    // Set the session variable to indicate the alert has been shown
    $_SESSION['login_alert_shown'] = true;

    // Display SweetAlert for successful login
    echo "
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>
        setTimeout(function() {
            Swal.fire({
                title: 'Login Successful',
                text: 'Welcome Dr. " . $username . "!',
                icon: 'success',
                confirmButtonText: 'OK'
            });
        }, 250); // Delay for 250ms
    </script>
    ";
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
                                    <p class="profile-title"><?php echo substr($username,0,13)  ?></p>
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
                            </tr>
                    </table>
                    </td>
                </tr>
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
                    <td class="menu-btn menu-icon-patient menu-active menu-icon-patient-active">
                        <a href="patient.php" class="non-style-link-menu"><div><p class="menu-text">Patients</p></a></div>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-settings">
                        <a href="settings.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">Settings</p></a></div>
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
                                Telephone 
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
                                    $tel=$row["phone_number"];
                                    
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
                                        $tel.'
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
            $phone_number=$row["phone_number"];
            $address=$row["paddress"];
            
            // Detailed age calculation
            $birthDate = new DateTime($dob);
            $currentDate = new DateTime();
            $age = $currentDate->diff($birthDate);
            
            // Format age string
            $ageString = $age->y . ' years';
            if ($age->m > 0) {
                $ageString .= ', ' . $age->m . ' months';
            }
            
            echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"></script>
            <script type="text/javascript">
            Swal.fire({
                title: "Patient Profile",
                html: `
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
                                <i class="fas fa-phone"></i>
                                <div>
                                    <h3>Telephone</h3>
                                    <p>' . htmlspecialchars($phone_number) . '</p>
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
                        </div>
                    </div>
                `,
                icon: "info",
                confirmButtonText: "Close",
                showCloseButton: true,
                customClass: {
                    popup: "patient-profile-popup",
                    htmlContainer: "patient-profile-container",
                    confirmButton: "btn btn-primary"
                },
                didOpen: () => {
                    // Add Font Awesome if not already loaded
                    if (!document.querySelector("link[href*=\'fontawesome\']")) {
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