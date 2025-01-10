<?php
// Start the session

session_start(); // Always start the session at the beginning of your script

// Before redirecting, check session variables

// Redirect if the user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location:user_dashboard.php");
    exit();
}

// Include your database connection and PHPMailer files
include('db_connect.php');
include('generate_otp.php');


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
            sendOTPEmail($email, $otp, $username);
            $_SESSION['user'] = $username;
            $_SESSION['email'] = $email;
            header('Location:verify_otp.php'); // Redirect to OTP verification page
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
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        // JavaScript function to validate password
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
    </script>
</head>
<body>
<div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="row w-100 border rounded p-3">
            <div class="col-12 col-md-8 col-lg-6 mx-auto">
                <h2 class="text-center mb-4">Register</h2>
                <form action="register.php" method="POST">
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
                                <input type="password" id="password" name="password" class="form-control" required>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile Number and Email in Same Row for Large Screens -->
                    <div class="row">
                        <div class="col-12 col-xl-6">
                            <div class="mb-3">
                                <label for="mobile_number" class="form-label">Mobile Number</label>
                                <input type="text" id="mobile_number" name="mobile_number" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-12 col-xl-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email ID</label>
                                <input type="email" id="email" name="email" class="form-control" required>
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
                
                <p class="mt-3 text-center">Go to Home Page <a href="index.php">Home-Page</a></p>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


</body>
</html>
