<?php
session_start();
include __DIR__ . "/includes/db.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/PHPMailer-master/src/Exception.php';
require __DIR__ . '/PHPMailer-master/src/PHPMailer.php';
require __DIR__ . '/PHPMailer-master/src/SMTP.php';

$message = "";
$redirect = false;

if (isset($_POST['submit'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    // Check if email exists in users table
    $query_user = "SELECT * FROM users WHERE email='$email' LIMIT 1";
    $result_user = mysqli_query($conn, $query_user);

    if (mysqli_num_rows($result_user) > 0) {
        $user = mysqli_fetch_assoc($result_user);

        $_SESSION['role'] = $user['role']; 
        $_SESSION['reset_email'] = $email;
        $_SESSION['otp'] = rand(100000, 999999);

        // PHPMailer
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;

            // ✅ SECURE WAY (NO HARDCODED PASSWORD)
            $mail->Username   = getenv('SMTP_USER');
            $mail->Password   = getenv('SMTP_PASS');

            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            $mail->setFrom(getenv('SMTP_USER'), 'Complaint Management System');
            $mail->addAddress($email); // send OTP to user email

            $mail->isHTML(true);
            $mail->Subject = 'Your OTP for Password Reset';
            $mail->Body    = "
                OTP for resetting <b>{$email}</b> is: 
                <h2>{$_SESSION['otp']}</h2>
            ";

            $mail->send();
            $message = "OTP sent successfully for <b>{$email}</b>";
            $redirect = true;

        } catch (Exception $e) {
            $message = "Mailer Error: {$mail->ErrorInfo}";
        }

    } else {
        $message = "Email not found!";
        $_SESSION['role'] = null;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="/complaint_management/css/style.css">
</head>

<body class="reset-page">
    <div class="login-container">
        <h2>Reset Password</h2>

        <form method="POST" action="">
            <input type="email" name="email" placeholder="Enter your registered email" required>
            <button type="submit" name="submit">Send OTP</button>
        </form>

        <?php if (!empty($message)): ?>
            <p class="<?= empty($_SESSION['role']) ? 'error' : 'success' ?>">
                <?= $message ?>
            </p>
        <?php endif; ?>

        <p><a href="/complaint_management/login.php" class="back-link">← Back to Login</a></p>
    </div>

    <?php if ($redirect): ?>
        <script>
            setTimeout(() => {
                window.location.href = '/complaint_management/verify_otp.php';
            }, 1500);
        </script>
    <?php endif; ?>
</body>
</html>
