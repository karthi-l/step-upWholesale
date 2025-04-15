<?php
include("../includes/session_dbConn.php");

$user_id = $_SESSION['user_id'];
$invoiceNo = $_SESSION['invno'];
$billNo = $_SESSION['billno'];
$orderNo = $_SESSION['orderno'];
$bill_file = 'invoices/invoice_' . $invoiceNo . '_' . $billNo . '_' . $orderNo . '.pdf';
$grand_total = $_SESSION['grandTotal'];
$totalnos = $_SESSION['totalnos'];

$today = new DateTime();
$saturday = new DateTime('next saturday');
if ($today->format('l') === 'Saturday') {
    $saturday->modify('+7 days');
}
$estimated_delivery_date = $saturday->format('Y-m-d');

$conn->begin_transaction();
try {
    // Step 1: Insert Order
    $insert_order = "
        INSERT INTO orders (user_id, order_placed_time, order_updated_time, totalnos, bill_amount, bill_file, delivery_status, estimated_delivery_date) 
        VALUES (?, NOW(), NOW(), ?, ?, ?, 'Pending', ?)";
    $order_stmt = $conn->prepare($insert_order);
    $order_stmt->bind_param("iidss", $user_id, $totalnos, $grand_total, $bill_file, $estimated_delivery_date);
    $order_stmt->execute();
    $order_id = $order_stmt->insert_id;
    $order_stmt->close();

    if (!$order_id) {
        throw new Exception("Failed to create order.");
    }

    // Step 2: Insert order items
    $fetch_cart_items = "
        SELECT uc.model_id, uc.quantity, fm.price, fs.size_variation, svn.nos_in_set
        FROM user_cart uc 
        JOIN footwear_models fm ON uc.model_id = fm.model_id 
        JOIN footwear_stock fs ON uc.model_id = fs.model_id
        JOIN size_variation_nos svn ON fs.size_variation = svn.size_set
        WHERE uc.user_id = ?";
    $stmt_items = $conn->prepare($fetch_cart_items);
    $stmt_items->bind_param("i", $user_id);
    $stmt_items->execute();
    $result = $stmt_items->get_result();

    $insert_item = $conn->prepare("
        INSERT INTO order_items (order_id, user_id, model_id, quantity, unit_price) 
        VALUES (?, ?, ?, ?, ?)
    ");

    $items_inserted = 0;
    while ($item = $result->fetch_assoc()) {
        $totalNos = $item['quantity'] * $item['nos_in_set'];
        $insert_item->bind_param("iiiid", $order_id, $user_id, $item['model_id'], $totalNos, $item['price']);
        if ($insert_item->execute()) {
            $items_inserted++;
        }
    }

    $insert_item->close();
    $stmt_items->close();

    // Only continue if items were inserted
    if ($items_inserted === 0) {
        throw new Exception("Failed to insert order items.");
    }

    // Step 3: Update stock
    $update_stock = "
        UPDATE footwear_stock fs
        JOIN user_cart uc ON fs.model_id = uc.model_id
        SET fs.stock = fs.stock - uc.quantity
        WHERE uc.user_id = ? AND fs.stock >= uc.quantity;
    ";
    $stmt = $conn->prepare($update_stock);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        // Step 4: Clear cart
        $delete_cart = "DELETE FROM user_cart WHERE user_id = ?";
        $delete_stmt = $conn->prepare($delete_cart);
        $delete_stmt->bind_param("i", $user_id);
        $delete_stmt->execute();
        $delete_stmt->close();

        $conn->commit();
        header("Location:../orders/order_history.php");
    } else {
        throw new Exception("Not enough stock for some items. Order not placed.");
    }

    $stmt->close();
} catch (Exception $e) {
    $conn->rollback();
    echo "Order failed: " . $e->getMessage();
}

$conn->close();
?>
