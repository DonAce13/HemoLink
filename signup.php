<?php
<<<<<<< HEAD
// Include the Twilio SDK autoload file
require 'vendor/autoload.php';

// Use the Twilio namespace
use Twilio\Rest\Client;

// Start the session
session_start();

date_default_timezone_set('Asia/Manila');
$date = date('Y-m-d');
$_SESSION["date"] = $date;

if ($_POST) {
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    
    // Clean and format the street number
    $street_number = trim($_POST['street_number']);
    $street_number = ltrim($street_number, '#');
    $street_number = '#' . $street_number;
    
    // Format the address
    $address = sprintf("%s %s Avenue, Mabayuan, Olongapo City", 
        $street_number, 
        trim($_POST['street_name'])
    );
    
    $nic = $_POST['nic'];
    $dob = $_POST['dob'];
    $email = $_POST['email'] ?? "";
    $newpassword = $_POST['password'] ?? "";
    $cpassword = $_POST['confirm_password'] ?? "";
    $tele = $_POST['tele'] ?? "";

    // Initialize SweetAlert script
    $sweet_alert = "";

    // Validation checks with SweetAlert
    if (!preg_match("/^[a-zA-Z ]+$/", $fname) || !preg_match("/^[a-zA-Z ]+$/", $lname)) {
        $sweet_alert = "
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Invalid Name Format',
                    text: 'First name and last name can only contain letters and spaces.',
                    icon: 'error',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#2d6a4f'
                });
            });
        </script>";
    } elseif ($newpassword !== $cpassword) {
        $sweet_alert = "
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Password Mismatch',
                    text: 'Password confirmation does not match! Please try again.',
                    icon: 'error',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#2d6a4f'
                });
            });
        </script>";
    } else {
        try {
            $database = new mysqli("localhost", "root", "", "SQL_Database_Hemolink");

            if ($database->connect_error) {
                throw new Exception("Connection failed: " . $database->connect_error);
            }

            $database->begin_transaction();

            try {
                // Check if email exists
                $stmt = $database->prepare("SELECT email FROM webuser WHERE email = ?");
                $stmt->bind_param("s", $email);
                $stmt->execute();
                if ($stmt->get_result()->num_rows > 0) {
                    throw new Exception("This email address is already registered");
                }

                // Check if phone exists
                $stmt = $database->prepare("SELECT ptel FROM patient WHERE ptel = ?");
                $stmt->bind_param("s", $tele);
                $stmt->execute();
                if ($stmt->get_result()->num_rows > 0) {
                    throw new Exception("This phone number is already registered");
                }

                // Generate OTP
                $otp = rand(100000, 999999);

                // Insert patient data with OTP
                $stmt = $database->prepare("INSERT INTO patient (pemail, pname, ppassword, paddress, pnic, pdob, ptel, otp) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $fullName = $fname . ' ' . $lname;
                $stmt->bind_param("ssssssss", $email, $fullName, $newpassword, $address, $nic, $dob, $tele, $otp);
                
                if (!$stmt->execute()) {
                    throw new Exception("Error registering patient data");
                }

                // Insert webuser data
                $stmt = $database->prepare("INSERT INTO webuser (email, usertype) VALUES (?, ?)");
                $userType = 'p';
                $stmt->bind_param("ss", $email, $userType);
                
                if (!$stmt->execute()) {
                    throw new Exception("Error creating user account");
                }

                // Send OTP via Twilio
                $account_sid = 'ACbf56b173b4f3c4b0cef7f2497d30d6a5';
                $auth_token = 'd95e0606e4591b5f251cba67d54f7628';
                $twilio_number = '14055432932';

                $client = new Client($account_sid, $auth_token);

                $client->messages->create(
                    $tele,
                    [
                        'from' => $twilio_number,
                        'body' => "Your OTP is: $otp"
                    ]
                );

                $database->commit();

                // Set session variables
                $_SESSION["user"] = $email;
                $_SESSION["usertype"] = "p";
                $_SESSION["username"] = $fname;
                $_SESSION["sweet_alert"] = true;
                $_SESSION["login_success"] = true;
                $_SESSION["user_type"] = "Patient";
                $_SESSION["user_name"] = $fname;

                header("Location: verify_otp.php");
                exit();

            } catch (Exception $e) {
                $database->rollback();
                $sweet_alert = "
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            title: 'Registration Error',
                            text: '" . addslashes($e->getMessage()) . "',
                            icon: 'error',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#2d6a4f'
                        });
                    });
                </script>";
            }

            $database->close();

        } catch (Exception $e) {
            $sweet_alert = "
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        title: 'Connection Error',
                        text: 'Unable to connect to database. Please try again later.',
                        icon: 'error',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#2d6a4f'
                    });
                });
            </script>";
        }
    }
    
    // Output the SweetAlert if there's an error
    if (!empty($sweet_alert)) {
        echo $sweet_alert;
    }
}

