<?php

// Start the session to check for user login
session_start();

// Debug session
error_log("Session data: " . print_r($_SESSION, true));

// Check if user is logged in
if (!isset($_SESSION["user"]) || empty($_SESSION["user"]) || $_SESSION['usertype'] != 'p') {
    error_log("User not logged in or invalid session");
        header("location: ../login.php");
        exit;
    }

// Include the database connection file
include("../connection.php");  // Make sure this path is correct

// Get user ID from session email
$useremail = $_SESSION["user"];
error_log("User email from session: " . $useremail);

$sqlmain = "SELECT pid, pname FROM patient WHERE pemail=?";
$stmt = $database->prepare($sqlmain);
$stmt->bind_param("s", $useremail);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $userfetch = $result->fetch_assoc();
    $userid = $userfetch["pid"];
    $username = $userfetch["pname"];
    error_log("User found - ID: " . $userid . ", Name: " . $username);
} else {
    error_log("No user found with email: " . $useremail);
    session_destroy();
    header("location: ../login.php");
    exit;
}

// If no action is set, show the main menu
if (!isset($_GET['action'])) {
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
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <title>Settings</title>
        <style>
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
                width: 28px;
                height: 4px;
                margin: 3px 0;
                background: #2d6a4f;
                border-radius: 2px;
                transition: all 0.3s;
            }
            .menu {
                position: fixed;
                top: 54px;
                left: 0;
                width: 75vw;
                max-width: 320px;
                height: 100vh;
                background: #fff;
                box-shadow: 2px 0 8px rgba(0,0,0,0.08);
                z-index: 2050;
                transform: translateX(-100%);
                transition: transform 0.3s;
                opacity: 0;
                pointer-events: none;
            }
            .menu.show {
                transform: translateX(0);
                opacity: 1;
                pointer-events: auto;
                background: #40916c !important;
            }
        }
        @media (min-width: 993px) {
            #mobile-hamburger-header {
                display: none !important;
            }
            .hamburger {
                display: none !important;
            }
            .menu {
                position: static;
                transform: none;
                opacity: 1;
                pointer-events: auto;
                box-shadow: none;
                width: 250px;
                max-width: none;
                height: auto;
            }
        }
        /* --- End Mobile Sticky Hamburger Header --- */

