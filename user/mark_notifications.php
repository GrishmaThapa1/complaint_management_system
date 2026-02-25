<?php
ob_start();
session_start();
include "../includes/db.php";
ob_clean();

if (!isset($_SESSION['user_id'])) {
    echo "unauthorized";
    exit;
}

$user_id = intval($_SESSION['user_id']);

if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    echo "invalid";
    exit;
}

$notif_id = intval($_POST['id']);

$check = $conn->prepare("SELECT status FROM notification WHERE id = ? AND user_id = ?");
$check->bind_param("ii", $notif_id, $user_id);
$check->execute();
$result = $check->get_result();

if ($result->num_rows === 0) {
    echo "No such notification";
    exit;
}

$row = $result->fetch_assoc();

if ($row['status'] === 'read') {
    echo "Already read";
    exit;
}

$update = $conn->prepare("UPDATE notification SET status = 'read' WHERE id = ? AND user_id = ?");
$update->bind_param("ii", $notif_id, $user_id);

if ($update->execute() && $conn->affected_rows > 0) {
    echo "success";
} else {
    echo "error";
}
exit;
