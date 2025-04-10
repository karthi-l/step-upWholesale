<?php
include('../../includes/session_dbConn.php');

$result = $conn->query("SELECT sr.*, u.username, u.email FROM support_requests sr JOIN usersretailers u ON sr.user_id = u.user_id ORDER BY sr.created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Support Requests</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4"><i class="bi bi-life-preserver"></i> User Support Requests</h2>

    <div class="table-responsive">
        <table class="table table-hover table-bordered align-middle">
            <thead class="table-dark">
                <tr>
                    <th>User</th>
                    <th>Email</th>
                    <th>Subject</th>
                    <th>Message</th>
                    <th>Attachment</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Update Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><i class="bi bi-person-circle text-primary"></i> <?= htmlspecialchars($row['username']) ?></td>
                    <td><a href="mailto:<?= htmlspecialchars($row['email']) ?>"><?= htmlspecialchars($row['email']) ?></a></td>
                    <td><?= htmlspecialchars($row['subject']) ?></td>
                    <td style="max-width: 300px; white-space: pre-wrap;"><?= nl2br(htmlspecialchars($row['message'])) ?></td>
                    <td>
                        <?php if ($row['attachment']): ?>
                            <a href="<?= htmlspecialchars($row['attachment']) ?>" target="_blank" class="btn btn-sm btn-outline-info" data-bs-toggle="tooltip" title="View Attachment">
                                <i class="bi bi-file-earmark-image"></i>
                            </a>
                        <?php else: ?>
                            <span class="text-muted">No file</span>
                        <?php endif; ?>
                    </td>
                    <td><?= date("d M Y, h:i A", strtotime($row['created_at'])) ?></td>
                    <td>
                        <span class="badge 
                            <?= $row['status'] === 'New' ? 'bg-warning text-dark' : ($row['status'] === 'In Progress' ? 'bg-primary' : 'bg-success') ?>">
                            <?= htmlspecialchars($row['status']) ?>
                        </span>
                    </td>
                    <td>
                        <form method="POST" action="update_status.php" class="d-flex flex-column gap-1">
                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                            <select name="status" class="form-select form-select-sm">
                                <option value="New" <?= $row['status'] === 'New' ? 'selected' : '' ?>>New</option>
                                <option value="In Progress" <?= $row['status'] === 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                                <option value="Resolved" <?= $row['status'] === 'Resolved' ? 'selected' : '' ?>>Resolved</option>
                            </select>
                            <button type="submit" class="btn btn-sm btn-success mt-1">
                                <i class="bi bi-check-circle"></i> Update
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
<div class="d-flex justify-content-end">
            <a href="../../index.php" class="btn btn-primary">Home</a>
        </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Initialize all Bootstrap tooltips
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
    tooltipTriggerList.forEach(el => new bootstrap.Tooltip(el))
</script>
</body>
</html>
