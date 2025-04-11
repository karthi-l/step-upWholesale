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
<!-- Keep your PHP code as is (handling form submission and DB fetching) -->
<!-- ... -->

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-chat-square-heart-fill text-primary"></i> Support Center</h2>  
        <a href="index.php" class="btn btn-primary ms-auto"><i class="bi bi-house-door-fill"></i> Home</a>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success">Support request submitted successfully!</div>
    <?php elseif ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- Support Request Form -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-primary text-white">
            <i class="bi bi-pencil-square"></i> Submit a Support Request
        </div>
        <div class="card-body">
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

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-send-check-fill"></i> Send Request
                </button>
            </form>
        </div>
    </div>

    <div class="mb-3 d-flex flex-wrap gap-2">
    <button type="button" class="btn btn-outline-secondary active" onclick="filterCards('all')">Show All</button>
    <button type="button" class="btn btn-outline-warning" onclick="filterCards('pending')">Only Pending</button>
    <button type="button" class="btn btn-outline-success" onclick="filterCards('responded')">Only Responded</button>
    </div>

    <!-- User's Past Support Requests -->
    <h4 class="mb-3"><i class="bi bi-inbox-fill text-secondary"></i> My Support Requests</h4>

    <?php
    $userId = $_SESSION['user_id'];
    $result = $conn->query("SELECT * FROM support_requests WHERE user_id = $userId ORDER BY created_at DESC");
    if ($result->num_rows == 0): ?>
        <div class="alert alert-info">No support requests yet. Feel free to contact us above!</div>
    <?php endif; ?>

    <div class="row row-cols-1 row-cols-md-2 g-4">
    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="col support-card <?= $row['is_responded'] ? 'responded' : 'pending' ?>">
        <div class="card shadow-sm border-<?= $row['is_responded'] ? 'success' : 'warning' ?>">
                <div class="card-header bg-light d-flex justify-content-between">
                    <strong><?= htmlspecialchars($row['subject']) ?></strong>
                    <span class="badge <?= $row['is_responded'] ? 'bg-success' : 'bg-warning text-dark' ?>">
                        <?= $row['is_responded'] ? 'Responded' : 'Sent' ?>
                    </span>
                </div>
                <div class="card-body">
                    <p><strong>Your Message:</strong><br>
                    <?= nl2br(htmlspecialchars($row['message'])) ?></p>

                    <?php if ($row['attachment']): ?>
                            <p><strong>Attachment:</strong> 
                            <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#userAttachmentModal<?= $row['id'] ?>">
                                <i class="bi bi-file-earmark-image"></i> View
                            </button>
                            </p>
                        <?php else: ?>
                            <p><strong>Attachment:</strong> 
                                <button class="btn btn-sm btn-outline-info" disabled>
                                    <i class="bi bi-file-earmark-x"></i>No attachment
                        </button>
                            </p>
                        <?php endif; ?>
                    <?php if ($row['is_responded']): ?>
                        <hr>
                        <p><strong>Admin Response:</strong><br>
                            <?= nl2br(htmlspecialchars($row['response'])) ?>
                        </p>
                    <?php else: ?>
                        <p class="text-muted"><i class="bi bi-hourglass-split"></i> Awaiting response...</p>
                    <?php endif; ?>
                </div>
                <div class="card-footer small text-muted">
                    <i class="bi bi-calendar-plus"></i> Sent: <?= date("d M Y, h:i A", strtotime($row['created_at'])) ?>
                    <br>
                    <?php if ($row['responded_at']): ?>
                        <i class="bi bi-calendar-check"></i> Responded: <?= date("d M Y, h:i A", strtotime($row['responded_at'])) ?>
                    <?php endif; ?>
                </div>
            </div>
            <?php if ($row['attachment']): ?>
                <div class="modal fade" id="userAttachmentModal<?= $row['id'] ?>" tabindex="-1" aria-labelledby="userAttachmentModalLabel<?= $row['id'] ?>" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-md">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Your Attachment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <img src="<?= htmlspecialchars($row['attachment']) ?>" class="img-fluid rounded shadow-sm" alt="Your Attachment">
                    </div>
                    </div>
                </div>
                </div>
                <?php endif; ?>

        </div>
    <?php endwhile; ?>
    </div>
</div>
<script>
function filterCards(filterType) {
    const cards = document.querySelectorAll('.support-card');
    const buttons = document.querySelectorAll('.btn-group .btn, .mb-3 .btn');

    buttons.forEach(btn => btn.classList.remove('active'));

    switch(filterType) {
        case 'all':
            document.querySelector('[onclick="filterCards(\'all\')"]').classList.add('active');
            cards.forEach(card => card.style.display = '');
            break;
        case 'pending':
            document.querySelector('[onclick="filterCards(\'pending\')"]').classList.add('active');
            cards.forEach(card => {
                card.style.display = card.classList.contains('pending') ? '' : 'none';
            });
            break;
        case 'responded':
            document.querySelector('[onclick="filterCards(\'responded\')"]').classList.add('active');
            cards.forEach(card => {
                card.style.display = card.classList.contains('responded') ? '' : 'none';
            });
            break;
    }
}
</script>

<?php include('includes/inc_scripts.php');?>
</body>
</html>
