<?php
include('includes/session_dbConn.php');

$success = false;
$error = false;

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $userId = $_SESSION['user_id']; // Ensure user is logged in
    $subject = $_POST['subject'];
    $message = $_POST['message'];
    $uploadPath = null;

    if (!empty($_FILES['screenshot']['name'])) {
        $targetDir = "uploads/support/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $uploadPath = $targetDir . basename($_FILES["screenshot"]["name"]);
        if (move_uploaded_file($_FILES["screenshot"]["tmp_name"], $uploadPath)) {
            // File uploaded successfully
        } else {
            $error = "File upload failed.";
        }
    }

    $stmt = $conn->prepare("INSERT INTO support_requests (user_id, subject, message, attachment) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $userId, $subject, $message, $uploadPath);

    if ($stmt->execute()) {
        $success = true;
    } else {
        $error = "Database error.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Get Support</title>
    <?php include('includes/inc_styles.php');?>
</head>
<body>
<div class="container mt-5">
    <h2>User Support Request</h2>

    <?php if ($success): ?>
        <div class="alert alert-success">Support request submitted successfully!</div>
    <?php elseif ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form action="" method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="subject" class="form-label">Subject</label>
            <select name="subject" class="form-select" required>
                <option value="Order Issue">Order Issue</option>
                <option value="Payment">Payment</option>
                <option value="Product Info">Product Info</option>
                <option value="Others">Others</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="message" class="form-label">Message</label>
            <textarea name="message" class="form-control" rows="4" required></textarea>
        </div>

        <div class="mb-3">
            <label for="screenshot" class="form-label">Attach Screenshot (Optional)</label>
            <input type="file" name="screenshot" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary">Send Request</button>
    </form>
</div>
<div class="d-flex justify-content-end">
            <a href="index.php" class="btn btn-primary">Home</a>
        </div>
<?php include('includes/inc_scripts.php');?>
</body>
</html>
