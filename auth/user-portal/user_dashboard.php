<?php
// Start session to access session variables

// Include your database connection file
include('../../includes/session_dbConn.php');
include('../../includes/bootstrap-css-js.php');
if(isset($_SESSION['admin_id'])){
    header('Location:admin_dashboard.php');
}
// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    // User is logged in, fetch their details from the database
    $user_id = $_SESSION['user_id'];

    // Use prepared statements to prevent SQL injection
    $query = "SELECT username, shop_name, shop_address, email, mobile_number FROM usersretailers WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if a user was found
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
    } else {
        echo "
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
                    <h4>Unable to fetch your details. Please contact support.</h4>
                    <div class='d-flex justify-content-center'>
                        <a href='../logout.php' class='btn btn-primary mx-2'>Logout</a>
                        <a href='../../index.php' class='btn btn-info mx-2'>Home</a>
                    </div>
                </div>
            </div>
        <body>
        </html>
        ";
        exit;
    }
} else {
    // User is not logged in, display the login and register buttons
    echo "
    <!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Account Center - Wholesale Footwear Management</title>
    </head>
    <body>
        <div class='container row mt-5 m-auto'>
            <div class='alert alert-danger text-center col-12 col-md-9 col-lg-8 col-xl-6 col-xxl-5 m-auto'>
                <h4>You must be logged in as a user to access.</h4>
                <p>Please log in to access your account.</p>
                <div class='d-flex justify-content-center'>
                    <a href='user_login.php' class='btn btn-primary mx-2'>Login</a>
                    <a href='../../index.php' class='btn btn-info mx-2'>Home</a>
                </div>
            </div>
        </div>
    </body>
    </html>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Center - Wholesale Footwear Management</title>
</head>
<body>
    <div class="container mt-5">
        <!-- Welcome Section -->
        <h2 class="text-center">Welcome, <?php echo htmlspecialchars($user['username']); ?></h2>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Shop Name: <?php echo htmlspecialchars($user['shop_name']); ?></h5>
                <p><strong>Shop Address:</strong> <?php echo htmlspecialchars($user['shop_address']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                <p><strong>Mobile Number:</strong> <?php echo htmlspecialchars($user['mobile_number']); ?></p>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="mt-4 d-flex justify-content-between flex-wrap">
            <a href="edit_profile.php" class="btn btn-primary mb-2">Edit Profile</a>
            <a href="../change_password.php" class="btn btn-warning mb-2">Change Password</a>
            <a href="order_history.php" class="btn btn-info mb-2">Order History</a>
            <a href="wishlist.php" class="btn btn-secondary mb-2">Wishlist</a>
            <a href="notifications.php" class="btn btn-light mb-2">Notifications</a>
            <a href="support.php" class="btn btn-outline-primary mb-2">Get Support</a>
        </div>

        <!-- Account Statistics -->
        <div class="card mt-4">
            <div class="card-body">
                <h5 class="card-title">Account Statistics</h5>
                <p><strong>Total Purchases:</strong> 20</p>
                <p><strong>Total Amount Spent:</strong> $1,500</p>
            </div>
        </div>

        <div class="mt-4 d-flex justify-content-between flex-wrap">
            <a href="../../index.php" class="btn btn-primary mt-3" >Home</a>
            <!-- Logout Button -->
            <a href="../logout.php" class="btn btn-danger mt-3" onclick="return confirm('Are you sure you want to log out?');">Logout</a>
        </div>
    </div>
</body>
</html>
