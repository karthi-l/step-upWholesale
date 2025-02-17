<?php
// Start the session


// Include database connection
include('../../includes/session_dbConn.php');
include('../../includes/generate_otp.php');
include('../../includes/bootstrap-css-js.php');

//If the user is logged in Redirect to Account dashboard
if(isset($_SESSION['admin_id'])){
    header("Location:admin_dashboard.php");
}
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data and sanitize
    $adminname = mysqli_real_escape_string($conn, $_POST['adminname']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    
    // Query to fetch user data based on username
    $query = "SELECT admin_id, adminname, password, email FROM admins WHERE adminname = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $adminname);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if user exists
    if ($result->num_rows == 1) {
        $admin = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $admin['password'])) {
            // Password is correct; generate OTP
            $otp = generateOTP();

            // Save OTP and its expiry in the database
            $otp_expiry = date("Y-m-d H:i:s", strtotime("+10 minutes")); // OTP valid for 10 minutes
            $update_query = "UPDATE admins SET otp = ?, otp_expiry = ? WHERE admin_id = ?";
            $update_stmt = $conn->prepare($update_query);
            $update_stmt->bind_param("ssi", $otp, $otp_expiry, $admin['admin_id']);
            $update_stmt->execute();
            $_SESSION['authType'] = "login";
            // Send OTP email
            
            // Store user info in session for OTP verification
            $_SESSION['auth_name'] = $admin['adminname'];
            $_SESSION['user_or_admin'] = "admin";
            $_SESSION['auth_email'] = $admin['email'];
            
            // Redirect to OTP verification page
            sendOTPEmail($admin['email'], $otp, $admin['adminname']);
            header("Location:../verify_otp.php");
            exit(0);
        } else {
            // Incorrect password
            $incorrect_password = true;
        }
    } else {
        // User not found
        $user_not_found = true;
    }
}
?>

<!-- The rest of your HTML login form remains unchanged -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Wholesale Footwear Management</title>
</head>
<body>
<div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="row w-100 border rounded">
            <div class="col-12 col-md-6 col-lg-4 mx-auto">
                <h2 class="text-center mb-4">Login</h2>
                <?php if (isset($_GET['registration']) && $_GET['registration'] == 'success'): ?>
                    <div class="alert alert-success text-center mb-4">
                        Registration successful! You can now login here.
                    </div>
                <?php endif; ?>
                <form action="admin_login.php" method="POST">
                    <div class="mb-3">
                        <label for="adminname" class="form-label">Admin-name</label>
                        <input type="text" id="adminname" name="adminname" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" id="password" name="password" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Login</button>
                </form>
                    
                <p class="mt-3 text-center">Go to Home Page <a href="../../index.php">Home-Page</a></p>
            </div>
        </div>
    </div>

    <!-- Bootstrap Modal for Incorrect Login -->
    <?php if (isset($incorrect_password) && $incorrect_password) { ?>
    <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="errorModalLabel">Login Error</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Incorrect password. Please try again.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        // Show the modal after page load if login failed
        window.onload = function() {
            var myModal = new bootstrap.Modal(document.getElementById('errorModal'));
            myModal.show();
        };
    </script>
    <?php } ?>

    <!-- Bootstrap Modal for User Not Found -->
    <?php if (isset($user_not_found) && $user_not_found) { ?>
    <div class="modal fade" id="userNotFoundModal" tabindex="-1" aria-labelledby="userNotFoundModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="userNotFoundModalLabel">Login Error</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    No user found with that username. Please check and try again.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        // Show the modal after page load if no user found
        window.onload = function() {
            var myModal = new bootstrap.Modal(document.getElementById('userNotFoundModal'));
            myModal.show();
        };
    </script>
    <?php } ?>

</body>
</html>
