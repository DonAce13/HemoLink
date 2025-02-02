<?php
session_start();
include("connection.php");

// Initialize session variables
$_SESSION["user"] = "";
$_SESSION["usertype"] = "";
date_default_timezone_set('Asia/Manila');
$_SESSION["date"] = date('Y-m-d');

// Variable to store alert message
$alertMessage = '';

// Login logic
if ($_POST) {
    // Clear the logout message flag when attempting to log in
    unset($_SESSION['logout_shown']);
    
    $email = $_POST['useremail'];
    $password = $_POST['userpassword'];

    try {
        $result = $database->query("SELECT * FROM webuser WHERE email='$email'");
        if($result && $result->num_rows==1){
            $utype = $result->fetch_assoc()['usertype'];
            
            $loginSuccessful = false;
            
            switch($utype) {
                case 'p':
                    $checker = $database->query("SELECT * FROM patient WHERE pemail='$email' AND ppassword='$password'");
                    if ($checker && $checker->num_rows==1) {
                        $patient = $checker->fetch_assoc();
                        $_SESSION['user'] = $email;
                        $_SESSION['usertype'] = 'p';
                        $_SESSION['login_success'] = true;
                        $_SESSION['user_type'] = 'Patient';
                        $_SESSION['user_name'] = $patient['pname'];
                        header('Location: patient/index.php');
                        exit();
                    }
                    break;
                    
                case 'a':
                    $checker = $database->query("SELECT * FROM admin WHERE aemail='$email' AND apassword='$password'");
                    if ($checker && $checker->num_rows==1) {
                        $admin = $checker->fetch_assoc();
                        $_SESSION['user'] = $email;
                        $_SESSION['usertype'] = 'a';
                        $_SESSION['login_success'] = true;
                        $_SESSION['user_type'] = 'Administrator';
                        $_SESSION['user_name'] = $admin['aname'];
                        header('Location: admin/index.php');
                        exit();
                    }
                    break;
                    
                case 'd':
                    $checker = $database->query("SELECT * FROM doctor WHERE docemail='$email' AND docpassword='$password'");
                    if ($checker && $checker->num_rows==1) {
                        $doctor = $checker->fetch_assoc();
                        $_SESSION['user'] = $email;
                        $_SESSION['usertype'] = 'd';
                        $_SESSION['login_success'] = true;
                        $_SESSION['user_type'] = 'Doctor';
                        $_SESSION['user_name'] = $doctor['docname'];
                        header('Location: doctor/index.php');
                        exit();
                    }
                    break;
            }
            
            // If we get here, password was wrong
            $alertMessage = [
                'icon' => 'error',
                'title' => 'Login Failed',
                'text' => 'Incorrect password. Please try again.'
            ];
        } else {
            // User doesn't exist
            $alertMessage = [
                'icon' => 'error',
                'title' => 'User Not Found',
                'text' => 'No account found with this email address.'
            ];
        }
    } catch (Exception $e) {
        $alertMessage = [
            'icon' => 'error',
            'title' => 'System Error',
            'text' => 'A database error occurred. Please try again later.'
        ];
    }
}

// Handle logout success message
if (isset($_GET['logout'])) {
    // Remove the logout parameter from URL without refreshing the page
    echo "
    <script>
    if (window.history.replaceState) {
        window.history.replaceState({}, document.title, window.location.pathname);
    }
    </script>";
    
    // Only show the message if it hasn't been shown before
    if (!isset($_SESSION['logout_shown'])) {
        $alertMessage = [
            'icon' => 'success',
            'title' => 'Logged Out',
            'text' => 'You have been successfully logged out.'
        ];
        $_SESSION['logout_shown'] = true;
    }
}