.dashbord-tables {
    animation: transitionIn-Y-over 0.5s;
}
.filter-container {
    animation: transitionIn-X 0.5s;
}
.sub-table {
    animation: transitionIn-Y-bottom 0.5s;
}
            @media (max-width: 768px) {
                .dash-body {
                    width: 100% !important;
                    padding: 8px !important;
                    margin: 0 !important;
                    box-sizing: border-box;
                    overflow-x: hidden !important;
                }
                .filter-container {
                    width: 100% !important;
                    padding: 8px !important;
                    margin: 0 !important;
                    box-sizing: border-box;
                    overflow-x: auto;
                }
                .filter-container table,
                .filter-container tr,
                .filter-container td {
                    width: 100% !important;
                    box-sizing: border-box;
                    padding: 0 !important;
                    margin: 0 !important;
                }
                .container {
                    width: 100% !important;
                    padding: 0 !important;
                    margin: 0 !important;
                    box-sizing: border-box;
                    overflow-x: hidden !important;
                }
            }
            @media (max-width: 480px) {
                .dash-body {
                    padding: 4px !important;
                }
                .filter-container {
                    padding: 4px !important;
                }
            }
        </style>
    </head>
    <body>
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
                                    <td width="30%" style="padding-left:20px">
                                        <img src="../img/user.png" alt="" width="100%" style="border-radius:50%">
                                    </td>
                                    <td style="padding:0px;margin:0px;">
                                        <p class="profile-title"><?php echo $username ?></p>
                                        <p class="profile-subtitle"><?php echo $useremail ?></p>
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
                    <tr class="menu-row">
                        <td class="menu-btn menu-icon-dashbord">
                            <a href="index.php" class="non-style-link-menu"><div><p class="menu-text">Home</p></a></div>
                        </td>
                    </tr>
                    <tr class="menu-row">
                        <td class="menu-btn menu-icon-appoinment">
                            <a href="appointment.php" class="non-style-link-menu"><div><p class="menu-text">Booking History</p></a></div>
                        </td>
                    </tr>
                    <tr class="menu-row">
                        <td class="menu-btn menu-icon-settings menu-active menu-icon-settings-active">
                            <a href="settings.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">Settings</p></a></div>
                        </td>
                    </tr>
                </table>
            </div>
            <script>
        // Hamburger and menu logic (single declaration, 992px breakpoint)
        const hamburger = document.getElementById('hamburger');
        const menu = document.getElementById('menu');
        // Toggle menu and lock scroll on mobile
        hamburger.addEventListener('click', () => {
            hamburger.classList.toggle('active');
            menu.classList.toggle('show');
            if (window.innerWidth <= 992) {
                if (menu.classList.contains('show')) {
                    document.body.style.overflow = 'hidden';
                } else {
                    document.body.style.overflow = '';
                }
            }
        });
        // Restore scroll on resize
        window.addEventListener('resize', function() {
            if (window.innerWidth > 992) {
                document.body.style.overflow = '';
            } else if (!menu.classList.contains('show')) {
                document.body.style.overflow = '';
            }
        });
        // Responsive display logic (hide/show hamburger/header based on width)
        function updateHeaderMenuDisplay() {
            if (window.innerWidth <= 992) {
                if (document.getElementById('mobile-hamburger-header')) {
                    document.getElementById('mobile-hamburger-header').style.display = 'flex';
                }
                hamburger.style.display = 'flex';
            } else {
                if (document.getElementById('mobile-hamburger-header')) {
                    document.getElementById('mobile-hamburger-header').style.display = 'none';
                }
                hamburger.style.display = 'none';
                menu.classList.remove('show');
                document.body.style.overflow = '';
            }
        }
        window.addEventListener('resize', updateHeaderMenuDisplay);
        document.addEventListener('DOMContentLoaded', updateHeaderMenuDisplay);
    </script>
            <div class="dash-body" style="margin-top: 15px">
                <table border="0" width="100%" style="border-spacing: 0;margin:0;padding:0;">
                    <tr>
                        <td>
                            <p style="font-size: 23px;padding-left:12px;font-weight: 600;">Settings</p>
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
                                        <a href="?action=edit&id=<?php echo $userid; ?>" class="non-style-link">
                                            <div class="dashboard-items setting-tabs" style="padding:20px;margin:auto;width:95%;display: flex">
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
                                    <td style="width: 25%;">
                                        <a href="?action=view&id=<?php echo $userid; ?>" class="non-style-link">
                                            <div class="dashboard-items setting-tabs" style="padding:20px;margin:auto;width:95%;display: flex;">
                                                <div class="btn-icon-back dashboard-icons-setting" style="background-image: url('../img/icons/view-iceblue.svg');"></div>
                                                <div>
                                                    <div class="h1-dashboard">
                                                        View Account Details
                                                    </div><br>
                                                    <div class="h3-dashboard" style="font-size: 15px;">
                                                        View Personal information About Your Account
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            </center>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
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
    </body>
    </html>
    <?php
    exit;
}

// If action is edit but no ID is set, add the user ID
if ($_GET['action'] == 'edit' && (!isset($_GET['id']) || empty($_GET['id']))) {
    header("Location: settings.php?action=edit&id=" . $userid);
    exit;
}

// Verify the ID matches the logged-in user
if (isset($_GET['id']) && $_GET['id'] != $userid) {
    error_log("ID mismatch - Session ID: " . $userid . ", URL ID: " . $_GET['id']);
    header("Location: settings.php?action=edit&id=" . $userid);
    exit;
}

// Assuming $patientEmail is fetched from the database

