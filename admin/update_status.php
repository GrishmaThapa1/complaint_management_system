<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include "../includes/db.php";
include "../includes/header.php";

if (!isset($_GET['id'])) {
    header("Location: view_complaints.php");
    exit();
}

$complaint_id = intval($_GET['id']);

/* Fetch complaint */
$stmt = $conn->prepare(
    "SELECT user_id, status, admin_remarks 
     FROM complaints 
     WHERE id=?"
);
$stmt->bind_param("i", $complaint_id);
$stmt->execute();
$stmt->bind_result($user_id, $status, $admin_remarks);
$stmt->fetch();
$stmt->close();

$status = strtolower($status ?? "pending"); // default to pending
$admin_remarks = $admin_remarks ?? "";

$error_message = "";

/* Handle update */
if ($_SERVER['REQUEST_METHOD'] === "POST" && $status === 'pending') {

    $new_remarks = trim($_POST['admin_remarks'] ?? '');

    if (empty($new_remarks)) {
        $error_message = "Admin remark is required when resolving a complaint.";
    } else {

        /* Update complaint to resolved */
        $stmt = $conn->prepare(
            "UPDATE complaints 
             SET status='resolved', admin_remarks=? 
             WHERE id=?"
        );
        $stmt->bind_param("si", $new_remarks, $complaint_id);

        if ($stmt->execute()) {

            $_SESSION['success_message'] = "Complaint resolved successfully.";

            /* Send notification to user */
            $message = "Your complaint has been resolved. Admin Remark: " . $new_remarks;
            $notify = $conn->prepare(
                "INSERT INTO notifications (user_id, message, status)
                 VALUES (?, ?, 'unread')"
            );
            $notify->bind_param("is", $user_id, $message);
            $notify->execute();

            $status = 'resolved';
            $admin_remarks = $new_remarks;
        } else {
            $error_message = "Failed to update complaint.";
        }
    }
}
?>

<link rel="stylesheet" href="../css/style.css">

<div class="update-status-container">
    <h2>Update Complaint Status</h2>

    <?php if (!empty($_SESSION['success_message'])): ?>
        <p class="message" id="adminRemarkMsg"><?= $_SESSION['success_message']; ?></p>
        <?php unset($_SESSION['success_message']); ?>

        <!-- Redirect to view_complaints.php after 2.5 seconds -->
        <script>
            setTimeout(() => {
                window.location.href = "view_complaints.php";
            }, 2500);
        </script>
    <?php endif; ?>

    <?php if (!empty($error_message)): ?>
        <p class="error-message"><?= $error_message; ?></p>
    <?php endif; ?>

    <?php if ($status === 'resolved'): ?>
        <p class="info-message">This complaint is already resolved.</p>
        <div style="margin-top:15px; padding:12px; background:#f0f0f0;
                    border-left:4px solid #28a745; border-radius:6px;">
            <strong>Admin Remark:</strong>
            <p><?= htmlspecialchars($admin_remarks); ?></p>
        </div>

        <!-- Back to View Complaints button for resolved complaints -->
        <div style="text-align:center; margin-top:20px;">
            <a href="view_complaints.php"
                style="padding:12px 24px; border-radius:8px; background:#007bff; color:white; 
                       text-decoration:none; font-weight:bold; font-size:16px; display:inline-block; 
                       text-align:center; cursor:pointer; box-shadow:0 4px 6px rgba(0,123,255,0.3); border:none; transition:all 0.3s ease;">
                Back to View Complaints
            </a>
        </div>

    <?php else: ?>
        <!-- Pending complaint → admin can resolve -->
        <form method="POST">
            <label>Admin Remark (Required)</label>
            <textarea name="admin_remarks" rows="4" required
                style="width:100%; padding:8px; border-radius:6px; border:1px solid #ccc;"></textarea>
            <br><br>
            <div style="text-align:center; gap:15px; display:flex; justify-content:center; flex-wrap:wrap; margin-top:20px;">
                <!-- Submit button -->
                <input type="submit" value="Mark as Resolved"
                    style="padding:12px 24px; border-radius:8px; background:#007bff; color:white; 
                           border:none; cursor:pointer; font-weight:bold; font-size:16px; 
                           box-shadow:0 4px 6px rgba(0,123,255,0.3); transition:all 0.3s ease; appearance:none; -webkit-appearance:none;">
                <!-- Back button -->
                <a href="view_complaints.php"
                    style="padding:12px 24px; border-radius:8px; background:#007bff; color:white; 
                           text-decoration:none; font-weight:bold; font-size:16px; display:inline-block; 
                           text-align:center; cursor:pointer; box-shadow:0 4px 6px rgba(0,123,255,0.3); border:none; transition:all 0.3s ease;">
                    Back to View Complaints
                </a>
            </div>
        </form>
    <?php endif; ?>
</div>

<?php include "../includes/footer.php"; ?>