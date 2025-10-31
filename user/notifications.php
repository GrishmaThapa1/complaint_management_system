<?php
session_start();
include "../includes/db.php";
include "../includes/header.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = intval($_SESSION['user_id']);

$sql = "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$notifications = $stmt->get_result();
?>

<!-- External CSS -->
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



<?php include "../includes/footer.php"; ?>