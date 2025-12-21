<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

include "../includes/db.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: dashboard.php");
    exit;
}

$user_id = intval($_SESSION['user_id']);
$complaint_id = intval($_POST['complaint_id']);
$rating = intval($_POST['rating']);
$comment = trim($_POST['comment']);

// Validate rating
if ($rating < 1 || $rating > 5) {
    die("Invalid rating.");
}

// Check complaint belongs to user and is resolved
$stmt = $conn->prepare(
    "SELECT status FROM complaints WHERE id=? AND user_id=?"
);
$stmt->bind_param("ii", $complaint_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Unauthorized access.");
}

$complaint = $result->fetch_assoc();
if (strtolower($complaint['status']) !== 'resolved') {
    die("Feedback allowed only after resolution.");
}

// Check if feedback already exists
$check_stmt = $conn->prepare(
    "SELECT id FROM feedback WHERE complaint_id=?"
);
$check_stmt->bind_param("i", $complaint_id);
$check_stmt->execute();
$check_stmt->store_result();

if ($check_stmt->num_rows > 0) {
    die("Feedback already submitted.");
}

// Insert feedback
$insert_stmt = $conn->prepare(
    "INSERT INTO feedback (complaint_id, user_id, rating, comment)
     VALUES (?, ?, ?, ?)"
);
$insert_stmt->bind_param(
    "iiis",
    $complaint_id,
    $user_id,
    $rating,
    $comment
);
$insert_stmt->execute();

// Redirect back
header("Location: view_complaint.php?id=" . $complaint_id);
exit;
