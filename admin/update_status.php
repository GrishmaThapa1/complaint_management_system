<?php
session_start();

// Prevent browser caching
header("Cache-Control: no-store, no-cache, must-revalidate, private");
header("Pragma: no-cache");
header("Expires: 0");

// Check admin login
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit;
}

include "../includes/db.php";

if (!isset($_GET['id'])) {
    header("Location: view_complaints.php");
    exit;
}

$complaint_id = intval($_GET['id']);

// Fetch complaint info
$stmt = $conn->prepare("SELECT user_id, status, admin_remarks FROM complaints WHERE id=?");
$stmt->bind_param("i", $complaint_id);
$stmt->execute();
$stmt->bind_result($user_id, $status, $admin_remarks);
if (!$stmt->fetch()) {
    $stmt->close();
    $_SESSION['error_message'] = "Complaint not found.";
    header("Location: view_complaints.php");
    exit;
}
$stmt->close();

$status = strtolower($status ?? 'pending');
$admin_remarks = $admin_remarks ?? "";
$success_message = "";

// Handle POST → mark as resolved
if ($_SERVER['REQUEST_METHOD'] === "POST" && $status === 'pending') {

    $new_remarks = trim($_POST['admin_remarks'] ?? '');
    if (empty($new_remarks)) {
        $_SESSION['error_message'] = "Admin remark is required.";
        header("Location: update_status.php?id=$complaint_id");
        exit;
    }

    // Update complaint status to resolved
    $stmt = $conn->prepare("UPDATE complaints SET status='resolved', admin_remarks=? WHERE id=?");
    $stmt->bind_param("si", $new_remarks, $complaint_id);

    if ($stmt->execute()) {

        // Insert notification
        $message = "Your complaint has been resolved. Admin Remark: " . $new_remarks;
        $notify = $conn->prepare("INSERT INTO notification (user_id, message, status) VALUES (?, ?, 'unread')");
        $notify->bind_param("is", $user_id, $message);
        $notify->execute();
        $notify->close();

        $success_message = "✅ Complaint resolved successfully!";
        $status = 'resolved';
        $admin_remarks = $new_remarks;
    } else {
        $_SESSION['error_message'] = "Failed to update complaint.";
        header("Location: update_status.php?id=$complaint_id");
        exit;
    }
}

include "../includes/header.php";
?>

<link rel="stylesheet" href="../css/style.css">

<div class="update-status-container">
    <h2>Update Complaint Status</h2>

    <!-- Flash Messages -->
    <?php if (!empty($_SESSION['error_message'])): ?>
        <p class="error-message"><?= htmlspecialchars($_SESSION['error_message']); ?></p>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <?php if (!empty($success_message)): ?>
        <p class="message"><?= htmlspecialchars($success_message); ?></p>
        <script>
            // Redirect to dashboard after 2.5 seconds
            setTimeout(() => {
                window.location.href = "dashboard.php";
            }, 2500);
        </script>
    <?php endif; ?>

    <!-- Show already resolved only if page is loaded fresh -->
    <?php if ($status === 'resolved' && empty($success_message)): ?>
        <p class="info-message">This complaint is already resolved.</p>
        <div style="margin-top:15px; padding:12px; background:#f0f0f0;
                    border-left:4px solid #28a745; border-radius:6px;">
            <strong>Admin Remark:</strong>
            <p><?= htmlspecialchars($admin_remarks); ?></p>
        </div>
        <div style="text-align:center; margin-top:20px;">
            <a href="view_complaints.php" class="btn">Back to View Complaints</a>
        </div>
    <?php elseif ($status === 'pending'): ?>
        <form method="POST">
            <label>Admin Remark (Required)</label>
            <textarea name="admin_remarks" rows="4" required
                style="width:100%; padding:8px; border-radius:6px; border:1px solid #ccc;"></textarea>
            <br><br>
            <div style="text-align:center; gap:15px; display:flex; justify-content:center; flex-wrap:wrap; margin-top:20px;">
                <input type="submit" value="Mark as Resolved" class="btn">
                <a href="view_complaints.php" class="btn">Back to View Complaints</a>
            </div>
        </form>
    <?php endif; ?>
</div>

<?php include "../includes/footer.php"; ?>