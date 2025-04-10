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

        // Validate input
        $errors = [];
    
        if (empty($errors)) {
            // Fetch admin details if needed
            if ($deliverystatus === 'Packaged' || $deliverystatus === 'Delivered') {
                $admin_id = $_SESSION['admin_id'] ?? null;
            }
            $allowed_transitions = [
                'Pending' => ['Packaged'],
                'Packaged' => ['Shipped'],
                'Shipped' => ['Delivered'],
                'Delivered' => [] // No further updates after Delivered
            ];
            if (!isset($allowed_transitions[$current_status]) || !in_array($deliverystatus, $allowed_transitions[$current_status])) {
                $_SESSION['alertMessage'] = "Invalid status transition from $current_status to $deliverystatus.";
                $_SESSION['alertType'] = "danger";
                header("Location: order_history.php");
                exit();
            } else {
            // Prepare update query
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
    
        // Refresh user list
        $result = $conn->query("SELECT * FROM orders");
        $orders = $result->fetch_all(MYSQLI_ASSOC);
    }
}else{
    include("../auth/ua-auth/auth.php");
    exit;
}

$order_stmt->execute();
$orders = $order_stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order -History</title>
    <?php include('../includes/inc_styles.php');?>
</head>
<body>
<div class="container mt-5">
        <h2 class="mb-4">
            <?php if(isset($_SESSION['user_id'])){
                echo "My Orders";
            }elseif(isset($_SESSION['admin_id'])){
                echo "Manage Orders";
            }
            ?>    
        </h2>
        <?php if (isset($_SESSION['alertMessage'])) { ?>
            <div class="alert alert-<?= $_SESSION['alertType']; ?>"><?= $_SESSION['alertMessage']; ?></div>
            <?php unset($_SESSION['alertMessage'], $_SESSION['alertType']); // Clear after showing ?>
        <?php } ?>
        <?php if ($orders->num_rows > 0) { ?>
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <?php if(isset($_SESSION['admin_id'])){ ?>
                            <th>Order ID</th>
                            <th>Orderd By</th>
                            <th>Ordered Time<br>Updated Time</th>
                            <th>Packaged Time <br>Delivered Time</th>
                            <th>Packaged By</th>
                            <th>Bill Amount</th>
                            <th>Status</th>
                            <th>Estimated Delivery</th>
                            <th>Action</th>
                        <?php }elseif(isset($_SESSION['user_id'])){ ?>
                            <th>Order ID</th>
                            <th>Orderd By</th>
                            <th>Ordered Time<br>Updated Time</th>
                            <th>Packaged Time<br>Delivered Time</th>
                            <th>Packaged By</th>
                            <th>Bill Amount</th>
                            <th>Invoice</th>
                            <th>Status</th>
                            <th>Estimated Delivery</th>
                        <?php }?>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $orders->fetch_assoc()) { ?>
                        <tr>
                            <td><?= $row['order_id'] ?></td>
                            <?php
                                $query= "SELECT username FROM usersretailers WHERE user_id = ".$row['user_id'] ." LIMIT 1";
                                $result = $conn->query($query);
                                $username = $result->fetch_assoc();
                            ?>
                            <td><?= $username['username'] ?></td>
                            <td><?= $row['order_placed_time'] ?><br><?= $row['order_updated_time'] ?></td>
                            <td><?= $row['order_packaged_time'] ?: 'N/A' ?><br><?= $row['delivered_time'] ?: 'N/A' ?></td>
                            <?php
                                if(isset($row['packaged_by'])){
                                    $query = "SELECT adminname FROM admins WHERE admin_id = ".$row['packaged_by']." LIMIT 1";
                                    $result = $conn->query($query); 
                                    if ($result) {
                                        $admin = $result->fetch_assoc();
                                        $adminname = $admin['adminname'] ?? 'N/A';
                                    } else {
                                        $adminname = 'N/A';
                                    }
                                } else{
                                    $adminname = 'N/A';
                                }
                                ?>

                            <td><?= $adminname ?></td>
                            <td>₹<?= number_format($row['bill_amount'], 2) ?></td>
                            <?php if(isset($_SESSION['user_id'])){ ?>
                            <td>
                                <?php if ($row['bill_file']) { ?>
                                    <a href="../products/<?= $row['bill_file'] ?>" class="btn btn-sm btn-primary" target="_blank">View</a>
                                <?php } else { echo 'N/A'; } ?>
                            </td>
                            <?php } ?>
                            <td>
                                <span class="badge bg-<?= getBadgeClass($row['delivery_status']) ?>">
                                    <?= $row['delivery_status'] ?>
                                </span>
                            </td>
                            <td><?= $row['estimated_delivery_date'] ?></td>
                            <?php if(isset($_SESSION['admin_id'])){?>
                                <td><button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editStatusModal<?php echo $row['order_id']; ?>">Edit</button></td>
                            <?php }?>
                        </tr>
                        <?php if(isset($_SESSION['admin_id'])){?>
                            <div class="modal fade" id="editStatusModal<?php echo $row['order_id']; ?>" tabindex="-1" aria-labelledby="editStatusModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit User</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form method="POST" action="">
                                            <input type="hidden" name="order_id" value="<?php echo $row['order_id']; ?>">
                                                <div class="mb-3">
                                                    <label for="delivery_status" class="form-label">Delivery Status</label>
                                                    <select class="form-select" aria-label="Default select example" name="delivery_status">
                                                        <option selected value="<?php echo htmlspecialchars($row['delivery_status']);?>"><?php echo htmlspecialchars($row['delivery_status']);?></option>
                                                        <option value="Pending">Pending</option>
                                                        <option value="Packaged">Packaged</option>
                                                        <option value="Shipped">Shipped</option>
                                                        <option value="Delivered">Delivered</option>
                                                    </select>
                                                </div>
                                                <div class="text-center">
                                                    <?php if ($row['bill_file']) { ?>
                                                        <a href="../products/<?= $row['bill_file'] ?>" class="btn btn-primary mb-3" target="_blank">View Invoice</a>
                                                    <?php } else { echo 'N/A'; } ?>
                                                </div>
                                                <div class="text-center">
                                                    <button type="submit" name="edit_status" class="btn btn-success">Save Changes</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    <?php } ?>
                </tbody>
            </table>
        <?php } else { ?>
            <div class="alert alert-info">No orders found.</div>
        <?php } ?>
        <div class="d-flex justify-content-end">
            <a href="../index.php" class="btn btn-primary">Home</a>
        </div>
    </div>
<?php include('../includes/inc_scripts.php');?>
<?php
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
</body>
</html>