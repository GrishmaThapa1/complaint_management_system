<?php
session_start();
include "../includes/db.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer-master/src/Exception.php';
require '../PHPMailer-master/src/PHPMailer.php';
require '../PHPMailer-master/src/SMTP.php';

$message = "";

if (isset($_POST['submit'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    // Check if email exists in DB
    $query = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $_SESSION['reset_email'] = $email; // store imaginary email

        // Generate OTP
        $otp = rand(100000, 999999);
        $_SESSION['otp'] = $otp;

        // Store your real email for sending OTP
        $_SESSION['otp_sent_to'] = "grishmathp@gmail.com";

        // Send OTP email
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'grishmathp@gmail.com'; // YOUR email
            $mail->Password   = 'txeg lonp ieyh mjul';   // Gmail app password
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            $mail->setFrom('grishmathp@gmail.com', 'Complaint Management System');
            $mail->addAddress($_SESSION['otp_sent_to']);

            $mail->isHTML(true);
            $mail->Subject = 'Your OTP for Password Reset';
            $mail->Body    = "OTP for resetting <b>{$email}</b> is: <b>$otp</b>";

            $mail->send();

            $message = "OTP sent successfully to your email.";
            header("Location: verify_otp.php");
            exit();
        } catch (Exception $e) {
            $message = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        $message = "Email not found!";
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>

    <div class="reset-page">
        <div class="login-container">
            <h2>Reset Password</h2>

            <form method="POST" action="">
                <input type="email" name="email" placeholder="Enter your registered email" required>
                <button type="submit" name="submit">Send OTP</button>
            </form>

            <?php if (!empty($message)) echo "<p class='error'>$message</p>"; ?>

            <p><a href="../login.php">← Back to Login</a></p>
        </div>
    </div>

</body>

</html>