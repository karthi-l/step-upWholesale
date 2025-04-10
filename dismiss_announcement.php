<?php
include('includes/session_dbConn.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? 0;
    $id = (int)$id;
    if ($id > 0) {
        $stmt = $conn->prepare("UPDATE announcements SET is_read = 1 WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }
}
