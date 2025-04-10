<?php
require '../includes/session_dbConn.php';

$user_id = $_GET['user_id'] ?? 0;

$stmt = $conn->prepare("
    SELECT o.user_id, COUNT(DISTINCT o.order_id) AS total_orders, 
           MAX(o.order_placed_time) AS last_order,
           SUM(oi.quantity * oi.unit_price) AS total_spent,
           oi.model_id, SUM(oi.quantity) AS product_qty
    FROM orders o
    JOIN order_items oi ON o.order_id = oi.order_id
    WHERE o.user_id = ?
    GROUP BY oi.model_id
    ORDER BY product_qty DESC
    LIMIT 1
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

if ($row) {
    echo "<ul class='list-group'>
        <li class='list-group-item'><strong>User ID:</strong> {$row['user_id']}</li>
        <li class='list-group-item'><strong>Total Orders:</strong> {$row['total_orders']}</li>
        <li class='list-group-item'><strong>Total Spent:</strong> ₹" . number_format($row['total_spent']) . "</li>
        <li class='list-group-item'><strong>Most Ordered Product:</strong> {$row['model_id']} ({$row['product_qty']} qty)</li>
        <li class='list-group-item'><strong>Last Order Date:</strong> {$row['last_order']}</li>
    </ul>";
} else {
    echo "<p class='text-danger'>No data found for this user.</p>";
}
?>
