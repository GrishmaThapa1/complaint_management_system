<?php
$pageTitle = "Complaint Management System - Contact Us";
include "includes/header.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';

$messageSent = "";

if (isset($_POST['send_message'])) {
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $messageContent = htmlspecialchars(trim($_POST['message']));

    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'grishmathp@gmail.com'; // your email
        $mail->Password   = 'txeg lonp ieyh mjul';   // gmail app password
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom($email, $name);
        $mail->addAddress('grishmathp@gmail.com', 'Complaint Management System'); // your email

        // Content
        $mail->isHTML(true);
        $mail->Subject = "New Contact Message from $name";
        $mail->Body    = "
            <h3>New Message from Complaint Management System</h3>
            <p><b>Name:</b> {$name}</p>
            <p><b>Email:</b> {$email}</p>
            <p><b>Message:</b><br>{$messageContent}</p>
        ";

        $mail->send();
        $messageSent = "Your message has been sent successfully!";
    } catch (Exception $e) {
        $messageSent = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>

<section class="contact-section">
    <div class="contact-wrapper">
        <h2>Contact Us</h2>
        <p>If you have any questions or need assistance, please contact us using the information below.</p>

        <div class="contact-container">
            <div class="contact-image">
                <img src="Image/contact.jpg" alt="Contact Us">
            </div>

            <div class="contact-form">
                <h3>Send a Message</h3>
                <?php if (!empty($messageSent)) echo "<p class='success'>{$messageSent}</p>"; ?>
                <form action="" method="post">
                    <input type="text" name="name" placeholder="Your Name" required>
                    <input type="email" name="email" placeholder="Your Email" required>
                    <textarea name="message" placeholder="Your Message" required rows="5"></textarea>
                    <input type="submit" name="send_message" value="Send Message">
                </form>
            </div>

            <div class="contact-details">
                <h3>Our Office</h3>
                <p><i class="fas fa-map-marker-alt"></i> Kathmandu, Nepal</p>
                <p><i class="fas fa-envelope"></i> info@complaintsystem.com</p>
                <p><i class="fas fa-phone"></i> +977 9800000000</p>
            </div>
        </div>
    </div>
</section>

<?php include "includes/footer.php"; ?>