// Prepare a secure query using prepared statements
$sqlmain = "SELECT * FROM patient WHERE pemail=?";
$stmt = $database->prepare($sqlmain);

$stmt->bind_param("s", $_SESSION["user"]);
$stmt->execute();
$result = $stmt->get_result();

// Check if query was successful and fetch the email
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $patientEmail = $row['pemail']; // Retrieve the patient's email
} else {
    // Handle the case where no patient is found
    echo "Error: Patient email not found in the database.";
    exit;
}

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        


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
        @media (max-width: 768px) {
            .dash-body {
                width: 100% !important;
                padding: 8px !important;
                margin: 0 !important;
                box-sizing: border-box;
                overflow-x: hidden !important;
            }
            .filter-container {
                width: 100% !important;
                padding: 8px !important;
                margin: 0 !important;
                box-sizing: border-box;
                overflow-x: auto;
            }
            .filter-container table,
            .filter-container tr,
            .filter-container td {
                width: 100% !important;
                box-sizing: border-box;
                padding: 0 !important;
                margin: 0 !important;
            }
            .container {
                width: 100% !important;
                padding: 0 !important;
                margin: 0 !important;
                box-sizing: border-box;
                overflow-x: hidden !important;
            }
        }
        @media (max-width: 480px) {
            .dash-body {
                padding: 4px !important;
            }
            .filter-container {
                padding: 4px !important;
            }
        }
    </style>
    
    
</head>
<body>
    <div id="mobile-hamburger-header" style="display:none; align-items:center; justify-content:center;">
        <div style="display:flex;align-items:center;justify-content:center;height:54px;width:100vw;">
            <span style="color:#fff;font-size:1.25em;font-weight:bold;letter-spacing:1px;line-height:1;">Mabayuan Health Care</span>
        </div>
    </div>
    <?php

    

    //import database
    $useremail = $_SESSION["user"]; 
    $sqlmain = "select * from patient where pemail=?";
    $stmt = $database->prepare($sqlmain);
    $stmt->bind_param("s",$useremail);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $userfetch = $result->fetch_assoc();
        $userid = $userfetch["pid"];
        $username = $userfetch["pname"];
    } else {
        echo "<script>
            Swal.fire({
                title: 'Error!',
                text: 'User not found.',
                icon: 'error',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = '../logout.php';
            });
          </script>";
        exit;
    }


    ?>
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
                            <td width="30%" style="padding-left:20px">
                                <img src="../img/user.png" alt="" width="100%" style="border-radius:50%">
                            </td>
                            <td style="padding:0px;margin:0px;">
                                <p class="profile-title"><?php echo $username  ?></p>
                                <p class="profile-subtitle"><?php echo $patientEmail; ?></p> <!-- Display admin email here -->
                            </td>
                        </tr>

                            <tr>
                            <td colspan="2">
        <button onclick="confirmLogout()" class="logout-btn btn-primary-soft btn">Log out</button>
    </td>
