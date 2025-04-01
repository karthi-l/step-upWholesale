<?php 
include('../includes/session_dbConn.php');
include('../includes/bootstrap-css-js.php');
if(isset($_SESSION['user_id'])){
    header("Location: user-portal/user_dashboard.php");
}elseif(isset($_SESSION['admin_id'])){
    header("Location: admin-portal/admin_dashboard.php");
}else{
    include('ua-auth/auth.php');
}
?>