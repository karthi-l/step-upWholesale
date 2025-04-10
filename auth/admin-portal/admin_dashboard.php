<?php
include('../../includes/session_dbConn.php');

if (isset($_SESSION['admin_id'])) {
    include('../ua-auth/fetching_details.php');
} else {
    include('http://localhost:80/step-upWholesale-git/step-upWholesale/auth/ua-auth/admin_auth.php');
    exit;
}
$orders = $conn->query("SELECT COUNT(*)as totalOrders, SUM(totalnos) as totalQuantitySold, AVG(totalnos) as avgQuantity, AVG(bill_amount) as avgAmount FROM orders");
$orderresult = $orders->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Center - Wholesale Footwear Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <?php include('http://localhost:80/step-upWholesale-git/step-upWholesale/includes/inc_styles.php'); ?>
    <style>
        .dashboard-card {
            transition: transform 0.2s ease-in-out;
        }
        .dashboard-card:hover {
            transform: scale(1.03);
        }
        .action-card img {
            height: 80px;
            width: auto;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <!-- Welcome Section -->
<!-- Welcome Banner -->
<div class="bg-primary text-white rounded-3 p-4 mb-4 shadow text-center">
    <h2 class="mb-1">Welcome, <?php echo htmlspecialchars($adminDetails['adminname']); ?> 👋</h2>
    <p class="text-light mb-0">Managing your wholesale operations with ease</p>
</div>

<!-- Admin Details Section -->
    <div class="row g-4 mb-4">

        <!-- Account Info Card -->
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title mb-3"><i class="bi bi-person-circle me-2"></i>Account Information</h5>
                    <ul class="list-unstyled">
                        <li><strong>Role:</strong> <?php echo htmlspecialchars($adminDetails['Role']); ?> <small class="text-muted">(ID: <?php echo htmlspecialchars($adminDetails['admin_id']); ?>)</small></li>
                        <li><strong>Email:</strong> <?php echo htmlspecialchars($adminDetails['email']); ?></li>
                        <li><strong>Mobile:</strong> <?php echo htmlspecialchars($adminDetails['mobile_number']); ?></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Other Details Card -->
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title mb-3"><i class="bi bi-clock-history me-2"></i>Activity</h5>
                    <ul class="list-unstyled">
                        <li><strong>Joined:</strong> <?php echo htmlspecialchars($adminDetails['CreatedDate']); ?></li>
                        <li><strong>Last Login:</strong> <?php echo htmlspecialchars($adminDetails['LastLogin']); ?></li>
                        <li>
                            <strong>Status:</strong>
                            <?php
                            $status = htmlspecialchars($adminDetails['Status']);
                            $badgeClass = $status === 'Active' ? 'bg-success' : 'bg-danger';
                            ?>
                            <span class="badge <?php echo $badgeClass; ?>"><?php echo $status; ?></span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

    </div>


        <!-- Dashboard Actions -->
        <div class="row text-center mb-4">
            <div class="col-md-3 mb-3">
                <div class="card dashboard-card shadow action-card p-3">
                    <img src="https://cdn-icons-png.flaticon.com/512/2920/2920277.png" alt="Manage Users" class="mx-auto">
                    <div class="card-body">
                        <a href="edit_users.php" class="stretched-link text-decoration-none fw-bold">Manage Users</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card dashboard-card shadow action-card p-3">
                    <img src="https://cdn-icons-png.flaticon.com/512/2203/2203183.png" alt="Order History" class="mx-auto">
                    <div class="card-body">
                        <a href="../../orders/order_history.php" class="stretched-link text-decoration-none fw-bold">Manage Order</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card dashboard-card shadow action-card p-3">
                    <img src="https://cdn-icons-png.flaticon.com/512/1827/1827301.png" alt="Notifications" class="mx-auto">
                    <div class="card-body">
                        <a href="manage_announcement.php" class="stretched-link text-decoration-none fw-bold">Announcements</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card dashboard-card shadow action-card p-3">
                    <img src="https://cdn-icons-png.flaticon.com/512/726/726623.png" alt="Support" class="mx-auto">
                    <div class="card-body">
                        <a href="../../get_support.php" class="stretched-link text-decoration-none fw-bold">View FAQ</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-6 mb-4">
                <div class="card dashboard-card shadow text-center p-4">
                    <i class="bi bi-box-seam fs-1 text-primary mb-2"></i>
                    <h5>Total Orders Received</h5>
                    <p class="fs-4 fw-bold"><?php echo $orderresult['totalOrders']; ?></p>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card dashboard-card shadow text-center p-4">
                    <i class="bi bi-bar-chart-line-fill fs-1 text-success mb-2"></i>
                    <h5>Total Quantity Sold</h5>
                    <p class="fs-4 fw-bold"><?php echo $orderresult['totalQuantitySold']; ?></p>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card dashboard-card shadow text-center p-4">
                    <i class="bi bi-graph-up-arrow fs-1 text-warning mb-2"></i></i>
                    <h5>Average Sale per Order</h5>
                    <p class="fs-4 fw-bold"><?php echo floor($orderresult['avgQuantity']); ?></p>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card dashboard-card shadow text-center p-4">
                    <i class="bi bi-currency-exchange fs-1 text-danger mb-2"></i>
                    <h5>Average Amount per order</h5>
                    <p class="fs-4 fw-bold"><?php echo floor($orderresult['avgAmount']); ?></p>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card dashboard-card shadow text-center p-4">
                    <i class="bi bi-receipt-cutoff"></i>
                    <h5>Sales Report</h5>
                    <div class="btn-btn-primary"><a class="text-decoration-none" href="../../orders/sales_report.php">Sales</a></div>
                </div>
            </div>
        </div>

        <!-- Home and Logout -->
        <div class="d-flex justify-content-between mt-4">
            <a href="../../index.php" class="btn btn-outline-primary">
                <i class="bi bi-house-door-fill"></i> Home
            </a>
            <a href="../logout.php" class="btn btn-outline-danger" onclick="return confirm('Are you sure you want to log out?');">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php include('http://localhost:80/step-upWholesale-git/step-upWholesale/includes/inc_scripts.php'); ?>
</body>
</html>
