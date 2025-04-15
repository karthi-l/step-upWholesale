<?php

include('../includes/session_dbConn.php');
if(isset($_SESSION['user_id'])){
    $user_id = $_SESSION['user_id'];
    $order_query = "SELECT * FROM orders WHERE user_id = ?";
    $order_stmt = $conn->prepare($order_query);
    $order_stmt->bind_param('i',$user_id);
}elseif (isset($_SESSION['admin_id'])){
    $order_query = "SELECT * FROM orders";
    $order_stmt = $conn->prepare($order_query);

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_status'])) {
        $order_id = trim($_POST['order_id']);
        $deliverystatus = trim($_POST['delivery_status']); 
        $check_query = "SELECT delivery_status FROM orders WHERE order_id = ?";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param("i", $order_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        $order = $check_result->fetch_assoc();
        $current_status = $order['delivery_status'];

        $errors = [];

        if (empty($errors)) {
            if ($deliverystatus === 'Packaged' || $deliverystatus === 'Delivered') {
                $admin_id = $_SESSION['admin_id'] ?? null;
            }
            $allowed_transitions = [
                'Pending' => ['Packaged'],
                'Packaged' => ['Shipped'],
                'Shipped' => ['Delivered'],
                'Delivered' => []
            ];
            if (!isset($allowed_transitions[$current_status]) || !in_array($deliverystatus, $allowed_transitions[$current_status])) {
                $_SESSION['alertMessage'] = "Invalid status transition from $current_status to $deliverystatus.";
                $_SESSION['alertType'] = "danger";
                header("Location: order_history.php");
                exit();
            } else {
                if ($deliverystatus === 'Packaged') {
                    $query = "UPDATE orders SET order_updated_time = NOW(), delivery_status = ?, packaged_by = ?, order_packaged_time = NOW() WHERE order_id = ?";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("ssi", $deliverystatus, $admin_id, $order_id);
                } elseif ($deliverystatus === 'Delivered') {
                    $query = "UPDATE orders SET order_updated_time = NOW(), delivery_status = ?, delivered_by = ?, delivered_time = NOW() WHERE order_id = ?";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("ssi", $deliverystatus, $admin_id, $order_id);
                } else {
                    $query = "UPDATE orders SET order_updated_time = NOW(), delivery_status = ? WHERE order_id = ?";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("si", $deliverystatus, $order_id);
                }

                if ($stmt->execute()) {
                    $_SESSION['alertMessage'] = "Order updated successfully!";
                    $_SESSION['alertType'] = "success";
                    // Fetch the user_id for the order (if not already available)
                    $user_fetch_query = "SELECT user_id FROM orders WHERE order_id = ?";
                    $user_fetch_stmt = $conn->prepare($user_fetch_query);
                    $user_fetch_stmt->bind_param("i", $order_id);
                    $user_fetch_stmt->execute();
                    $user_result = $user_fetch_stmt->get_result();
                    $user_data = $user_result->fetch_assoc();
                    $user_id = $user_data['user_id'] ?? null;

                    if ($user_id) {
                        $title = "Order updated";
                        $message = "Your order #$order_id has been $deliverystatus.";
                        $target = "user";

                        $announcement_query = "INSERT INTO announcements (title, message, target, user_id, created_at) VALUES (?, ?, ?, ?, NOW())";
                        $announcement_stmt = $conn->prepare($announcement_query);
                        $announcement_stmt->bind_param("sssi", $title, $message, $target, $user_id);
                        $announcement_stmt->execute(); // You could also check if it's successful
                    }

                } else {
                    $_SESSION['alertMessage'] = "Failed to update order.";
                    $_SESSION['alertType'] = "danger";
                }
            }
        } else {
            $_SESSION['alertMessage'] = implode("<br>", $errors);
            $_SESSION['alertType'] = "danger";
            header("Location: order_history.php");
            exit();
        }

        $result = $conn->query("SELECT * FROM orders");
        $orders = $result->fetch_all(MYSQLI_ASSOC);
    }
}else{
    include("../auth/ua-auth/auth.php");
    exit;
}

$order_stmt->execute();
$orders = $order_stmt->get_result();

