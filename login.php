<?php
session_start();
include "includes/db.php";

$error = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usernameOrEmail = trim($_POST['username_or_email']);
    $password = $_POST['password'];

    $user = null;

    // Check if admin
    $stmt = $conn->prepare("SELECT id, username, password FROM admins WHERE username=?");
    $stmt->bind_param("s", $usernameOrEmail);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $user['role'] = "admin";
    } else {
        // Check regular users
        $stmt = $conn->prepare("SELECT id, name, email, password, role FROM users WHERE email=?");
        $stmt->bind_param("s", $usernameOrEmail);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
        }
    }

    if ($user) {
        if (password_verify($password, $user['password'])) {
            // Set session based on role
            $_SESSION['role'] = $user['role'];

            if ($user['role'] === "admin") {
                $_SESSION['admin_id'] = $user['id'];
                $_SESSION['name'] = $user['username'];
                header("Location: /complaint_management/admin/dashboard.php");
                exit();
            } else {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                header("Location: /complaint_management/user/dashboard.php");
                exit();
            }
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "Invalid credentials.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="/complaint_management/css/style.css">
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
        }

        .password-toggle:hover {
            color: #333;
        }
    </style>
</head>

<body class="login-page">
    <section class="login-section">
        <div class="login-container">
            <h2>Login</h2>

            <?php if ($error): ?>
                <p class="error"><?= $error ?></p>
            <?php endif; ?>

            <form method="POST" id="loginForm" autocomplete="off">
                <div class="form-group">
                    <label for="username_or_email">Username or Email</label>
                    <input type="text" id="username_or_email" name="username_or_email" placeholder="Enter username or email" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="password-wrapper">
                        <input type="password" id="password" name="password" placeholder="Enter password" required>
                        <i class="fa-solid fa-eye-slash password-toggle" id="togglePassword"></i>
                    </div>
                </div>

                <input type="submit" value="Login" class="btn-login">
            </form>

            <div class="text-center">
                <p>Don't have an account? <a href="/complaint_management/user/register.php">Register</a></p>
                <p>Forgot password? <a href="/complaint_management/reset_password.php">Reset Password</a></p>
            </div>
        </div>
    </section>

    <script>
        const togglePassword = document.getElementById('togglePassword');
        const passwordField = document.getElementById('password');

        togglePassword.addEventListener('click', function() {
            const type = passwordField.type === 'password' ? 'text' : 'password';
            passwordField.type = type;
            this.classList.toggle('fa-eye-slash');
            this.classList.toggle('fa-eye');
        });
    </script>
    <script src="/complaint_management/Js/script.js"></script>

</body>

</html>