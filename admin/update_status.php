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

// Fetch complaint status
$stmt = $conn->prepare("SELECT status FROM complaints WHERE id=?");
$stmt->bind_param("i", $complaint_id);
$stmt->execute();
$stmt->bind_result($status);
$stmt->fetch();
$stmt->close();

// Handle POST update
$error_message = "";
if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $new_status = $_POST['status'];

    if (empty($new_status)) {
        $error_message = "Please select a status first!";
    } else {
        $stmt = $conn->prepare("UPDATE complaints SET status=? WHERE id=?");
        $stmt->bind_param("si", $new_status, $complaint_id);
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Status updated successfully";
            // ✅ REMOVED redirect - let JS handle it
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
        <select name="status" id="status" required>
            <option value="" disabled <?php echo empty($status) || ($status !== "Pending" && $status !== "Resolved") ? "selected" : ""; ?>>Select Status</option>
            <option value="Pending" <?php echo $status === "Pending" ? "selected" : ""; ?>>Pending</option>
            <option value="Resolved" <?php echo $status === "Resolved" ? "selected" : ""; ?>>Resolved</option>
        </select>
        <br><br>
        <input type="submit" value="Update Status">
    </form>
</div>

<script src="../Js/script.js"></script>

<?php include "../includes/footer.php"; ?>