</tr>


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
                        <a href="index.php" class="non-style-link-menu "><div><p class="menu-text">Home</p></a></div></a>
                    </td>
                </tr>
                <!-- <tr class="menu-row" >
                    <td class="menu-btn menu-icon-schedule">
                        <a href="schedule.php" class="non-style-link-menu"><div><p class="menu-text">Schedule</p></div></a>
                    </td>
                </tr> -->
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-appoinment">
                        <a href="appointment.php" class="non-style-link-menu"><div><p class="menu-text">Booking History</p></a></div>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-settings menu-active menu-icon-settings-active">
                        <a href="settings.php?action=edit&id=<?php echo $userid; ?>" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">Settings</p></a></div>
                    </td>
                </tr>
                <!-- <tr class="menu-row" >
                    <td class="menu-btn menu-icon-patient">
                        <a href="patient.php" class="non-style-link-menu"><div><p class="menu-text">Patients</p></a></div>
                    </td>
                </tr> -->
            </table>
        </div>
        <script>
    const hamburger = document.getElementById('hamburger');
    const menu = document.getElementById('menu');
    hamburger.addEventListener('click', () => {
    console.log("Hamburger clicked!"); // Debugging line
    menu.classList.toggle('show');
    });

    </script>
        <div class="dash-body" style="margin-top: 15px">
            <table border="0" width="100%" style=" border-spacing: 0;margin:0;padding:0;" >
                        
                    <tr >
                            
                    <!-- <td width="13%" >
                    <a href="settings.php" ><button  class="login-btn btn-primary-soft btn btn-icon-back"  style="padding-top:11px;padding-bottom:11px;margin-left:20px;width:125px"><font class="tn-in-text">Back</font></button></a>
                    </td> -->
                    
                                <tr class="date-container">
                                <td width="100%">
                                    <p style="font-size: 14px;color: rgb(119, 119, 119);padding: 0;margin: 0;">
                                        Today's Date
                                    </p>
                                <p class="heading-sub12" style="margin: 0;">
                            
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
                            

                        </tr>
                        <td>
                        <p style="font-size: 23px;padding-left:12px;font-weight: 600;">Settings</p>
                                           
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
        <a href="?action=edit&id=<?php echo $userid; ?>&error=0" class="non-style-link">
            <div class="dashboard-items setting-tabs" style="padding:20px;margin:auto;width:95%;display: flex">
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
    <td style="width: 25%;">
        <a href="?action=view&id=<?php echo $userid; ?>" class="non-style-link">
            <div class="dashboard-items setting-tabs" style="padding:20px;margin:auto;width:95%;display: flex;">
                <div class="btn-icon-back dashboard-icons-setting" style="background-image: url('../img/icons/view-iceblue.svg');"></div>
                <div>
                    <div class="h1-dashboard">
                        View Account Details
                    </div><br>
                    <div class="h3-dashboard" style="font-size: 15px;">
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
                                    <a href="?action=drop&id=<?php echo $userid ?>&name=<?php echo urlencode($username); ?>" class="non-style-link">

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
                                                
                                    </div> -->
                                    </a>
                                </td>
                                
                            </tr>
                        </table>
                    </center>
                    </td>
                </tr>
            
            </table>
        </div>
    </div>
<?php 
            echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';

            // Get parameters safely
            $userid = isset($_GET['id']) ? intval($_GET['id']) : 0;
            $action = $_GET['action'] ?? '';



            $error = $_GET['error'] ?? '';

            if ($error) {
                echo '<script>';
                switch($error) {
                    case '2':
                        echo "Swal.fire({
                            title: 'Password Mismatch!',
                            text: 'New password and confirm password do not match.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });";
                        break;
                    case '3':
                        echo "Swal.fire({
                            title: 'Missing Fields!',
                            text: 'Please fill in all required fields.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });";
                        break;
                    case '4':
                        echo "Swal.fire({
                            title: 'Success!',
                            text: 'Profile updated successfully.',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        });";
                        break;
                    case '5':
                    echo "Swal.fire({
                        title: 'Error!',
                        text: 'Current password is incorrect.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });";
                        break;
                    case '6':
                        echo "Swal.fire({
                            title: 'Error!',
                            text: 'User not found.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });";
                        break;
                    case '7':
                        echo "Swal.fire({
                            title: 'Duplicate Entry!',
                            text: 'This email or phone number is already registered.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });";
                        break;
                    case 'missing_id':
                        echo "Swal.fire({
                            title: 'Error!',
                            text: 'Missing user ID.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });";
                        break;
                    case 'invalid_id':
                        echo "Swal.fire({
                            title: 'Error!',
                            text: 'Invalid user ID.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });";
                        break;
                    case 'unauthorized':
                        echo "Swal.fire({
                            title: 'Error!',
                            text: 'Unauthorized access.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });";
                        break;
                }
                echo '</script>';
            }
     





        // Now handle other actions, e.g. 'drop'
        if ($action == 'drop') {
            $nameget = $_GET["name"] ?? '';
            $safeName = htmlspecialchars(substr($nameget, 0, 40));
            echo "
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Are you sure?',
                    html: 'You want to delete your account:<br><strong>{$safeName}</strong>',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'No, cancel',
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'delete-account.php?id={$userid}';
                    } else {
                        window.location.href = 'settings.php';
                    }
                });
            });
            </script>";
        
    }

