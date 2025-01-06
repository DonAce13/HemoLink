<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/animations.css">  
    <link rel="stylesheet" href="../css/main.css">  
    <link rel="stylesheet" href="../css/admin.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <title>Sessions</title>
    <style>
        .popup {
            animation: transitionIn-Y-bottom 0.5s;
        }
        .sub-table {
            animation: transitionIn-Y-bottom 0.5s;
        }
        .hamburger {
            display: flex;
            flex-direction: column;
            justify-content: space-around;
            width: 30px;
            height: 25px;
            cursor: pointer;
            transition: transform 0.3s;
        }
        .bar {
            height: 4px;
            width: 100%;
            background-color: #333;
            border-radius: 2px;
            transition: all 0.3s ease-in-out;
        }
        .hamburger.active .bar:nth-child(1) {
            transform: rotate(45deg) translateY(10px);
        }
        .hamburger.active .bar:nth-child(2) {
            opacity: 0;
        }
        .hamburger.active .bar:nth-child(3) {
            transform: rotate(-45deg) translateY(-10px);
        }
        .menu {
            display: none; /* Initially hide the menu */
            transition: all 0.3s ease-in-out;
        }
        .menu.show {
            display: block; /* Show menu when toggled */
        }
    </style>
</head>
<body>
    <?php
    session_start();

    if (isset($_SESSION["user"])) {
        if (($_SESSION["user"]) == "" || $_SESSION['usertype'] != 'p') {
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

    date_default_timezone_set('Asia/Kolkata');
    $today = date('Y-m-d');
    ?>
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
                                    <a href="../logout.php"><input type="button" value="Log out" class="logout-btn btn-primary-soft btn"></a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-home">
                        <a href="index.php" class="non-style-link-menu"><div><p class="menu-text">Home</p></div></a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-doctor">
                        <a href="doctors.php" class="non-style-link-menu"><div><p class="menu-text">All Doctors</p></div></a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-session menu-active menu-icon-session-active">
                        <a href="schedule.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">Schedule</p></div></a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-appoinment">
                        <a href="appointment.php" class="non-style-link-menu"><div><p class="menu-text">My Bookings</p></div></a>
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
            <table border="0" width="100%" style=" border-spacing: 0;margin:0;padding:0;margin-top:25px; ">
                <tr >
                    <!-- <td width="13%" >
                    <a href="schedule.php" ><button  class="login-btn btn-primary-soft btn btn-icon-back"  style="padding-top:11px;padding-bottom:11px;margin-left:20px;width:125px"><font class="tn-in-text">Back</font></button></a>
                    </td> -->
                    <td >
                            <form action="schedule.php" method="post" class="header-search">

                                        <input type="search" name="search" class="input-text header-searchbar" placeholder="Search Doctor name or Email or Date (YYYY-MM-DD)" list="doctors" >&nbsp;&nbsp;
                                        
                                        <?php
                                            echo '<datalist id="doctors">';
                                            $list11 = $database->query("select DISTINCT * from  doctor;");
                                            $list12 = $database->query("select DISTINCT * from  schedule GROUP BY title;");
                                            

                                            


                                            for ($y=0;$y<$list11->num_rows;$y++){
                                                $row00=$list11->fetch_assoc();
                                                $d=$row00["docname"];
                                               
                                                echo "<option value='$d'><br/>";
                                               
                                            };


                                            for ($y=0;$y<$list12->num_rows;$y++){
                                                $row00=$list12->fetch_assoc();
                                                $d=$row00["title"];
                                               
                                                echo "<option value='$d'><br/>";
                                                                                         };

                                        echo ' </datalist>';
            ?>
                                        
                                
                                        <input type="Submit" value="Search" class="login-btn btn-primary btn" style="padding-left: 25px;padding-right: 25px;padding-top: 10px;padding-bottom: 10px;">
                                        </form>
                    </td>
                    <td width="15%">
                        <p style="font-size: 14px;color: rgb(119, 119, 119);padding: 0;margin: 0;text-align: right;">
                            Today's Date
                        </p>
                        <p class="heading-sub12" style="padding: 0;margin: 0;">
                            <?php 

                                
                                echo $today;

                                

                        ?>
                        </p>
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

    // Prepare the SQL query
    $sqlmain = "SELECT * FROM schedule INNER JOIN doctor ON schedule.docid = doctor.docid WHERE schedule.scheduleid = ? ORDER BY schedule.scheduledate DESC";
    $stmt = $database->prepare($sqlmain);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    $scheduleid = $row["scheduleid"];
    $title = $row["title"];
    $docname = $row["docname"];
    $docemail = $row["docemail"];
    $scheduledate = $row["scheduledate"];
    $scheduletime = $row["scheduletime"];

    // Get the appointment number
    $sql2 = "SELECT * FROM appointment WHERE scheduleid = $id";
    $result12 = $database->query($sql2);
    $apponum = ($result12->num_rows) + 1;

    // Display the booking form
    echo '
    <form action="booking-complete.php" method="post">
        <input type="hidden" name="scheduleid" value="' . $scheduleid . '" >
        <input type="hidden" name="apponum" value="' . $apponum . '" >

        <table>
            <tr>
                <td style="width: 100%;">
                    <div class="dashboard-items search-items">
                        <div style="width:100%">
                            <div class="h1-search" style="font-size:25px;">
                                Session Details
                            </div><br><br>
                            <div class="h3-search" style="font-size:18px;line-height:30px">
                                Doctor name:  &nbsp;&nbsp;<b>' . $docname . '</b><br>
                                Doctor Email:  &nbsp;&nbsp;<b>' . $docemail . '</b>
                            </div>
                            <div class="h3-search" style="font-size:18px;">
                                Session Title: ' . $title . '<br>
                                Session Scheduled Date: ' . $scheduledate . '<br>
                                Session Starts: ' . $scheduletime . '<br>
                            </div>
                            <br>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td style="width: 100%;">
                    <div class="dashboard-items search-items">
                        <div style="width:100%;padding-top: 15px;padding-bottom: 15px;">
                            <div class="h1-search" style="font-size:20px;line-height: 35px;margin-left:8px;text-align:center;">
                                Your Appointment Number
                            </div>
                            <center>
                                <div class="dashboard-icons" style="margin-left: 0px;width:90%;font-size:70px;font-weight:800;text-align:center;color:var(--btnnictext);background-color: var(--btnice)">' . $apponum . '</div>
                            </center>
                        </div><br>
                    </div>
                </td>
            </tr>
<tr>
    <td style="width: 100%;">
        <div class="dashboard-items search-items">
            <div style="width:100%">
                <div class="h1-search" style="font-size:25px;">
                    Book For:
                </div><br><br>
                <div class="h3-search" style="font-size:18px;line-height:30px">
                    <label for="is_self">Choose an option:</label><br>
                    <input type="radio" id="self" name="is_self" value="0" onclick="toggleOtherPatientForm()" checked> Myself<br>
                    <input type="radio" id="others" name="is_self" value="1" onclick="toggleOtherPatientForm()"> Someone Else<br><br>
                </div>
                <div id="otherPatientForm" style="display: none;">
                    <label for="other_patient_name">Other Patient Name:</label><br>
                    <input type="text" id="other_patient_name" name="other_patient_name"><br><br>

                    <label for="philhealth_id">PhilHealth ID:</label><br>
                    <input type="text" id="philhealth_id" name="philhealth_id" maxlength="12"><br><br>

                    <label for="age">Age:</label><br>
                    <input type="number" id="age" name="age" max="999"><br><br>

                    <label for="description">Description:</label><br>
                    <textarea id="description" name="description"></textarea><br><br>
                </div>
            </div>
        </div>
    </td>
</tr>
<tr>
    <td>
        <input type="submit" class="login-btn btn-primary btn btn-book" style="margin-left:10px;padding-left: 25px;padding-right: 25px;padding-top: 10px;padding-bottom: 10px;width:95%;text-align: center;" value="Book now" name="booknow" onclick="validateForm(event)">
    </td>
</tr>
</table>
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
        if (!isSelf) {
            const otherPatientName = document.getElementById("other_patient_name").value.trim();
            const philhealthId = document.getElementById("philhealth_id").value.trim();
            const age = document.getElementById("age").value.trim();
            const description = document.getElementById("description").value.trim();

            // Validate PhilHealth ID (must be exactly 12 digits)
            const philhealthPattern = /^\d{12}$/;
            if (!philhealthPattern.test(philhealthId)) {
                event.preventDefault();
                Swal.fire({
                    title: "Invalid PhilHealth ID",
                    text: "PhilHealth ID must be exactly 12 digits.",
                    icon: "error",
                    confirmButtonText: "OK"
                });
                return;
            }

            // Validate Age (must not exceed 3 digits)
            if (age < 1 || age > 100) {
                event.preventDefault();
                Swal.fire({
                    title: "Invalid Age",
                    text: "Age must be a number between 1 and 100.",
                    icon: "error",
                    confirmButtonText: "OK"
                });
                return;
            }

            // Check if all fields are filled
            if (!otherPatientName || !philhealthId || !age || !description) {
                event.preventDefault();
                Swal.fire({
                    title: "All Information Needs to be Filled",
                    text: "Please fill in all the required fields before proceeding.",
                    icon: "warning",
                    confirmButtonText: "OK"
                });
                return;
            }
        }
    }
</script>

    ';
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

</body>
</html>