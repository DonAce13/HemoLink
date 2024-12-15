<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/animations.css">  
    <link rel="stylesheet" href="css/main.css">  
    <link rel="stylesheet" href="css/signup.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        
    <title>Sign Up</title>
    
</head>
<body>
<?php
session_start();

// SweetAlert welcome alert
if (isset($_SESSION["welcome_alert"])) {
    echo "
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>
        Swal.fire({
            title: 'Welcome, " . $_SESSION["username"] . "!',
            text: 'Your account has been successfully created.',
            icon: 'success',
            confirmButtonText: 'Continue'
        });
    </script>
    ";
    unset($_SESSION["welcome_alert"]); // Remove the session variable after showing the alert
}

// Unset all server-side variables
$_SESSION["user"] = "";
$_SESSION["usertype"] = "";

// Set the timezone
date_default_timezone_set('Asia/Kolkata');
$date = date('Y-m-d');
$_SESSION["date"] = $date;

$error = ""; // Error message variable

if ($_POST) {
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $address = $_POST['address'];
    $nic = $_POST['nic'];
    $dob = $_POST['dob'];
    $email = $_POST['email'] ?? ""; // Email field (assume you have it in the form)
    $newpassword = $_POST['password'] ?? ""; // Password field
    $cpassword = $_POST['confirm_password'] ?? ""; // Confirm password field
    $tele = $_POST['tele'] ?? ""; // Telephone field

// Server-side validation
if (!preg_match("/^[a-zA-Z]+$/", $fname) || !preg_match("/^[a-zA-Z]+$/", $lname)) {
    $error = '<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">First name and last name can only contain letters.</label>';
} elseif ($newpassword !== $cpassword) {
    $error = '<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Password Confirmation Error! Reconfirm Password</label>';
} else {
    // Database connection (replace with your database details)
    $database = new mysqli("192.168.56.1", "root", "", "HemoLink_Database");
    if ($database->connect_error) {
        die("Connection failed: " . $database->connect_error);
    }

    // Check if email already exists in the webuser table
    $stmt = $database->prepare("SELECT * FROM webuser WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $emailResult = $stmt->get_result();
    if ($emailResult->num_rows > 0) {
        $error = '<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Already have an account for this Email address.</label>';
    } else {
        // Check if mobile number already exists in the patient table
        $stmt = $database->prepare("SELECT * FROM patient WHERE ptel = ?");
        $stmt->bind_param("s", $tele);
        $stmt->execute();
        $teleResult = $stmt->get_result();
        if ($teleResult->num_rows > 0) {
            $error = '<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Mobile number is already registered.</label>';
        } else {
            // Insert new record into the patient table
            $stmt = $database->prepare("INSERT INTO patient (pemail, pname, ppassword, paddress, pnic, pdob, ptel) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $hashedPassword = password_hash($newpassword, PASSWORD_BCRYPT); // Hash the password for security
            $fullName = $fname . ' ' . $lname;
            $stmt->bind_param("sssssss", $email, $fullName, $hashedPassword, $address, $nic, $dob, $tele);
            $stmt->execute();

            // Insert new record into the webuser table
            $stmt = $database->prepare("INSERT INTO webuser (email, usertype) VALUES (?, ?)");
            $userType = 'p';
            $stmt->bind_param("ss", $email, $userType);
            $stmt->execute();

            // Set session variables and redirect to patient dashboard
            $_SESSION["user"] = $email;
            $_SESSION["usertype"] = "p";
            $_SESSION["username"] = $fname;

            header('Location: patient/index.php');
            exit();
        }
        $stmt->close();
    }
    $database->close();
}
}
?>


<center>
<div class="background-wrapper">
<div class="container">
    <table border="0">
        <tr>
            <td colspan="2">
                <p class="header-text">Let's Get Started</p>
                <p class="sub-text">Add Your Personal Details to Continue</p>
                <?php if ($error) echo $error; ?>
            </td>
        </tr>
        <!-- First Name And Last Name -->
        <tr>
            <form action="" method="POST">
            <td class="label-td" colspan="2">
                <label for="name" class="form-label">Name: </label>
            </td>
        </tr>
        <tr>
            <td class="label-td">
                <input type="text" name="fname" class="input-text" placeholder="First Name" required
                pattern="[A-Za-z]+" title="First name can only contain letters.">
            </td>
            <td class="label-td">
                <input type="text" name="lname" class="input-text" placeholder="Last Name" required
                pattern="[A-Za-z]+" title="Last name can only contain letters.">
            </td>
        </tr>

        <!-- Address -->
        <tr>
            <td class="label-td" colspan="2">
                <label for="address" class="form-label">Address: </label>
            </td>
        </tr>
        <tr>
            <td class="label-td" colspan="2">
                <input type="text" name="address" class="input-text" placeholder="Address" required 
                    pattern="^[0-9]+, [A-Za-z ]+$" title="Address should be in the format: #123, Street Name">
            </td>
        </tr>



        <!-- PhilHealth Question -->
        <tr>
            <td class="label-td" colspan="2">
                <label for="hasPhilhealth" class="form-label">Do you have a PhilHealth Number?</label>
            </td>
        </tr>
        <tr>
            <td class="label-td">
                <input type="radio" name="hasPhilhealth" value="yes" id="philhealth-yes" onclick="togglePhilhealthField()"> Yes
            </td>
            <td class="label-td">
                <input type="radio" name="hasPhilhealth" value="no" id="philhealth-no" onclick="togglePhilhealthField()"> No
            </td>
        </tr>

        <!-- PhilHealth Number input (only shown if "Yes" is selected) -->
        <!-- <tr id="philhealth-row" style="display: none;">
            <td class="label-td" colspan="2">
                <label for="nic" class="form-label">.PhilHealth No: </label>
            </td>
        </tr> -->
    
        <tr id="philhealth-row-input" style="display: none;">
            <td class="label-td" colspan="2">
                <input type="text" id="philhealth-input" name="nic" class="input-text" placeholder="Exclude the '-' when inputting"
                    minlength="12" maxlength="12" pattern="\d{12}" title="PhilHealth ID must be exactly 12 digits">
            </td>
        </tr>



        <!-- Date Of Birth -->           
        <tr>
            <td class="label-td" colspan="2">
                <label for="dob" class="form-label">Date of Birth: </label>
        <?php
        $today = date('Y-m-d');
        echo '<input type="date" name="dob" class="input-text" max="' . $today . '" required>';
        ?>
        
            </td>
        </tr>

        <!-- Add email, password, confirm password, and telephone fields -->
        <tr>
            <td class="label-td" colspan="2">
                <label for="email" class="form-label">Email: </label>
                <input type="email" name="email" class="input-text" placeholder="Email Address" required>
            </td>
        </tr>
        <tr>
            <td class="label-td">
                <label for="password" class="form-label">Password: </label>
                <input type="password" name="password" class="input-text" placeholder="Password" required>
            </td>
            <td class="label-td">
                <label for="confirm_password" class="form-label">Confirm Password: </label>
                <input type="password" name="confirm_password" class="input-text" placeholder="Confirm Password" required>
            </td>
        </tr>
        <tr>
        <td class="label-td" colspan="2">
            <label for="tele" class="form-label">Telephone: </label>
            <input type="tel" name="tele" class="input-text" placeholder="Telephone Number" required 
                pattern="^09\d{9}$" title="The number should start at 09 and be exactly 11 digits long.">
        </td>
    </tr>

        <tr>
            <td>
                <input type="reset" value="Reset" class="login-btn btn-primary-soft btn">
            </td>
            <td>
                <input type="submit" value="Next" class="login-btn btn-primary btn">
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <br>
                <label for="" class="sub-text" style="font-weight: 280;">Already have an account&#63; </label>
                <a href="login.php" class="hover-link1 non-style-link">Login</a>
                <br><br><br>
            </td>
        </tr>
            </form>
        </tr>
    </table>
</div>
</div>
</center>
<script>
    function togglePhilhealthField() {
    var hasPhilhealthYes = document.getElementById('philhealth-yes').checked;
    var philhealthRow = document.getElementById('philhealth-row-input');
    var philhealthInput = document.getElementById('philhealth-input');

    // Show or hide the input field based on the radio button selection
    if (hasPhilhealthYes) {
        philhealthRow.style.display = 'table-row'; // Show if "Yes" is selected
        philhealthInput.setAttribute('required', 'required'); // Add required attribute
    } else {
        philhealthRow.style.display = 'none'; // Hide if "No" is selected
        philhealthInput.removeAttribute('required'); // Remove required attribute
    }
}

</script>

</body>
</html>
