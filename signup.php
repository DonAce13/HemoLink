<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';
session_start(); // This must be at the top
ob_start(); // Optional: Buffer output to prevent header errors
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Sign Up - Mabayuan Health</title>
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
        .text-decoration-none {
            color: #2d6a4f; /* Same as your primary button color */
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            font-size:1.5rem;
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

        .go-back-link:hover svg circle {
            fill: #b7e4c7;
        }
        .go-back-link .go-back-text {
            transition: color 0.2s;
        }
        .go-back-link:hover .go-back-text {
            color: #40916c;
        }
        @media (max-width: 480px) {
            .go-back-link svg {
                width: 32px;
                height: 32px;
            }
            .go-back-link .go-back-text {
                font-size: 1rem;
            }
        }
        
        .password-input-wrapper {
            position: relative;
        }
        .password-container {
            position: relative;
            width: 100%;
            margin-bottom: 0;
        }
        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 2;
            pointer-events: auto;
        }
        #password-guide {
            margin-top: 5px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 5px;
            border: 1px solid #dee2e6;
            position: static;
        }
        
        #password-requirements {
            margin-left: 20px;
        }
        
        #password-requirements li {
            margin-bottom: 5px;
        }
    </style>
</head>
<body>

<center>
    <div class">
        <div class="container">
        <div style="width:100%; text-align:left; margin-bottom:-12px;">
        <a href="/index.html" class="go-back-link" aria-label="Go back to home" style="display:inline-flex; align-items:center; gap:10px; margin-bottom: 24px;">
            <svg width="40" height="40" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" style="vertical-align:middle;">
                <circle cx="16" cy="16" r="16" fill="#e9ecef"/>
                <path d="M18.5 10L13 16L18.5 22" stroke="#2d6a4f" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span class="go-back-text" style="font-size: 1.2rem; color: #2d6a4f; font-weight: 600; letter-spacing: 0.5px;">Go Back</span>
        </a>
    </div>
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
                        <input type="radio" name="hasPhilhealth" value="yes" id="philhealth-yes"> Yes
                    </td>
                    <td class="label-td">
                        <input type="radio" name="hasPhilhealth" value="no" id="philhealth-no" checked> No
                    </td>
                </tr>
                
                <!-- PWD/SENIOR/IP Field -->
                <tr>
                    <td class="label-td" colspan="3">
                        <label for="hasPhilhealth" class="form-label">
                            Where do you classify in this category (Leave blank if none)
                        </label>
                    </td>
                </tr>
                <tr>
                    <td class="label-td">
                        <input type="checkbox" name="categories[]" value="IP" class="category-option"> Indigenous People
                    </td>
                    <td class="label-td">
                        <input type="checkbox" name="categories[]" value="SENIOR CITIZEN" class="category-option"> Senior Citizen
                    </td>
                    <td class="label-td">
                        <input type="checkbox" name="categories[]" value="PWD" class="category-option"> PWD
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
                        $today = date('Y-m-d');
                        $minDate = date('Y-m-d', strtotime('-100 years', strtotime($today)));
                        $maxDate = date('Y-m-d', strtotime('-18 years', strtotime($today)));
                        echo '<input type="date" name="dob" class="input-text" min="' . $minDate . '" max="' . $maxDate . '" onchange="calculateAge(this.value)" required>';
                        ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" id="ageDisplay" style="text-align: center; padding: 10px 0; font-weight: bold; color: #2d6a4f;"></td>
                </tr>

                <!-- Email Field with Send OTP Button -->
                <tr>
                    <td class="label-td" colspan="2">
                        <label for="email" class="form-label">Email:</label>
                        <input type="email" name="email" id="email" class="input-text" placeholder="Email Address" required>
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
                <tr>
                    <td colspan="2">
                        <div id="otpMessage" style="color: green; font-weight: bold; text-align: center;"></div>
                    </td>
                </tr>

                <!-- Password Fields -->
                <tr>
                    <td class="label-td" colspan="2">
                        <label for="password" class="form-label">Password:</label>
                        <div class="password-input-wrapper">
                            <div class="password-container">
                                <input type="password" name="password" id="password" class="input-text" placeholder="Password" required>
                                <i class="fas fa-eye password-toggle" onclick="togglePassword('password')"></i>
                            </div>
                            <div id="password-guide" style="display:none;"></div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="label-td" colspan="2">
                        <label for="confirm_password" class="form-label">Confirm Password:</label>
                        <div class="password-input-wrapper">
                            <div class="password-container">
                                <input type="password" name="confirm_password" id="confirm_password" class="input-text" placeholder="Confirm Password" required>
                                <i class="fas fa-eye password-toggle" onclick="togglePassword('confirm_password')"></i>
                            </div>
                            <div id="confirm-feedback" style="margin-top:5px;"></div>
                        </div>
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

                <tr>
                    <td class="label-td" colspan="2">
                        <div class="cf-turnstile" data-sitekey="0x4AAAAAAA8HgcMMy1gC84ju"></div>
                    </td>
                </tr>
                <tr>
                    <td class="label-td" colspan="2">
                        <div class="cf-turnstile" data-sitekey="0x4AAAAAAA8HgcMMy1gC84ju"></div>
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
            
            <td>
                <p class="text-center mb-0" style="font-size: 1.2rem;">
                    Already have an account?
                    <a href="login.php" class="text-decoration-none">Sign In</a>
                </p>
            </td>        
        </form>
    </div>
