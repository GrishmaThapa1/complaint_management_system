<?php
session_start();
include "includes/db.php";

$message = ""; // success message
$error = "";   // error message

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usernameOrEmail = trim($_POST['username_or_email']);
    $password = $_POST['password'];

    $user = null;

    // Check if admin (username OR email, email case-insensitive)
    $stmt = $conn->prepare("SELECT id, username, email, password FROM admins WHERE username=? OR LOWER(email)=LOWER(?) LIMIT 1");
    $stmt->bind_param("ss", $usernameOrEmail, $usernameOrEmail);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $user['role'] = "admin";
        $user['session_name'] = $user['username']; // for session
    } else {
        // Check regular users (username OR email, email case-insensitive)
        $stmt = $conn->prepare("SELECT id, username, email, password, role FROM users WHERE username=? OR LOWER(email)=LOWER(?) LIMIT 1");
        $stmt->bind_param("ss", $usernameOrEmail, $usernameOrEmail);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $user['session_name'] = $user['username']; // for session
        }
    }

    // Verify password
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['role'] = $user['role'];
        if ($user['role'] === "admin") {
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['name'] = $user['session_name'];
            $redirect_url = "/complaint_management/admin/dashboard.php";
        } else {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['session_name'];
            $redirect_url = "/complaint_management/user/dashboard.php";
        }
        $message = "Successfully logged in! Redirecting...";
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
</head>

<body class="login-page">
    <section class="login-section">
        <div class="login-container">
            <h2>Login</h2>

            <?php if ($error): ?>
                <p class="error"><?= $error ?></p>
            <?php endif; ?>

            <?php if (!empty($message)): ?>
                <p class="success"><?= $message ?></p>
                <script>
                    setTimeout(() => {
                        window.location.href = '<?= $redirect_url ?>';
                    }, 1500);
                </script>
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
</body>

</html>