<?php
include('../../includes/session_dbConn.php');
if(!isset($_SESSION['admin_id'])){
    include('../ua-auth/admin_auth.php');
    exit;
}
$filter = $_GET['filter'] ?? 'all';

$query = "SELECT sr.*, u.username, u.email, u.shop_name 
          FROM support_requests sr 
          JOIN usersretailers u ON sr.user_id = u.user_id";

if ($filter === 'responded') {
    $query .= " WHERE sr.is_responded = 1";
} elseif ($filter === 'pending') {
    $query .= " WHERE sr.is_responded = 0";
}

$query .= " ORDER BY sr.created_at DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Support Requests</title>
    <?php include('../../includes/inc_styles.php');?>
</head>
<body>
    <div class="container mt-5">
        <div class="d-flex justify-content-center align-items-center mb-4">
            <h2><i class="bi bi-life-preserver me-2"></i> User Support Requests</h2>
            <a href="../../index.php" class="btn btn-primary ms-auto"><i class="bi bi-house-door-fill"></i> Home</a>
        </div>

        <div class="d-flex justify-content-center mb-4">
            <div class="btn-group" role="group" aria-label="Filter Support Requests">
                <a href="?filter=all" class="btn btn-outline-primary <?= ($filter === 'all') ? 'active' : '' ?>">All</a>
                <a href="?filter=responded" class="btn btn-outline-success <?= ($filter === 'responded') ? 'active' : '' ?>">Responded</a>
                <a href="?filter=pending" class="btn btn-outline-warning <?= ($filter === 'pending') ? 'active' : '' ?>">Pending</a>
            </div>
        </div>
        
        <h5 class="text-center text-muted mb-4">
            <?= ucfirst($filter) ?> Support Requests (<?= $result->num_rows ?>)
        </h5>


        <?php if ($result->num_rows > 0): ?>
            <div class="row g-4">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <h5 class="card-title"><i class="bi bi-person-circle text-primary"></i> <?= htmlspecialchars($row['username']) ?></h5>

                            <h6 class="card-subtitle mb-2 text-muted"><?= htmlspecialchars($row['shop_name']) ?></h6>
                            <h6 class="card-subtitle mb-2 text-muted"><?= htmlspecialchars($row['email']) ?></h6>

                            <p><strong>Subject:</strong> <?= htmlspecialchars($row['subject']) ?></p>
                            <p><strong>Message:</strong><br><?= nl2br(htmlspecialchars($row['message'])) ?></p>

                            <?php if ($row['attachment']): ?>
                                <button class="btn btn-sm btn-outline-info mb-3" data-bs-toggle="modal" data-bs-target="#attachmentModal<?= $row['id'] ?>">
                                    <i class="bi bi-eye"></i> View
                                </button>
                            <?php else: ?> 
                                <button class="btn btn-sm btn-outline-info mb-3" disabled >
                                    <i class="bi bi-eye-slash"></i> No-file
                                </button>
                            <?php endif; ?>


                            <p><strong>Sent At:</strong> <?= date("d M Y, h:i A", strtotime($row['created_at'])) ?></p>
                            <p>
                                <strong>Status:</strong> 
                                <?php if ($row['is_responded']): ?>
                                    <span class="badge bg-success"><i class="bi bi-check2-circle"></i> Responded</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark"><i class="bi bi-clock-fill"></i> Pending</span>
                                <?php endif; ?>
                            </p>

                            <?php if ($row['responded_at']): ?>
                                <p><strong>Responded At:</strong> <?= date("d M Y, h:i A", strtotime($row['responded_at'])) ?></p>
                            <?php endif; ?>

                            <?php if ($row['is_responded']): ?>
                                <div class="alert alert-light border mt-3">
                                    <strong>Response:</strong><br><?= nl2br(htmlspecialchars($row['response'])) ?>
                                </div>
                            <?php else: ?>
                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#respondModal<?= $row['id'] ?>">
                                    <i class="bi bi-reply-fill"></i> Respond
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Response Modal -->
                <div class="modal fade" id="respondModal<?= $row['id'] ?>" tabindex="-1" aria-labelledby="respondModalLabel<?= $row['id'] ?>" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <form method="POST" action="respond_support.php">
                                <div class="modal-header">
                                    <h5 class="modal-title">Respond to <?= htmlspecialchars($row['username']) ?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="request_id" value="<?= $row['id'] ?>">
                                    <div class="mb-3">
                                        <label class="form-label">Your Response</label>
                                        <textarea name="response" class="form-control" rows="5" required></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-success"><i class="bi bi-send-fill"></i> Send</button>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php if ($row['attachment']): ?>
                    <div class="modal fade" id="attachmentModal<?= $row['id'] ?>" tabindex="-1" aria-labelledby="attachmentModalLabel<?= $row['id'] ?>" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-md">
                        <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Attachment from <?= htmlspecialchars($row['username']) ?></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center">
                            <img src="../../<?= htmlspecialchars($row['attachment']) ?>" class="img-fluid rounded shadow-sm" alt="Attachment">
                        </div>
                        </div>
                    </div>
                    </div>
                    <?php endif; ?>

            <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center">No support requests available.</div>
        <?php endif; ?>
    </div>
    <?php include('../../includes/inc_scripts.php');?>
</body>
</html>
