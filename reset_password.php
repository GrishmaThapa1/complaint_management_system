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

    // Check in users table
    $query_user = "SELECT * FROM users WHERE email='$email'";
    $result_user = mysqli_query($conn, $query_user);

    if (mysqli_num_rows($result_user) > 0) {
        $_SESSION['role'] = 'user';
    } else {
        // Check in admins table
        $query_admin = "SELECT * FROM admins WHERE email='$email'";
        $result_admin = mysqli_query($conn, $query_admin);

        if (mysqli_num_rows($result_admin) > 0) {
            $_SESSION['role'] = 'admin';
        } else {
            $message = "Email not found!";
            $_SESSION['role'] = null;
        }
    }

    if (!empty($_SESSION['role'])) {
        $_SESSION['reset_email'] = $email;
        $_SESSION['otp'] = rand(100000, 999999);

        // Send OTP
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'grishmathp@gmail.com';
            $mail->Password   = 'txeg lonp ieyh mjul';
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            $mail->setFrom('grishmathp@gmail.com', 'Complaint Management System');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Your OTP for Password Reset';
            $mail->Body    = "OTP for resetting <b>{$email}</b> is: <b>{$_SESSION['otp']}</b>";
            $mail->send();

            $message = "OTP sent successfully to your email.";
            $redirect = true; // Flag to redirect after showing message
        } catch (Exception $e) {
            $message = "Mailer Error: {$mail->ErrorInfo}";
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="/complaint_management/css/style.css">
</head>

<body>
    <div class="reset-page">
        <div class="login-container">
            <h2>Reset Password</h2>
            <form method="POST" action="">
                <input type="email" name="email" placeholder="Enter your registered email" required>
                <button type="submit" name="submit">Send OTP</button>
            </form>

            <?php if (!empty($message)) echo "<p class='success'>$message</p>"; ?>

            <p><a href="/complaint_management/login.php" class="back-link">← Back to Login</a></p>
        </div>
    </div>

    <?php if ($redirect): ?>
        <script>
            // Redirect after 2 seconds
            setTimeout(function() {
                window.location.href = '/complaint_management/verify_otp.php';
            }, 2000);
        </script>
    <?php endif; ?>
</body>

</html>