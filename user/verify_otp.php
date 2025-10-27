<?php
session_start();

if (!isset($_SESSION['reset_email']) || !isset($_SESSION['otp'])) {
    header("Location: reset_password.php");
    exit();
}

$error = "";

if (isset($_POST['verify'])) {
    $entered_otp = trim($_POST['otp']);

    if ($entered_otp == $_SESSION['otp']) {
        header("Location: new_password.php");
        exit();
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
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>

    <div class="reset-page">
        <div class="login-container">
            <h2>Verify OTP</h2>
            <p>Resetting password for: <b><?= $_SESSION['reset_email'] ?></b></p>

            <form method="POST" action="">
                <input type="text" name="otp" placeholder="Enter OTP" required>
                <button type="submit" name="verify">Verify</button>
            </form>

            <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>

            <p><a href="reset_password.php">← Back to Reset Password</a></p>
        </div>
    </div>

</body>

</html>