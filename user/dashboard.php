<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}
include "../includes/db.php";
include "../includes/header.php";

$user_id = intval($_SESSION['user_id']);
$complaints = $conn->query("SELECT * FROM complaints WHERE user_id=$user_id ORDER BY created_at DESC");
?>

<div class="dashboard-container">
    <div class="dashboard-header">
        <h1>Welcome, <?= htmlspecialchars($_SESSION['user_name']) ?></h1>
        <div class="header-buttons">
            <a href="submit_complaint.php" class="btn btn-primary">Submit Complaint</a>
        </div>
    </div>

    <section class="complaints-section">
        <h2>Your Complaints</h2>

        <?php if ($complaints && $complaints->num_rows > 0): ?>
            <table class="complaints-table">
                <tr>
                    <th>Complaint</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>View</th>
                </tr>
                <?php while ($row = $complaints->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars(substr($row['complaint_text'], 0, 50)) ?>...</td>
                        <td><?= htmlspecialchars($row['status']) ?></td>
                        <td><?= date("d M Y", strtotime($row['created_at'])) ?></td>
                        <td><a href="view_complaint.php?id=<?= $row['id'] ?>" class="btn btn-view">View</a></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <div class="no-complaints">
                <p><strong>No complaints found.</strong></p>
                <p><a href="submit_complaint.php" class="btn btn-secondary">Submit Your First Complaint</a></p>
            </div>
        <?php endif; ?>
    </section>
</div>

<?php include "../includes/footer.php"; ?>