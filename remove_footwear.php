<?php
session_start();
include('db_connect.php');

if (isset($_GET['model_id'])) {
    $model_id = intval($_GET['model_id']);

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

        // Redirect back with a success message
        $_SESSION['success_message'] = "Model removed successfully.";
        header("Location: fetch_footwear.php");
    } catch (Exception $e) {
        // Rollback transaction in case of an error
        $conn->rollback();

        // Redirect back with an error message
        $_SESSION['error_message'] = "Failed to remove the model: " . $e->getMessage();
        header("Location: fetch_footwear.php");
    }
} else {
    // Redirect back if no model_id is provided
    $_SESSION['error_message'] = "Invalid model selected.";
    header("Location: fetch_footwear.php");
}

exit();
?>
    