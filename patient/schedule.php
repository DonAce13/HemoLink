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
    <link rel="icon" type="image/png" href="../img/bg01.png">
    <link rel="icon" type="image/png" href="../img/bg01.png">
    <link rel="shortcut icon" type="image/png" href="../img/bg01.png">
    <link rel="stylesheet" href="../css/schedule.css">
    <link rel="stylesheet" href="../css/animations.css">  
    <link rel="stylesheet" href="../css/main.css">  
    <link rel="stylesheet" href="../css/admin.css">
        
    <title>Sessions</title>
    <style>
        .popup {
            animation: transitionIn-Y-bottom 0.5s;
        }
        .sub-table {
            animation: transitionIn-Y-bottom 0.5s;
        }
    </style>
</head>
<body>
    <?php
    if (isset($_SESSION["user"])) {
        if ($_SESSION["user"] == "" || $_SESSION['usertype'] != 'p') {
            header("location: ../login.php");
        } else {
            $useremail = $_SESSION["user"];
        }
    } else {
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

    date_default_timezone_set('Asia/Manila');
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
                    <td colspan="2">
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
                        </table>
                    </td>
                </tr>
                <?php
                    // Get the current script name
                    $currentPage = basename($_SERVER['PHP_SELF']);
                ?>
                <!-- Menu Items -->
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-schedule <?php if ($currentPage == 'schedule.php') echo 'menu-active menu-icon-schedule-active'; ?>">
                        <a href="schedule.php" class="non-style-link-menu <?php if ($currentPage == 'schedule.php') echo 'non-style-link-menu-active'; ?>">
                            <div><p class="menu-text">Schedule</p></div>
                        </a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-settings <?php if ($currentPage == 'settings.php') echo 'menu-active menu-icon-settings-active'; ?>">
                        <a href="settings.php" class="non-style-link-menu <?php if ($currentPage == 'settings.php') echo 'non-style-link-menu-active'; ?>">
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
        <tr class="date-container">
            <td width="100%">
                <p style="font-size: 14px;color: rgb(119, 119, 119);padding: 0;margin: 0;">Today's Date</p>
                <p class="heading-sub12" style="margin: 0;"><?php echo $today; ?></p>
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
                    <tr>
                        <td colspan="4" class="nav-bar">
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
                                <input type="Submit" value="Search" class="btn-primary-soft btn button-icon btn-search" style="padding-left: 25px;padding-right: 25px;padding-top: 10px;padding-bottom: 10px;">
                            </form>
                        </td>
                    </tr>
                </center>
            </td>
        </tr>

        <tbody>
        <?php
        // Number of records per page
        $records_per_page = 3;

        // Get the current page from the URL, default to 1
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

        // Calculate the starting record for the query
        $start_from = ($page - 1) * $records_per_page;

        // Determine if a date filter is applied, otherwise show all sessions
        $filter_date = isset($_POST['scheduledate']) && !empty($_POST['scheduledate']) ? $_POST['scheduledate'] : '';
       
        echo $filter_date;
        // Base Query
        $sqlmain = "SELECT * FROM schedule 
                    INNER JOIN doctor ON schedule.docid = doctor.docid 
                    WHERE schedule.scheduledate IS NOT NULL 
                    AND schedule.scheduletime IS NOT NULL 
                    AND schedule.scheduletime != ''";
        
        // Apply the filter if a date is provided
        if ($filter_date) {
            $sqlmain .= " AND schedule.scheduledate = ?"; // FIX: Changed WHERE to AND
        }
        
        // Add ordering and pagination
        $sqlmain .= " ORDER BY schedule.scheduledate DESC LIMIT ?, ?";
        
        // Prepare the statement
        $stmt = $database->prepare($sqlmain);
        
        // Bind parameters correctly
        if ($filter_date) {
            $stmt->bind_param("sii", $filter_date, $start_from, $records_per_page); // If filtering by date
        } else {
            $stmt->bind_param("ii", $start_from, $records_per_page); // If no date filter
        }
        
        // Execute and fetch results
        $stmt->execute();
        $schedulerow = $stmt->get_result();
        

        // Current datetime for comparison
        $current_datetime = date("Y-m-d H:i:s");

        // Separate sessions into future and past
        $future_sessions = [];
        $past_sessions = [];

        while ($row = $schedulerow->fetch_assoc()) {
            $scheduleid = $row["scheduleid"];
            $scheduledate = $row["scheduledate"];
            $scheduletime = $row["scheduletime"];

            // Combine scheduled date and time
            $schedule_datetime = $scheduledate . ' ' . $scheduletime;

            // Categorize the session based on the current datetime
            if (strtotime($schedule_datetime) >= strtotime($current_datetime)) {
                $future_sessions[] = $row; // Future session
            } else {
                $past_sessions[] = $row; // Past session
            }
        }

        // Function to display sessions
        function display_sessions($sessions, $database, $current_datetime, $is_future) {
            foreach ($sessions as $row) {
                $scheduleid = $row["scheduleid"];
                $title = $row["title"];
                $docname = $row["docname"];
                $scheduledate = $row["scheduledate"];
                $scheduletime = $row["scheduletime"];
                $session_duration = $row["session_duration"]; // Duration in minutes
                
                // Calculate the end time by adding session duration to the scheduled time
                $start_datetime = new DateTime($scheduledate . ' ' . $scheduletime);
                $end_datetime = clone $start_datetime;
                $end_datetime->modify('+' . $session_duration . ' minutes');
                $end_time = $end_datetime->format('Y-m-d H:i:s'); // end_time for comparison
                
                // Check how many patients have already booked
                $sql_schedule = $database->query("SELECT nop FROM schedule WHERE scheduleid = '$scheduleid'");
                $schedule_data = $sql_schedule->fetch_assoc();
                $max_patients = $schedule_data['nop'];
                
                // Check how many patients have already booked
                $patient_count = $database->query("SELECT COUNT(*) AS patient_count FROM appointment WHERE scheduleid = '$scheduleid'")->fetch_assoc();
                $patient_count_value = $patient_count['patient_count']; // Current number of booked patients
                
                // Get the current time
                $current_time = new DateTime($current_datetime);
                
                // Check for session passed first
                if ($current_time >= new DateTime($end_time)) {
                    // Session passed
                    $button_disabled = '<button class="cancel-booking-btn btn-primary-soft btn btn-session-passed" style="width:97%;" disabled>Session Passed</button>';
                } elseif ($current_time >= $start_datetime && $current_time <= $end_datetime) {
                    // Session is ongoing
                    $button_disabled = '<button class="cancel-booking-btn btn-primary-soft btn btn-session-ongoing" style="width:97%;" disabled>Session is Still Ongoing</button>';
                } else {
                    // Session is in the future
                    if ($patient_count_value >= $max_patients) {
                        // Session is full
                        $button_disabled = '<button class="cancel-booking-btn btn-primary-soft btn btn-session-full" style="width:97%;" disabled>Session Full</button>';
                    } else {
                        // Logic for showing "Book Now" button
                        $booking_check = $database->query("SELECT * FROM appointment WHERE pid = (SELECT pid FROM patient WHERE pemail = '" . $_SESSION["user"] . "') AND scheduleid = '$scheduleid'");
                        if ($booking_check->num_rows > 0) {
                            // Already booked
                            $button_disabled = '<button class="cancel-booking-btn btn-primary-soft btn btn-booked" style="width:97%; background-color: #2d6a4f; border-radius: 15px;" disabled>Already Booked</button>';
                        } else {
                            // Not booked yet
                            $button_disabled = '<a href="booking?id=' . $scheduleid . '"><button class="cancel-booking-btn btn-primary-soft btn btn-book-now" style="width:95%;">Book Now</button></a>';
                        }
                    }
                }
                
                // Display the session row with the modified div structure
                echo '
                <tr>
                    <td style="width: 25%; padding: 15px;">
                        <div class="dashboard-items search-items">
                            <div style="width:100%;">
                                <div class="h1-search">' . substr($title, 0, 21) . '</div><br>
                                <div class="h3-search">' . substr($docname, 0, 30) . '</div>
                                <div class="h4-search">' . $scheduledate . '<br>Starts: <b>@' . substr($scheduletime, 0, 5) . '</b> (24h)
                                <br>Ends: <b>@' . substr($end_time, 11, 5) . '</b> (24h)</div>
                                <br>
                                <div>' . $button_disabled . '</div>
                            </div>
                        </div>
                    </td>
                </tr>';
            }
        }

        // Display future sessions first
        if (!empty($future_sessions)) {
            display_sessions($future_sessions, $database, $current_datetime, true);
        }

        // Display past sessions
        if (!empty($past_sessions)) {
            display_sessions($past_sessions, $database, $current_datetime, false);
        }

        // If no sessions exist
        if (empty($future_sessions) && empty($past_sessions)) {
            echo '<tr>
                    <td colspan="4">
                        <center>
                            <img src="../img/notfound.svg" width="25%">
                            <p class="heading-main12">We couldn\'t find anything related to your keywords!</p>
                            <a class="non-style-link" href="schedule.php">
                                <button class="login-btn btn-primary-soft btn">Show all Sessions</button>
                            </a>
                        </center>
                    </td>
                </tr>';
                
        }


       
        ?>
    </tbody>
    
</table>
<?php
    // Pagination logic
    // Pagination logic: Adjusting query based on filter
    if ($filter_date) {
        $total_records_query = $database->query("SELECT COUNT(*) FROM schedule WHERE scheduledate = '$filter_date' AND scheduledate IS NOT NULL");
    } else {
        $total_records_query = $database->query("SELECT COUNT(*) FROM schedule WHERE scheduledate IS NOT NULL");
    }
    

    $total_records = $total_records_query->fetch_row()[0];
    $total_pages = ceil($total_records / $records_per_page);

    // Display pagination only if there are more records than the records per page
    if ($total_pages > 1) {
        echo '<div class="pagination">';
        if ($page > 1) {
            echo '<a href="?page=' . ($page - 1) . '">Previous</a>';
        }
        for ($i = 1; $i <= $total_pages; $i++) {
            echo '<a href="?page=' . $i . '" ' . ($i == $page ? 'class="active"' : '') . '>' . $i . '</a>';
        }
        if ($page < $total_pages) {
            echo '<a href="?page=' . ($page + 1) . '">Next</a>';
        }
        echo '</div>';
    }
    ?>
</div>

                            </div>
                        </center>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
