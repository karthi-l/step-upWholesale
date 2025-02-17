<?php
include('includes/session_dbConn.php');
include('includes/filter_query.php');
/*
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
                <a href='admin_login.php' class='btn btn-primary mx-2'>Login</a>
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
}*/

if (isset($_SESSION['success_message'])) {
    echo '<script>alert("' . htmlspecialchars($_SESSION['success_message']) . '")</script>';
    unset($_SESSION['success_message']);
}

if (isset($_SESSION['error_message'])) {
    echo '<script>alert("' . htmlspecialchars($_SESSION['error_message']) . '")</script>';
    unset($_SESSION['error_message']);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Footwear Models</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .footwear_list .card {
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .footwear_list .card img {
            width:100%;
            max-height: 250px;  
            object-fit: contain;
            border-radius: 0.33rem;
        }
        .footwear_list .card-body .card-text {
            overflow:auto;
            max-height: 100px;
            text-align: center;
        }
        .sticky_filter{
            position: sticky;
            top:9%;
            height:100%;
        }
        .dropdown-menu {
            width: 100%; /* Adjust width to fit your form */
            height: 100%; /* Optional: Limit the height */
        
        }

    </style>
    <link rel="stylesheet" href="app.css">
</head>
<body>
    <?php include('includes/footwear_fetching.php'); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