</center>
<script>

document.addEventListener('DOMContentLoaded', function() {
    const resetBtn = document.querySelector('input[type="reset"]');
    if (resetBtn) {
        resetBtn.addEventListener('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Are you sure?',
                text: 'This will clear all the information you have entered.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, reset',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#2d6a4f'
            }).then((result) => {
                if (result.isConfirmed) {
                    e.target.form.reset();
                    // Hide OTP input and message if present
                    const otpRow = document.getElementById('otpRow');
                    if (otpRow) otpRow.style.display = 'none';
                    const otpMsg = document.getElementById('otpMessage');
                    if (otpMsg) otpMsg.innerHTML = '';
                    const ageDisplay = document.getElementById('ageDisplay');
                    if (ageDisplay) ageDisplay.innerText = '';
                }
            });
        });
    }
    setupPasswordGuide();
});

// Terms and Conditions Alert
document.getElementById('termsLink').addEventListener('click', function(e) {
    e.preventDefault();
    Swal.fire({
        title: 'Terms and Conditions',
        html: `
            <div style="text-align: left; padding: 10px;">
                <h3>Effective Date: January 14, 2025</h3>
                <p>Welcome to Mabayuan Health! By using this website or mobile app, you agree to these Terms and Conditions.</p>
                
                <h4 style="margin-top: 15px;">1. Acceptance of Terms</h4>
                <p>By accessing or using Mabayuan Health, you agree to be bound by these terms.</p>

                <h4 style="margin-top: 15px;">2. Service Description</h4>
                <p>Mabayuan Health provides an online platform for scheduling healthcare appointments and accessing medical records.</p>

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
                <p>Mabayuan Health is not liable for any damages arising from use of the service.</p>

                <h4 style="margin-top: 15px;">6. Legal Rights and Priorities</h4>
                <p>Mabayuan Health is committed to providing equitable access to healthcare services and upholding the rights of all individuals, including senior citizens, indigenous peoples, and persons with disabilities (PWD), as protected by Philippine law.</p>

                <h4style="margin-top: 15px;">6.1 Senior Citizens' Rights</h4>
                <p>In accordance with Republic Act No. 7432, which was later amended by RA 9472, senior citizens have the right to priority services in various establishments, including healthcare facilities. Mabayuan Health will ensure that priority is given to senior citizens during healthcare appointments, in line with the provisions of these laws.</p>

                <h4 style="margin-top: 15px;">6.2 Persons with Disabilities (PWD) Rights</h4>
                <p>As per Republic Act No. 10754, which expands the benefits and privileges for persons with disabilities, Mabayuan Health is committed to offering priority service to PWDs. This includes providing express lanes for PWDs in all healthcare appointments. In the absence of express lanes, Mabayuan Health ensures that priority is given to persons with disabilities to ensure timely access to necessary medical services.</p>

                <h4 style="margin-top: 15px;">7. Privacy and Data Protection</h4>
                <p>We take your privacy seriously and are committed to protecting your personal information. Please review our privacy policy for details on how we collect, store, and protect your personal information.</p>

                <h4 style="margin-top: 15px;">8. Booking Service Policy</h4>
                <p>Cancellation of Approved Booking Services must be only available up until 2 days before the session will commence a day before that will not be applicable or honored.</p>
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

    // Password strength validation
    if (password.length < 4 || !/[^a-zA-Z0-9]/.test(password)) {
        event.preventDefault();
        Swal.fire({
            title: 'Weak Password',
            text: 'Password must be at least 4 characters and contain at least one special character.',
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
                <p>At Mabayuan Health, we value your privacy and are committed to protecting your personal information. Our goal is to ensure that your data is handled securely, and we take all necessary measures to protect it from unauthorized access, loss, or misuse.</p>
                
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
                <p>Mabayuan Health is fully committed to preventing unauthorized access and vulnerabilities. We employ best practices to prevent common threats like SQL injection attacks. Our system is designed with built-in protection to ensure that no SQL injection vulnerabilities can be exploited.</p>
                <p>Additionally, all data access is secured with strict access control, ensuring that only authorized personnel within Mabayuan Health have access to your information. This access is limited to essential personnel only, and all private personnel handling data are bound by strict confidentiality agreements.</p>

                <h4 style="margin-top: 15px;">4. Security Tools Used</h4>
                <p>To further enhance our security measures, we rely on three advanced tools to protect and monitor your data:</p>
                <ul style="margin-left: 20px;">
                    <li><b>ManageEngine EventLog Analyzer</b>: This tool provides comprehensive log management and monitoring for SQL Server. It helps us collect, analyze, and report on SQL Server logs, providing real-time alerts for suspicious activities, unauthorized access, or potential security breaches.</li>
                    <li><b>SQL Secure (Idera)</b>: This tool helps us perform comprehensive security assessments and monitoring. It allows our database administrators to identify potential vulnerabilities and take corrective actions before they can be exploited. SQL Secure also provides detailed reports on user permissions, security policy compliance, and potential security risks.</li>
                    <li><b>Safe Backup</b>: We use this tool for reliable backup and disaster recovery solutions. It ensures that your data is securely backed up, and in the event of a security breach or data loss, we can restore services quickly and reliably.</li>
                </ul>

                <h4 style="margin-top: 15px;">5. Data Disclosure</h4>
                <p>Your personal information will only be disclosed to authorized personnel within Mabayuan Health who are directly involved in providing healthcare services. We do not sell, share, or rent your personal data to third parties for marketing or any other purposes without your explicit consent. In certain cases, we may disclose information to legal authorities if required by law or if it is necessary to protect your safety or the safety of others.</p>
                <p><b>For further verification of identity, all patients must present a valid ID when showing up for their approved booking. Administrators will verify the appropriate ID before allowing access to healthcare services.</b></p>

                <h4 style="margin-top: 15px;">6. Your Rights</h4>
                <ul style="margin-left: 20px;">
                    <li>Access your personal information at any time</li>
                    <li>Request corrections or updates to your personal information</li>
                    <li>Delete your account and personal information upon request, subject to applicable legal requirements</li>
                </ul>

                <h4 style="margin-top: 15px;">7. Contact Us</h4>
                <p>If you have any questions or concerns about this Privacy Policy, or if you wish to exercise your rights, please contact us at Mabayuan Health@gmail.com</a>.</p>
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
    const emailInput = document.getElementById('email'); 
    const sendOtpButton = document.getElementById('sendOtpButton');
    sendOtpButton.disabled = !emailInput.value.match(/^\S+@\S+\.\S+$/);
}

// Add event listener to email input for enabling Send OTP button
document.getElementById('email').addEventListener('input', enableSendOtpButton);
// AJAX call to send OTP
function sendOtp() {
    var email = document.getElementById('email').value;

    // Validate email format
    if (email.match(/^\S+@\S+\.\S+$/)) {
        fetch('send_otp', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'email=' + encodeURIComponent(email)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.text();
        })
        .then(data => {
            // Check if the response contains the success message
            if (data.includes('OTP has been sent to your email')) {
                Swal.fire({
                    title: 'OTP Sent!',
                    text: 'Please check your email for the verification code.',
                    icon: 'success',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#2d6a4f'
                });
                document.getElementById('otpMessage').innerHTML = 'OTP has been sent to your email. Please check your inbox';
                showOtpInput(); // Show the OTP input
            } else if (
                data.includes('already registered') ||
                data.includes('already sent') ||
                data.includes('Invalid email') ||
                data.includes('required') ||
                data.includes('Failed to send OTP')
            ) {
                Swal.fire({
                    title: 'OTP Error',
                    text: data,
                    icon: 'error',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#2d6a4f'
                });
                document.getElementById('otpMessage').innerHTML = '';
            } else {
                // Handle any other response
                Swal.fire({
                    title: 'OTP Sent!',
                    text: 'Please check your email for the verification code.',
                    icon: 'success',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#2d6a4f'
                });
                document.getElementById('otpMessage').innerHTML = 'OTP has been sent to your email. Please check your inbox';
                showOtpInput(); // Show the OTP input
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                title: 'Network Error',
                text: 'An error occurred. Please try again.',
                icon: 'error',
                confirmButtonText: 'OK',
                confirmButtonColor: '#2d6a4f'
            });
            document.getElementById('otpMessage').innerHTML = '';
        });
    } else {
        Swal.fire({
            title: 'Invalid Email',
            text: 'Invalid email address! Please enter a valid email.',
            icon: 'error',
            confirmButtonText: 'OK',
            confirmButtonColor: '#2d6a4f'
        });
    }
}

// Add event listener to telephone input for enabling Send OTP button
// document.getElementById('phone_number').addEventListener('input', enableSendOtpButton);
</script>
<script>
function calculateAge(dob) {
    const today = new Date();
    const birthDate = new Date(dob);
    let age = today.getFullYear() - birthDate.getFullYear();
    const monthDiff = today.getMonth() - birthDate.getMonth();
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
        age--;
    }
    document.getElementById('ageDisplay').innerText = 'Age: ' + age;
    if (age < 18 && dob) {
        setTimeout(() => {
        Swal.fire({
            title: 'Age Restriction',
            text: 'Only users 18 years old and above can register.',
            icon: 'error',
            confirmButtonText: 'OK',
            confirmButtonColor: '#2d6a4f'
            }).then(() => {
        document.querySelector('input[name="dob"]').value = '';
        document.getElementById('ageDisplay').innerText = '';
            });
        }, 100);
    }
}
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = field.nextElementSibling;
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}
function setupPasswordGuide() {
    const passwordField = document.getElementById('password');
    const confirmField = document.getElementById('confirm_password');
    
    // Password requirements guide
    const guide = document.createElement('div');
    guide.id = 'password-guide';
    guide.style.display = 'none';
    guide.style.marginTop = '5px';
    guide.style.padding = '10px';
    guide.style.backgroundColor = '#f8f9fa';
    guide.style.borderRadius = '5px';
    guide.style.border = '1px solid #dee2e6';
    guide.innerHTML = `
        <p style="margin-bottom: 8px; font-weight: 600; color: #495057;">Password Requirements:</p>
        <ul id="password-requirements" style="margin-left: 5px; list-style: none; padding-left: 5px;">
            <li id="req-length" style="margin-bottom: 5px;"><i class="fas fa-check-circle" style="margin-right: 8px;"></i>At least 8 characters</li>
            <li id="req-upper" style="margin-bottom: 5px;"><i class="fas fa-check-circle" style="margin-right: 8px;"></i>At least 1 uppercase letter</li>
            <li id="req-number" style="margin-bottom: 5px;"><i class="fas fa-check-circle" style="margin-right: 8px;"></i>At least 1 number</li>
            <li id="req-special"><i class="fas fa-check-circle" style="margin-right: 8px;"></i>At least 1 special character</li>
        </ul>
    `;
    passwordField.parentNode.parentNode.appendChild(guide);

    // Confirm password feedback
    const confirmFeedback = document.createElement('div');
    confirmFeedback.id = 'confirm-feedback';
    confirmFeedback.style.marginTop = '5px';
    confirmField.parentNode.parentNode.appendChild(confirmFeedback);

    // Responsive adjustments
    function adjustLayout() {
        if (window.innerWidth < 768) {
            guide.style.width = '100%';
            confirmFeedback.style.width = '100%';
        } else {
            guide.style.width = 'calc(100% - 20px)';
            confirmFeedback.style.width = 'calc(100% - 20px)';
        }
    }

    window.addEventListener('resize', adjustLayout);
    adjustLayout();

    // Event listeners
    passwordField.addEventListener('focus', function() {
        guide.style.display = 'block';
        checkPasswordRequirements(passwordField.value);
    });

    passwordField.addEventListener('blur', function() {
        if (document.activeElement !== confirmField) {
            guide.style.display = 'none';
        }
    });

    confirmField.addEventListener('focus', function() {
        guide.style.display = 'block';
    });

    confirmField.addEventListener('blur', function() {
        guide.style.display = 'none';
    });

    passwordField.addEventListener('input', function() {
        checkPasswordRequirements(passwordField.value);
        checkPasswordMatch();
    });

    confirmField.addEventListener('input', checkPasswordMatch);
}

function checkPasswordRequirements(password) {
    const requirements = {
        length: password.length >= 8,
        upper: /[A-Z]/.test(password),
        number: /[0-9]/.test(password),
        special: /[^A-Za-z0-9]/.test(password)
    };

    // Update styling with colors
    document.getElementById('req-length').style.color = requirements.length ? '#2d6a4f' : '#6c757d';
    document.getElementById('req-upper').style.color = requirements.upper ? '#2d6a4f' : '#6c757d';
    document.getElementById('req-number').style.color = requirements.number ? '#2d6a4f' : '#6c757d';
    document.getElementById('req-special').style.color = requirements.special ? '#2d6a4f' : '#6c757d';
}

function checkPasswordMatch() {
    const password = document.getElementById('password').value;
    const confirm = document.getElementById('confirm_password').value;
    const feedback = document.getElementById('confirm-feedback');

    if (confirm.length === 0) {
        feedback.innerHTML = '';
    } else if (password === confirm) {
        feedback.innerHTML = '<span style="color:#2d6a4f;"><i class="fas fa-check-circle"></i> Passwords match</span>';
    } else {
        feedback.innerHTML = '<span style="color:#dc3545;"><i class="fas fa-times-circle"></i> Passwords do not match</span>';
    }
}
</script>

<?php if (isset($_SESSION["sweet_alert"])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Welcome to Mabayuan Health!',
                text: 'Your account has been successfully created.',
                icon: 'success',
                confirmButtonText: 'Continue'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'login.php';
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

$secretKey = '0x4AAAAAAA8HgceQH3B41BjuaBPZYm34S_k';

date_default_timezone_set('Asia/Manila');
$date = date('Y-m-d');
$_SESSION["date"] = $date;

// Database connection with error logging
// $database = new mysqli("", "root", "", "sql_database_hemolink");
$database = new mysqli("localhost", "u763411610_Ayysue", "BarkForMeDog011303", "u763411610_Mabayuan_HC");
if ($database->connect_errno) {
    $sweet_alert = "<script>Swal.fire({
        title: 'Database Connection Error', 
        text: 'Failed to connect to database: " . addslashes($database->connect_error) . "', 
        icon: 'error', 
        confirmButtonText: 'OK'
    });</script>";
    error_log("Database Connection Failed: " . $database->connect_error);
    echo $sweet_alert;
    exit;
}

if ($_POST && isset($_POST['otp'])) {
    // Step 1: Verify OTP
    $entered_otp = trim($_POST['otp']);
    $pemail = $_POST['email'] ?? '';
    
    // Fetch the latest OTP for this email
    $stmt = $database->prepare("SELECT otp FROM otp_verifications WHERE pemail = ? ORDER BY created_at DESC LIMIT 1");
    $stmt->bind_param("s", $pemail);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $latest_otp = $row['otp'];
        if ($entered_otp === $latest_otp) {
            // OTP is correct - proceed with registration
            $delete_stmt = $database->prepare("DELETE FROM otp_verifications WHERE pemail = ?");
            $delete_stmt->bind_param("s", $pemail);
            $delete_stmt->execute();
            
            // Registration logic
            $fname = trim($_POST['fname']);
            $lname = trim($_POST['lname']);
            $street_number = '#' . ltrim(trim($_POST['street_number']), '#');
            $address = sprintf("%s %s Avenue, Mabayuan, Olongapo City", $street_number, trim($_POST['street_name']));
            $hasPhilhealth = $_POST['hasPhilhealth'] ?? 'no';
            $dob = $_POST['dob'];
            $newpassword = $_POST['password'] ?? "";
            $cpassword = $_POST['confirm_password'] ?? "";
            $fullName = $fname . ' ' . $lname;
            $categories = isset($_POST['categories']) ? implode(',', $_POST['categories']) : NULL;
            // Validate inputs
            if (empty($fname) || empty($lname) || empty($pemail) || empty($newpassword)) {
                echo "<script>Swal.fire({title: 'Registration Error', text: 'All fields are required.', icon: 'error', confirmButtonText: 'OK'});</script>";
                exit;
            }
            if ($newpassword !== $cpassword) {
                echo "<script>Swal.fire({title: 'Registration Error', text: 'Passwords do not match.', icon: 'error', confirmButtonText: 'OK'});</script>";
                exit;
            }
            // Password strength validation (backend)
            if (strlen($newpassword) < 4 || !preg_match('/[^a-zA-Z0-9]/', $newpassword)) {
                echo "<script>Swal.fire({title: 'Weak Password', text: 'Password must be at least 4 characters and contain at least one special character.', icon: 'error', confirmButtonText: 'OK'});</script>";
                exit;
            }
            // Check if email exists in patient table
            $stmt = $database->prepare("SELECT pemail FROM patient WHERE pemail = ?");
            $stmt->bind_param("s", $pemail);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                echo "<script>Swal.fire({title: 'Registration Error', text: 'This email address is already registered.', icon: 'error', confirmButtonText: 'OK'});</script>";
                exit;
            }
            // Check if email exists in webuser table
            $stmt = $database->prepare("SELECT email FROM webuser WHERE email = ?");
            $stmt->bind_param("s", $pemail);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                echo "<script>Swal.fire({title: 'Registration Error', text: 'This email address is already registered as a web user.', icon: 'error', confirmButtonText: 'OK'});</script>";
                exit;
            }
            // Insert patient data (without phone_number)
            $stmt = $database->prepare("INSERT INTO patient (pemail, pname, ppassword, paddress, hasPhilhealth, pdob, patient_category) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssss", $pemail, $fullName, $newpassword, $address, $hasPhilhealth, $dob, $categories);
            if (!$stmt->execute()) {
                error_log("Patient Insert Failed: " . $stmt->error);
                echo "<script>Swal.fire({title: 'Registration Error', text: 'Error registering patient data: " . addslashes($stmt->error) . "', icon: 'error', confirmButtonText: 'OK'});</script>";
                exit;
            }
            // Insert webuser data
            $stmt = $database->prepare("INSERT INTO webuser (email, usertype) VALUES (?, ?)");
            $userType = 'p';
            $stmt->bind_param("ss", $pemail, $userType);
            if (!$stmt->execute()) {
                echo "<script>Swal.fire({title: 'Registration Error', text: 'Error creating user account: " . addslashes($stmt->error) . "', icon: 'error', confirmButtonText: 'OK'});</script>";
                exit;
            }
            // Set session for successful registration
            $_SESSION["sweet_alert"] = true;
            $_SESSION["user"] = $pemail;
            $_SESSION["usertype"] = "p";
            $_SESSION["username"] = $fname;
            $_SESSION["login_success"] = true;
            $_SESSION["user_type"] = "Patient";
            $_SESSION["user_name"] = $fname;
            echo "<script>Swal.fire({
                title: 'Verification Successful!', 
                text: 'Your email has been verified and your account has been created.', 
                icon: 'success', 
                confirmButtonText: 'Continue'
            }).then((result) => { 
                if (result.isConfirmed) { 
                    window.location.href = 'login.php'; 
                }
            });</script>";
        } else {
            echo "<script>Swal.fire({title: 'Invalid OTP', text: 'The OTP you entered is incorrect. Please try again.', icon: 'error', confirmButtonText: 'OK'});</script>";
            exit;
        }
    } else {
        echo "<script>Swal.fire({title: 'OTP Error', text: 'No OTP found for this email. Please request a new one.', icon: 'error', confirmButtonText: 'OK'});</script>";
        exit;
    }
}

// Display any error alert
if (!empty($sweet_alert)) {
    echo $sweet_alert;
}

ob_end_flush(); // Flush output buffer
?>
