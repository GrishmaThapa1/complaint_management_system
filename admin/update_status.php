<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include "../includes/db.php";
include "../includes/header.php"; // <-- only once

if (!isset($_GET['id'])) {
    header("Location: view_complaints.php");
    exit();
}

$complaint_id = intval($_GET['id']);

// Fetch complaint status
$stmt = $conn->prepare("SELECT status FROM complaints WHERE id=?");
$stmt->bind_param("i", $complaint_id);
$stmt->execute();
$stmt->bind_result($status);
$stmt->fetch();
$stmt->close();

// Update status if form submitted
if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $new_status = $_POST['status'];
    $stmt = $conn->prepare("UPDATE complaints SET status=? WHERE id=?");
    $stmt->bind_param("si", $new_status, $complaint_id);
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Status updated successfully";
        header("Location: view_complaints.php");
        exit();
    } else {
        echo "Error updating status: " . $conn->error;
    }
}
?>

<div class="update-status-container">
    <h2>Update Complaint Status</h2>
    <form method="POST">
        <label for="status">Select Status:</label><br>
        <select name="status" id="status" required>
            <option value="Pending" <?php if ($status == "Pending") echo "selected"; ?>>Pending</option>
            <option value="In Progress" <?php if ($status == "In Progress") echo "selected"; ?>>In Progress</option>
            <option value="Resolved" <?php if ($status == "Resolved") echo "selected"; ?>>Resolved</option>
        </select><br><br>
        <input type="submit" value="Update Status">
    </form>
</div>

<?php include "../includes/footer.php"; ?>