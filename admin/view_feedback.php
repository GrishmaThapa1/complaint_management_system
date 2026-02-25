<?php
session_start();

// Prevent browser caching
header("Cache-Control: no-store, no-cache, must-revalidate, private");
header("Pragma: no-cache");
header("Expires: 0");

if ($_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

include "../includes/db.php";
include "../includes/header.php";

// Fetch all feedbacks with complaint and user info
$sql = "SELECT f.id AS feedback_id, f.rating, f.comments, f.created_at AS feedback_date,
        c.subject AS complaint_subject, u.username AS user_name
        FROM feedback f
        LEFT JOIN complaints c ON f.complaint_id = c.id
        LEFT JOIN users u ON f.user_id = u.id
        ORDER BY f.created_at DESC";

$result = $conn->query($sql);
?>

<script>
    window.onpageshow = function(event) {
        if (event.persisted) {
            window.location.reload();
        }
    };
</script>

<link rel="stylesheet" href="../css/style.css">

<div class="dashboard-wrapper">
    <div class="dashboard-container">
        <div class="dashboard-inner">
            <h2>User Feedbacks</h2>

            <?php if ($result && $result->num_rows > 0): ?>
                <div class="feedback-table-container">
                    <table class="feedback-table">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Complaint</th>
                                <th>Rating</th>
                                <th>Comment</th>
                                <th>Submitted On</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['user_name'] ?? 'Unknown User') ?></td>
                                    <td><?= htmlspecialchars($row['complaint_subject'] ?? 'Unknown Complaint') ?></td>
                                    <td><?= htmlspecialchars($row['rating']) ?> ⭐</td>
                                    <td class="comment"><?= htmlspecialchars($row['comments']) ?></td>
                                    <td><?= date("d M Y", strtotime($row['feedback_date'])) ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p>No feedback submitted yet.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>