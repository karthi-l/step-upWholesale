<?php
include('../../includes/session_dbConn.php');

// Check if the user is logged in
if (isset($_SESSION['admin_id'])) {
    include('../ua-auth/fetching_details.php');
    // User is logged in, fetch their details from the database


}else{
    include('http://localhost:80/step-upWholesale-git/step-upWholesale/auth/ua-auth/admin_auth.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Center - Wholesale Footwear Management</title>
    <?php include('http://localhost:80/step-upWholesale-git/step-upWholesale/includes/inc_styles.php'); ?>
</head>
<body>
    <div class="container mt-5">
        <!-- Welcome Section -->
        <h2 class="text-center">Welcome, <?php echo htmlspecialchars($adminDetails['adminname']);?></h2>
        <div class="card">
            <div class="card-body">
                <p><strong>Role :</strong> <?php echo htmlspecialchars($adminDetails['Role']); ?> (ID:<?php echo htmlspecialchars($adminDetails['admin_id']);?>)</p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($adminDetails['email']); ?></p>
                <p><strong>Mobile Number:</strong> <?php echo htmlspecialchars($adminDetails['mobile_number']); ?></p>
                <p><strong>Joined :</strong> <?php echo htmlspecialchars($adminDetails['CreatedDate']); ?></p>
                <p><strong>Last Login:</strong> <?php echo htmlspecialchars($adminDetails['LastLogin']); ?></p>
                <p><strong>Status :</strong> <?php echo htmlspecialchars($adminDetails['Status']); ?></p>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="mt-4 d-flex justify-content-between flex-wrap">
            <a href="edit_users.php" class="btn btn-primary mb-2">Manages Users</a>
            <a href="../../orders/order_history.php" class="btn btn-info mb-2">Order History</a>
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
            <a href="../../index.php" class="btn btn-primary mt-3">Home</a>    
            <!-- Logout Button -->
            <a href="../logout.php" class="btn btn-danger mt-3" onclick="return confirm('Are you sure you want to log out?');">Logout</a>
        </div>
    </div>
    <?php include('http://localhost:80/step-upWholesale-git/step-upWholesale/includes/inc_scripts.php'); ?>
</body>
</html>
