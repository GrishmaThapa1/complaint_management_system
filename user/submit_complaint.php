<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}
include "../includes/db.php";
include "../includes/header.php";

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $complaint_text = htmlspecialchars(trim($_POST['complaint_text']));
    $user_id = $_SESSION['user_id'];

    if (empty($complaint_text)) {
        $error = "Please enter your complaint.";
    } else {
        $stmt = $conn->prepare("INSERT INTO complaints (user_id, complaint_text, status, created_at) VALUES (?, ?, 'Pending', NOW())");
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("is", $user_id, $complaint_text);

        if ($stmt->execute()) {
            $success = "Complaint submitted successfully!";
        } else {
            $error = "Failed to submit complaint. Try again.";
        }
    }
}
?>

<main class="submit-page-content">
    <div class="submit-container">
        <h2>Submit Complaint</h2>

        <?php if (!empty($error)): ?>
            <p class="error"><?= $error ?></p>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <p class="success"><?= $success ?></p>
            <p><a href="dashboard.php" class="btn btn-primary">Back to Dashboard</a></p>
        <?php endif; ?>

        <form method="post">
            <div class="form-group">
                <label for="complaint_text">Complaint</label>
                <textarea id="complaint_text" name="complaint_text" placeholder="Enter your complaint..." required></textarea>
            </div>
            <input type="submit" value="Submit Complaint" class="btn-submit">
        </form>
    </div>
</main>

<?php include "../includes/footer.php"; ?>