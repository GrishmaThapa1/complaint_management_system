<?php
session_start();
include "includes/db.php";

$error = "";

// Show success message from registration
$success = "";
if (!empty($_SESSION['success'])) {
    $success = $_SESSION['success'];
    unset($_SESSION['success']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usernameOrEmail = trim($_POST['username_or_email']);
    $password = $_POST['password'];

    // Check if admin first
    $stmt = $conn->prepare("SELECT id, username, password FROM admins WHERE username=?");
    $stmt->bind_param("s", $usernameOrEmail);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $user['role'] = "admin"; // backend sets role
    } else {
        // Check users table
        $stmt = $conn->prepare("SELECT id, name, email, password, role FROM users WHERE email=?");
        $stmt->bind_param("s", $usernameOrEmail);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
        }
    }

    if (!empty($user)) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['role'] = $user['role'];

            if ($user['role'] === "admin") {
                $_SESSION['admin_id'] = $user['id'];
                $_SESSION['name'] = $user['username'];
                header("Location: admin/dashboard.php");
            } else {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                header("Location: user/dashboard.php");
            }
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "Invalid credentials.";
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body class="login-page">

    <section class="login-section">
        <div class="login-container">
            <h2>Login</h2>

            <?php if ($success) echo "<p class='success'>$success</p>"; ?>
            <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>

            <form method="POST" id="loginForm">
                <div class="form-group">
                    <label for="username_or_email">Username or Email</label>
                    <input type="text" id="username_or_email" name="username_or_email" placeholder="Enter username or email" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter password" required>
                </div>

                <input type="submit" value="Login" class="btn-login">
            </form>

            <div class="text-center">
                <p>Don't have an account? <a href="user/register.php">Register</a></p>
                <p>Forgot password? <a href="user/reset_password.php">Reset Password</a></p>
            </div>
        </div>
    </section>

</body>

</html>