<?php
session_start();
include __DIR__ . "/includes/db.php";

if (!isset($_SESSION['reset_email']) || !isset($_SESSION['role'])) {
    header("Location: /complaint_management/reset_password.php");
    exit();
}

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $email = $_SESSION['reset_email'];
    $role = $_SESSION['role'];

    if (empty($new_password) || empty($confirm_password)) {
        $error = "All fields are required.";
    } elseif ($new_password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (strlen($new_password) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $table = ($role === 'admin') ? 'admins' : 'users';

        $stmt = $conn->prepare("UPDATE $table SET password=? WHERE email=?");
        $stmt->bind_param("ss", $hashed_password, $email);

        if ($stmt->execute()) {
            unset($_SESSION['reset_email']);
            unset($_SESSION['otp']);
            unset($_SESSION['role']);

            $success = "Password reset successfully!";
        } else {
            $error = "Something went wrong! Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set New Password</title>
    <link rel="stylesheet" href="/complaint_management/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="reset-page">
    <div class="login-container">
        <h2>Set New Password</h2>

        <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
        <?php if (!empty($success)) echo "<p class='success'>$success</p>"; ?>

        <form method="POST" id="resetForm" autocomplete="off">
            <div class="form-group">
                <label for="password">New Password</label>
                <div class="password-wrapper">
                    <input type="password" id="password" name="password" placeholder="Enter new password" required>
                    <i class="fa-solid fa-eye-slash password-toggle"></i>
                </div>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <div class="password-wrapper">
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm new password" required>
                    <i class="fa-solid fa-eye-slash password-toggle"></i>
                </div>
            </div>

            <button type="submit" class="btn-login">Update Password</button>
        </form>

        <p><a href="/complaint_management/login.php" class="back-link">← Back to Login</a></p>
    </div>

    <script src="/complaint_management/Js/script.js"></script>

    <?php if (!empty($success)): ?>
        <script>
            setTimeout(() => {
                window.location.href = '/complaint_management/login.php';
            }, 2000); // redirect after 2 seconds
        </script>
    <?php endif; ?>
</body>

</html>