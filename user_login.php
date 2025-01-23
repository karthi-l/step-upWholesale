<?php
// Start the session

// Include database connection
include('session_dbConn.php');
include('generate_otp.php');

//If the user is logged in Redirect to Account dashboard
if(isset($_SESSION['user_id'])){
    header("Location:user_dashboard.php");
}
if(isset($_SESSION['admin_id'])){
    header("Location:admin_dashboard.php");
}
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data and sanitize
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    
    // Query to fetch user data based on username
    $query = "SELECT user_id, username, password, email FROM usersretailers WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if user exists
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $user['password'])) {
            // Password is correct; generate OTP
            $otp = generateOTP();

            // Save OTP and its expiry in the database
            $otp_expiry = date("Y-m-d H:i:s", strtotime("+10 minutes")); // OTP valid for 10 minutes
            $update_query = "UPDATE usersretailers SET otp = ?, otp_expiry = ? WHERE user_id = ?";
            $update_stmt = $conn->prepare($update_query);
            $update_stmt->bind_param("ssi", $otp, $otp_expiry, $user['user_id']);
            $update_stmt->execute();
            $_SESSION['authType'] = "login";
            $_SESSION['user_or_admin'] = "user";
            $_SESSION['auth_name'] = $user['username'];
            $_SESSION['auth_email'] = $user['email'];
            // Send OTP email
            sendOTPEmail($user['email'], $otp, $user['username']);

            // Store user info in session for OTP verification

            // Redirect to OTP verification page
            header("Location:verify_otp.php");
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
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
                <form action="user_login.php" method="POST">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" id="username" name="username" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" id="password" name="password" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Login</button>
                </form>
                    
                <p class="mt-3 text-center">Go to Home Page <a href="index.php">Home-Page</a></p>
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

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
