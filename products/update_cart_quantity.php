<?php
include ("../includes/session_dbConn.php"); // Ensure correct path

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = $_SESSION['user_id'] ?? null; // Assuming user is logged in
    $model_id = $_POST['model_id'] ?? null;
    $quantity = $_POST['quantity'] ?? null;

    if ($user_id !== null && $model_id !== null && is_numeric($quantity) && $quantity > 0) {
        $query = "UPDATE user_cart SET quantity = ? WHERE user_id = ? AND model_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iii", $quantity, $user_id, $model_id);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success"]);
        } else {
            echo json_encode(["status" => "error", "message" => $stmt->error]);
        }
        $stmt->close();
    } else {
        echo json_encode(["status" => "invalid_request", "message" => "Missing or invalid parameters"]);
    }
} else {
    echo json_encode(["status" => "invalid_method", "message" => "Only POST requests are allowed"]);
}
?>