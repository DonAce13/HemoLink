<?php

// Start the session to check for user login
session_start();

if (isset($_SESSION["user"])) {
    if ($_SESSION["user"] == "" || $_SESSION['usertype'] != 'p') {
        header("location: ../login.php");
        exit;
    }
} else {
    header("location: ../login.php");
    exit;
}

// Include the database connection file
include("../connection.php");  // Make sure this path is correct

// Assuming $patientEmail is fetched from the database
$query = "SELECT pemail FROM patient WHERE pemail = ?";  // Use 'pemail' as per the patient table schema

// Prepare a secure query using prepared statements
$stmt = $database->prepare($query);
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
    <link rel="stylesheet" href="../css/animations.css">  
    <link rel="stylesheet" href="../css/main.css">  
    <link rel="stylesheet" href="../css/admin.css">
        
    <title>Sessions</title>
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

    if(isset($_SESSION["user"])){
        if(($_SESSION["user"])=="" or $_SESSION['usertype']!='p'){
            header("location: ../login.php");
        }else{
            $useremail=$_SESSION["user"];
        }
    }else{
        header("location: ../login.php");
    }

    // Import database
    include("../connection.php");
    $sqlmain = "SELECT * FROM patient WHERE pemail=?";
    $stmt = $database->prepare($sqlmain);
    $stmt->bind_param("s", $useremail);
    $stmt->execute();
    $result = $stmt->get_result();
    $userfetch = $result->fetch_assoc();
    $userid = $userfetch["pid"];
    $username = $userfetch["pname"];

    date_default_timezone_set('Asia/Kolkata');
    $today = date('Y-m-d');
    ?>
    <div class="container">
        <div class="hamburger" id="hamburger">
            <div class="bar"></div>
            <div class="bar"></div>
            <div class="bar"></div>
        </div>

        <!-- Menu Container -->
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
                                    <p class="profile-subtitle"><?php echo $patientEmail; ?></p> <!-- Display admin email here -->
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
                <?php
                    // Get the current script name
                    $currentPage = basename($_SERVER['PHP_SELF']);
                ?>
                <!-- Menu Items -->
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-dashbord <?php if ($currentPage == 'index.php') echo 'menu-active menu-icon-dashbord-active'; ?>">
                        <a href="index.php" class="non-style-link-menu <?php if ($currentPage == 'index.php') echo 'non-style-link-menu-active'; ?>">
                            <div><p class="menu-text">Dashboard</p></div>
                        </a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-doctor <?php if ($currentPage == 'doctors.php') echo 'menu-active menu-icon-doctor-active'; ?>">
                        <a href="doctors.php" class="non-style-link-menu <?php if ($currentPage == 'doctors.php') echo 'non-style-link-menu-active'; ?>">
                            <div><p class="menu-text">Doctors</p></div>
                        </a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-doctor <?php if ($currentPage == 'schedule.php') echo 'menu-active menu-icon-doctor-active'; ?>">
                        <a href="schedule.php" class="non-style-link-menu <?php if ($currentPage == 'schedule.php') echo 'non-style-link-menu-active'; ?>">
                            <div><p class="menu-text">Schedule</p></div>
                        </a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-doctor <?php if ($currentPage == 'appointment.php') echo 'menu-active menu-icon-doctor-active'; ?>">
                        <a href="appointment.php" class="non-style-link-menu <?php if ($currentPage == 'appointment.php') echo 'non-style-link-menu-active'; ?>">
                            <div><p class="menu-text">Appointment</p></div>
                        </a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-doctor <?php if ($currentPage == '"settings.php') echo 'menu-active menu-icon-doctor-active'; ?>">
                        <a href="settings.php" class="non-style-link-menu <?php if ($currentPage == '"settings.php') echo 'non-style-link-menu-active'; ?>">
                            <div><p class="menu-text">Settings</p></div>
                        </a>
                    </td>
                </tr>

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

        <?php
            // Query to get today's schedule
            $sqlmain = "SELECT * FROM schedule INNER JOIN doctor ON schedule.docid=doctor.docid WHERE schedule.scheduledate >= '$today' ORDER BY schedule.scheduledate ASC";
            $schedulerow = $database->query($sqlmain);
        ?>

        <div class="dash-body">
            <table border="0" width="100%" style="border-spacing: 0;margin:0;padding:0;margin-top:25px;">
                <div class="dash-body" style="margin-top: 15px">
                    <table border="0" width="100%" style="border-spacing: 0;margin:0;padding:0;">
                        <tr class="date-container">
                            <td width="100%">
                                <p style="font-size: 14px;color: rgb(119, 119, 119);padding: 0;margin: 0;">Today's Date</p>
                                <p class="heading-sub12" style="margin: 0;"><?php echo $today; ?></p>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="2" class="nav-bar">
                                <form action="doctors.php" method="post" class="header-search">
                                    <input type="search" name="search" class="input-text header-searchbar" placeholder="Search Doctor name or Email" list="doctors">&nbsp;&nbsp;
                                    <?php
                                    echo '<datalist id="doctors">';
                                    $list11 = $database->query("SELECT docname, docemail FROM doctor;");
                                    for ($y = 0; $y < $list11->num_rows; $y++) {
                                        $row00 = $list11->fetch_assoc();
                                        $d = $row00["docname"];
                                        $c = $row00["docemail"];
                                        echo "<option value='$d'><br/>";
                                        echo "<option value='$c'><br/>";
                                    }
                                    echo '</datalist>';
                                    ?>
                                    <input type="Submit" value="Search" class="login-btn btn-primary-soft btn" style="padding-left: 25px;padding-right: 25px;padding-top: 10px;padding-bottom: 10px;">
                                </form>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="4">
                                <center>
                                    <div class="abc scroll">
                                        <table width="100%" class="sub-table scrolldown" border="0" style="padding: 50px;border:none">
                                            <tbody>
                                                <?php
                                                // Check if there are any schedules
                                                if ($schedulerow->num_rows == 0) {
                                                    echo '<tr>
                                                    <td colspan="4">
                                                    <br><br><br><br>
                                                    <center>
                                                    <img src="../img/notfound.svg" width="25%">
                                                    <br>
                                                    <p class="heading-main12" style="margin-left: 45px;font-size:20px;color:rgb(49, 49, 49)">We couldn\'t find anything related to your keywords !</p>
                                                    <a class="non-style-link" href="schedule.php"><button class="login-btn btn-primary-soft btn" style="display: flex;justify-content: center;align-items: center;margin-left:20px;">&nbsp; Show all Sessions &nbsp;</button></a>
                                                    </center>
                                                    <br><br><br><br>
                                                    </td>
                                                    </tr>';
                                                } else {
                                                    // Iterate through the schedules
                                                    while ($row = $schedulerow->fetch_assoc()) {
                                                        $scheduleid = $row["scheduleid"];
                                                        $title = $row["title"];
                                                        $docname = $row["docname"];
                                                        $scheduledate = $row["scheduledate"];
                                                        $scheduletime = $row["scheduletime"];

                                                        // Get the maximum number of patients (nop) from the schedule
                                                        $sql_schedule = $database->query("SELECT nop FROM schedule WHERE scheduleid = '$scheduleid'");
                                                        $schedule_data = $sql_schedule->fetch_assoc();
                                                        $max_patients = $schedule_data['nop'];

                                                        // Check how many patients have already booked
                                                        $patient_count = $database->query("SELECT COUNT(*) AS patient_count FROM appointment WHERE scheduleid = '$scheduleid'")->fetch_assoc();
                                                        $patient_count_value = $patient_count['patient_count']; // Current number of booked patients

                                                        // Check if the session is full
                                                        if ($patient_count_value >= $max_patients) {
                                                            // Disable the button and make it unclickable
                                                            $button_disabled = '<button class="login-btn btn-primary-soft btn" style="padding-top:11px;padding-bottom:11px;width:100%" disabled>
                                                                                    <font class="tn-in-text">Session Full</font>
                                                                                </button>';
                                                        } else {
                                                            // Logic for showing "Book Now" button or already booked status
                                                            $booking_check = $database->query("SELECT * FROM appointment WHERE pid = (SELECT pid FROM patient WHERE pemail = '" . $_SESSION["user"] . "') AND scheduleid = '$scheduleid'");
                                                            if ($booking_check->num_rows > 0) {
                                                                $button_disabled = '<button class="login-btn btn-primary-soft btn" style="padding-top:11px;padding-bottom:11px;width:100%" disabled><font class="tn-in-text">Already Booked</font></button>';
                                                            } else {
                                                                $button_disabled = '<a href="booking.php?id=' . $scheduleid . '">
                                                                                    <button class="login-btn btn-primary-soft btn" style="padding-top:11px;padding-bottom:11px;width:100%">
                                                                                        <font class="tn-in-text">Book Now</font>
                                                                                    </button>
                                                                                </a>';
                                                            }
                                                        }
                                                        


                                                        echo '
                                                        <tr>
                                                            <td colspan="4">
                                                                <div class="sub-table">
                                                                    <div class="table-row">
                                                                        <p class="tn-in-text-title">'.$docname.'</p>
                                                                        <p class="tn-in-text">'.$scheduledate.' | '.$scheduletime.'</p>
                                                                        <p class="tn-in-text">Session title: '.$title.'</p>
                                                                        <div class="sub-btn">'.$button_disabled.'</div>
                                                                    </div>
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

                                            </tbody>
                                        </table>
                                    </div>
                                </center>
                            </td>
                        </tr>
                    </table>
                </div>
            </table>
        </div>
    </div>
</body>
</html>
