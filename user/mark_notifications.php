<?php
session_start();
include "../includes/db.php";

// Ensure clean output
ob_clean();
header('Content-Type: text/plain');

// Check if user is logged in and ID is sent
if (!isset($_SESSION['user_id']) || !isset($_POST['id'])) {
    http_response_code(400);
    exit("Invalid request");
}

$user_id = intval($_SESSION['user_id']);
$notification_id = intval($_POST['id']);

// Step 1: Verify notification exists for this user
$stmt = $conn->prepare("SELECT status FROM notifications WHERE id=? AND user_id=? LIMIT 1");
$stmt->bind_param("ii", $notification_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if (!$result || $result->num_rows === 0) {
    exit("No such notification");
}

$row = $result->fetch_assoc();

// Step 2: Check if already read
if ($row['status'] === 'read') {
    exit("Already read");
}

// Step 3: Update status to 'read'
$stmt = $conn->prepare("UPDATE notifications SET status='read' WHERE id=? AND user_id=?");
$stmt->bind_param("ii", $notification_id, $user_id);
$stmt->execute();

// Step 4: Return clean success/fail
if ($stmt->affected_rows > 0) {
    exit("success");
} else {
    exit("fail");
}
