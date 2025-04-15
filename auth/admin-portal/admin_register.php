<?php
// Include your database connection and PHPMailer files
include('../../includes/session_dbConn.php');
include('../../includes/generate_otp.php');
// Start the session

 // Always start the session at the beginning of your script

// Before redirecting, check session variables

// Redirect if the user is already logged in
if (isset($_SESSION['admin_id'])) {
    header("Location:admin_dashboard.php");
    exit();
}



// Function to generate a 6-digit OTP
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and get form data
    $adminname = mysqli_real_escape_string($conn, $_POST['adminname']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $mobile_number = mysqli_real_escape_string($conn, $_POST['mobile_number']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if the username or email already exists
    $check_query = "SELECT * FROM admins WHERE adminname = ? OR email = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("ss", $adminname, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<div class='alert alert-danger'>Adminname or Email already exists. Please use a different one.</div>";
    } else {
        // Generate OTP
        $otp = generateOTP();
        $otp_expiry = date('Y-m-d H:i:s', strtotime('+10 minutes')); // Set expiry time to 10 minutes

        // Insert user data along with OTP and OTP expiry into the database
        $insert_query = "INSERT INTO admins (adminname, password, mobile_number, email, role, otp, otp_expiry) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("sssssss", $adminname, $hashed_password, $mobile_number, $email, $role, $otp, $otp_expiry);

        if ($stmt->execute()) {
            // Send OTP to the user's email
            $_SESSION['authType'] = "register"; 
            $_SESSION['auth_name'] = $adminname;
            $_SESSION['user_or_admin'] = "admin";
            $_SESSION['auth_email'] = $email;
            sendOTPEmail($email, $otp, $adminname);
            header('Location:../verify_otp.php'); // Redirect to OTP verification page
            exit(0);
        } else {
            echo "<div class='alert alert-danger'>Error during registration. Please try again.</div>";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Wholesale Footwear Management</title>
    <?php include('../../includes/inc_styles.php');?>
</head>
<body>
<div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="row w-100">
            <div class="col-12 col-md-10 col-lg-8 col-xl-6  mx-auto border rounded p-3">
                <h2 class="text-center mb-4">Admin-Register</h2>
                <form action="admin_register.php" method="POST">
                    <!-- Shop Name and Shop Details (Same Row on Larger Screens) -->
                    <div class="">
                        <div class="">
                            <div class="mb-3">
                                <label for="adminname" class="form-label">Admin-name</label>
                                <input type="text" id="adminname" name="adminname" class="form-control" required>
                            </div>
                        </div>
                        <div class="">
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <!-- Password Field -->
                                <input type="password" id="password" name="password" class="form-control" required oninput="checkPasswordStrength()" onblur="validatePassword()">
                                <div class="mt-2">
                                    <div class="progress">
                                        <div id="password-strength-bar" class="progress-bar" role="progressbar" style="width: 0%"></div>
                                    </div>
                                    <small id="password-strength-text" class="text-muted"></small>
                                </div>
                                <small id="password-error" class="text-danger d-none">Password must be at least 8 characters and include uppercase, lowercase, number, and symbol.</small>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile Number and Email in Same Row for Large Screens -->
                    <div class="">
                        <div class="">
                            <div class="mb-3">
                                <label for="mobile_number" class="form-label">Mobile Number</label>
                                <input type="text" id="mobile_number" name="mobile_number" class="form-control" required onblur="validateMobile()">
                                <small id="mobile-error" class="text-danger d-none">Mobile number must be exactly 10 digits.</small>
                            </div>
                        </div>
                        <div class="">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email ID</label>
                                <!-- Email Field -->
                                <input type="email" id="email" name="email" class="form-control" required onblur="validateEmail()">
                                <small id="email-error" class="text-danger d-none">Please enter a valid email address.</small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 mx-auto">
                            <div class="mb-3">
                                <label for="role" class="form-label">Role</label>
                                <select name="role" id="role" class="form-select">
                                    <option value="">Select Role</option>
                                    <option value="Super-Admin">Super-Admin</option>
                                    <option value="Stock-Manager">Stock-Manager</option>
                                    <option value="Order-Manager">Order-Manager</option>
                                    <option value="Delivery-Manager">Delivery-Manager</option>
                                    <option value="Accountant-Manager">Accountant-Manager</option>
                                    <option value="User-Manager">User-Manager</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <!-- Submit Button -->
                    <div class="row">
                        <div class="col-12 d-flex justify-content-center">
                            <button type="submit" name="submitRegistration" class="btn btn-primary mx-auto">Register</button>
                        </div>
                    </div>
                    <p class="mt-3 text-center">Go to Home Page <a href="../../index.php">Home-Page</a></p>
                </form>
                
            </div>
        </div>
    </div>
 
<script>
    function validatePassword() {
        var password = document.getElementById('password').value;
        var errorEl = document.getElementById('password-error');
        var pattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$/;

        if (!pattern.test(password)) {
            errorEl.classList.remove('d-none');
            return false;
        } else {
            errorEl.classList.add('d-none');
            return true;
        }
    }

    function checkPasswordStrength() {
        var password = document.getElementById('password').value;
        var strengthBar = document.getElementById('password-strength-bar');
        var strengthText = document.getElementById('password-strength-text');

        let strength = 0;
        if (password.length >= 8) strength += 1;
        if (/[A-Z]/.test(password)) strength += 1;
        if (/[a-z]/.test(password)) strength += 1;
        if (/\d/.test(password)) strength += 1;
        if (/[@$!%*?&]/.test(password)) strength += 1;

        let percent = (strength / 5) * 100;
        let barClass = 'bg-danger';
        let text = 'Very Weak';

        if (strength === 2 || strength === 3) {
            barClass = 'bg-warning';
            text = 'Weak';
        } 
        if (strength === 4) {
            barClass = 'bg-info';
            text = 'Good';
        } 
        if (strength === 5) {
            barClass = 'bg-success';
            text = 'Strong';
        }

        strengthBar.style.width = percent + '%';
        strengthBar.className = 'progress-bar ' + barClass;
        strengthText.innerText = text;
    }

    function validateMobile() {
        var mobile = document.getElementById('mobile_number').value;
        var errorEl = document.getElementById('mobile-error');
        var pattern = /^\d{10}$/;

        if (!pattern.test(mobile)) {
            errorEl.classList.remove('d-none');
            return false;
        } else {
            errorEl.classList.add('d-none');
            return true;
        }
    }

    function validateEmail() {
        var email = document.getElementById('email').value;
        var errorEl = document.getElementById('email-error');
        var pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (!pattern.test(email)) {
            errorEl.classList.remove('d-none');
            return false;
        } else {
            errorEl.classList.add('d-none');
            return true;
        }
    }

    document.querySelector('form').addEventListener('submit', function(e) {
        let isPasswordValid = validatePassword();
        let isMobileValid = validateMobile();
        let isEmailValid = validateEmail();

        if (!isPasswordValid || !isMobileValid || !isEmailValid) {
            e.preventDefault(); // Prevent form from submitting
        }
    });
</script>

    <?php include('../../includes/inc_scripts.php');?>
</body>
</html>
