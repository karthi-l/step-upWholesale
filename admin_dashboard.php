<?php
// Start session to access session variables


// Include your database connection file
include('session_dbConn.php');

// Check if the user is logged in
if (isset($_SESSION['admin_id'])) {
    $id = $_SESSION['admin_id'];
    echo $id;

    // User is logged in, fetch their details from the database
    
    echo '<br><a href="logout.php">logout</a>';
    echo '<br><a href="footwear_stock.php">stock</a>';

}else{
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
                <a href='admin_login.php' class='btn btn-primary mx-2'>Login</a>
                <a href='index.php' class='btn btn-info mx-2'>Home</a>
                <a href='user_dashboard.php' class='btn btn-primary mx-2'>User-Portal</a>
                </div>
            </div>
        </div>
        <!-- Bootstrap 5 JS -->
        <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js'></script>
    </body>
    </html>
    ";
}
?>
