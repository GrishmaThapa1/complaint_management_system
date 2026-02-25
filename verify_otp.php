<?php
session_start();

if (!isset($_SESSION['reset_email']) || !isset($_SESSION['otp']) || !isset($_SESSION['role'])) {
    header("Location: /complaint_management/reset_password.php");
    exit();
}

$error = "";
$success = "";

if (isset($_POST['verify'])) {
    $entered_otp = trim($_POST['otp']);

    if ($entered_otp == $_SESSION['otp']) {
        $success = "OTP verified successfully! Redirecting...";
        
    } else {
        $error = "Invalid OTP!";
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP</title>
    <link rel="stylesheet" href="/complaint_management/css/style.css">
    
</head>

<body>
    <div class="reset-page">
        <div class="login-container">
            <h2>Verify OTP</h2>
            <p>Resetting password for: <b><?= htmlspecialchars($_SESSION['reset_email']); ?></b></p>

            <form method="POST" action="">
                <input type="text" name="otp" placeholder="Enter OTP" required>
                <button type="submit" name="verify">Verify</button>
            </form>

            <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
            <?php if (!empty($success)) echo "<p class='success'>$success</p>"; ?>

            <?php if (!empty($success)): ?>
                <script>
                    // Redirect to new_password.php after 2 seconds
                    setTimeout(function() {
                        window.location.href = '/complaint_management/new_password.php';
                    }, 2000);
                </script>
            <?php endif; ?>

            <p><a href="/complaint_management/reset_password.php" class="back-link">← Back to Reset Password</a></p>
        </div>
    </div>
</body>

</html>