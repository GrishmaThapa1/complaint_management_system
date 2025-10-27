<?php
session_start();
include "../includes/db.php";

if (!isset($_SESSION['reset_email'])) {
    header("Location: reset_password.php");
    exit();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_password = mysqli_real_escape_string($conn, $_POST['password']);
    $email = $_SESSION['reset_email'];
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    $update = "UPDATE users SET password='$hashed_password' WHERE email='$email'";
    if (mysqli_query($conn, $update)) {
        unset($_SESSION['reset_email']);
        unset($_SESSION['otp']);
        echo "<script>alert('Password reset successfully for $email'); window.location.href='../login.php';</script>";
    } else {
        $error = "Something went wrong!";
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set New Password</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>

    <div class="reset-page">
        <div class="login-container">
            <h2>Set New Password</h2>

            <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>

            <form method="POST">
                <input type="password" name="password" placeholder="Enter new password" required>
                <button type="submit">Update Password</button>
            </form>

            <p><a href="../login.php">← Back to Login</a></p>
        </div>
    </div>

</body>

</html>