<?php


// Optional: for checking if user is logged in
$user_id = $_SESSION['user_id'] ?? null;

$result = $conn->query("SELECT * FROM announcements WHERE is_read = 0 AND (target = 'all' OR target = 'visitors'" . ($user_id ? " OR (target = 'user' AND user_id = $user_id)" : "") . ") ORDER BY created_at DESC");
?>

<?php if ($result->num_rows > 0): ?>
    <div class="container mt-4">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="alert alert-info d-flex justify-content-between align-items-center" id="announcement-<?= $row['id'] ?>">
                <div>
                    <strong><?= htmlspecialchars($row['title']) ?>:</strong> <?= nl2br(htmlspecialchars($row['message'])) ?>
                </div>
                <button class="btn btn-sm btn-outline-primary" onclick="dismissAnnouncement(<?= $row['id'] ?>)">OK</button>
            </div>
        <?php endwhile; ?>
    </div>
<?php endif; ?>

<script>
function dismissAnnouncement(id) {
    fetch('dismiss_announcement.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'id=' + id
    }).then(res => {
        if (res.ok) {
            document.getElementById('announcement-' + id).remove();
        }
    });
}
</script>
