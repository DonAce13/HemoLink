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
$query = "SELECT pemail, pname FROM patient WHERE pemail = ?";  // Use 'pemail' as per the patient table schema

// Prepare a secure query using prepared statements
$stmt = $database->prepare($query);
$stmt->bind_param("s", $_SESSION["user"]);
$stmt->execute();
$result = $stmt->get_result();

// Check if query was successful and fetch the email and name
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $patientEmail = $row['pemail']; // Retrieve the patient's email
    $patientName = $row['pname'];   // Retrieve the patient's name
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
        
    <title>Dashboard</title>
    <style>
        .dashbord-tables {
            animation: transitionIn-Y-over 0.5s;
        }
        .filter-container {
            animation: transitionIn-Y-bottom 0.5s;
        }
        .sub-table, .anime {
            animation: transitionIn-Y-bottom 0.5s;
        }
    </style>
</head>
<body>
    <?php
    // Check if a session is already started to avoid the warning
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Check if the user is logged in and is a patient
    if (!isset($_SESSION["user"]) || empty($_SESSION["user"]) || $_SESSION['usertype'] != 'p') {
        header("location: ../login.php");
        exit;
    }

    $useremail = $_SESSION["user"];

    // Import the database connection
    include("../connection.php");

    // Fetch patient details securely using prepared statements
    $sqlmain = "SELECT * FROM patient WHERE pemail = ?";
    $stmt = $database->prepare($sqlmain);
    $stmt->bind_param("s", $useremail);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $userfetch = $result->fetch_assoc();
        $userid = $userfetch["pid"];
        $username = $userfetch["pname"];
    } else {
        // Handle the case where no patient is found
        $username = "Unknown User";
        $userid = 0;
    }


    if (isset($_SESSION["login_success"]) && !isset($_SESSION["alert_shown"])) {
        $userType = $_SESSION["user_type"] ?? "Patient";
        $userName = $_SESSION["user_name"] ?? $username;
        echo "
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Welcome to Mabayuan Health!',
                    text: 'You have successfully logged in as {$userType}',
                    icon: 'success',
                    confirmButtonText: 'Continue',
                    confirmButtonColor: '#2d6a4f'
                });
            });
        </script>";
        $_SESSION["alert_shown"] = true;
    }
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
                                <p class="profile-title"><?php echo $username  ?></p>
                                <p class="profile-subtitle"><?php echo $patientEmail; ?></p> <!-- Display admin email here -->
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
                    <td class="menu-btn menu-icon-dashbord menu-active menu-icon-dashbord-active" >
                        <a href="index.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">Dashboard</p></a></div></a>
                    </td>
                </tr>
                <!-- <tr class="menu-row" >
                    <td class="menu-btn menu-icon-schedule">
                        <a href="schedule.php" class="non-style-link-menu"><div><p class="menu-text">Schedule</p></div></a>
                    </td>
                </tr> -->
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-appoinment">
                        <a href="appointment.php" class="non-style-link-menu"><div><p class="menu-text">My Booking History</p></a></div>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-settings">
                        <a href="settings.php" class="non-style-link-menu"><div><p class="menu-text">Settings</p></a></div>
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
                                <td width="10%">
                                    <!-- <button class="btn-label">
                                        <img src="../img/calendar.svg" width="100%">
                                    </button> -->
                                </td>
                            </tr>
                            
                <tr>
                    <td colspan="4" >
                        
                    <center>
                    <table class="filter-container doctor-header patient-header" style="border: none;width:95%" border="0" >
                    <tr>
                        <td >
                            <h3>Welcome!</h3>
                            <h1><?php echo $username  ?>.</h1>
                    </tr>
                    <td colspan="2" class="nav-bar" >
                                
                                <form action="" method="get" class="header-search">
                                    <label for="session_date">Filter Sessions by Date:</label>
                                    <input type="date" id="session_date" name="session_date" class="input-text header-searchbar" 
                                           value="<?php echo isset($_GET['session_date']) ? htmlspecialchars($_GET['session_date']) : ''; ?>">
                                    
                                    <input type="submit" value="Filter Sessions" class="btn-primary-soft btn button-icon btn-search" 
                                           style="padding-left: 25px;padding-right: 25px;padding-top: 10px;padding-bottom: 10px;">
                                    
                                    <?php if(isset($_GET['session_date'])): ?>
                                        <a href="index.php" class="btn-primary-soft btn button-icon btn-search" 
                                           style="padding-left: 15px;padding-right: 15px;padding-top: 10px;padding-bottom: 10px; margin-left: 10px;">
                                            Reset Filter
                                        </a>
                                    <?php endif; ?>
                                </form>
                            </td>
                    
                    </table>
                    </center>
                    
                </td>
                </tr>
                <tr>

                                    </center>








                                </td>
                                
                            </tr>
                        </table>
                    </td>
                    <td>


                            
                                    <p style="font-size: 20px;font-weight:600;padding-left: 40px;" class="anime">Available Sessions</p>
                                    <center>
                                        <div class="abc scroll" style="height: 500px;padding: 0;margin: 0;">
                                        <?php
                                        // Get current month and year
                                        $current_month = date('F Y');
                                        
                                        // Check if a specific date is selected
                                        $filter_date = isset($_GET['session_date']) ? $_GET['session_date'] : null;
                                        
                                        // Display month or filtered date
                                        if ($filter_date) {
                                            $formatted_date = date('F d, Y', strtotime($filter_date));
                                            echo "<h2 style='text-align: center; color: #2d6a4f; margin-bottom: 20px;'>Sessions on $formatted_date</h2>";
                                        } else {
                                            echo "<h2 style='text-align: center; color: #2d6a4f; margin-bottom: 20px;'>$current_month</h2>";
                                        }
                                        ?>
                                        <?php
                                        // Days of the week
                                        ?>
                                        <table width="95%" class="sub-table scrolldown" border="0">
                                        <thead>
                                            <tr>
                                                <th class="table-headin">Session Title</th>
                                                <th class="table-headin">Doctor</th>
                                                <th class="table-headin">Date & Time</th>
                                                <th class="table-headin">Availability</th>
                                                <th class="table-headin">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $today = date('Y-m-d');
                                            $current_time = date('H:i:s');
                                            $nextweek = date("Y-m-d", strtotime("+1 week"));

                                            // Check if a specific date is selected for filtering
                                            $filter_date = isset($_GET['session_date']) ? $_GET['session_date'] : null;

                                            // Prepare the base SQL query
                                            $sqlmain = "SELECT schedule.*, doctor.docname, 
                                                        (SELECT COUNT(*) FROM appointment WHERE scheduleid = schedule.scheduleid) as booked_count,
                                                        DAYOFWEEK(schedule.scheduledate) as day_of_week,
                                                        DAYNAME(schedule.scheduledate) as day_name
                                                        FROM schedule 
                                                        INNER JOIN doctor ON schedule.docid = doctor.docid
                                                        WHERE 1=1";

                                            // Add date filtering conditions
                                            if ($filter_date) {
                                                // If a specific date is selected, show only sessions for that date
                                                $sqlmain .= " AND schedule.scheduledate = '$filter_date'";
                                            } else {
                                                // Default behavior: show sessions from today to next week
                                                $sqlmain .= " AND schedule.scheduledate >= '$today' 
                                                              AND schedule.scheduledate <= '$nextweek'
                                                              AND DAYOFWEEK(schedule.scheduledate) BETWEEN 2 AND 6
                                                              AND (schedule.scheduledate > '$today' OR 
                                                                   (schedule.scheduledate = '$today' AND schedule.scheduletime > '$current_time'))";
                                            }

                                            // Complete the query
                                            $sqlmain .= " ORDER BY 
                                                            CASE day_name 
                                                                WHEN 'Monday' THEN 1 
                                                                WHEN 'Tuesday' THEN 2 
                                                                WHEN 'Wednesday' THEN 3 
                                                                WHEN 'Thursday' THEN 4 
                                                                WHEN 'Friday' THEN 5 
                                                            END,
                                                            schedule.scheduletime";

                                            $result = $database->query($sqlmain);

                                            if ($result->num_rows == 0) {
                                                echo '<tr>
                                                <td colspan="5">
                                                <br><br><br><br>
                                                <center>
                                                <img src="../img/notfound.svg" width="25%">
                                                <br>
                                                <p class="heading-main12" style="margin-left: 45px;font-size:20px;color:rgb(49, 49, 49)">No available sessions at the moment!</p>
                                                </center>
                                                <br><br><br><br>
                                                </td>
                                                </tr>';
                                            } else {
                                                // Group sessions by day of the week
                                                $sessions_by_day = [];
                                                $days_order = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
                                                
                                                while ($row = $result->fetch_assoc()) {
                                                    $day_name = $row['day_name'];
                                                    $sessions_by_day[$day_name][] = $row;
                                                }

                                                // Iterate through days in order
                                                foreach ($days_order as $day_name) {
                                                    if (isset($sessions_by_day[$day_name])) {
                                                        // Display day header
                                                        echo "<tr><td colspan='5' style='background-color: #f0f0f0; font-weight: bold; text-align: center;'>$day_name</td></tr>";
                                                        
                                                        // Sort sessions for this day by time
                                                        usort($sessions_by_day[$day_name], function($a, $b) {
                                                            return strtotime($a['scheduletime']) - strtotime($b['scheduletime']);
                                                        });

                                                        // Display sessions for this day
                                                        foreach ($sessions_by_day[$day_name] as $row) {
                                                            $scheduleid = $row["scheduleid"];
                                                            $title = $row["title"];
                                                            $docname = $row["docname"];
                                                            $scheduledate = $row["scheduledate"];
                                                            $scheduletime = $row["scheduletime"];
                                                            $nop = $row["nop"]; // Number of participants
                                                            $booked_count = $row["booked_count"];
                                                            $remaining_slots = $nop - $booked_count;

                                                            // Determine button state
                                                            $button_state = "book-now";
                                                            $button_text = "Book Now";
                                                            $button_disabled = "";

                                                            if ($remaining_slots <= 0) {
                                                                $button_state = "session-full";
                                                                $button_text = "Session Full";
                                                                $button_disabled = "disabled";
                                                            }

                                                            // Check if session has reached 5 bookings
                                                            $booking_count_query = "SELECT COUNT(*) as booking_count FROM appointment WHERE scheduleid = $scheduleid";
                                                            $booking_count_result = $database->query($booking_count_query);
                                                            $booking_count_row = $booking_count_result->fetch_assoc();
                                                            $current_bookings = $booking_count_row['booking_count'];

                                                            if ($current_bookings >= 5) {
                                                                $button_state = "session-full";
                                                                $button_text = "Max Bookings Reached";
                                                                $button_disabled = "disabled";
                                                            }

                                                            echo '<tr>
                                                            <td style="text-align:center;">' . substr($title, 0, 30) . '</td>
                                                            <td style="text-align:center;">' . substr($docname, 0, 20) . '</td>
                                                            <td style="text-align:center;">' . 
                                                                substr($scheduledate, 0, 10) . ' @ ' . 
                                                                substr($scheduletime, 0, 5) . 
                                                            '</td>
                                                            <td style="text-align:center;">' . $remaining_slots . '/' . $nop . ' slots</td>
                                                            <td style="text-align:center;">
                                                                <a href="booking.php?id=' . $scheduleid . '">
                                                                    <button class="login-btn btn-primary-soft btn ' . $button_state . '" ' . $button_disabled . ' style="width:100%">
                                                                        ' . $button_text . '
                                                                    </button>
                                                                </a>
                                                            </td>
                                                            </tr>';
                                                        }
                                                    }
                                                }
                                            }
                                            ?>
                                        </tbody>
                                        </table>
                                        </div>
                                    </center>

                                    <p style="font-size: 20px;font-weight:600;padding-left: 40px;" class="anime">Your Upcoming Booking</p>
                                    <center>
                                        <div class="abc scroll" style="height: 1000px;padding: 0;margin: 0;">
                                        <table width="85%" class="sub-table scrolldown" border="0" >
                                        <thead>
                                            
                                        <tr>
                                        <th class="table-headin">
                                                    
                                                
                                                    Appoint. Number
                                                    
                                                    </th>
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
                                                    
                                                Booked For
                                                        
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        
                                            <?php
                                            $nextweek = date("Y-m-d", strtotime("+1 week"));
                                            $sqlmain = "SELECT *, IF(is_self = 0, 'For Myself', other_patient_name) AS patient_display FROM schedule 
                                                        INNER JOIN appointment ON schedule.scheduleid = appointment.scheduleid 
                                                        INNER JOIN patient ON patient.pid = appointment.pid 
                                                        INNER JOIN doctor ON schedule.docid = doctor.docid 
                                                        WHERE patient.pid = $userid AND schedule.scheduledate >= '$today' 
                                                        ORDER BY schedule.scheduledate ASC";
                                            $result = $database->query($sqlmain);

                                            if ($result->num_rows == 0) {
                                                echo '<tr>
                                                <td colspan="6">
                                                <br><br><br><br>
                                                <center>
                                                <img src="../img/notfound.svg" width="25%">
                                                
                                                <br>
                                                <p class="heading-main12" style="margin-left: 45px;font-size:20px;color:rgb(49, 49, 49)">Nothing to show here!</p>
                                                <a class="non-style-link" href="schedule.php"><button class="login-btn btn-primary-soft btn" style="display: flex;justify-content: center;align-items: center;margin-left:20px;">&nbsp; Find a Schedule &nbsp;</font></button>
                                                </a>
                                                </center>
                                                <br><br><br><br>
                                                </td>
                                                </tr>';
                                            } else {
                                                for ($x = 0; $x < $result->num_rows; $x++) {
                                                    $row = $result->fetch_assoc();
                                                    $scheduleid = $row["scheduleid"];
                                                    $title = $row["title"];
                                                    $apponum = $row["apponum"];
                                                    $docname = $row["docname"];
                                                    $scheduledate = $row["scheduledate"];
                                                    $scheduletime = $row["scheduletime"];
                                                    $patient_display = $row["patient_display"];

                                                    echo '<tr>
                                                    <td style="padding:30px;text-align:center;font-size:25px;font-weight:700;">&nbsp;' . $apponum . '</td>
                                                    <td style="padding:20px;text-align:center;">&nbsp;' . substr($title, 0, 30) . '</td>
                                                    <td style="text-align:center;">' . substr($docname, 0, 20) . '</td>
                                                    <td style="text-align:center;">' . substr($scheduledate, 0, 10) . ' ' . substr($scheduletime, 0, 5) . '</td>
                                                    <td style="text-align:center;">' . $patient_display . '</td>
                                                    </tr>';
                                                }
                                            }
                                            ?>
                 
                                            </tbody>
                
                                        </table>
                                        </div>
                                        </center>







                                </td>
                <tr>
            </table>
        </div>
    </div>


</body>
</html>