<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: ../login.php");
    exit;
}

include "../includes/db.php";

if (!isset($_GET['id'])) {
    echo "No complaint selected.";
    exit;
}

$id = intval($_GET['id']);
$user_id = intval($_SESSION['user_id']);

// Fetch complaint including admin remarks
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
    "SELECT rating, comments, created_at 
     FROM feedback 
     WHERE complaint_id=?"
);
$feedback_stmt->bind_param("i", $id);
$feedback_stmt->execute();
$feedback_result = $feedback_stmt->get_result();
$feedback = $feedback_result->fetch_assoc();

// Handle feedback submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['feedback_submit'])) {
    $rating = intval($_POST['rating']);
    $comment = trim($_POST['comment']);

    if ($rating >= 1 && $rating <= 5 && !$feedback && $status === 'resolved') {
        $insert_stmt = $conn->prepare(
            "INSERT INTO feedback (complaint_id, user_id, rating, comments) VALUES (?, ?, ?, ?)"
        );
        $insert_stmt->bind_param("iiis", $id, $user_id, $rating, $comment);
        $insert_stmt->execute();

        // Fetch the feedback immediately after insert
        $feedback = [
            'rating' => $rating,
            'comments' => $comment,
            'created_at' => date("Y-m-d H:i:s")
        ];
    }
}

include "../includes/header.php";
?>

<div class="complaint-view">
    <h2 style="text-align:center;">Complaint Details</h2>

    <div style="margin:15px 0; padding:15px; background:#f8f9fa; border-radius:10px;">
        <p><strong>Complaint:</strong> <?= htmlspecialchars($complaint['complaint_text']) ?></p>
    </div>

    <div style="margin:15px 0; padding:15px; background:#fff3cd; border-left:5px solid #ffecb5; border-radius:8px;">
        <strong>Admin Remark:</strong> <?= htmlspecialchars($admin_remark) ?>
    </div>

    <?php if (!empty($complaint['attachment'])): ?>
        <div style="margin:15px 0; text-align:center;">
            <?php
            $file_path = '../uploads/' . $complaint['attachment'];
            $ext = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])): ?>
                <a href="<?= $file_path ?>" target="_blank">
                    <img src="<?= $file_path ?>" style="max-width:300px; max-height:300px; border-radius:8px;">
                </a>
            <?php elseif ($ext === 'pdf'): ?>
                <a href="<?= $file_path ?>" target="_blank" style="padding:10px 18px; background:#5563DE; color:white; border-radius:8px; text-decoration:none;">📄 View PDF</a>
            <?php else: ?>
                <a href="<?= $file_path ?>" target="_blank">Download Attachment</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div style="margin:15px 0; padding:15px; background:#f8f9fa; border-radius:10px; display:flex; justify-content:space-between;">
        <span style="background:<?= $status === 'pending' ? '#f0ad4e' : '#28a745' ?>; color:white; padding:6px 14px; border-radius:6px;">
            <?= ucfirst($status) ?>
        </span>
        <span>Created: <?= date("d M Y", strtotime($complaint['created_at'])) ?></span>
    </div>

    <?php if ($status === 'resolved'): ?>
        <hr>
        <?php if ($feedback): ?>
            <div style="padding:15px; background:#f0f8ff; border-radius:10px; margin-bottom:20px;">
                <h3>Your Feedback</h3>
                <p><strong>Rating:</strong> <?= $feedback['rating'] ?>/5 ⭐</p>
                <p><strong>Comment:</strong> <?= htmlspecialchars($feedback['comments']) ?></p>
                <small>Submitted on <?= date("d M Y", strtotime($feedback['created_at'])) ?></small>
            </div>
        <?php else: ?>
            <form method="POST" style="padding:15px; background:#f9f9f9; border-radius:10px;">
                <label>Rating:</label>
                <select name="rating" required style="padding:6px 10px; margin:5px 0 15px 0;">
                    <option value="">Select</option>
                    <option value="5">⭐⭐⭐⭐⭐</option>
                    <option value="4">⭐⭐⭐⭐</option>
                    <option value="3">⭐⭐⭐</option>
                    <option value="2">⭐⭐</option>
                    <option value="1">⭐</option>
                </select>

                <label>Comment:</label>
                <textarea name="comment" rows="4" placeholder="Write your feedback..." style="width:100%; padding:8px;"></textarea>

                <button type="submit" name="feedback_submit" style="padding:8px 18px; margin-top:10px;">Submit Feedback</button>
            </form>
        <?php endif; ?>
    <?php endif; ?>

    <div style="text-align:center; margin-top:20px;">
        <a href="dashboard.php" style="padding:10px 18px; background:#0073ff; color:white; border-radius:8px; text-decoration:none;">Back to Dashboard</a>
    </div>
</div>

<?php include "../includes/footer.php"; ?>