=======
session_start(); // This must be at the top
ob_start(); // Optional: Buffer output to prevent header errors
>>>>>>> d74e3b600e093ccdce5c5f1c1cf13eb569fa1cb8
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
    <link rel="stylesheet" href="css/animations.css">  
    <link rel="stylesheet" href="css/main.css">  
    <link rel="stylesheet" href="css/signup.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Sign Up - HemoLink</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

        body {
            line-height: 1.6;
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
            padding: 20px;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('../img/bg05.png') center/cover no-repeat;
            opacity: 0.05;
            z-index: -1;
        }

        .background-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            border-radius: 50px;
        }

        .container {
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 800px;
            margin: 20px auto;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .header-text {
            color: #1b4332;
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 15px;
            text-align: center;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }

        .sub-text {
            color: #6c757d;
            font-size: 1.1rem;
            margin-bottom: 30px;
            text-align: center;
        }

        .form-label {
            color: #1b4332;
            font-weight: 600;
            margin-bottom: 8px;
            display: block;
            font-size: 1rem;
        }

        .input-text {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
            margin-bottom: 15px;
        }

        .input-text:focus {
            border-color: #2d6a4f;
            outline: none;
            box-shadow: 0 0 0 3px rgba(45, 106, 79, 0.1);
        }

        .radio-group {
            display: flex;
            gap: 20px;
            margin: 10px 0;
        }

        .radio-label {
            display: flex;
            align-items: center;
            gap: 5px;
            cursor: pointer;
        }

        .btn {
            padding: 12px 25px;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            text-align: center;
            margin: 5px 0;
        }

        .btn-primary {
            background: linear-gradient(135deg, #2d6a4f 0%, #40916c 100%);
            color: white;
            border: none;
        }

        .btn-primary-soft {
            background: #e9ecef;
            color: #2d6a4f;
            border: 2px solid #2d6a4f;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .checkbox-group {
            margin: 15px 0;
        }

        .checkbox-group label {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
            cursor: pointer;
        }

        .checkbox-group a {
            color: #2d6a4f;
            text-decoration: none;
            font-weight: 600;
        }

        .error-label {
            color: #dc3545;
            background: rgba(220, 53, 69, 0.1);
            padding: 10px;
            border-radius: 10px;
            margin: 10px 0;
            text-align: center;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                padding: 25px;
                margin: 10px;
            }

            .header-text {
                font-size: 2rem;
            }

            .input-text {
                padding: 10px;
            }

            table {
                display: block;
            }

            tr {
                display: block;
                margin-bottom: 15px;
            }

            td {
                display: block;
                width: 100%;
            }

            .btn {
                margin: 10px 0;
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 20px;
            }

            .header-text {
                font-size: 1.8rem;
            }

            .sub-text {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>

<center>
    <div class">
        <div class="container">
            <p class="header-text">Let's Get Started</p>
            <p class="sub-text">Add Your Personal Details to Continue</p>
            

            <form action="" method="POST" onsubmit="prepareForm()">
                <table>
                    <!-- Name Fields -->
                    <tr>
                        <td class="label-td" colspan="2">
                            <label for="name" class="form-label">Name:</label>
                        </td>
                    </tr>
                    <tr>
                        <td class="label-td">
                            <input type="text" name="fname" class="input-text" placeholder="First Name" required pattern="[A-Za-z ]+" title="First name can only contain letters and spaces.">
                        </td>
                        <td class="label-td">
                            <input type="text" name="lname" class="input-text" placeholder="Last Name" required pattern="[A-Za-z ]+" title="Last name can only contain letters and spaces.">
                        </td>
                    </tr>


                    <!-- Address Field -->
                    <tr>
                        <td class="label-td" colspan="2">
                            <label for="address" class="form-label">Address:</label>
                        </td>
                    </tr>
                    <tr>
                        <td class="label-td" colspan="2">
                            <!-- Street Number Input -->
                            <input type="text" 
                                name="street_number" 
                                id="street_number" 
                                class="input-text" 
                                placeholder="Enter Street Number (e.g., #12a)" 
                                pattern="^#?\d+[a-zA-Z]?$" 
                                title="Enter a number optionally followed by a letter (e.g., 12a or #12a)"
                                required>
                        </td>
                    </tr>
                    <tr>
                        <td class="label-td" colspan="2">
                            <!-- Street Name Dropdown -->
                            <select name="street_name" id="street_name" class="input-text" required>
                                <option value="" disabled selected>Select Street Name</option>
                                <option value="Amagis">Amagis Avenue</option>
                                <option value="Calimbas">Calimbas Street </option>
                                <option value="De Aro">De Aro Street</option>
                                <option value="Grace Pauline">Grace Pauline Street</option>
                                <option value="Labrador">Labrador Street</option>
                                <option value="Leyva">Leyva Street</option>
                                <option value="Mercurio">Mercurio Street</option>
                                <option value="Napalan">Napalan Street</option>
                                <option value="Nieves">Nieves Street</option>
                                <option value="Otero">Otero Avenue</option>
                                <option value="Rodriquez">Rodriquez Street</option>
                                <option value="Rosete">Rosete Street</option>
                            </select>
                        </td>
                    </tr>

                    <!-- PhilHealth Field -->
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

                    <!-- PhilHealth ID Input (Only Visible When "Yes" is Selected) -->
                    <tr id="philhealth-row-input" style="display: none;">
                        <td class="label-td" colspan="2">
                            <input type="text" id="philhealth-input" name="nic" class="input-text" placeholder="PhilHealth ID (12 digits)" minlength="12" maxlength="12" pattern="\d{12}" title="PhilHealth ID must be exactly 12 digits">
                        </td>
                    </tr>

                    <!-- Date of Birth Field -->
                    <tr>
                        <td class="label-td" colspan="2">
                            <label for="dob" class="form-label">Date of Birth:</label>
                        </td>
                    </tr>
                    <tr>
                        <td class="label-td" colspan="2">
                            <?php
                            // Get today's date
                            $today = date('Y-m-d');
                            // Calculate the date 18 years ago
                            $minDate = date('Y-m-d', strtotime('-18 years', strtotime($today)));
                            // Output the date input with max and min attributes
                            echo '<input type="date" name="dob" class="input-text" max="' . $minDate . '" required>';
                            ?>
                        </td>
                    </tr>

                    <!-- Email Field -->
                    <tr>
                        <td class="label-td" colspan="2">
                            <label for="email" class="form-label">Email:</label>
                            <input type="email" name="email" class="input-text" placeholder="Email Address" required>
                        </td>
                    </tr>

                    <!-- Password Fields -->
                    <tr>
                        <td class="label-td">
                            <label for="password" class="form-label">Password:</label>
                            <input type="password" name="password" class="input-text" placeholder="Password" required>
                        </td>
                        <td class="label-td">
                            <label for="confirm_password" class="form-label">Confirm Password:</label>
                            <input type="password" name="confirm_password" class="input-text" placeholder="Confirm Password" required>
                        </td>
                    </tr>

                   <!-- Telephone Field with Send OTP Button -->
                    <tr>
                        <td class="label-td" colspan="2">
                            <label for="tele" class="form-label">Telephone:</label>
                            <input type="tel" name="tele" id="tele" class="input-text" placeholder="Telephone Number" required pattern="^09\d{9}$" title="The number should start at 09 and be exactly 11 digits long.">
                            <button type="button" id="sendOtpButton" disabled onclick="sendOtp()">Send OTP</button>
                        </td>
                    </tr>

                    <!-- OTP Field (initially hidden) -->
                    <tr id="otpRow" style="display: none;">
                        <td class="label-td" colspan="2">
                            <label for="otp" class="form-label">Enter OTP:</label>
                            <input type="text" name="otp" id="otp" class="input-text" placeholder="OTP" required>
                        </td>
                    </tr>

                    <!-- Terms and Conditions Checkbox -->
                    <tr>
                        <td class="label-td" colspan="2">
                            <label>
                                <input type="checkbox" id="termsCheckbox" name="termsAccepted" required> I accept the <a href="#" id="termsLink">Terms and Conditions</a>
                            </label>
                        </td>
                    </tr>

                    <!-- Privacy Policy Checkbox -->
                    <tr>
                        <td class="label-td" colspan="2">
                            <label>
                                <input type="checkbox" id="privacyCheckbox" name="privacyAccepted" required> I accept the <a href="#" id="privacyPolicyLink">Privacy Policy</a>
                            </label>
                        </td>
                    </tr>

                    <!-- Submit and Reset Buttons -->
                    <tr>
                        <td>
                            <input type="reset" value="Reset" class="login-btn btn-primary-soft btn">
                        </td>
                        <td>
                            <input type="submit" value="Next" class="login-btn btn-primary btn">
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
</center>
<script>
// Terms and Conditions Alert
document.getElementById('termsLink').addEventListener('click', function(e) {
    e.preventDefault();
    Swal.fire({
        title: 'Terms and Conditions',
        html: `
            <div style="text-align: left; padding: 10px;">
                <h3>Effective Date: January 14, 2025</h3>
                <p>Welcome to Hemolink! By using this website or mobile app, you agree to these Terms and Conditions.</p>
                
                <h4 style="margin-top: 15px;">1. Acceptance of Terms</h4>
                <p>By accessing or using Hemolink, you agree to be bound by these terms.</p>

                <h4 style="margin-top: 15px;">2. Service Description</h4>
                <p>Hemolink provides an online platform for scheduling healthcare appointments and accessing medical records.</p>

                <h4 style="margin-top: 15px;">3. User Responsibilities</h4>
                <ul style="margin-left: 20px;">
                    <li>Provide accurate information</li>
                    <li>Maintain account security</li>
                    <li>Comply with appointment policies</li>
                    <li>Respect healthcare providers</li>
                </ul>

                <h4 style="margin-top: 15px;">4. Appointment Policies</h4>
                <ul style="margin-left: 20px;">
                    <li>24-hour cancellation notice required</li>
                    <li>Arrive 15 minutes before appointment</li>
                    <li>Bring valid ID and medical records</li>
                </ul>

                <h4 style="margin-top: 15px;">5. Limitation of Liability</h4>
                <p>Hemolink is not liable for any damages arising from use of the service.</p>

                <h4 style="margin-top: 15px;">6. Legal Rights and Priorities</h4>
                <p>Hemolink is committed to providing equitable access to healthcare services and upholding the rights of all individuals, including senior citizens, indigenous peoples, and persons with disabilities (PWD), as protected by Philippine law.</p>

                <h4style="margin-top: 15px;">6.1 Senior Citizens' Rights</h4>
                <p>In accordance with Republic Act No. 7432, which was later amended by RA 9472, senior citizens have the right to priority services in various establishments, including healthcare facilities. Hemolink will ensure that priority is given to senior citizens during healthcare appointments, in line with the provisions of these laws.</p>

                <h4 style="margin-top: 15px;">6.2 Indigenous Peoples' Rights</h4>
                <p>In line with the Indigenous Peoples' Rights Act of 1997, Hemolink recognizes and respects the rights of indigenous peoples, ensuring that they are provided with accessible healthcare services. Indigenous individuals will not be discriminated against in any manner, and their unique cultural and health needs will be taken into account.</p>

                <h4 style="margin-top: 15px;">6.3 Persons with Disabilities (PWD) Rights</h4>
                <p>As per Republic Act No. 10754, which expands the benefits and privileges for persons with disabilities, Hemolink is committed to offering priority service to PWDs. This includes providing express lanes for PWDs in all healthcare appointments. In the absence of express lanes, Hemolink ensures that priority is given to persons with disabilities to ensure timely access to necessary medical services.</p>

                <h4 style="margin-top: 15px;">7. Privacy and Data Protection</h4>
                <p>We take your privacy seriously and comply with relevant laws on data protection. Please review our privacy policy for details on how we collect, store, and protect your personal information.</p>
            </div>
        `,
        width: '600px',
        confirmButtonText: 'I Understand',
        confirmButtonColor: '#2d6a4f',
        showClass: {
            popup: 'animate__animated animate__fadeInDown'
        },
        hideClass: {
            popup: 'animate__animated animate__fadeOutUp'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('termsCheckbox').checked = true;
        }
    });
});

document.querySelector('form').addEventListener('submit', function(event) {
    const fname = document.querySelector('input[name="fname"]').value;
    const lname = document.querySelector('input[name="lname"]').value;
    const password = document.querySelector('input[name="password"]').value;
    const confirmPassword = document.querySelector('input[name="confirm_password"]').value;

    // Name validation
    if (!/^[a-zA-Z ]+$/.test(fname) || !/^[a-zA-Z ]+$/.test(lname)) {
        event.preventDefault();
        Swal.fire({
            title: 'Invalid Name Format',
            text: 'First name and last name can only contain letters and spaces.',
            icon: 'error',
            confirmButtonText: 'OK',
            confirmButtonColor: '#2d6a4f'
        });
        return;
    }

    // Password match validation
    if (password !== confirmPassword) {
        event.preventDefault();
        Swal.fire({
            title: 'Password Mismatch',
            text: 'Password confirmation does not match! Please try again.',
            icon: 'error',
            confirmButtonText: 'OK',
            confirmButtonColor: '#2d6a4f'
        });
        return;
    }
});



// Privacy Policy Alert
document.getElementById('privacyPolicyLink').addEventListener('click', function(e) {
    e.preventDefault();
    Swal.fire({
        title: 'Privacy Policy',
        html: `
            <div style="text-align: left; padding: 10px;">
                <h3>Effective Date: January 14, 2025</h3>
                <p>At Hemolink, we value your privacy and are committed to protecting your personal information. Our goal is to ensure that your data is handled securely, and we take all necessary measures to protect it from unauthorized access, loss, or misuse.</p>
                
                <h4 style="margin-top: 15px;">1. Information We Collect</h4>
                <ul style="margin-left: 20px;">
                    <li>Personal information (name, address, contact details)</li>
                    <li>Medical history and health records</li>
                    <li>Appointment details and preferences</li>
                </ul>

                <h4 style="margin-top: 15px;">2. How We Use Your Information</h4>
                <ul style="margin-left: 20px;">
                    <li>To provide healthcare services</li>
                    <li>To manage and schedule appointments</li>
                    <li>To enhance and improve the quality of our services</li>
                    <li>To communicate with you about your health and appointment reminders</li>
                </ul>

                <h4 style="margin-top: 15px;">3. Information Security</h4>
                <p>Your personal information is stored securely using industry-standard encryption methods. We have implemented robust security measures to protect your data from unauthorized access, loss, or misuse.</p>
                <p>Hemolink is fully committed to preventing unauthorized access and vulnerabilities. We employ best practices to prevent common threats like SQL injection attacks. Our system is designed with built-in protection to ensure that no SQL injection vulnerabilities can be exploited.</p>
                <p>Additionally, all data access is secured with strict access control, ensuring that only authorized personnel within Hemolink have access to your information. This access is limited to essential personnel only, and all private personnel handling data are bound by strict confidentiality agreements.</p>

                <h4 style="margin-top: 15px;">4. Security Tools Used</h4>
                <p>To further enhance our security measures, we rely on three advanced tools to protect and monitor your data:</p>
                <ul style="margin-left: 20px;">
                    <li><b>ManageEngine EventLog Analyzer</b>: This tool provides comprehensive log management and monitoring for SQL Server. It helps us collect, analyze, and report on SQL Server logs, providing real-time alerts for suspicious activities, unauthorized access, or potential security breaches.</li>
                    <li><b>SQL Secure (Idera)</b>: This tool helps us perform comprehensive security assessments and monitoring. It allows our database administrators to identify potential vulnerabilities and take corrective actions before they can be exploited. SQL Secure also provides detailed reports on user permissions, security policy compliance, and potential security risks.</li>
                    <li><b>Safe Backup</b>: We use this tool for reliable backup and disaster recovery solutions. It ensures that your data is securely backed up, and in the event of a security breach or data loss, we can restore services quickly and reliably.</li>
                </ul>

                <h4 style="margin-top: 15px;">5. Data Disclosure</h4>
                <p>Your personal information will only be disclosed to authorized personnel within Hemolink who are directly involved in providing healthcare services. We do not sell, share, or rent your personal data to third parties for marketing or any other purposes without your explicit consent. In certain cases, we may disclose information to legal authorities if required by law or if it is necessary to protect your safety or the safety of others.</p>

                <h4 style="margin-top: 15px;">6. Your Rights</h4>
                <ul style="margin-left: 20px;">
                    <li>Access your personal information at any time</li>
                    <li>Request corrections or updates to your personal information</li>
                    <li>Delete your account and personal information upon request, subject to applicable legal requirements</li>
                </ul>

                <h4 style="margin-top: 15px;">7. Contact Us</h4>
                <p>If you have any questions or concerns about this Privacy Policy, or if you wish to exercise your rights, please contact us at hemolink@gmail.com</a>.</p>
            </div>
        `,
        width: '600px',
        confirmButtonText: 'I Understand',
        confirmButtonColor: '#2d6a4f',
        showClass: {
            popup: 'animate__animated animate__fadeInDown'
        },
        hideClass: {
            popup: 'animate__animated animate__fadeOutUp'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('privacyCheckbox').checked = true;
        }
    });
});

// Add some CSS for better styling of the SweetAlert content
document.head.insertAdjacentHTML('beforeend', `
    <style>
        .swal2-html-container {
            text-align: left !important;
        }
        .swal2-html-container h3 {
            color: #2d6a4f;
            margin-top: 20px;
            margin-bottom: 10px;
        }
        .swal2-html-container ul {
            margin-left: 20px;
            margin-bottom: 15px;
        }
        .swal2-html-container p {
            margin-bottom: 10px;
        }
    </style>
`);
function togglePhilhealthField() {
    var philhealthRow = document.getElementById('philhealth-row-input');
    var philhealthInput = document.getElementById('philhealth-input');
    var yesRadio = document.getElementById('philhealth-yes');
    
    if (yesRadio.checked) {
        philhealthRow.style.display = 'table-row';
        philhealthInput.required = true;
    } else {
        philhealthRow.style.display = 'none';
        philhealthInput.required = false;
        philhealthInput.value = ''; // Clear the input when hidden
    }
}
function updateAddress() {
    const streetNumber = document.getElementById('street_number').value;
    const streetName = document.getElementById('street_name').value;
    
    if(streetNumber && streetName) {
        const fullAddress = `#${streetNumber} ${streetName} Avenue, Mabayuan, Olongapo City`;
        document.getElementById('complete_address').value = fullAddress;
    }
}

// Initialize PhilHealth field on page load
document.addEventListener('DOMContentLoaded', function() {
    togglePhilhealthField();
});




// Function to show OTP input
function showOtpInput() {
    document.getElementById('otpRow').style.display = 'table-row';
}

// Function to enable Send OTP button
function enableSendOtpButton() {
    const teleInput = document.getElementById('tele');
    const sendOtpButton = document.getElementById('sendOtpButton');
    sendOtpButton.disabled = !teleInput.value.match(/^09\d{9}$/);
}

// AJAX call to send OTP
function sendOtp() {
    var tele = document.getElementById('tele').value;
    if (tele.match(/^09\d{9}$/)) {
        // Assuming send_otp.php handles the sending of OTP
        fetch('send_otp.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'tele=' + tele
        })
        .then(response => response.text())
        .then(data => {
            console.log(data); // Logs response in the console
            document.getElementById('otpMessage').innerHTML = data; // Display message on the frontend
            showOtpInput(); // Show the OTP input if successful
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('otpMessage').innerHTML = "An error occurred. Please try again."; // Error message
        });
    } else {
        alert('Invalid phone number!');
    }
}


// Add event listener to telephone input for enabling Send OTP button
document.getElementById('tele').addEventListener('input', enableSendOtpButton);
</script>


<?php if (isset($_SESSION["sweet_alert"])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Welcome to HemoLink!',
                text: 'Your account has been successfully created.',
                icon: 'success',
                confirmButtonText: 'Continue'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'patient.php';
                }
            });
        });
    </script>
    <?php 
        unset($_SESSION["sweet_alert"]); 
    endif; 
    ?>
