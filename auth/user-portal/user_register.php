<?php
// Include your database connection and PHPMailer files
include('../../includes/session_dbConn.php');
include('../../includes/generate_otp.php');


// Before redirecting, check session variables

// Redirect if the user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location:user_dashboard.php");
    exit();
}


// Function to generate a 6-digit OTP
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and get form data
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $owner_name = mysqli_real_escape_string($conn, $_POST['owner_name']);
    $shop_name = mysqli_real_escape_string($conn, $_POST['shop_name']);
    $shop_address = mysqli_real_escape_string($conn, $_POST['shop_address']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $mobile_number = mysqli_real_escape_string($conn, $_POST['mobile_number']);

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if the username or email already exists
    $check_query = "SELECT * FROM usersretailers WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<div class='alert alert-danger'>Username or Email already exists. Please use a different one.</div>";
    } else {
        // Generate OTP
        $otp = generateOTP();
        $otp_expiry = date('Y-m-d H:i:s', strtotime('+10 minutes')); // Set expiry time to 10 minutes

        // Insert user data along with OTP and OTP expiry into the database
        $insert_query = "INSERT INTO usersretailers (username, password, shop_name, shop_address, email, mobile_number, otp, otp_expiry, owner_name) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("sssssssss", $username, $hashed_password, $shop_name, $shop_address, $email, $mobile_number, $otp, $otp_expiry,$owner_name);

        if ($stmt->execute()) {
            // Send OTP to the user's email
            $_SESSION['authType'] = "register"; 
            $_SESSION['user_or_admin'] = 'user';
            $_SESSION['auth_name'] = $username;
            $_SESSION['auth_email'] = $email;
            sendOTPEmail($email, $otp, $username);
            header('Location:../verify_otp.php'); // Redirect to OTP verification page
            exit(0);
        } else {
            echo "<div class='alert alert-danger'>Error during registration. Please try again.</div>";
        }
    }
}
?>
<?php if(isset($_SESSION['admin_id'])): ?>

    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Wholesale Footwear Management</title>
    <?php include('../../includes/inc_styles.php');?>

    <script>
        // JavaScript function to validate password strength
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

        // Password validation function (checks password strength pattern)
        function validatePassword() {
            var password = document.getElementById('password').value;
            var passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
            
            if (!password.match(passwordPattern)) {
                document.getElementById('password-error').style.display = 'block';
            } else {
                document.getElementById('password-error').style.display = 'none';
            }
        }

        // Mobile validation function (only digits allowed)
        function validateMobile() {
            var mobile = document.getElementById('mobile_number').value;
            var mobilePattern = /^\d{10}$/; // Assuming mobile number is exactly 10 digits
            if (!mobile.match(mobilePattern)) {
                document.getElementById('mobile-error').style.display = 'block';
            } else {
                document.getElementById('mobile-error').style.display = 'none';
            }
        }

        // Email validation function (checks for valid email format)
        function validateEmail() {
            var email = document.getElementById('email').value;
            var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!email.match(emailPattern)) {
                document.getElementById('email-error').style.display = 'block';
            } else {
                document.getElementById('email-error').style.display = 'none';
            }
        }

        // Toggle password visibility
        function togglePassword() {
            var passwordField = document.getElementById('password');
            var passwordIcon = document.getElementById('password-toggler');
            if (passwordField.type === "password") {
                passwordField.type = "text"; // Show password
                passwordIcon.classList.remove("bi-eye-slash"); // Remove "eye-slash" class
                passwordIcon.classList.add("bi-eye"); // Add "eye" class
            } else {
                passwordField.type = "password"; // Hide password
                passwordIcon.classList.remove("bi-eye"); // Remove "eye" class
                passwordIcon.classList.add("bi-eye-slash"); // Add "eye-slash" class
            }
        }

        // Prevent form submission if validation fails
        document.querySelector('form').addEventListener('submit', function(e) {
            let isPasswordValid = document.getElementById('password-error').style.display === 'none';
            let isMobileValid = document.getElementById('mobile-error').style.display === 'none';
            let isEmailValid = document.getElementById('email-error').style.display === 'none';

            if (!isPasswordValid || !isMobileValid || !isEmailValid) {
                e.preventDefault(); // Prevent form from submitting
            }
        });
    </script>

    <!-- Add Bootstrap Icons for the Eye Icon -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
<div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="row w-100 border rounded p-3">
            <div class="col-12 col-md-8 col-lg-6 mx-auto">
                <h2 class="text-center mb-4">Register</h2>
                <form action="user_register.php" method="POST">
                    <!-- Shop Name and Shop Details (Same Row on Larger Screens) -->
                    <div class="row">
                        <div class="col-12 col-xl-6">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" id="username" name="username" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-12 col-xl-6">
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <input type="password" id="password" name="password" class="form-control" required oninput="checkPasswordStrength()">
                                    <button type="button" class="btn btn-outline-secondary" onclick="togglePassword()" id="password-toggler">
                                        <i class="bi bi-eye-slash"></i> <!-- Initial "eye-slash" icon -->
                                    </button>
                                </div>
                                <div class="mt-2">
                                    <div class="progress">
                                        <div id="password-strength-bar" class="progress-bar" role="progressbar" style="width: 0%"></div>
                                    </div>
                                    <small id="password-strength-text" class="text-muted">Very Weak</small>
                                </div>
                                <small id="password-error" class="text-danger" style="display:none;">Password must be at least 8 characters and include uppercase, lowercase, number, and symbol.</small>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile Number and Email in Same Row for Large Screens -->
                    <div class="row">
                        <div class="col-12 col-xl-6">
                            <div class="mb-3">
                                <label for="mobile_number" class="form-label">Mobile Number</label>
                                <input type="text" id="mobile_number" name="mobile_number" class="form-control" required onblur="validateMobile()">
                                <small id="mobile-error" class="text-danger" style="display:none;">Mobile number must be exactly 10 digits.</small>
                            </div>
                        </div>
                        <div class="col-12 col-xl-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email ID</label>
                                <input type="email" id="email" name="email" class="form-control" required onblur="validateEmail()">
                                <small id="email-error" class="text-danger" style="display:none;">Please enter a valid email address.</small>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="owner_name" class="form-label">Owner Name</label>
                        <input type="text" id="owner_name" name="owner_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="shop_name" class="form-label">Shop Name</label>
                        <input type="text" id="shop_name" name="shop_name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="shop_address" class="form-label">Shop Address</label>
                        <textarea id="shop_address" name="shop_address" class="form-control" rows="3" required></textarea>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" name="submitRegistration" class="btn btn-primary w-100">Register</button>
                </form>
                
                <p class="mt-3 text-center">Go to Home Page <a href="../../index.php">Home-Page</a></p>
            </div>
        </div>
    </div>
    <?php include('../../includes/inc_scripts.php');?>
</body>
</html>

<?php  else: echo" 
    <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Account Center - Wholesale Footwear Management</title>
        </head>
        <body>
            <div class='container text-center mt-5'>
                <div class='alert alert-warning m-auto text-center'>
                    <h4>You dont have the previlages to access the page</h4>
                    <div class='d-flex justify-content-center'>
                        <a href='../../index.php' class='btn btn-primary mx-2'>Home</a>
                    </div>
                </div>
                

            </div>
        <body>
        </html>

"; endif;?>
