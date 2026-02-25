<?php
session_start();
include "../includes/db.php";
include "../includes/header.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = intval($_SESSION['user_id']);

$stmt = $conn->prepare("SELECT * FROM notification WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$notifications = $stmt->get_result();
?>

<link rel="stylesheet" href="../css/style.css">

<div class="notifications-page">
    <h2>Your Notifications</h2>

    <?php if ($notifications && $notifications->num_rows > 0): ?>
        <ul>
     <?php while ($notif = $notifications->fetch_assoc()): ?>
         <li class="<?= $notif['status'] === 'unread' ? 'unread' : 'read'; ?>" data-id="<?= $notif['id']; ?>">
      <div class="notification-content">
           <p><?= htmlspecialchars($notif['message']); ?></p>
             <small><?= date("d M Y", strtotime($notif['created_at'])); ?></small>
         <?php if ($notif['status'] === 'unread'): ?>
          <button type="button" class="mark-read-btn" data-id="<?= $notif['id']; ?>">Mark as Read</button>
            <?php endif; ?>
                    </div>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p class="no-notifications">No notifications yet.</p>
    <?php endif; ?>
</div>

<script>
    document.querySelectorAll('.mark-read-btn').forEach(function(button) {
        button.addEventListener('click', function() {
            const btn = this;
            const notifId = btn.getAttribute('data-id');

            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'mark_notifications.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

            xhr.onload = function() {
                const response = xhr.responseText.trim();
                console.log('Server response: [' + response + ']');

                if (response === 'success') {
                    const li = btn.closest('li');
                    li.classList.remove('unread');
                    li.classList.add('read');
                    btn.remove();
                } else {
                    alert('Error: ' + response);
                }
            };

            xhr.onerror = function() {
                alert('Request failed.');
            };

            xhr.send('id=' + notifId);
        });
    });
</script>

<?php include "../includes/footer.php"; ?>