elseif ($action == 'view') {
    $sqlmain = "SELECT * FROM patient WHERE pid=?";
    $stmt = $database->prepare($sqlmain);
    $stmt->bind_param("i", $userid);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    $name = htmlspecialchars($row["pname"]);
    $email = htmlspecialchars($row["pemail"]);
    $address = htmlspecialchars($row["paddress"]);
    $dob = htmlspecialchars($row["pdob"]);
    $phone_number = htmlspecialchars($row["phone_number"]);

    // Inline SVG icons (simple, clean)
    $nameSvg = '<svg xmlns="http://www.w3.org/2000/svg" style="vertical-align: middle;" width="20" height="20" fill="#4c9f70" viewBox="0 0 16 16"><path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/></svg>';
    $emailSvg = '<svg xmlns="http://www.w3.org/2000/svg" style="vertical-align: middle;" width="20" height="20" fill="#4c9f70" viewBox="0 0 16 16"><path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v.217l-8 4.8-8-4.8V4z"/><path d="M0 6.383v5.617a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V6.383l-8 4.8-8-4.8z"/></svg>';
    $phoneSvg = '<svg xmlns="http://www.w3.org/2000/svg" style="vertical-align: middle;" width="20" height="20" fill="#4c9f70" viewBox="0 0 16 16"><path d="M3.654 1.328a.678.678 0 0 1 1.015-.063l2.29 2.29a.678.678 0 0 1-.063 1.015l-1.232.87a.678.678 0 0 0-.168.739c.257.667.62 1.283 1.077 1.757.454.47 1.002.832 1.674 1.09a.678.678 0 0 0 .739-.168l.87-1.232a.678.678 0 0 1 1.015-.063l2.29 2.29a.678.678 0 0 1-.063 1.015l-2.507 1.9a1.745 1.745 0 0 1-1.962-.288 16.627 16.627 0 0 1-5.07-5.07 1.745 1.745 0 0 1-.288-1.962l1.9-2.507z"/></svg>';
    $addressSvg = '<svg xmlns="http://www.w3.org/2000/svg" style="vertical-align: middle;" width="20" height="20" fill="#4c9f70" viewBox="0 0 16 16"><path d="M8 0a5 5 0 0 0-5 5c0 4.667 5 11 5 11s5-6.333 5-11a5 5 0 0 0-5-5zm0 7a2 2 0 1 1 0-4 2 2 0 0 1 0 4z"/></svg>';
    $dobSvg = '<svg xmlns="http://www.w3.org/2000/svg" style="vertical-align: middle;" width="20" height="20" fill="#4c9f70" viewBox="0 0 16 16"><path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h.5A1.5 1.5 0 0 1 15 2.5v11A1.5 1.5 0 0 1 13.5 15h-11A1.5 1.5 0 0 1 1 13.5v-11A1.5 1.5 0 0 1 2.5 1H3v-.5a.5.5 0 0 1 .5-.5zM2 5v8.5a.5.5 0 0 0 .5.5H13v-9H2z"/></svg>';

    echo "
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            title: 'Patient Details',
            html: `
                <div class='swal2-details-responsive'>
                    <div class='swal2-details-row'><span class='swal2-details-icon'>$nameSvg</span><span class='swal2-details-label'>Name:</span><span class='swal2-details-value'>$name</span></div>
                    <div class='swal2-details-row'><span class='swal2-details-icon'>$emailSvg</span><span class='swal2-details-label'>Email:</span><span class='swal2-details-value'>$email</span></div>
                    <div class='swal2-details-row'><span class='swal2-details-icon'>$phoneSvg</span><span class='swal2-details-label'>Phone:</span><span class='swal2-details-value'>$phone_number</span></div>
                    <div class='swal2-details-row'><span class='swal2-details-icon'>$addressSvg</span><span class='swal2-details-label'>Address:</span><span class='swal2-details-value'>$address</span></div>
                    <div class='swal2-details-row'><span class='swal2-details-icon'>$dobSvg</span><span class='swal2-details-label'>Date of Birth:</span><span class='swal2-details-value'>$dob</span></div>
                </div>
            `,
            confirmButtonText: 'OK',
            showCloseButton: true,
            customClass: {
                popup: 'swal2-popup-custom-responsive'
            }
        });
    });
    </script>
    <style>
    .swal2-popup-custom-responsive {
        width: 460px !important;
        padding: 24px 32px !important;
        border-radius: 18px !important;
        font-family: Arial, sans-serif;
    }
    .swal2-details-responsive {
        display: flex;
        flex-direction: column;
        gap: 18px;
        margin-top: 10px;
    }
    .swal2-details-row {
            display: flex;
            align-items: center;
        gap: 10px;
        font-size: 1.05em;
        background: #f8f9fa;
        border-radius: 8px;
        padding: 10px 12px;
    }
    .swal2-details-icon {
            flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        background: #e6f3e6;
        border-radius: 50%;
    }
    .swal2-details-label {
        font-weight: 600;
        color: #2d6a4f;
        min-width: 90px;
    }
    .swal2-details-value {
        color: #333;
        word-break: break-word;
        flex: 1;
    }
    @media (max-width: 600px) {
        .swal2-popup-custom-responsive {
            width: 98vw !important;
            min-width: 0 !important;
            padding: 10px 4vw !important;
        }
        .swal2-details-row {
            font-size: 0.98em;
            flex-direction: column;
            align-items: flex-start;
            gap: 4px;
            padding: 8px 6px;
        }
        .swal2-details-label {
            min-width: 0;
        }
        .swal2-details-icon {
            width: 22px;
            height: 22px;
        }
        }
    </style>";
}


