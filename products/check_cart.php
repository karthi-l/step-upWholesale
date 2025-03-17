<?php

include("../includes/session_dbConn.php");

$user_id = isset($_POST['user_id']) ? $_POST['user_id'] : null;

if ($user_id !== null) {
    $cartItems = [];
    
    $query = "SELECT model_id FROM user_cart WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $cartItems[] = $row;
    }

    echo json_encode($cartItems);
}
?>
