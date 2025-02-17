<?php
if(!isset($_SESSION['admin_id'])){
    echo "
    <!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Account Center - Wholesale Footwear Management</title>
    </head>
    <body>
        <div class='container row mt-5 m-auto'>
            <div class='alert alert-danger text-center col-12 col-md-9 col-lg-8   m-auto'>
                <h2>With Great Powers Comes Grate Responsibilities.</h2>
                <h5>You must be logged in as a Admin to access.</h5>
                <div class='d-flex justify-content-center mt-3'>
                <a href='../auth/admin-portal/admin_login.php' class='btn btn-primary mx-2'>Login</a>
                <a href='../index.php' class='btn btn-info mx-2'>Home</a>
                </div>
            </div>
        </div>
    </body>
    </html>";
    exit;
}
?>