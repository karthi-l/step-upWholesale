<?php
// Include your database connection and PHPMailer files
include('../../includes/session_dbConn.php');
include('../../includes/generate_otp.php');
include('../../includes/bootstrap-css-js.php');
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
                <h2 class="text-center mb-4">Admin-Register</h2>
                <form action="admin_register.php" method="POST">
                    <!-- Shop Name and Shop Details (Same Row on Larger Screens) -->
                    <div class="row">
                        <div class="col-12 col-xl-6">
                            <div class="mb-3">
                                <label for="adminname" class="form-label">Admin-name</label>
                                <input type="text" id="adminname" name="adminname" class="form-control" required>
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
</body>
</html>
