<?php
// Start session
include('../../includes/session_dbConn.php');
include('../../includes/bootstrap-css-js.php');

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit;
}

$alertMessage = '';
$alertType = '';

// Fetch all users
$query = "SELECT * FROM usersretailers";
$result = $conn->query($query);
$users = $result->fetch_all(MYSQLI_ASSOC);

// Handle user update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_user'])) {
    $user_id = $_POST['user_id'];
    $shopname = trim($_POST['shopname']);
    $shop_address = trim($_POST['shop_address']);
    $email = trim($_POST['email']);
    $gstin = isset($_POST['gstin']) ? trim($_POST['gstin']) : null;
    $mobile_number = trim($_POST['mobile_number']);
    $user_type = trim($_POST['user_type']);
    
    // Validate input
    $errors = [];
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    if (!preg_match('/^\d{10}$/', $mobile_number)) {
        $errors[] = "Invalid mobile number. It must be 10 digits.";
    }

    if (empty($errors)) {
        $query = "UPDATE usersretailers SET shop_name = ?, shop_address = ?, email = ?, mobile_number = ?, gstin = ?, userType = ? WHERE user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssssi",  $shopname, $shop_address, $email, $mobile_number, $gstin, $user_type, $user_id);
        
        if ($stmt->execute()) {
            $alertMessage = "User updated successfully!";
            $alertType = "success";
        } else {
            $alertMessage = "Failed to update user.";
            $alertType = "danger";
        }
    } else {
        $alertMessage = implode("<br>", $errors);
        $alertType = "danger";
    }
    
    // Refresh user list
    $result = $conn->query("SELECT * FROM usersretailers");
    $users = $result->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
</head>
<body>
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center px-3">
        <h2 class="text-center flex-grow-1 text-center">Manage Users</h2>
        <a class="btn btn-primary text-none" href="../../index.php">Home</a>
    </div>
    <?php if (!empty($alertMessage)): ?>
        <div class="alert alert-<?php echo $alertType; ?> text-center" role="alert">
            <?php echo $alertMessage; ?>
        </div>
    <?php endif; ?>
    
    <table class="table table-bordered table-striped mt-3">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Shop Name</th>
                <th>Mobile</th>
                <th>User Type</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo $user['user_id']; ?></td>
                    <td><?php echo $user['username']; ?></td>
                    <td><?php echo $user['shop_name']; ?></td>
                    <td><?php echo $user['mobile_number']; ?></td>
                    <td><?php echo $user['userType']; ?></td>
                    <td>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editUserModal<?php echo $user['user_id']; ?>">Edit</button>
                    </td>
                </tr>
                <!-- Edit User Modal -->
                <div class="modal fade" id="editUserModal<?php echo $user['user_id']; ?>" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Edit User</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form method="POST" action="">
                                    <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                    <div class="mb-3">
                                        <label for="username" class="form-label">Username</label>
                                        <input type="text" class="form-control" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required disabled>
                                    </div>
                                    <div class="mb-3">
                                        <label for="username" class="form-label">Shop Name</label>
                                        <input type="text" class="form-control" name="shopname" value="<?php echo htmlspecialchars($user['shop_name']); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="shop_address" class="form-label">Shop Address</label>
                                        <textarea class="form-control" name="shop_address" required><?php echo htmlspecialchars($user['shop_address']); ?></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="mobile_number" class="form-label">GST IN</label>
                                        <input type="text" class="form-control" name="gstin" value="<?php echo htmlspecialchars($user['gstin']); ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label for="mobile_number" class="form-label">Mobile Number</label>
                                        <input type="text" class="form-control" name="mobile_number" value="<?php echo htmlspecialchars($user['mobile_number']); ?>" required>
                                    </div>  
                                    <div class="mb-3">
                                        <label for="user_type" class="form-label">User Type</label>
                                        <select class="form-select" aria-label="Default select example" name="user_type">
                                            <option selected value="<?php echo htmlspecialchars($user['userType']);?>"><?php echo htmlspecialchars($user['userType']);?></option>
                                            <option value="Regular">Regular</option>
                                            <option value="Frequent">Frequent</option>
                                            <option value="VIP">VIP</option>
                                        </select>
                                    </div>
                                    <div class="text-center">
                                        <button type="submit" name="edit_user" class="btn btn-success">Save Changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
            </div>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
