<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

include "../includes/db.php";
include "../includes/header.php";

if (!isset($_GET['id'])) {
    echo "No complaint selected.";
    exit;
}

$id = intval($_GET['id']);
$user_id = intval($_SESSION['user_id']);

// Fetch complaint including admin_remarks
$stmt = $conn->prepare(
    "SELECT complaint_text, attachment, status, created_at, admin_remarks 
     FROM complaints 
     WHERE id=? AND user_id=?"
);
$stmt->bind_param("ii", $id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Complaint not found.";
    exit;
}

$complaint = $result->fetch_assoc();
$status = strtolower($complaint['status']);
$admin_remark = !empty($complaint['admin_remarks'])
    ? $complaint['admin_remarks']
    : "Your complaint has been received and is under review.";

// Check if feedback already exists
$feedback_stmt = $conn->prepare(
    "SELECT rating, comment, created_at 
     FROM feedback 
     WHERE complaint_id=?"
);
$feedback_stmt->bind_param("i", $id);
$feedback_stmt->execute();
$feedback_result = $feedback_stmt->get_result();
$feedback = $feedback_result->fetch_assoc();
?>

<div class="complaint-view">
    <h2 style="margin-bottom:25px; text-align:center;">Complaint Details</h2>

    <!-- Complaint Text -->
    <div style="margin-bottom:20px; padding:15px; background:#f8f9fa; border-radius:10px; box-shadow:0 2px 6px rgba(0,0,0,0.05);">
        <p style="margin:0; font-size:16px; line-height:1.6;"><strong>Complaint:</strong> <?= htmlspecialchars($complaint['complaint_text']) ?></p>
    </div>

    <!-- Admin Remarks (Always shown) -->
    <div style="margin-bottom:20px; padding:15px; background:#fff3cd; border-left:5px solid #ffecb5; border-radius:8px; font-size:15px; color:#856404;">
        <strong>Admin Remark:</strong> <?= htmlspecialchars($admin_remark) ?>
    </div>

    <!-- Attachment -->
    <?php if (!empty($complaint['attachment'])): ?>
        <div class="complaint-attachment" style="margin: 20px 0; text-align:center;">
            <?php
            $file_path = '../uploads/' . $complaint['attachment'];
            $ext = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));

            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])): ?>
                <a href="<?= $file_path ?>" target="_blank">
                    <img src="<?= $file_path ?>" style="max-width:300px; max-height:300px; border:1px solid #ccc; border-radius:8px;">
                </a>
            <?php elseif ($ext === 'pdf'): ?>
                <a href="<?= $file_path ?>" target="_blank" style="padding:10px 18px; background:#5563DE; color:white; border-radius:8px; text-decoration:none;">📄 View PDF</a>
            <?php else: ?>
                <a href="<?= $file_path ?>" target="_blank">Download Attachment</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- Status & Date Box -->
    <div style="padding:15px; background:#f8f9fa; border-radius:10px; box-shadow:0 2px 6px rgba(0,0,0,0.05); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; margin-bottom:25px;">
        <span style="background:<?= $status === 'pending' ? '#f0ad4e' : '#28a745' ?>; color:white; padding:6px 14px; border-radius:6px; font-weight:500;">
            <?= ucfirst($status) ?>
        </span>
        <span style="font-size:14px; color:#555;">Created: <?= date("d M Y", strtotime($complaint['created_at'])) ?></span>
    </div>

    <!-- Feedback Section -->
    <?php if ($status === 'resolved'): ?>
        <hr style="margin:25px 0; border-color:#ddd;">
        <?php if ($feedback): ?>
            <div class="feedback-box" style="padding:15px; background:#f0f8ff; border-radius:10px; box-shadow:0 2px 6px rgba(0,0,0,0.05); margin-bottom:20px;">
                <h3 style="margin-top:0;">Your Feedback</h3>
                <p><strong>Rating:</strong> <?= $feedback['rating'] ?>/5 ⭐</p>
                <p><strong>Comment:</strong> <?= htmlspecialchars($feedback['comment']) ?></p>
                <small>Submitted on <?= date("d M Y", strtotime($feedback['created_at'])) ?></small>
            </div>
        <?php else: ?>
            <div class="feedback-form" style="padding:15px; background:#f9f9f9; border-radius:10px; box-shadow:0 2px 6px rgba(0,0,0,0.05); margin-bottom:20px;">
                <h3 style="margin-top:0;">Submit Feedback</h3>
                <form method="POST" action="submit_feedback.php">
                    <input type="hidden" name="complaint_id" value="<?= $id ?>">
                    <label>Rating:</label>
                    <select name="rating" required style="padding:6px 10px; margin:5px 0 15px 0; border-radius:5px; border:1px solid #ccc;">
                        <option value="">Select</option>
                        <option value="5">⭐⭐⭐⭐⭐</option>
                        <option value="4">⭐⭐⭐⭐</option>
                        <option value="3">⭐⭐⭐</option>
                        <option value="2">⭐⭐</option>
                        <option value="1">⭐</option>
                    </select>

                    <label>Comment:</label>
                    <textarea name="comment" rows="4" placeholder="Write your feedback..." style="width:100%; padding:8px; border-radius:5px; border:1px solid #ccc; margin-bottom:10px;"></textarea>

                    <button type="submit" class="btn" style="display:inline-block;">Submit Feedback</button>
                </form>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <!-- Back Button -->
    <div class="btn-center" style="text-align:center; margin-top:20px;">
        <a href="dashboard.php" class="btn-back" style="padding:10px 18px; border-radius:8px; background:#0073ff; color:white; text-decoration:none;">Back to Dashboard</a>
    </div>
</div>

<?php include "../includes/footer.php"; ?>