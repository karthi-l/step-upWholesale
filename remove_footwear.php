<?php

if(!isset($_SESSION['admin_id'])){
    echo "
    <!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Account Center - Wholesale Footwear Management</title>
        <!-- Bootstrap 5 CSS -->
        <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css' rel='stylesheet'>
    </head>
    <body>
        <div class='container row mt-5 m-auto'>
            <div class='alert alert-danger text-center col-12 col-md-9 col-lg-8   m-auto'>
                <h2>With Great Powers Comes Grate Responsibilities.</h2>
                <h5>You must be logged in as a Admin to access.</h5>
                <div class='d-flex justify-content-center mt-3'>
                <a href='user_login.php' class='btn btn-primary mx-2'>Login</a>
                <a href='index.php' class='btn btn-info mx-2'>Home</a>
                <a href='user_dashboard.php' class='btn btn-primary mx-2'>User-Portal</a>
                </div>
            </div>
        </div>
        <!-- Bootstrap 5 JS -->
        <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js'></script>
    </body>
    </html>";
    exit;
}
include('session_dbConn.php');

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
        header("Location: footwear_stock.php");
    } catch (Exception $e) {
        // Rollback transaction in case of an error
        $conn->rollback();

        // Redirect back with an error message
        $_SESSION['error_message'] = "Failed to remove the model: " . $e->getMessage();
        header("Location: footwear_stock.php");
    }
} else {
    // Redirect back if no model_id is provided
    $_SESSION['error_message'] = "Invalid model selected.";
    header("Location: footwear_stock.php");
}

exit();
?>
    