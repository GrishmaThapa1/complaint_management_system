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
    $subject = htmlspecialchars(trim($_POST['subject']));
    $complaint_text = htmlspecialchars(trim($_POST['complaint_text']));
    $user_id = $_SESSION['user_id'];

    if (empty($subject) || empty($complaint_text)) {
        $error = "Please fill in both subject and complaint.";
    } else {
        $stmt = $conn->prepare("INSERT INTO complaints (user_id, subject, complaint_text, status, created_at) VALUES (?, ?, ?, 'Pending', NOW())");
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("iss", $user_id, $subject, $complaint_text);

        if ($stmt->execute()) {
            $success = "Complaint submitted successfully!";
        } else {
            $error = "Failed to submit complaint. Try again.";
        }
    }
}
?>

<body class="submit-page">
    <main class="submit-page-content">
        <div class="submit-container">
            <h2>Submit Complaint</h2>

            <?php if (!empty($error)): ?>
                <p class="error message"><?= $error ?></p>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <!-- Added id for external JS redirect -->
                <p id="successMsg" class="success message"><?= $success ?></p>
            <?php endif; ?>

            <form method="post">
                <div class="form-group">
                    <label for="subject">Subject</label>
                    <input type="text" id="subject" name="subject" placeholder="Enter subject..." required>
                </div>
                <div class="form-group">
                    <label for="complaint_text">Complaint</label>
                    <textarea id="complaint_text" name="complaint_text" placeholder="Enter your complaint..." required></textarea>
                </div>
                <input type="submit" value="Submit Complaint" class="btn-submit">
            </form>
        </div>
    </main>
   

    <?php include "../includes/footer.php"; ?>
</body>