<?php
session_start();

// Prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, private");
header("Pragma: no-cache");
header("Expires: 0");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit;
}

include "../includes/db.php";
include "../includes/header.php";

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $subject = trim($_POST['subject']);
    $complaint_text = trim($_POST['complaint_text']);
    $user_id = intval($_SESSION['user_id']);

    // Validation
    if (empty($subject) || empty($complaint_text)) {
        $error = "❌ Subject and Complaint cannot be empty.";
    }

    // File Upload Handling
    $attachment_path = NULL;

    if (empty($error) && isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {

        $file_tmp  = $_FILES['attachment']['tmp_name'];
        $original_name = basename($_FILES['attachment']['name']);
        $file_ext  = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));

        $allowed_types = ['jpg', 'jpeg', 'png', 'pdf'];

        if (!in_array($file_ext, $allowed_types)) {
            $error = "❌ Invalid file type. Only JPG, PNG, PDF allowed.";
        }

        if ($_FILES['attachment']['size'] > 2 * 1024 * 1024) {
            $error = "❌ File size must be less than 2MB.";
        }

        if (empty($error)) {

            $upload_dir = "../uploads/";
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            $new_file_name = time() . "_" . uniqid() . "." . $file_ext;
            $destination = $upload_dir . $new_file_name;

            if (move_uploaded_file($file_tmp, $destination)) {
                $attachment_path = $new_file_name;
            } else {
                $error = "❌ Failed to upload file.";
            }
        }
    }

    // Check for duplicate pending complaint (robust)
    if (empty($error)) {

        // Normalize strings: trim, lowercase, remove extra spaces
        $norm_subject = mb_strtolower(trim($subject));
        $norm_text = mb_strtolower(trim($complaint_text));
        $norm_text = preg_replace('/\s+/', ' ', $norm_text); // remove multiple spaces/newlines

        $dup_stmt = $conn->prepare(
            "SELECT id FROM complaints 
             WHERE user_id=? AND status='pending' 
             AND LOWER(TRIM(subject))=? 
             AND LOWER(TRIM(complaint_text))=?"
        );
        $dup_stmt->bind_param("iss", $user_id, $norm_subject, $norm_text);
        $dup_stmt->execute();
        $dup_result = $dup_stmt->get_result();

        if ($dup_result && $dup_result->num_rows > 0) {
            $error = "⚠️ You already submitted a similar complaint that is still pending";
        }
    }

    // Insert complaint
    if (empty($error)) {

        $stmt = $conn->prepare("INSERT INTO complaints 
            (user_id, subject, complaint_text, attachment, status, created_at) 
            VALUES (?, ?, ?, ?, 'pending', NOW())");

        $stmt->bind_param("isss", $user_id, $subject, $complaint_text, $attachment_path);

        if ($stmt->execute()) {
            $success = "✅ Complaint submitted successfully!";
        } else {
            $error = "❌ Failed to submit complaint. Try again.";
        }
    }
}
?>

<script>
    window.onpageshow = function(event) {
        if (event.persisted) {
            window.location.reload();
        }
    };

    <?php if (!empty($success)): ?>
        setTimeout(() => {
            window.location.href = "dashboard.php";
        }, 2500);
    <?php endif; ?>
</script>

<body class="submit-page">

    <div class="submit-page-content-wrapper">
        <div class="submit-page-content">
            <h2>Submit Complaint</h2>

            <?php if (!empty($error)): ?>
                <p class="error"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <p class="success"><?= htmlspecialchars($success) ?></p>
            <?php endif; ?>

            <form method="post" enctype="multipart/form-data">

                <div class="form-group">
                    <label>Subject</label>
                    <input type="text" name="subject" placeholder="Enter subject..." required>
                </div>

                <div class="form-group">
                    <label>Complaint</label>
                    <textarea name="complaint_text" placeholder="Enter your complaint..." required></textarea>
                </div>

                <div class="form-group">
                    <label>Attachment (optional)</label>
                    <input type="file" name="attachment" accept=".jpg,.jpeg,.png,.pdf">
                    <small>Max size: 2MB</small>
                </div>

                <input type="submit" value="Submit Complaint" class="btn-submit">

            </form>
        </div>
    </div>

    <?php include "../includes/footer.php"; ?>
</body>