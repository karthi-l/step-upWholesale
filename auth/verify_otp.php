<?php
include('../includes/session_dbConn.php');
include('../includes/bootstrap-css-js.php');
// Check if user is already logged in
if(isset($_SESSION['user_id'])){
    header("Location:user-portal/user_dashboard.php");
}
if(isset($_SESSION['admin_id'])){
    header("Location:admin-portal/admin_dashboard.php");
}
// Check if username and email is not set
if (!isset($_SESSION['auth_name']) && !isset($_SESSION['auth_email'])) {
    if($_SESSION['user_or_admin'] === 'user'){
        if($_SESSION['authType'] == 'login'){
            header("Location:user-portal/user_login.php");
            exit(0);
        } elseif($_SESSION['authType'] == 'register'){
            header("Location:user-portal/user_register.php");
            exit(0);
        }
    }elseif($_SESSION['user_or_admin'] === 'admin'){
        if($_SESSION['authType'] == 'login'){
            header("Location:admin-portaladmin_login.php");
            exit(0);
        } elseif($_SESSION['authType'] == 'register'){
            header("Location:admin-portal/admin_register.php");
            exit(0);
        }
    }
}


// Handle OTP verification
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_SESSION['auth_email']; // Retrieve email from session
    $otp = mysqli_real_escape_string($conn, $_POST['otp']); // Get OTP from the form

    // Fetch OTP and expiry from the database
    if($_SESSION['user_or_admin'] === 'user'){
        $sql = "SELECT otp, otp_expiry, user_id FROM usersretailers WHERE email = ?";
    }elseif($_SESSION['user_or_admin'] === 'admin'){
        $sql = "SELECT otp, otp_expiry, admin_id FROM admins WHERE email = ?";
    }    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    if ($result) {
        if ($result['otp'] == $otp && strtotime($result['otp_expiry']) > time()) {
            // OTP is valid; clear OTP and update session
            if($_SESSION['authType'] == 'register'){
                if($_SESSION['user_or_admin'] === 'user'){
                    $sql_update = "UPDATE usersretailers SET otp = NULL, otp_expiry = NULL, userCreationDate = Now() WHERE email = ?";
                }elseif($_SESSION['user_or_admin'] === 'admin'){
                    $sql_update = "UPDATE admins SET otp = NULL, otp_expiry = NULL , CreatedDate = Now() WHERE email = ?";
                }
            }elseif($_SESSION['authType'] == 'login'){
                if($_SESSION['user_or_admin'] === 'user'){
                    $sql_update = "UPDATE usersretailers SET otp = NULL, otp_expiry = NULL, LastLogin = Now() WHERE email = ?";
                }elseif($_SESSION['user_or_admin'] === 'admin'){
                    $sql_update = "UPDATE admins SET otp = NULL, otp_expiry = NULL, LastLogin = Now() WHERE email = ?";
                }
            }
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("s", $email);
            $stmt_update->execute();

            // Set session variable for successful login
            $_SESSION['is_verified'] = true;
            if($_SESSION['user_or_admin'] === 'user'){
                $_SESSION['user_id'] = $result['user_id'];
                // Redirect to account/dashboard page
                header('Location:user-portal/user_dashboard.php');
                exit(0);
            }elseif($_SESSION['user_or_admin'] === 'admin'){
                $_SESSION['admin_id'] = $result['admin_id'];
                // Redirect to account/dashboard page
                header('Location:admin-portal/admin_dashboard.php');
            }
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
</body>
</html>
