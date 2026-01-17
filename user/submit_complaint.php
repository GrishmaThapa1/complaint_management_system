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

    // Handle file upload
    $attachment_path = NULL; // default if no file uploaded
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['attachment']['tmp_name'];
        $file_name = time() . '_' . uniqid() . '_' . basename($_FILES['attachment']['name']); // unique filename
        $file_type = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        $allowed_types = ['jpg', 'jpeg', 'png', 'pdf'];

        if (!in_array($file_type, $allowed_types)) {
            $error = "❌ Invalid file type. Only JPG, PNG, and PDF allowed.";
        } elseif ($_FILES['attachment']['size'] > 2 * 1024 * 1024) { // 2MB limit
            $error = "❌ File size must be less than 2MB.";
        } else {
            $upload_dir = "../uploads/"; // <-- just one folder now
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

            $destination = $upload_dir . $file_name;
            if (move_uploaded_file($file_tmp, $destination)) {
                $attachment_path = $file_name;
            } else {
                $error = "❌ Failed to upload file.";
            }
        }
    }

    if (empty($error)) {
        $stmt = $conn->prepare("INSERT INTO complaints (user_id, subject, complaint_text, attachment, status, created_at) VALUES (?, ?, ?, ?, 'Pending', NOW())");
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("isss", $user_id, $subject, $complaint_text, $attachment_path);

        if ($stmt->execute()) {
            $success = "✅ Complaint submitted successfully!";
        } else {
            $error = "❌ Failed to submit complaint. Try again.";
        }
    }
}
?>

<body class="submit-page">

    <div class="submit-page-content-wrapper">
        <div class="submit-page-content">
            <h2>Submit Complaint</h2>

            <?php if (!empty($error)): ?>
                <p id="errorMsg" class="error-msg"><?= $error ?></p>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <p id="successMsg" class="success-msg"><?= $success ?></p>
            <?php endif; ?>

            <form method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="subject">Subject</label>
                    <input type="text" id="subject" name="subject" placeholder="Enter subject..." required>
                </div>
                <div class="form-group">
                    <label for="complaint_text">Complaint</label>
                    <textarea id="complaint_text" name="complaint_text" placeholder="Enter your complaint..." required></textarea>
                </div>
                <div class="form-group">
                    <label for="attachment">Attachment (optional)</label>
                    <input type="file" id="attachment" name="attachment" accept=".jpg,.jpeg,.png,.pdf">
                </div>
                <input type="submit" value="Submit Complaint" class="btn-submit">
            </form>
        </div>
    </div>

    <script>
        // Auto fade out messages
        document.addEventListener("DOMContentLoaded", function() {
            const successMsg = document.getElementById("successMsg");
            const errorMsg = document.getElementById("errorMsg");

            [successMsg, errorMsg].forEach(msg => {
                if (msg) {
                    setTimeout(() => {
                        msg.style.opacity = "0";
                        msg.style.transform = "translateY(-10px)";
                        setTimeout(() => msg.remove(), 1000);
                    }, 2500); // 2.5 seconds visible
                }
            });
        });
    </script>

    <?php include "../includes/footer.php"; ?>
</body>