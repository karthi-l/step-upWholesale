<?php
include('../includes/session_dbConn.php');

include('../auth/ua-auth/admin_auth.php');

// Set the content type to application/json
header('Content-Type: application/json');

if (isset($_POST['model_id'])) {
    $model_id = intval($_POST['model_id']);

    // Start transaction
    $conn->begin_transaction();

    try {
        // Delete from footwear_stock table
        $delete_stock_query = "DELETE FROM footwear_stock WHERE model_id = ?";
        $stock_stmt = $conn->prepare($delete_stock_query);
        $stock_stmt->bind_param("i", $model_id);
        $stock_stmt->execute();

        // Delete from footwear_models table
        $delete_model_query = "DELETE FROM footwear_models WHERE model_id = ?";
        $model_stmt = $conn->prepare($delete_model_query);
        $model_stmt->bind_param("i", $model_id);
        $model_stmt->execute();

        // Commit transaction
        $conn->commit();

        // Send a JSON success response
        echo json_encode(['success' => true, 'message' => 'Model removed successfully.']);
    } catch (Exception $e) {
        // Rollback transaction in case of an error
        $conn->rollback();

        // Send a JSON error response
        echo json_encode(['success' => false, 'message' => 'Failed to remove the model: ' . $e->getMessage()]);
    }
} else {
    // Send a JSON error response if no model_id is provided
    echo json_encode(['success' => false, 'message' => 'Invalid model selected.']);
}

exit();
?>