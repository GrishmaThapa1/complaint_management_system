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

$stmt = $conn->prepare("SELECT complaint_text, status, created_at FROM complaints WHERE id=? AND user_id=?");
$stmt->bind_param("ii", $id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Complaint not found.";
    exit;
}

$complaint = $result->fetch_assoc();
?>

<div class="complaint-view">
    <h2>Complaint Details</h2>
    <p><strong>Complaint:</strong> <?= htmlspecialchars($complaint['complaint_text']) ?></p>
    <p><strong>Status:</strong> <?= htmlspecialchars($complaint['status']) ?></p>
    <p><strong>Date:</strong> <?= date("d M Y", strtotime($complaint['created_at'])) ?></p>

    <a href="dashboard.php" class="btn btn-back">Back to Dashboard</a>
</div>

<?php include "../includes/footer.php"; ?>