<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/animations.css">  
    <link rel="stylesheet" href="css/main.css">  
    <link rel="stylesheet" href="css/login.css">
        
    <title>Login</title>
</head>
<body>
    <?php
    $error = '';
    // Start session
    session_start();

    $_SESSION["user"] = "";
    $_SESSION["usertype"] = "";

    // Set the new timezone
    date_default_timezone_set('Asia/Kolkata');
    $date = date('Y-m-d');
    $_SESSION["date"] = $date;

    // Import database
    include("connection.php");

    if ($_POST) {
        $email = $_POST['useremail'];
        $password = $_POST['userpassword'];
        $error = '<label for="promter" class="form-label"></label>';
    
        // Prepared statement to check if email exists in webuser table
        $stmt = $database->prepare("SELECT * FROM webuser WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            $utype = $user['usertype'];

            switch ($utype) {
                case 'p': // Patient Login
                    $stmt = $database->prepare("SELECT * FROM patient WHERE pemail = ?");
                    break;
                case 'd': // Doctor Login
                    $stmt = $database->prepare("SELECT * FROM doctor WHERE docemail = ?");
                    break;
                case 'a': // Admin Login
                    $stmt = $database->prepare("SELECT * FROM admin WHERE aemail = ?");
                    break;
                default:
                    $error = '<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Invalid user type.</label>';
                    break;
            }

            $stmt->bind_param("s", $email);
            $stmt->execute();
            $userResult = $stmt->get_result();

            if ($userResult->num_rows == 1) {
                $userDetails = $userResult->fetch_assoc();
                // Retrieve the correct password field for the user type
                $plainPassword = ($utype == 'a') ? $userDetails['apassword'] : 
                                 ($utype == 'p' ? $userDetails['ppassword'] : $userDetails['docpassword']);

                // Direct comparison of passwords (plain text)
                if ($password === $plainPassword) {
                    $_SESSION['user'] = $email;
                    $_SESSION['usertype'] = $utype;

                    // Redirect to the appropriate dashboard
                    if ($utype == 'p') {
                        header('Location: patient/index.php');
                    } elseif ($utype == 'd') {
                        header('Location: doctor/index.php');
                    } else {
                        header('Location: admin/index.php');
                    }
                    exit();
                } else {
                    $error = '<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Wrong credentials: Invalid password.</label>';
                }
            } else {
                $error = '<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">No record found for this email.</label>';
            }
        } else {
            $error = '<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">We can\'t find any account for this email.</label>';
        }
    }
    ?>
    <center>
    <div class="container">
        <table border="0" style="margin: 0;padding: 0;width: 60%;">
            <tr>
                <td>
                    <p class="header-text">Welcome Back!</p>
                </td>
            </tr>
        <div class="form-body">
            <tr>
                <td>
                    <p class="sub-text">Login with your details to continue</p>
                </td>
            </tr>
            <tr>
                <form action="" method="POST" >
                <td class="label-td">
                    <label for="useremail" class="form-label">Email: </label>
                </td>
            </tr>
            <tr>
                <td class="label-td">
                    <input type="email" name="useremail" class="input-text" placeholder="Email Address" required>
                </td>
            </tr>
            <tr>
                <td class="label-td">
                    <label for="userpassword" class="form-label">Password: </label>
                </td>
            </tr>

            <tr>
                <td class="label-td">
                    <input type="Password" name="userpassword" class="input-text" placeholder="Password" required>
                </td>
            </tr>

            <tr>
                <td><br>
                <?php echo $error ?>
                </td>
            </tr>

            <tr>
                <td>
                    <input type="submit" value="Login" class="login-btn btn-primary btn">
                </td>
            </tr>
        </div>
            <tr>
                <td>
                    <br>
                    <label for="" class="sub-text" style="font-weight: 280;">Don't have an account&#63; </label>
                    <a href="signup.php" class="hover-link1 non-style-link">Sign Up</a>
                    <br><br><br>
                </td>
            </tr>
        </form>
        </table>
    </div>
    </center>
</body>
</html>
