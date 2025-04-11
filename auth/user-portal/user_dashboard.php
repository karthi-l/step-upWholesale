<?php
// Start session to access session variables
include('../../includes/session_dbConn.php');

if(isset($_SESSION['admin_id'])){
    header('Location:admin_dashboard.php');
}

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
  <title>User Dashboard - Step-up Wholesale</title>
  <?php include('../../includes/inc_styles.php'); ?>
  <style>
    .card-hover:hover {
      box-shadow: 0 0 20px rgba(0,0,0,0.1);
      transform: scale(1.01);
      transition: all 0.3s ease-in-out;
    }
    .icon {
      font-size: 1.5rem;
    }
    .sm-button {
      width: 22%;
      min-width: 150px;
    }
    .stats-box {
      background: #f8f9fa;
      border-radius: 12px;
      padding: 20px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
  </style>
</head>
<body>
  <div class="container py-5">
    <h2 class="text-center mb-2">Welcome, <?php echo htmlspecialchars($userDetails['username']); ?>!</h2>
    <p class="text-muted text-center">Glad to see you again!</p>

    <div class="row g-4">
      <!-- Profile Card -->
      <div class="col-md-6">
        <div class="card card-hover">
          <div class="card-header bg-primary text-white">Your Profile</div>
          <div class="card-body">
            <p><i class="bi bi-shop icon me-2"></i><strong>Shop Name:</strong> <?= htmlspecialchars($userDetails['shop_name']) ?></p>
            <p><i class="bi bi-geo-alt icon me-2"></i><strong>Address:</strong> <?= htmlspecialchars($userDetails['shop_address']) ?></p>
            <p><i class="bi bi-envelope icon me-2"></i><strong>Email:</strong> <?= htmlspecialchars($userDetails['email']) ?></p>
            <p><i class="bi bi-phone icon me-2"></i><strong>Mobile:</strong> <?= htmlspecialchars($userDetails['mobile_number']) ?></p>
            <p><i class="bi bi-person-badge icon me-2"></i><strong>Tier:</strong> <?= htmlspecialchars($userDetails['userType']) ?></p>
            <p><i class="bi bi-calendar-check icon me-2"></i><strong>Joined:</strong> <?= htmlspecialchars($userDetails['userCreationDate']) ?></p>
            <p><i class="bi bi-clock-history icon me-2"></i><strong>Last Login:</strong> <?= htmlspecialchars($userDetails['LastLogin']) ?></p>
          </div>
        </div>
      </div>
      <?php 
        $user_id = $_SESSION['user_id'];
        $query = "SELECT 
        COUNT(order_id) AS total_orders, 
        SUM(bill_amount) AS total_bill, 
        MIN(bill_amount) AS min_bill, 
        MAX(bill_amount) AS max_bill, 
        AVG(bill_amount) AS avg_bill,
        SUM(totalNos) AS total_nos
        FROM orders WHERE user_id = ?";

        $stmt = $conn->prepare($query);
        $stmt->bind_param('i',$user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        $totalOrders = $row['total_orders'] ?? 0;
        $totalBill = $row['total_bill'] ?? 0.00;
        $minBill = $row['min_bill'] ?? 0.00;
        $maxBill = $row['max_bill'] ?? 0.00;
        $avgBill = $row['avg_bill'] ?? 0.00;
        $total_nos = $row['total_nos'] ?? 0;
    ?>
      <!-- Statistics Card -->
      <div class="col-md-6">
        <div class="card card-hover">
          <div class="card-header bg-info text-white">Account Statistics</div>
          <div class="card-body">
            <p><i class="bi bi-bag-check icon me-2"></i><strong>Total Purchases:</strong> <?= $totalOrders ?></p>
            <p><i class="bi bi-currency-rupee icon me-2"></i><strong>Total Spent:</strong> &#8377;<?= number_format($totalBill,2); ?></p>
            <p><i class="bi bi-arrow-up-circle icon me-2"></i><strong>Max Bill:</strong> &#8377;<?= number_format($maxBill,2); ?></p>
            <p><i class="bi bi-arrow-down-circle icon me-2"></i><strong>Min Bill:</strong> &#8377;<?= number_format($minBill,2); ?></p>
            <p><i class="bi bi-bar-chart-line icon me-2"></i><strong>Avg Bill:</strong> &#8377;<?= number_format($avgBill,2); ?></p>
            <p><i class="bi bi-box-seam icon me-2"></i><strong>Total Quanitiy purchased:</strong> &#8377;<?= $total_nos ?></p>
          </div>
        </div>
      </div>

      <!-- Action Cards -->
      <div class="col-12">
        <div class="row g-3 justify-content-center">
          <div class="col-sm-6 col-lg-3">
            <a href="edit_profile.php" class="btn btn-outline-primary w-100 py-3"><i class="bi bi-pencil-square me-2"></i>Edit Profile</a>
          </div>
          <div class="col-sm-6 col-lg-3">
            <a href="../change_password.php" class="btn btn-outline-warning w-100 py-3"><i class="bi bi-lock me-2"></i>Change Password</a>
          </div>
          <div class="col-sm-6 col-lg-3">
            <a href="../../get_support.php" class="btn btn-outline-success w-100 py-3"><i class="bi bi-question-circle me-2"></i>Get Support</a>
          </div>
          <div class="col-sm-6 col-lg-3">
            <a href="../../my_announcements.php" class="btn btn-outline-dark w-100 py-3"><i class="bi bi-bell me-2"></i>Announcements</a>
          </div>
        </div>
      </div>

      <div class="col-12 mt-3">
        <div class="row g-3 justify-content-center">
          <div class="col-sm-6 col-lg-3">
            <a href="../../orders/order_history.php" class="btn btn-info w-100 py-3 text-white"><i class="bi bi-clock me-2"></i>Order History</a>
          </div>
          <div class="col-sm-6 col-lg-3">
            <a href="../../products/products.php" class="btn btn-primary w-100 py-3 text-white"><i class="bi bi-box-seam me-2"></i>Products</a>
          </div>
          <div class="col-sm-6 col-lg-3">
            <a href="../../products/user_cart.php" class="btn btn-secondary w-100 py-3 text-white"><i class="bi bi-cart3 me-2"></i>Cart</a>
          </div>
          <div class="col-sm-6 col-lg-3">
            <a href="wishlist.php" class="btn btn-secondary w-100 py-3 text-white"><i class="bi bi-heart me-2"></i>Wishlist</a>
          </div>
        </div>
      </div>

      <!-- Home and Logout -->
      <div class="col-12 mt-4 d-flex justify-content-between">
        <a href="../../index.php" class="btn btn-outline-primary"><i class="bi bi-house-door me-2"></i>Home</a>
        <a href="../logout.php" class="btn btn-danger" onclick="return confirm('Are you sure you want to log out?');">
          <i class="bi bi-box-arrow-right me-2"></i>Logout
        </a>
      </div>
    </div>
  </div>

  <?php include('../../includes/inc_scripts.php'); ?>
</body>
</html>


