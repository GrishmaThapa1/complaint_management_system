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

// Fetch complaint
$stmt = $conn->prepare(
    "SELECT complaint_text, status, created_at 
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
    <h2>Complaint Details</h2>

    <p><strong>Complaint:</strong> <?= htmlspecialchars($complaint['complaint_text']) ?></p>

    <p><strong>Status:</strong>
        <span class="status <?= strtolower($complaint['status']) ?>">
            <?= htmlspecialchars($complaint['status']) ?>
        </span>
    </p>

    <p class="date-line">
        <strong>Date:</strong> <?= date("d M Y", strtotime($complaint['created_at'])) ?>
    </p>

    <!-- ===== FEEDBACK SECTION ===== -->
    <?php if (strtolower($complaint['status']) === 'resolved'): ?>

        <hr>

        <?php if ($feedback): ?>
            <!-- Show submitted feedback -->
            <div class="feedback-box">
                <h3>Your Feedback</h3>
                <p><strong>Rating:</strong> <?= $feedback['rating'] ?>/5 ⭐</p>
                <p><strong>Comment:</strong> <?= htmlspecialchars($feedback['comment']) ?></p>
                <small>
                    Submitted on <?= date("d M Y", strtotime($feedback['created_at'])) ?>
                </small>
            </div>

        <?php else: ?>
            <!-- Feedback form -->
            <div class="feedback-form">
                <h3>Submit Feedback</h3>
                <form method="POST" action="submit_feedback.php">
                    <input type="hidden" name="complaint_id" value="<?= $id ?>">

                    <label>Rating:</label>
                    <select name="rating" required>
                        <option value="">Select</option>
                        <option value="5">⭐⭐⭐⭐⭐</option>
                        <option value="4">⭐⭐⭐⭐</option>
                        <option value="3">⭐⭐⭐</option>
                        <option value="2">⭐⭐</option>
                        <option value="1">⭐</option>
                    </select>

                    <label>Comment:</label>
                    <textarea name="comment" rows="4" placeholder="Write your feedback..."></textarea>

                    <button type="submit" class="btn">Submit Feedback</button>
                </form>
            </div>
        <?php endif; ?>

    <?php endif; ?>
    <!-- ===== END FEEDBACK ===== -->

    <div class="btn-center">
        <a href="dashboard.php" class="btn-back">Back to Dashboard</a>
    </div>
</div>

<?php include "../includes/footer.php"; ?>