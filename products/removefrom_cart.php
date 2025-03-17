<?php
include ("../includes/session_dbConn.php"); // Ensure correct path to your DB connection

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : null;
    $model_id = isset($_POST['model_id']) ? $_POST['model_id'] : null;

    if ($user_id !== null && $model_id !== null) {
        $query = "DELETE FROM user_cart WHERE user_id = ? AND model_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $user_id, $model_id);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success"]);
        } else {
            echo json_encode(["status" => "error"]);
        }
    } else {
        echo json_encode(["status" => "invalid_request"]);
    }
}
?>
