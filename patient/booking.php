<?php

    //learn from w3schools.com

    session_start();

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
    $today = date("d/m/Y");


    if($_POST){
        if(isset($_POST["booknow"])){
            $scheduleid = $_POST["scheduleid"];
            $date = $_POST["date"];
            $scheduletime = $_POST["scheduletime"];
            $is_self = $_POST["is_self"];
            $other_patient_name = $_POST["other_patient_name"];
            $description = $_POST["description"];
            $philhealth_id = $_POST["philhealth_id"];
            $age = $_POST["age"];
            $status = 'pending';
            $is_confirmed = 0;

            // Start a transaction to ensure data integrity
            $database->begin_transaction();

            try {
                // Check patient's total attempts for this session
                $patient_attempt_check = "SELECT COUNT(*) as total_attempts 
                    FROM appointment 
                    WHERE pid = ? AND scheduleid = ?";
                $stmt_attempt_check = $database->prepare($patient_attempt_check);
                $stmt_attempt_check->bind_param("ii", $userid, $scheduleid);
                $stmt_attempt_check->execute();
                $attempt_result = $stmt_attempt_check->get_result();
                $attempt_row = $attempt_result->fetch_assoc();

                // Check if patient has reached max attempts for this session
                if ($attempt_row['total_attempts'] >= 5) {
                    $database->rollback();
                    header("location: appointment.php?action=booking-failed&titleget=none&sweetalert=error&reason=Max booking attempts reached for this session");
                    exit;
                }

                // Check session availability for approved bookings
                $slots_query = "SELECT available_slots FROM schedule WHERE scheduleid = ?";
                $slots_stmt = $database->prepare($slots_query);
                $slots_stmt->bind_param("i", $scheduleid);
                $slots_stmt->execute();
                $slots_result = $slots_stmt->get_result();
                $slots_row = $slots_result->fetch_assoc();

                // Check if session is full
                if ($slots_row['available_slots'] <= 0) {
                    $database->rollback();
                    header("location: appointment.php?action=booking-failed&titleget=none&sweetalert=error&reason=Session Full");
                    exit;
                }

                // Generate unique appointment number for the session
                $apponum_query = "SELECT COALESCE(MAX(apponum), 0) + 1 as next_apponum 
                                  FROM appointment 
                                  WHERE scheduleid = ?";
                $stmt_apponum = $database->prepare($apponum_query);
                $stmt_apponum->bind_param("i", $scheduleid);
                $stmt_apponum->execute();
                $apponum_result = $stmt_apponum->get_result();
                $apponum_row = $apponum_result->fetch_assoc();
                $next_apponum = $apponum_row['next_apponum'];

                // Prepare and execute the insert statement
                $sql2 = "INSERT INTO appointment (pid, apponum, scheduleid, appodate, scheduletime, is_self, other_patient_name, description, philhealth_id, age, status, is_confirmed) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $database->prepare($sql2);
                $stmt->bind_param("iisssissisis", $userid, $next_apponum, $scheduleid, $date, $scheduletime, $is_self, $other_patient_name, $description, $philhealth_id, $age, $status, $is_confirmed);

                if ($stmt->execute()) {
                    // Commit the transaction
                    $database->commit();
                    header("location: appointment.php?action=booking-added&titleget=none&sweetalert=success");
                } else {
                    // Rollback the transaction on failure
                    $database->rollback();
                    header("location: appointment.php?action=booking-failed&titleget=none&sweetalert=error");
                }
            } catch (Exception $e) {
                // Rollback the transaction on any exception
                $database->rollback();
                header("location: appointment.php?action=booking-failed&titleget=none&sweetalert=error&reason=".urlencode($e->getMessage()));
            }
            exit;
        }
    }
 ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
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

        /* Responsive Design Enhancements */
        @media screen and (max-width: 768px) {
            .dash-body {
                padding: 10px;
            }
            .dashboard-items {
                flex-direction: column;
            }
            .dashboard-items > div {
                width: 100% !important;
                margin-bottom: 15px;
            }
            table {
                width: 100% !important;
            }
            .h1-search, .h3-search {
                font-size: 16px !important;
            }
            .dashboard-icons {
                font-size: 40px !important;
            }
            .login-btn {
                width: 100% !important;
                margin-left: 0 !important;
            }
            #otherPatientForm input, 
            #otherPatientForm textarea {
                width: 100%;
                box-sizing: border-box;
            }
        }

        /* Flexbox for better responsiveness */
        .booking-container {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }
        .booking-section {
            flex: 1;
            min-width: 300px;
        }

        /* Improved form styling */
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input, 
        .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        /* Enhanced radio button styling */
        .radio-group {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
        }
        .radio-group input {
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Hamburger Icon -->
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
                                    <p class="profile-title"><?php echo substr($username, 0, 13) ?>..</p>
                                    <p class="profile-subtitle"><?php echo substr($useremail, 0, 22) ?></p>
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
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-home">
                        <a href="index.php" class="non-style-link-menu"><div><p class="menu-text">Home</p></div></a>
                    </td>
                </tr>

                <tr class="menu-row">
                    <td class="menu-btn menu-icon-session menu-active menu-icon-session-active">
                        <a href="schedule.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">My Bookings</p></div></a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-settings">
                        <a href="settings.php" class="non-style-link-menu"><div><p class="menu-text">Settings</p></div></a>
                    </td>
                </tr>
            </table>
        </div>

        <!-- JavaScript -->
        <script>
            const hamburger = document.getElementById('hamburger');
            const menu = document.getElementById('menu');
            
            hamburger.addEventListener('click', () => {
                hamburger.classList.toggle('active'); // Transform hamburger to X
                menu.classList.toggle('show'); // Show/hide menu
            });
        </script>
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
                                <td width="10%">
                                    <button  class="btn-label"  style="display: flex;justify-content: center;align-items: center;"><img src="../img/calendar.svg" width="100%"></button>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4" style="padding-top:10px;width: 100%;" >
                                    <!-- <p class="heading-main12" style="margin-left: 45px;font-size:18px;color:rgb(49, 49, 49);font-weight:400;">Schedulens / Booking / <b>Review Booking</b></p> -->
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4">
                                    <center>
                                        <div class="abc scroll">
                                            <table width="100%" class="sub-table scrolldown" border="0" style="padding: 50px;border:none">
                                                <tbody>
                                                    <?php
                                                    // Ensure the 'id' parameter is present in the URL
                                                    if (isset($_GET["id"])) {
                                                        $id = $_GET["id"];

                                                        // Prepare the SQL query to get sessions ordered by day of the week
                                                        $sqlmain = "SELECT *, DAYNAME(scheduledate) as day_name FROM schedule INNER JOIN doctor ON schedule.docid = doctor.docid WHERE schedule.scheduleid = ? ORDER BY DAYOFWEEK(scheduledate), scheduletime";
                                                        $stmt = $database->prepare($sqlmain);
                                                        $stmt->bind_param("i", $id);
                                                        $stmt->execute();
                                                        $result = $stmt->get_result();

                                                        // Initialize an array to store sessions by day
                                                        $sessions_by_day = [];

                                                        // Fetch all sessions and group them by day name
                                                        while ($row = $result->fetch_assoc()) {
                                                            $day_name = $row['day_name'];
                                                            $sessions_by_day[$day_name][] = $row;
                                                        }

                                                        // Display sessions grouped by day
                                                        foreach ($sessions_by_day as $day => $sessions) {
                                                            echo "<h3>$day</h3>";
                                                            foreach ($sessions as $session) {
                                                                $scheduleid = $session["scheduleid"];
                                                                $title = $session["title"];
                                                                $docname = $session["docname"];
                                                                $docemail = $session["docemail"];
                                                                $scheduledate = $session["scheduledate"];
                                                                $scheduletime = $session["scheduletime"];

                                                                // Get the appointment number, counting only approved appointments for this specific session
                                                                $sql2 = "SELECT * FROM appointment WHERE scheduleid = ? AND status = 'Approved'";
                                                                $stmt2 = $database->prepare($sql2);
                                                                $stmt2->bind_param("i", $id);
                                                                $stmt2->execute();
                                                                $result12 = $stmt2->get_result();
                                                                $current_bookings = $result12->num_rows;

                                                                // Check if max bookings (5) have been reached for this specific session
                                                                if ($current_bookings >= 5) {
                                                                    echo '<div class="alert alert-danger">
                                                                            <p>Maximum number of bookings (5) has been reached for this session.</p>
                                                                          </div>';
                                                                    exit; // Stop further execution
                                                                }

                                                                // Generate unique appointment number for the session
                                                                $apponum_query = "SELECT COALESCE(MAX(apponum), 0) + 1 as next_apponum 
                                                                              FROM appointment 
                                                                              WHERE scheduleid = ?";
                                                                $stmt_apponum = $database->prepare($apponum_query);
                                                                $stmt_apponum->bind_param("i", $scheduleid);
                                                                $stmt_apponum->execute();
                                                                $apponum_result = $stmt_apponum->get_result();
                                                                $apponum_row = $apponum_result->fetch_assoc();
                                                                $next_apponum = $apponum_row['next_apponum'];

                                                                // Display the booking form
                                                                echo '
                                                                <div class="booking-container">
                                                                    <div class="booking-section">
                                                                        <div class="dashboard-items search-items">
                                                                            <div style="width:100%">
                                                                                <div class="h1-search" style="font-size:25px;">
                                                                                    Session Details
                                                                                </div><br>
                                                                                <div class="h3-search" style="font-size:18px;line-height:30px">
                                                                                    Doctor name:  &nbsp;&nbsp;<b>' . $docname . '</b><br>
                                                                                    Doctor Email:  &nbsp;&nbsp;<b>' . $docemail . '</b>
                                                                                </div>
                                                                                <div class="h3-search" style="font-size:18px;">
                                                                                    Session Title: ' . $title . '<br>
                                                                                    Session Scheduled Date: ' . $scheduledate . '<br>
                                                                                    Session Starts: ' . $scheduletime . '<br>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="booking-section">
                                                                        <div class="dashboard-items search-items">
                                                                            <div style="width:100%">
                                                                                <div class="h1-search" style="font-size:20px;line-height: 35px;margin-left:8px;text-align:center;">
                                                                                    Your Appointment Number
                                                                                </div>
                                                                                <center>
                                                                                    <div class="dashboard-icons" style="margin-left: 0px;width:90%;font-size:70px;font-weight:800;text-align:center;color:var(--btnnictext);background-color: var(--btnice)">' . $next_apponum . '</div>
                                                                                </center>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <form action="" method="post">
                                                                    <input type="hidden" name="scheduleid" value="' . $scheduleid . '" >
                                                                    <input type="hidden" name="apponum" value="' . $next_apponum . '" >
                                                                    <input type="hidden" name="date" value="' . $scheduledate . '" >
                                                                    <input type="hidden" name="scheduletime" value="' . $scheduletime . '" >
                                                                    <div class="form-group">
                                                                    <br>
                                                                    <br>
                                                                        <label for="is_self">Choose an option:</label><br>
                                                                        <div class="radio-group">
                                                                            <input type="radio" id="self" name="is_self" value="0" onclick="toggleOtherPatientForm()" checked> Myself<br>
                                                                            <input type="radio" id="others" name="is_self" value="1" onclick="toggleOtherPatientForm()"> Someone Else<br><br>
                                                                        </div>
                                                                    </div>
                                                                    <div id="otherPatientForm" style="display: none;">
                                                                        <div class="form-group">
                                                                            <label for="other_patient_name">Other Patient Name:</label><br>
                                                                            <input type="text" id="other_patient_name" name="other_patient_name"><br><br>

                                                                            <label for="philhealth_category">PhilHealth Category:</label><br>
                                                                            <select id="philhealth_category" name="philhealth_id" class="input-text" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                                                                                <option value="">Select PhilHealth Category</option>
                                                                                <option value="employed">Employed</option>
                                                                                <option value="self_employed">Self-Employed</option>
                                                                                <option value="ofw">Overseas Filipino Worker (OFW)</option>
                                                                                <option value="senior_citizen">Senior Citizen</option>
                                                                                <option value="indigent">Indigent</option>
                                                                                <option value="pwd">Person with Disability</option>
                                                                                <option value="non_paying">Non-Paying</option>
                                                                            </select><br><br>

                                                                            <label for="age">Age:</label><br>
                                                                            <input type="number" id="age" name="age" max="999"><br><br>

                                                                            <label for="description">Description:</label><br>
                                                                            <textarea id="description" name="description"></textarea><br><br>
                                                                        </div>
                                                                    </div>
                                                                    <input type="submit" class="login-btn btn-primary btn btn-book" style="margin-left:10px;padding-left: 25px;padding-right: 25px;padding-top: 10px;padding-bottom: 10px;width:95%;text-align: center;" value="Book now" name="booknow" onclick="validateForm(event)">
                                                                </form>

                                                                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

                                                                <script>
                                                                    function toggleOtherPatientForm() {
                                                                        const isSelf = document.getElementById("self").checked;
                                                                        const otherPatientForm = document.getElementById("otherPatientForm");
                                                                        if (isSelf) {
                                                                            otherPatientForm.style.display = "none";
                                                                        } else {
                                                                            otherPatientForm.style.display = "block";
                                                                        }
                                                                    }

                                                                    function validateForm(event) {
                                                                        const isSelf = document.getElementById("self").checked;
                                                                        
                                                                        // If booking for myself, allow submission
                                                                        if (isSelf) {
                                                                            return true;
                                                                        }
                                                                        
                                                                        // If booking for someone else, check all fields
                                                                        const otherPatientName = document.getElementById("other_patient_name").value.trim();
                                                                        const philhealthId = document.getElementById("philhealth_category").value.trim();
                                                                        const age = document.getElementById("age").value.trim();
                                                                        const description = document.getElementById("description").value.trim();

                                                                        // Check if any field is empty
                                                                        if (!otherPatientName || !philhealthId || !age || !description) {
                                                                            event.preventDefault();
                                                                            Swal.fire({
                                                                                title: "Incomplete Information",
                                                                                text: "When booking for someone else, ALL fields must be filled properly.",
                                                                                icon: "warning",
                                                                                confirmButtonText: "OK"
                                                                            });
                                                                            return false;
                                                                        }
                                                                        
                                                                        return true;
                                                                    }
                                                                </script>

                                                                ';
                                                            }
                                                        }
                                                    } else {
                                                        echo "Invalid appointment ID.";
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
            </div>
        </div>
    </div>
</body>
</html>

<?php
    // Add error handling for session full and other scenarios
    $error = isset($_GET['error']) ? $_GET['error'] : null;
    $sweetalert = isset($_GET['sweetalert']) ? $_GET['sweetalert'] : null;
    $error_messages = [
        'session_full' => 'Session Full',
        'incomplete_info' => 'Please fill in all required fields for booking someone else.',
        'invalid_philhealth' => 'PhilHealth ID must be exactly 12 digits.',
        'invalid_age' => 'Age must be between 1 and 100.',
        'database_error' => 'An error occurred while processing your booking.'
    ];

    if ($error && isset($error_messages[$error])) {
        echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'error',
                title: 'Booking Error',
                text: '".$error_messages[$error]."',
                confirmButtonText: 'OK'
            });
        });
        </script>";
    } elseif ($sweetalert == 'success') {
        echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: 'Booking Successful',
                text: 'Your booking has been successfully added.',
                confirmButtonText: 'OK'
            });
        });
        </script>";
    } elseif ($sweetalert == 'error') {
        echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'error',
                title: 'Booking Error',
                text: 'An error occurred while processing your booking.',
                confirmButtonText: 'OK'
            });
        });
        </script>";
    }
?>