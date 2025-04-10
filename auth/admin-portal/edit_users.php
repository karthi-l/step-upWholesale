<?php
// Start session
include('../../includes/session_dbConn.php');

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
    <?php include('../../includes/inc_styles.php');?>
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

    <?php
    $userTypes = ['All', 'VIP', 'Frequent', 'Regular'];
    ?>
    <?php
    // Count users by type
    $userCounts = [
        'All' => count($users),
        'VIP' => 0,
        'Frequent' => 0,
        'Regular' => 0
    ];
    
    foreach ($users as $u) {
        if (isset($userCounts[$u['userType']])) {
            $userCounts[$u['userType']]++;
        }
    }
    ?>

<ul class="nav nav-tabs mt-4" id="userTab" role="tablist">
    <?php foreach ($userTypes as $index => $type): ?>
        <li class="nav-item" role="presentation">
            <button class="nav-link <?php echo $index === 0 ? 'active' : ''; ?>" id="<?php echo $type; ?>-tab" data-bs-toggle="tab" data-bs-target="#<?php echo $type; ?>" type="button" role="tab">
                <?php echo $type; ?> Users
                <span class="badge bg-info"><?php echo $userCounts[$type]; ?></span>
            </button>
        </li>
    <?php endforeach; ?>
</ul>


    <div class="tab-content mt-3" id="userTabContent">
        <?php foreach ($userTypes as $index => $type): ?>
            <div class="tab-pane fade <?php echo $index === 0 ? 'show active' : ''; ?>" id="<?php echo $type; ?>" role="tabpanel">
                <div class="accordion" id="accordion<?php echo $type; ?>">
                    <?php
                    $filteredUsers = $type === 'All' ? $users : array_filter($users, fn($u) => $u['userType'] === $type);
                    foreach ($filteredUsers as $i => $user):
                        $collapseId = $type . 'Collapse' . $user['user_id'];
                    ?>
                    <div class="accordion-item mb-2">
                        <h2 class="accordion-header" id="heading<?php echo $user['user_id']; ?>">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#<?php echo $collapseId; ?>">
                                <?php echo htmlspecialchars($user['username']); ?> (<?php echo htmlspecialchars($user['userType']); ?>)
                            </button>
                        </h2>
                        <div id="<?php echo $collapseId; ?>" class="accordion-collapse collapse">
                            <div class="accordion-body">
                                <form method="POST" action="">
                                    <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                    <div class="mb-3">
                                        <label class="form-label">Username</label>
                                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Shop Name</label>
                                        <input type="text" class="form-control" name="shopname" value="<?php echo htmlspecialchars($user['shop_name']); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Shop Address</label>
                                        <textarea class="form-control" name="shop_address" required><?php echo htmlspecialchars($user['shop_address']); ?></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">GST IN</label>
                                        <input type="text" class="form-control" name="gstin" value="<?php echo htmlspecialchars($user['gstin']); ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Mobile Number</label>
                                        <input type="text" class="form-control" name="mobile_number" value="<?php echo htmlspecialchars($user['mobile_number']); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">User Type</label>
                                        <select class="form-select" name="user_type">
                                            <option value="Regular" <?php echo $user['userType'] === 'Regular' ? 'selected' : ''; ?>>Regular</option>
                                            <option value="Frequent" <?php echo $user['userType'] === 'Frequent' ? 'selected' : ''; ?>>Frequent</option>
                                            <option value="VIP" <?php echo $user['userType'] === 'VIP' ? 'selected' : ''; ?>>VIP</option>
                                        </select>
                                    </div>
                                    <button type="submit" name="edit_user" class="btn btn-success">Save Changes</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php include('../../includes/inc_scripts.php');?>
</body>
</html>