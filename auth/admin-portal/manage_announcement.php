<?php
include('../../includes/session_dbConn.php');

// Expire announcements

// Handle new announcement creation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $message = $_POST['message'];
    $target = $_POST['target'];
    $user_id = isset($_POST['user_id']) && $_POST['target'] === 'user' ? $_POST['user_id'] : null;

    $stmt = $conn->prepare("INSERT INTO announcements (title, message, target, user_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $title, $message, $target, $user_id);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_announcement.php");
    exit;
}

// Get active announcements
$result = $conn->query("SELECT a.*, u.username FROM announcements a LEFT JOIN usersretailers u ON a.user_id = u.user_id  ORDER BY a.created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Announcements</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container py-4">
  <h2 class="mb-4">Create Announcement</h2>
  <form method="POST" class="card p-4 mb-5">
    <div class="mb-3">
      <label class="form-label">Title</label>
      <input type="text" name="title" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Message</label>
      <textarea name="message" class="form-control" rows="4" required></textarea>
    </div>
    <div class="mb-3">
      <label class="form-label">Target</label>
      <select name="target" class="form-select" id="targetSelect" required>
        <option value="all">All Users</option>
        <option value="visitors">Visitors</option>
        <option value="user">Specific User</option>
      </select>
    </div>
    <div class="mb-3" id="userSelectDiv" style="display: none;">
      <label class="form-label">Select User</label>
      <select name="user_id" class="form-select">
        <?php
        $users = $conn->query("SELECT user_id, username FROM usersretailers");
        while ($user = $users->fetch_assoc()) {
            echo "<option value='{$user['user_id']}'>{$user['username']}</option>";
        }
        ?>
      </select>
    </div>
    <button type="submit" class="btn btn-success m-auto" style="width:15%;">Create</button>
  </form>

  <h3>Active Announcements</h3>
  <table class="table table-bordered table-striped">
    <thead>
      <tr>
        <th>Title</th>
        <th>Message</th>
        <th>Target</th>
        <th>User</th>
        <th>Status</th>
        <th>Created</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($row['title']) ?></td>
          <td><?= nl2br(htmlspecialchars($row['message'])) ?></td>
          <td><?= ucfirst($row['target']) ?></td>
          <td><?= $row['target'] === 'user' ? htmlspecialchars($row['username']) : '-' ?></td>
          <td>
              <?php 
              if($row['is_read'] == 0) {
                  echo "Unread";
              } elseif($row['is_read'] == 1) {
                  echo "Read";
              }
              ?>
          </td>

          <td><?= $row['created_at'] ?></td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
  <div class="d-flex justify-content-end">
              <a href="../../index.php" class="btn btn-primary">Home</a>
          </div>
</div>
<script>
  document.getElementById('targetSelect').addEventListener('change', function () {
    const userDiv = document.getElementById('userSelectDiv');
    userDiv.style.display = this.value === 'user' ? 'block' : 'none';
  });
</script>
</body>
</html>
