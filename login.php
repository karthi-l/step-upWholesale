<?php
// Start the session
session_start();

if (isset($_SESSION['user_id'])) {
    // Redirect to the account or dashboard page
    header("Location: account.php");
    exit(); // Stop further execution of the script
}
// Include your database connection file
include('db_connect.php');

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data and sanitize
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    
    // Query to fetch user data based on username
    $query = "SELECT user_id, username, password FROM usersretailers WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if user exists
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $user['password'])) {
            // Password is correct, start the session
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            // Redirect to the account page
            header("Location: account.php");
            exit();
            
        } else {
            // Incorrect password, set error flag
            $incorrect_password = true;
        }
    } else {
        // No user found, set error flag
        $user_not_found = true;
    }
}
?>

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
                <form action="login.php" method="POST">
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
                <p class="mt-3 text-center">Don't have an account? <a href="register.php">Register here</a></p>
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
