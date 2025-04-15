<?php
include('includes/session_dbConn.php');


// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/user-portal/user_login.php");
    exit;
}
$user_id = $_SESSION['user_id'];

// Fetch relevant announcements
$query = "SELECT * FROM announcements 
          WHERE  (target = 'all' OR target = 'visitors' OR (target = 'user' AND user_id = ?))
          ORDER BY created_at DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Announcements</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-5">
    <div class="d-flex justify-content-center align-items-center mb-4">
        <h2 class="mb-4">My Announcements</h2>
        <a href="index.php" class="btn btn-primary ms-auto"><i class="bi bi-house-door-fill"></i> Home</a>
    </div>
    <?php if ($result->num_rows > 0): ?>
        <div class="list-group">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="list-group-item <?= $row['is_read'] ? 'list-group-item-light' : 'list-group-item-warning' ?>">
                    <div class="d-flex w-100 justify-content-between">
                        <h5 class="mb-1"><?= htmlspecialchars($row['title']) ?></h5>
                        <small><?= date('d M Y h:i A', strtotime($row['created_at'])) ?></small>
                    </div>
                    <p class="mb-1"><?= nl2br(htmlspecialchars($row['message'])) ?></p>
                    <small>Status: <?= $row['is_read'] ? '<span class="text-success">Read</span>' : '<span class="text-danger">Unread</span>' ?></small>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info">No announcements available.</div>
    <?php endif; ?>
</div>
</body>
</html>
