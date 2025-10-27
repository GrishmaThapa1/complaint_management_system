<?php
session_start();
include "../includes/db.php";

if (!isset($_SESSION['reset_email'])) {
    header("Location: reset_password.php");
    exit();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $email = $_SESSION['reset_email'];

    // Validation
    if (empty($new_password) || empty($confirm_password)) {
        $error = "All fields are required.";
    } elseif ($new_password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (strlen($new_password) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("UPDATE users SET password=? WHERE email=?");
        $stmt->bind_param("ss", $hashed_password, $email);

        if ($stmt->execute()) {
            unset($_SESSION['reset_email']);
            unset($_SESSION['otp']);
            echo "<script>alert('Password reset successfully for $email'); window.location.href='../login.php';</script>";
        } else {
            $error = "Something went wrong!";
        }
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .password-wrapper {
            position: relative;
        }

        .password-wrapper input {
            width: 100%;
            padding-right: 40px;
        }

        .password-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #666;
            user-select: none;
        }

        .password-toggle:hover {
            color: #333;
        }
    </style>
</head>

<body>

    <div class="reset-page">
        <div class="login-container">
            <h2>Set New Password</h2>

            <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>

            <form method="POST" id="resetForm">
                <div class="form-group">
                    <label for="password">New Password</label>
                    <div class="password-wrapper">
                        <input type="password" id="password" name="password" placeholder="Enter new password" required>
                        <i class="fa-solid fa-eye-slash password-toggle" id="togglePassword"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <div class="password-wrapper">
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm new password" required>
                        <i class="fa-solid fa-eye-slash password-toggle" id="toggleConfirmPassword"></i>
                    </div>
                </div>

                <button type="submit">Update Password</button>
            </form>

            <p><a href="../login.php">← Back to Login</a></p>
        </div>
    </div>

    <script>
        // Password toggle
        const togglePassword = document.getElementById('togglePassword');
        const passwordField = document.getElementById('password');

        togglePassword.addEventListener('click', function() {
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);

            this.classList.toggle('fa-eye-slash');
            this.classList.toggle('fa-eye');
        });

        // Confirm Password toggle
        const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
        const confirmPasswordField = document.getElementById('confirm_password');

        toggleConfirmPassword.addEventListener('click', function() {
            const type = confirmPasswordField.getAttribute('type') === 'password' ? 'text' : 'password';
            confirmPasswordField.setAttribute('type', type);

            this.classList.toggle('fa-eye-slash');
            this.classList.toggle('fa-eye');
        });

        // Form validation
        document.getElementById('resetForm').addEventListener('submit', function(event) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;

            if (password !== confirmPassword) {
                alert('Passwords do not match!');
                event.preventDefault();
            } else if (password.length < 6) {
                alert('Password must be at least 6 characters!');
                event.preventDefault();
            }
        });
    </script>

</body>

</html>