elseif ($action == 'edit') {
    // First get the user's ID from their email
    $verify = $database->prepare("SELECT pid FROM patient WHERE pemail = ?");
    $verify->bind_param("s", $_SESSION["user"]);
    $verify->execute();
    $verifyResult = $verify->get_result();
    
    if ($verifyResult && $verifyResult->num_rows > 0) {
        $userData = $verifyResult->fetch_assoc();
        $userid = $userData['pid']; // Use the ID from the database
    } else {
        error_log("Debug - No user found with email: " . $_SESSION["user"]);
        header("location: ../login.php");
        exit;
    }
    
    // Now get the full user details
    $stmt = $database->prepare("SELECT * FROM patient WHERE pid = ?");
    $stmt->bind_param("i", $userid);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        error_log("Debug - No user found with ID: " . $userid);
        header("Location: settings.php");
        exit;
    }

    $row = $result->fetch_assoc();
    $userid = $row["pid"]; 
    $name = htmlspecialchars($row["pname"]);
    $email = htmlspecialchars($row["pemail"]);
    $nic = htmlspecialchars($row["hasPhilhealth"]);
    $address = htmlspecialchars($row["paddress"]);
    $phone_number = htmlspecialchars($row["phone_number"]);

    $error_1 = $_GET["error"] ?? '0';


    // SVG icons for inputs
    $emailSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#4c9f70" viewBox="0 0 16 16"><path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v.217l-8 4.8-8-4.8V4z"/><path d="M0 6.383v5.617a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V6.383l-8 4.8-8-4.8z"/></svg>';
    $nameSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#4c9f70" viewBox="0 0 16 16"><path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/></svg>';
    $phoneSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#4c9f70" viewBox="0 0 16 16"><path d="M3.654 1.328a.678.678 0 0 1 1.015-.063l2.29 2.29a.678.678 0 0 1-.063 1.015l-1.232.87a.678.678 0 0 0-.168.739c.257.667.62 1.283 1.077 1.757.454.47 1.002.832 1.674 1.09a.678.678 0 0 0 .739-.168l.87-1.232a.678.678 0 0 1 1.015-.063l2.29 2.29a.678.678 0 0 1-.063 1.015l-2.507 1.9a1.745 1.745 0 0 1-1.962-.288 16.627 16.627 0 0 1-5.07-5.07 1.745 1.745 0 0 1-.288-1.962l1.9-2.507z"/></svg>';
    $addressSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#4c9f70" viewBox="0 0 16 16"><path d="M8 0a5 5 0 0 0-5 5c0 4.667 5 11 5 11s5-6.333 5-11a5 5 0 0 0-5-5zm0 7a2 2 0 1 1 0-4 2 2 0 0 1 0 4z"/></svg>';
    $philhealthSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#4c9f70" viewBox="0 0 16 16"><path d="M2 2h12v12H2z" fill="none"/><path d="M4 4h8v8H4z"/></svg>'; // simple square icon as placeholder for PhilHealth ID
    $currentPassSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#4c9f70" viewBox="0 0 16 16"><path d="M8 1a4 4 0 0 0-4 4v2H3a1 1 0 0 0-1 1v5a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1v-5a1 1 0 0 0-1-1h-1V5a4 4 0 0 0-4-4zm-2 6V5a2 2 0 1 1 4 0v2H6z"/></svg>';
    $newPassSvg = $currentPassSvg;
    $confirmPassSvg = $currentPassSvg;

    // Add error reporting at the top
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    
    if ($error_1 != '4') {
        // Log the current state
        error_log("Settings page loaded with userid: $userid, error: $error_1");
        
        echo '
        <div id="popup1" class="overlay">
            <div class="popup refined-edit-form">
                <a class="close" href="settings.php">&times;</a>
                <div class="form-header">
                    <i class="fas fa-user-edit icon-circle"></i>
                    <h2>Edit Patient Profile</h2>
                </div>
                <form action="edit-user.php" method="POST" autocomplete="off" onsubmit="return validateForm(this);">
                    <div class="form-grid">
                        <div class="form-card">
                            <label>Email</label>
                            <input type="email" name="email" value="' . htmlspecialchars($email) . '" readonly style="background:#f5f5f5;cursor:not-allowed;" required>
                        </div>
                        <div class="form-card">
                            <label>Name</label>
                            <input type="text" name="name" value="' . htmlspecialchars($name) . '" readonly style="background:#f5f5f5;cursor:not-allowed;" required>
                        </div>
                        <div class="form-card">
                            <label>Phone Number</label>
                            <input type="tel" name="phone_number" value="' . htmlspecialchars($phone_number) . '" readonly style="background:#f5f5f5;cursor:not-allowed;" required>
                        </div>
                        <div class="form-card">
                            <label>Address</label>
                            <input type="text" name="address" value="' . htmlspecialchars($address) . '" readonly style="background:#f5f5f5;cursor:not-allowed;" required>
                        </div>
                        <div class="form-card">
                            <label>PhilHealth ID</label>
                            <input type="text" name="hasPhilhealth" value="' . htmlspecialchars($nic) . '" readonly style="background:#f5f5f5;cursor:not-allowed;" required>
                        </div>
                        <div class="form-card">
                            <label>Current Password</label>
                            <input type="password" name="current_password" autocomplete="current-password" required>
                        </div>
                        <div class="form-card">
                            <label>New Password</label>
                            <input type="password" name="password" autocomplete="new-password">
                        </div>
                        <div class="form-card">
                            <label>Confirm Password</label>
                            <input type="password" name="cpassword" autocomplete="new-password">
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="reset" class="btn secondary">Reset</button>
                        <button type="submit" class="btn primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
        <script>
        function validateForm(form) {
            if (!form.current_password.value.trim()) {
                alert("Please enter your current password");
                return false;
            }
            if (form.password.value.trim() && form.password.value !== form.cpassword.value) {
                alert("New password and confirm password do not match");
                return false;
            }
            return true;
        }
        </script>';
    } else {
        echo "
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Edit Successfully!',
                html: 'If you changed your email, please log out and log in again with the new email.',
                icon: 'success',
                showCancelButton: true,
                confirmButtonText: 'OK',
                cancelButtonText: 'Log out',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#6c757d'
            }).then((result) => {
                if (result.dismiss === Swal.DismissReason.cancel) {
                    window.location.href = '../logout.php';
                } else {
                    window.location.href = 'settings.php';
                }
            });
        });
        </script>";
    }
}
?>
<style>
/* Overlay and popup container */
.overlay {
  position: fixed;
  top: 0; left: 0;
  width: 100vw; height: 100vh;
  background: rgba(0,0,0,0.6);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 999;
}