// Handle welcome message for new registrations
if (isset($_SESSION["welcome_alert"])) {
    $alertMessage = [
        'icon' => 'success',
        'title' => 'Welcome to HemoLink!',
        'text' => 'Your account has been successfully created.'
    ];
    unset($_SESSION["welcome_alert"]);
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
    <title>Login - HemoLink</title>
    
    <!-- CSS and Scripts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/animations.css">
    <link rel="stylesheet" href="css/main.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
    <style>
        :root {
            --primary-color: #2d6a4f;
            --primary-light: #40916c;
            --primary-dark: #1b4332;
            --secondary-color: #95d5b2;
            --background-color: #f8f9fa;
            --text-color: #1b4332;
            --error-color: #dc3545;
            --success-color: #198754;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-image: url('../img/signup bg.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
        }

        /* Add green overlay */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(45, 106, 79, 0.85); /* Adjust opacity as needed */
            z-index: -1;
        }

        .container {
            padding: 2rem;
            background-color: white;
            border-radius: 50px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 800px;
            margin: 20px auto;
            position: relative;
            overflow: hidden;
        }

        .container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
        }

        .header-text {
            color: var(--primary-dark);
            font-size: 5rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 0.5rem;
        }

        .sub-text {
            color:rgb(56, 56, 56);
            font-size: 1.3rem;
            text-align: center;
            margin-bottom: 2rem;
        }

        .form-section {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            margin-bottom: 1.5rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .section-title {
            color: var(--primary-dark);
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-label {
            color: var(--text-color);
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(45, 106, 79, 0.25);
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: #2d6a4f;
            border: none;
        }

        .btn-primary:hover {
            background: #40916c;
            transform: translateY(-2px);
        }

        .error-message {
            color: var(--error-color);
            background: rgba(220, 53, 69, 0.1);
            padding: 1rem;
            border-radius: 10px;
            margin: 1rem 0;
            text-align: center;
        }

        /* Custom radio buttons */
        .radio-group {
            display: flex;
            gap: 2rem;
            margin: 1rem 0;
        }

        .radio-label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
        }

        /* Custom checkbox style */
        .checkbox-group {
            margin: 1rem 0;
        }

        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
        }
        .text-center {
            text-align: center;
            font-size: 1.5rem;
            color: #333;
            margin: 0;
        }

        .text-decoration-none {
            color: #2d6a4f; /* Same as your primary button color */
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .text-decoration-none:hover {
            color: #40916c; /* Lighter shade for hover */
            text-decoration: underline;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                padding: 1.5rem;
            }

            .header-text {
                font-size: 3rem;
            }

            .form-section {
                padding: 1.5rem;
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 1rem;
            }

            .header-text {
                font-size: 2rem;
            }

            .form-section {
                padding: 1rem;
            }

            .radio-group {
                flex-direction: column;
                gap: 1rem;
            }
            .text-center {
                text-align: center;
                font-size: 1rem;
                color: #333;
                margin: 0;
            }
            .sub-text {
                color:rgb(56, 56, 56);
                font-size: 1rem;
                text-align: center;
                margin-bottom: 2rem;
            }
        }
        
    </style>
</head>
<body>
    <?php if ($alertMessage): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: '<?php echo $alertMessage['icon']; ?>',
                title: '<?php echo $alertMessage['title']; ?>',
                text: '<?php echo $alertMessage['text']; ?>',
                confirmButtonColor: '#2d6a4f'
            });
        });
    </script>
    <?php endif; ?>
    
    <div class="container">
        <div class="row justify-content-center">
                    <div class="card-body p-5">
                        <h1 class="header-text mb-4">HemoLink</h1>
                        <p class="sub-text mb-4">Connecting you to better health</p>
                        <p class="sub-text mb-4">Login with your details to continue</p>
                        
                        <form action="" method="POST">
                            <div class="mb-4">
                                <label for="useremail" class="form-label">Email Address</label>
                                <input type="email" name="useremail" class="form-control" required>
                            </div>
                            
                            <div class="mb-4">
                                <label for="userpassword" class="form-label">Password</label>
                                <input type="password" name="userpassword" class="form-control" required>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100 mb-4">
                                Login
                            </button>
                            
                            <p class="text-center mb-0">
                                Don't have an account? 
                                <a href="signup.php" class="text-decoration-none">Sign Up</a>
                            </p>
                        </form>
                    </div>
        </div>
    </div>
</body>
</html>