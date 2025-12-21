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

// Fetch complaint details
$stmt = $conn->prepare("SELECT user_id, status FROM complaints WHERE id=?");
$stmt->bind_param("i", $complaint_id);
$stmt->execute();
$stmt->bind_result($user_id, $status);
$stmt->fetch();
$stmt->close();

// Ensure $status is always a string
$status = $status ?? "";

// Handle POST update
$error_message = "";
if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $new_status = $_POST['status'] ?? "";

    if (empty($new_status)) {
        $error_message = "Please select a status first!";
    } elseif (strtolower($status) === 'resolved') {
        // Prevent updating if already resolved
        $error_message = "Cannot update status. This complaint is already resolved.";
    } else {
        // Update complaint status
        $stmt = $conn->prepare("UPDATE complaints SET status=? WHERE id=?");
        $stmt->bind_param("si", $new_status, $complaint_id);
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Status updated successfully";

            // Create notification for the user
            $notification_message = "Your complaint status has been updated to: $new_status";
            $stmt = $conn->prepare("INSERT INTO notifications (user_id, message, status) VALUES (?, ?, 'unread')");
            $stmt->bind_param("is", $user_id, $notification_message);
            $stmt->execute();
        } else {
            $error_message = "Error updating status: " . $conn->error;
        }
    }
}
?>

<link rel="stylesheet" href="../css/style.css">

<div class="update-status-container">
    <h2>Update Complaint Status</h2>

    <?php if (!empty($_SESSION['success_message'])): ?>
        <p class="message" id="successMsg"><?php echo $_SESSION['success_message']; ?></p>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <?php if (!empty($error_message)): ?>
        <p class="error-message"><?php echo $error_message; ?></p>
    <?php endif; ?>

    <form method="POST">
        <label for="status">Update Status:</label><br>
        <select name="status" id="status" required <?php echo (strtolower($status) === 'resolved') ? 'disabled' : ''; ?>>
            <option value="" disabled <?php echo empty($status) || ($status !== "Pending" && $status !== "Resolved") ? "selected" : ""; ?>>Select Status</option>
            <option value="Pending" <?php echo $status === "Pending" ? "selected" : ""; ?>>Pending</option>
            <option value="Resolved" <?php echo $status === "Resolved" ? "selected" : ""; ?>>Resolved</option>
        </select>
        <br><br>
        <input type="submit" value="Update Status" <?php echo (strtolower($status) === 'resolved') ? 'disabled' : ''; ?>>
    </form>

    <?php if (strtolower($status) === 'resolved'): ?>
        <p class="info-message">This complaint is already resolved and cannot be updated.</p>
    <?php endif; ?>
</div>

<?php include "../includes/footer.php"; ?>