.popup.refined-edit-form {
  background: #fff;
  border-radius: 16px;
  padding: 2rem 2.5rem;
  width: 90%;
  max-width: 600px;
  box-shadow: 0 10px 25px rgba(0,0,0,0.15);
  position: relative;
  max-height: 90vh;
  overflow-y: auto;
}

/* Close button */
.popup .close {
  position: absolute;
  right: 20px;
  top: 20px;
  font-size: 1.5rem;
  color: #888;
  text-decoration: none;
  transition: color 0.3s ease;
}
.popup .close:hover {
  color: #4c9f70;
}

/* Header */
.form-header {
  text-align: center;
  margin-bottom: 1.5rem;
}
.icon-circle {
  font-size: 2.5rem;
  color: #4c9f70;
  border: 2px solid #4c9f70;
  border-radius: 50%;
  padding: 0.5rem;
  display: inline-block;
  margin-bottom: 0.5rem;
}

/* Form grid */
.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}

/* Single form card */
.form-card {
  background: #f9f9f9;
  padding: 1rem;
  border-radius: 12px;
  display: flex;
  flex-direction: column;
}

.form-card label {
  font-weight: 600;
  margin-bottom: 0.4rem;
  color: #333;
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 0.9rem;
}

.form-card input {
  border: 1px solid #ccc;
  border-radius: 8px;
  padding: 0.55rem 0.75rem;
  font-size: 1rem;
  transition: border-color 0.3s ease;
}

