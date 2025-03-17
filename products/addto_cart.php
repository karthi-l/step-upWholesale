<?php 


include("../includes/session_dbConn.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['model_id'], $_POST['quantity'], $_POST['user_id'])) {
        $model_id = intval($_POST['model_id']);
        $quantity = intval($_POST['quantity']);
        $user_id = intval($_POST['user_id']);

        if ($model_id > 0 && $quantity > 0 && $user_id > 0) {
            $stmt = $conn->prepare("INSERT INTO user_cart (user_id, model_id, quantity) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE quantity = quantity + ?");
            $stmt->bind_param("iiii", $user_id, $model_id, $quantity, $quantity);

            if ($stmt->execute()) {
                echo "success";
            } else {
                echo "error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "invalid input";
        }
    } else {
        echo "missing parameters";
    }
} else {
    echo "invalid request";
}
?>
