<?php
// Start session
session_start();
include('db_connect.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<div class='alert alert-danger'>You must be logged in to access this page. <a href='login.php'>Login here</a></div>";
    exit;
}

$user_id = $_SESSION['user_id'];
$user = [];
$alertMessage = ''; // Variable to store alert message
$alertType = ''; // Variable to store alert type (success/danger)

// Fetch current user details (GET request or after successful update)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $query = "SELECT * FROM usersretailers WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
    } else {
        $alertMessage = "Unable to fetch your details. Please contact support.";
        $alertType = "warning";
    }
}

// Handle form submission (POST request)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $shop_address = trim($_POST['shop_address']);
    $email = trim($_POST['email']);
    $mobile_number = trim($_POST['mobile_number']);
    $errors = [];

    // Validate username (no spaces and must be unique)
    if (preg_match('/\s/', $username)) {
        $errors[] = "Username cannot contain spaces.";
    } else {
        $query = "SELECT user_id FROM usersretailers WHERE username = ? AND user_id != ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $username, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $errors[] = "Username is already taken. Please choose another one.";
        }
    }

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    // Validate mobile number (e.g., 10 digits)
    if (!preg_match('/^\d{10}$/', $mobile_number)) {
        $errors[] = "Invalid mobile number. It must be 10 digits.";
    }

    // Update user details in the database if there are no errors
    if (empty($errors)) {
        $query = "UPDATE usersretailers 
                  SET username = ?, shop_address = ?, email = ?, mobile_number = ? 
                  WHERE user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssi", $username, $shop_address, $email, $mobile_number, $user_id);

        if ($stmt->execute()) {
            $alertMessage = "Profile updated successfully!";
            $alertType = "success";
            // Reload user details to reflect changes
            $query = "SELECT * FROM usersretailers WHERE user_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
        } else {
            $alertMessage = "Failed to update profile. Please try again.";
            $alertType = "danger";
        }
    } else {
        // Concatenate errors into a single message
        $alertMessage = implode("<br>", $errors);
        $alertType = "danger";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    
        <h2 class="text-center">Edit Profile </h2>
        <?php if (!empty($alertMessage)): ?>
        <div class="alert alert-<?php echo $alertType; ?> alert-dismissible fade show" role="alert" style="width:60%; margin:auto;">
            <?php echo $alertMessage; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    

    <!-- Bootstrap Alert -->
   

    <form method="POST" action="">
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>" required>
        </div>
        <div class="mb-3">
            <label for="shop_name" class="form-label">Shop Name</label>
            <input type="text" class="form-control" id="shop_name" name="shop_name" value="<?php echo htmlspecialchars($user['shop_name'] ?? ''); ?>" disabled>
        </div>
        <div class="mb-3">
            <label for="shop_address" class="form-label">Shop Address</label>
            <textarea class="form-control" id="shop_address" name="shop_address" required><?php echo htmlspecialchars($user['shop_address'] ?? ''); ?></textarea>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
        </div>
        <div class="mb-3">
            <label for="mobile_number" class="form-label">Mobile Number</label>
            <input type="text" class="form-control" id="mobile_number" name="mobile_number" value="<?php echo htmlspecialchars($user['mobile_number'] ?? ''); ?>" required>
        </div>
        <div class="d-flex justify-content-center">
        <button type="submit" class="btn btn-primary mx-1">Save Changes</button>
        <a href="account.php" class="btn btn-secondary mx-1">Back</a>
        </div>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
