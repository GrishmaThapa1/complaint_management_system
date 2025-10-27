<?php
include "../includes/db.php"; // adjust path if needed

$new_password = "admin123"; // the password you want
$hash = password_hash($new_password, PASSWORD_DEFAULT);

$sql = "UPDATE admins SET password = ? WHERE username = 'admin'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $hash);

if ($stmt->execute()) {
    echo "✅ Admin password reset successfully.<br>";
    echo "Username: admin<br>";
    echo "Password: $new_password";
} else {
    echo "❌ Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