function getBadgeClass($status) {
    return match ($status) {
        'Pending' => 'warning',
        'Packaged' => 'info',
        'Shipped' => 'primary',
        'Delivered' => 'success',
        default => 'secondary',
    };
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History</title>
    <?php include('../includes/inc_styles.php');?>
</head>
<body>
<div class="container py-5">
        <div class="d-flex justify-content-center align-items-center mb-4">
            <h2 class="mb-4 text-center">
                <?= isset($_SESSION['user_id']) ? 'My Orders' : 'Manage Orders'; ?>
            </h2>
            <a href="../index.php" class="btn btn-primary ms-auto"><i class="bi bi-house-door-fill"></i> Home</a>
        </div>

    <?php if (isset($_SESSION['alertMessage'])): ?>
        <div class="alert alert-<?= $_SESSION['alertType']; ?> alert-dismissible fade show" role="alert">
            <?= $_SESSION['alertMessage']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['alertMessage'], $_SESSION['alertType']); ?>
    <?php endif; ?>

    <?php if ($orders->num_rows > 0): ?>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <?php while ($row = $orders->fetch_assoc()): ?>
                <?php
                    $username = 'Unknown';
                    $query = "SELECT username FROM usersretailers WHERE user_id = " . $row['user_id'] . " LIMIT 1";
                    $result = $conn->query($query);
                    if ($result && $result->num_rows > 0) {
                        $userData = $result->fetch_assoc();
                        $username = $userData['username'];
                    }

                    $adminname = 'N/A';
                    if (!empty($row['packaged_by'])) {
                        $admin_query = "SELECT adminname FROM admins WHERE admin_id = " . $row['packaged_by'] . " LIMIT 1";
                        $admin_result = $conn->query($admin_query);
                        if ($admin_result && $admin_result->num_rows > 0) {
                            $admin = $admin_result->fetch_assoc();
                            $adminname = $admin['adminname'];
                        }
                    }
                ?>
                <div class="col">
                    <div class="card shadow h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Order #<?= $row['order_id'] ?></h5>
                            <span class="badge bg-<?= getBadgeClass($row['delivery_status']) ?>">
                                <?= $row['delivery_status'] ?>
                            </span>
                        </div>
                        <div class="card-body">
                            <p><strong>Ordered By:</strong> <?= htmlspecialchars($username) ?></p>
                            <p><strong>Order Time:</strong> <?= $row['order_placed_time'] ?><br><strong>Updated:</strong> <?= $row['order_updated_time'] ?></p>
                            <p><strong>Packaged:</strong> <?= $row['order_packaged_time'] ?: 'N/A' ?><br><strong>Delivered:</strong> <?= $row['delivered_time'] ?: 'N/A' ?></p>
                            <p><strong>Packaged By:</strong> <?= htmlspecialchars($adminname) ?></p>
                            <p><strong>Bill:</strong> ₹<?= number_format($row['bill_amount'], 2) ?></p>
                            <p><strong>Estimated Delivery:</strong> <?= $row['estimated_delivery_date'] ?></p>
                            <?php if (isset($_SESSION['user_id']) && $row['bill_file']): ?>
                                <a href="#" 
                                class="btn btn-sm btn-outline-primary" 
                                data-bs-toggle="modal" 
                                data-bs-target="#invoiceModal" 
                                data-file="../products/<?= $row['bill_file'] ?>">
                                View Invoice
                                </a>
                            <?php endif; ?>
                        </div>
                        <?php if (isset($_SESSION['admin_id'])): ?>
                            <div class="card-footer text-end d-flex justify-content-between">
                            <a href="#" 
                            class="btn btn-sm btn-outline-primary" 
                            data-bs-toggle="modal" 
                            data-bs-target="#invoiceModal" 
                            data-file="../products/<?= $row['bill_file'] ?>">
                            View Invoice
                            </a>
                            <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editStatusModal<?= $row['order_id'] ?>">Edit Status</button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <!-- Invoice Modal -->
                <div class="modal fade" id="invoiceModal" tabindex="-1" aria-labelledby="invoiceModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-centered">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="invoiceModalLabel">Invoice Preview</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <iframe id="invoiceFrame" src="" width="100%" height="600px" style="border: none;"></iframe>
                    </div>
                    </div>
                </div>
                </div>

                <?php if (isset($_SESSION['admin_id'])): ?>
                    <div class="modal fade" id="editStatusModal<?= $row['order_id'] ?>" tabindex="-1" aria-labelledby="editStatusModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Update Order #<?= $row['order_id'] ?> Status</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form method="POST">
                                        <input type="hidden" name="order_id" value="<?= $row['order_id'] ?>">
                                        <div class="mb-3">
                                            <label for="delivery_status" class="form-label">Delivery Status</label>
                                            <select class="form-select" name="delivery_status">
                                                <?php
                                                    $statuses = ['Pending', 'Packaged', 'Shipped', 'Delivered'];
                                                    foreach ($statuses as $status) {
                                                        $selected = ($status === $row['delivery_status']) ? 'selected' : '';
                                                        echo "<option value='$status' $selected>$status</option>";
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="text-center">
                                            <button type="submit" name="edit_status" class="btn btn-success">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info text-center">No orders found.</div>
    <?php endif; ?>

    <div class="text-end mt-4">
        <a href="../index.php" class="btn btn-primary">Back to Home</a>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
  var invoiceModal = document.getElementById('invoiceModal');
  invoiceModal.addEventListener('show.bs.modal', function (event) {
    var button = event.relatedTarget;
    var file = button.getAttribute('data-file');
    var iframe = invoiceModal.querySelector('#invoiceFrame');
    iframe.src = file;
  });

  // Optional: clear iframe on modal hide to stop playing PDFs or releasing memory
  invoiceModal.addEventListener('hidden.bs.modal', function () {
    var iframe = invoiceModal.querySelector('#invoiceFrame');
    iframe.src = '';
  });
});
</script>

<?php include('../includes/inc_scripts.php'); ?>
</body>
</html>