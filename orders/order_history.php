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
        <h2 class="mb-4">Order History</h2>
        <?php if ($orders->num_rows > 0) { ?>
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Order ID</th>
                        <th>Order Placed</th>
                        <th>Updated Time</th>
                        <th>Packaged Time</th>
                        <th>Packaged By</th>
                        <th>Bill Amount</th>
                        <th>Invoice</th>
                        <th>Status</th>
                        <th>Delivered Time</th>
                        <th>Estimated Delivery</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $orders->fetch_assoc()) { ?>
                        <tr>
                            <td><?= $row['order_id'] ?></td>
                            <td><?= $row['order_placed_time'] ?></td>
                            <td><?= $row['order_updated_time'] ?></td>
                            <td><?= $row['order_packaged_time'] ?: 'N/A' ?></td>
                            <td><?= $row['packaged_by'] ?: 'N/A' ?></td>
                            <td>₹<?= number_format($row['bill_amount'], 2) ?></td>
                            <td>
                                <?php if ($row['bill_file']) { ?>
                                    <a href="../products/<?= $row['bill_file'] ?>" class="btn btn-sm btn-primary" target="_blank">View</a>
                                <?php } else { echo 'N/A'; } ?>
                            </td>
                            <td>
                                <span class="badge bg-<?= getBadgeClass($row['delivery_status']) ?>">
                                    <?= $row['delivery_status'] ?>
                                </span>
                            </td>
                            <td><?= $row['delivered_time'] ?: 'N/A' ?></td>
                            <td><?= $row['estimated_delivery_date'] ?></td>
                        </tr>
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