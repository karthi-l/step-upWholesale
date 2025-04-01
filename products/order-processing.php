<?php
include("../includes/session_dbConn.php"); // Your database connection

$user_id = $_SESSION['user_id']; // Assuming user is logged in
$invoiceNo = $_SESSION['invno'];
$billNo = $_SESSION['billno'];
$orderNo = $_SESSION['orderno'];
$bill_file = 'invoices/invoice_'.$invoiceNo.'_'.$billNo.'_'.$orderNo.'.pdf';
$grand_total = $_SESSION['grandTotal']; 

$today = new DateTime();
$saturday = new DateTime('next saturday');
if ($today->format('l') === 'Saturday') {
    $saturday->modify('+7 days');
}
$estimated_delivery_date = $saturday->format('Y-m-d');

$conn->begin_transaction();
try {
        // Step 1: Insert Order into orders table
    $insert_order = "
    INSERT INTO orders (user_id, order_placed_time,order_updated_time, bill_amount, bill_file, delivery_status, estimated_delivery_date) 
    VALUES (?, NOW(), NOW(), ?, ?, 'Pending', ?)";


    $order_stmt = $conn->prepare($insert_order);
    $order_stmt->bind_param("idss", $user_id, $grand_total, $bill_file, $estimated_delivery_date);
    $order_stmt->execute();
    $order_id = $order_stmt->insert_id;
    $order_stmt->close();

    if (!$order_id) {
        throw new Exception("Failed to create order.");
    }
    // Update stock only if stock_quantity > cart quantity
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
        // Clear cart only if stock update was successful
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
