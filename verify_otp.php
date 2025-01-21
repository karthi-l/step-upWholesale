<?php
session_start();

include('db_connect.php');
// Check if user is already logged in
if(isset($_SESSION['user_id'])){
    header("Location:user_dashboard.php");
}
if(isset($_SESSION['admin_id'])){
    header("Location:admin_dashboard.php");
}
// Check if username and email is not set
if (!isset($_SESSION['auth_user']) && !isset($_SESSION['auth_email'])) {
    if($_SESSION['user_or_admin'] === 'user'){
        if($_SESSION['authType'] == 'login'){
            header("Location:user_login.php");
            exit(0);
        } elseif($_SESSION['authType'] == 'register'){
            header("Location:user_register.php");
            exit(0);
        }
    }elseif($_SESSION['user_or_admin'] === 'admin'){
        if($_SESSION['authType'] == 'login'){
            header("Location:admin_login.php");
            exit(0);
        } elseif($_SESSION['authType'] == 'register'){
            header("Location:admin_register.php");
            exit(0);
        }
    }
}


// Handle OTP verification
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_SESSION['email']; // Retrieve email from session
    $otp = mysqli_real_escape_string($conn, $_POST['otp']); // Get OTP from the form

    // Fetch OTP and expiry from the database
    $sql = "SELECT otp, otp_expiry, user_id FROM usersretailers WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    if ($result) {
        if ($result['otp'] == $otp && strtotime($result['otp_expiry']) > time()) {
            // OTP is valid; clear OTP and update session
            if($_SESSION['authType'] == 'register'){
                $sql_update = "UPDATE usersretailers SET otp = NULL, otp_expiry = NULL, registrationDate = Now(), LastLogin = NOW() WHERE email = ?";
            }elseif($_SESSION['authType'] == 'login'){
                $sql_update = "UPDATE usersretailers SET otp = NULL, otp_expiry = NULL, LastLogin = NOW() WHERE email = ?";
            }
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("s", $email);
            $stmt_update->execute();

            // Set session variable for successful login
            $_SESSION['is_verified'] = true;
            $_SESSION['user_id'] = $result['user_id'];

            // Redirect to account/dashboard page
            header('Location:user_dashboard.php');
            exit(0);
        } else {
            $alert = "<div class='alert alert-danger'>Invalid or expired OTP. Please try again.</div>";
            $sql_update = "UPDATE usersretailers SET otp = NULL, otp_expiry = NULL WHERE email = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("s", $email);
            $stmt_update->execute();
            $_SESSION['is_verified'] = false;
            
        }
    } else {
        $alert= "<div class='alert alert-danger m-auto'>No OTP found for this email.</div>";
    }
}
?>

<!-- The rest of your HTML form for OTP verification remains unchanged -->


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <form action="verify_otp.php" method="POST" class="border p-4 rounded">

        <h2 class="mb-4 text-center">Verify OTP</h2>
        <?php if(isset($alert)){ echo $alert ;} ?>
        <div class="mb-3">
            <label for="otp" class="form-label">Enter OTP</label>
            <input type="text" id="otp" name="otp" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Verify OTP</button>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
