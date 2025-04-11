<?php
include('../../includes/session_dbConn.php');
if (!isset($_SESSION['admin_id'])) {
    include('../ua-auth/admin_auth.php');
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $requestId = $_POST['request_id'];
    $response = $_POST['response'];

    $stmt = $conn->prepare("UPDATE support_requests SET response = ?, is_responded = 1, responded_at = NOW() WHERE id = ?");
    $stmt->bind_param("si", $response, $requestId);

    if ($stmt->execute()) {
        header("Location: view_support.php?success=1");
        exit;
    } else {
        echo "Error updating response.";
    }
}
?>
