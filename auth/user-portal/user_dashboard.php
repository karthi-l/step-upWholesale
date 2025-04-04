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
    include('../ua-auth/fetching_details.php');
} else {
   include('../ua-auth/user_auth.php');
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
        <h2 class="text-center">Welcome, <?php echo htmlspecialchars($userDetails['username']); ?></h2>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Shop Name: <?php echo htmlspecialchars($userDetails['shop_name']); ?></h5>
                <p><strong>Shop Address:</strong> <?php echo htmlspecialchars($userDetails['shop_address']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($userDetails['email']); ?></p>
                <p><strong>Mobile Number:</strong> <?php echo htmlspecialchars($userDetails['mobile_number']); ?></p>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="mt-4 d-flex justify-content-between flex-wrap">
            <a href="edit_profile.php" class="btn btn-primary mb-2">Edit Profile</a>
            <a href="../change_password.php" class="btn btn-warning mb-2">Change Password</a>
            <a href="../../orders/order_history.php" class="btn btn-info mb-2">Order History</a>
            <a href="wishlist.php" class="btn btn-secondary mb-2">Wishlist</a>
            <a href="notifications.php" class="btn btn-light mb-2">Notifications</a>
            <a href="support.php" class="btn btn-outline-primary mb-2">Get Support</a>
        </div>
        <?php 
            $query = "SELECT 
            COUNT(order_id) AS total_orders, 
            SUM(bill_amount) AS total_bill, 
            MIN(bill_amount) AS min_bill, 
            MAX(bill_amount) AS max_bill, 
            AVG(bill_amount) AS avg_bill 
          FROM orders";

            $stmt = $conn->prepare($query);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            $totalOrders = $row['total_orders'] ?? 0;
            $totalBill = $row['total_bill'] ?? 0.00;
            $minBill = $row['min_bill'] ?? 0.00;
            $maxBill = $row['max_bill'] ?? 0.00;
            $avgBill = $row['avg_bill'] ?? 0.00;

        ?>
        <!-- Account Statistics -->
        <div class="card mt-4">
            <div class="card-body">
                <h5 class="card-title">Account Statistics</h5>
                <p><strong>Total Purchases:</strong> <?php echo $totalOrders;?></p>
                <p><strong>Total Amount Spent:</strong> &#8377;<?php echo $totalBill;?></p>
                <p><strong>Highest Bill Amount:</strong> &#8377;<?php echo $maxBill;?></p>
                <p><strong>Lowest Bill Amount:</strong> &#8377;<?php echo $minBill;?></p>
                <p><strong>Average Bill Amount:</strong> &#8377;<?php echo $avgBill;?></p>
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
