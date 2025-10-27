<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include "../includes/db.php";
include "../includes/header.php";

// Query to get complaints with user names
$result = $conn->query("
    SELECT complaints.id, users.name, complaints.complaint_text, complaints.status, complaints.created_at
    FROM complaints
    JOIN users ON complaints.user_id = users.id
    ORDER BY complaints.created_at DESC
");

// Check for errors
if (!$result) {
    die("Query failed: " . $conn->error);
}
?>

<div class="admin-dashboard">
    <h2 style="text-align:center; margin-top:20px;">All Complaints</h2>

    <div class="view-complaints">
        <div class="cards">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="card">
                        <h2>Complaint #<?php echo $row['id']; ?></h2>
                        <p><strong>User:</strong> <?php echo htmlspecialchars($row['name']); ?></p>
                        <p><strong>Complaint:</strong> <?php echo htmlspecialchars($row['complaint_text']); ?></p>
                        <p><strong>Status:</strong> <?php echo htmlspecialchars($row['status']); ?></p>
                        <p><strong>Created:</strong> <?php echo $row['created_at']; ?></p>
                        <a class="btn" href="update_status.php?id=<?php echo $row['id']; ?>">Update Status</a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="text-align:center;">No complaints found.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>