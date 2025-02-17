<?php
include('../includes/session_dbConn.php');
include('../includes/filter_query.php');
include('../includes/bootstrap-css-js.php');

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
</head>
<body>
    <?php include('../includes/footwear_fetching.php'); ?>

</body>
</html>
