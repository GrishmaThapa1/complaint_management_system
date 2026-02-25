<?php
session_start();
include "includes/db.php";

// Prevent browser caching completely
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

// Redirect if already logged in
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: /complaint_management/admin/dashboard.php");
        exit;
    } elseif ($_SESSION['role'] === 'user') {
        header("Location: /complaint_management/user/dashboard.php");
        exit;
    }
}

$error = "";

// Handle login POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usernameOrEmail = trim($_POST['username_or_email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, username, email, password, role FROM users WHERE username=? OR LOWER(email)=LOWER(?) LIMIT 1");
    $stmt->bind_param("ss", $usernameOrEmail, $usernameOrEmail);
    $stmt->execute();
    $result = $stmt->get_result();

    $user = null;
    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
    }

    if ($user && password_verify($password, $user['password'])) {
        // Standard session variable
        $_SESSION['role'] = $user['role'];
        $_SESSION['name'] = $user['username'];

        if ($user['role'] === 'admin') {
            $_SESSION['admin_id'] = $user['id'];
            $redirect_url = "/complaint_management/admin/dashboard.php";
        } else {
            $_SESSION['user_id'] = $user['id'];
            $redirect_url = "/complaint_management/user/dashboard.php";
        }

        header("Location: $redirect_url");
        exit;
    } else {
        $error = "Invalid username/email or password.";
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
    <!-- Prevent caching -->
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
</head>

<body class="login-page">

    <section class="login-section">
        <div class="login-container">
            <h2>Login</h2>

            <?php if (!empty($error)): ?>
                <p class="error"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <form method="POST" id="loginForm" autocomplete="off" novalidate>
                <!--  hidden fields to prevent browser autofill -->
                <input type="text" name="fakeusernameremembered" style="display:none">
                <input type="password" name="fakepasswordremembered" style="display:none">

                <div class="form-group">
                    <label for="username_or_email">Username or Email</label>
                    <input type="text" id="username_or_email" name="username_or_email" placeholder="Enter username or email" required autocomplete="off">
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="password-wrapper">
                        <input type="password" id="password" name="password" placeholder="Enter password" required autocomplete="new-password">
                        <i class="fa-solid fa-eye-slash password-toggle"></i>
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

    <script src="/complaint_management/Js/script.js"></script>
    <script>
        // Reset form completely on page load
        window.onload = function() {
            const form = document.getElementById('loginForm');
            form.reset();
            setTimeout(() => {
                document.getElementById('username_or_email').value = '';
                document.getElementById('password').value = '';
            }, 50);
        };

        // Force reload if page is loaded from back/forward cache
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                window.location.reload();
            }
        });
    </script>

</body>

</html>