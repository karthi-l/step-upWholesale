<?php
// Start session to access session variables
session_start();

// Include your database connection file
include('db_connect.php');

// Check if the user is logged in
if (isset($_SESSION['admin_id'])) {
    // User is logged in, fetch their details from the database
    $adminid = $_SESSION['admin_id']; 
    echo $adminid.'lhjasdkjhfaljsdhflakjdhf';
}else{
    echo 'admin id not set, <br><a href="admin_login.php">login</a><a href="admin_register.php">register</a>';
}
?>
