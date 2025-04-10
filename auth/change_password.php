<?php

include('../includes/session_dbConn.php');
include('../includes/bootstrap-css-js.php');


$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Fetch the current password hash
    $query = "SELECT password FROM usersretailers WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($hashed_password);
    $stmt->fetch();
    $stmt->close();

    // Verify the current password
    if (password_verify($current_password, $hashed_password)) {
        if ($new_password === $confirm_password) {
            $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $query = "UPDATE usersretailers SET password = ? WHERE user_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("si", $new_hashed_password, $user_id);
            if ($stmt->execute()) {
                echo "<div class='alert alert-success'>Password changed successfully!</div>";
            } else {
                echo "<div class='alert alert-danger'>Failed to change password. Please try again.</div>";
            }
        } else {
            echo "<div class='alert alert-warning'>New passwords do not match.</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Incorrect current password.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
</head>
<body>
<div class="container mt-5">
    <h2>Change Password</h2>
    
    <form method="POST" action="">
        <div class="mb-3">
            <label for="current_password" class="form-label">Current Password</label>
            <input type="password" class="form-control" id="current_password" name="current_password" required>
        </div>
        <div class="mb-3">
            <label for="new_password" class="form-label">New Password</label>
            <input type="password" class="form-control" id="new_password" name="new_password" required>
        </div>
        <div class="mb-3">
            <label for="confirm_password" class="form-label">Confirm New Password</label>
            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
        </div>
        <button type="submit" class="btn btn-primary">Change Password</button>
        <a href="user-portal/user_dashboard.php" class="btn btn-secondary">Back</a>
    </form>
</div>
</body>
</html>