.form-card input:focus {
  outline: none;
  border-color: #4c9f70;
  box-shadow: 0 0 5px #4c9f70aa;
}

/* Buttons container */
.form-actions {
  text-align: center;
  margin-top: 1.5rem;
  display: flex;
  justify-content: center;
  gap: 12px;
}

.btn {
  padding: 0.6rem 1.6rem;
  border-radius: 8px;
  font-weight: 600;
  border: none;
  cursor: pointer;
  font-size: 1rem;
  transition: background-color 0.3s ease;
}

.btn.primary {
  background-color: #4c9f70;
  color: #fff;
}
.btn.primary:hover {
  background-color: #3c7a56;
}

.btn.secondary {
  background-color: #eee;
  color: #333;
}
.btn.secondary:hover {
  background-color: #ddd;
}

/* Responsive for small screens */
@media (max-width: 480px) {
  .popup.refined-edit-form {
    width: 95%;
    padding: 1rem 1.2rem;
  }
  .form-grid {
    grid-template-columns: 1fr;
    gap: 0.75rem;
  }
  .form-actions {
    flex-direction: column;
    gap: 10px;
    margin-top: 1rem;
  }
  .btn {
    width: 100%;
  }
  .popup .close {
    right: 10px;
    top: 10px;
    font-size: 1.3rem;
  }
}

</style>



</body>
</html>