</body>
</html>
<?php
// Include the Twilio SDK autoload file
require 'vendor/autoload.php';

// Use the Twilio namespace
use Twilio\Rest\Client;


date_default_timezone_set('Asia/Manila');
$date = date('Y-m-d');
$_SESSION["date"] = $date;

if ($_POST) {
    $fname = trim($_POST['fname']);
    $lname = trim($_POST['lname']);
    $street_number = '#' . ltrim(trim($_POST['street_number']), '#');
    $address = sprintf("%s %s Avenue, Mabayuan, Olongapo City", $street_number, trim($_POST['street_name']));
    $nic = $_POST['nic'];
    $dob = $_POST['dob'];
    $email = $_POST['email'] ?? "";
    $newpassword = $_POST['password'] ?? "";
    $cpassword = $_POST['confirm_password'] ?? "";
    $phone = preg_replace('/[^0-9]/', '', $_POST['tele']); // Remove non-numeric characters
if (substr($phone, 0, 1) === "0") {
    $phone = "+63" . substr($phone, 1); // Convert 09XXXXXXX to +639XXXXXXX
} else {
    $phone = "+$phone"; // If already in E.164 format, add "+"
}

    
    $sweet_alert = "";

    if (!preg_match("/^[a-zA-Z ]+$/", $fname) || !preg_match("/^[a-zA-Z ]+$/", $lname)) {
        $sweet_alert = "<script>Swal.fire({title: 'Invalid Name Format', text: 'First and last names must contain only letters and spaces.', icon: 'error', confirmButtonText: 'OK'});</script>";
    } elseif ($newpassword !== $cpassword) {
        $sweet_alert = "<script>Swal.fire({title: 'Password Mismatch', text: 'Passwords do not match!', icon: 'error', confirmButtonText: 'OK'});</script>";
    } else {
        try {
            $database = new mysqli("localhost", "root", "password", "hemolink_database");

            if ($database->connect_error) {
                throw new Exception("Database connection failed: " . $database->connect_error);
            }

            $database->begin_transaction();

            // Check if email exists
            $stmt = $database->prepare("SELECT pemail FROM patient WHERE pemail = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            if ($stmt->get_result()->num_rows > 0) {
                throw new Exception("This email address is already registered.");
            }

            // Check if phone number exists
            $stmt = $database->prepare("SELECT phone_number FROM patient WHERE phone_number = ?");
            $stmt->bind_param("s", $phone);
            $stmt->execute();
            if ($stmt->get_result()->num_rows > 0) {
                throw new Exception("This phone number is already registered.");
            }

            // Insert patient data
            $stmt = $database->prepare("INSERT INTO patient (pemail, pname, ppassword, paddress, pnic, pdob, phone_number) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $fullName = $fname . ' ' . $lname;
            $hashed_password = $newpassword;
            $stmt->bind_param("sssssss", $email, $fullName, $hashed_password, $address, $nic, $dob, $phone);
            if (!$stmt->execute()) {
                throw new Exception("Error registering patient data");
            }

            // Insert webuser data
            $stmt = $database->prepare("INSERT INTO webuser (email, usertype) VALUES (?, ?)");
            $userType = 'p';
            $stmt->bind_param("ss", $email, $userType);
            if (!$stmt->execute()) {
                throw new Exception("Error creating user account");
            }

            // Send OTP via Twilio
            $otp = rand(100000, 999999);
            $account_sid = 'ACbf56b173b4f3c4b0cef7f2497d30d6a5';
            $auth_token = 'd95e0606e4591b5f251cba67d54f7628';
            $twilio_number = '14055432932';
            $client = new Client($account_sid, $auth_token);

            $client->messages->create(
                $phone,
                ['from' => $twilio_number, 'body' => "Your OTP is: $otp"]
            );

            $database->commit();

            $_SESSION["user"] = $email;
            $_SESSION["usertype"] = "p";
            $_SESSION["username"] = $fname;
            $_SESSION["sweet_alert"] = true;
            $_SESSION["login_success"] = true;
            $_SESSION["user_type"] = "Patient";
            $_SESSION["user_name"] = $fname;

            header("Location: login.php");
            exit();
        } catch (Exception $e) {
            $database->rollback();
            $sweet_alert = "<script>Swal.fire({title: 'Error', text: '" . addslashes($e->getMessage()) . "', icon: 'error', confirmButtonText: 'OK'});</script>";
        }
    }

    if (!empty($sweet_alert)) {
        echo $sweet_alert;
    }
}
?>
<?php
ob_end_flush(); // Flush output buffer
?>
