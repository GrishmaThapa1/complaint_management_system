<?php
session_start();
include "../includes/db.php";

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    // Validation
    if (empty($name) || empty($email) || empty($password) || empty($confirmPassword)) {
        $error = "All fields are required.";
    } elseif ($password !== $confirmPassword) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "Email already exists.";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, created_at) VALUES (?, ?, ?, 'user', NOW())");
            $stmt->bind_param("sss", $name, $email, $hashedPassword);
            $stmt->execute();
            $_SESSION['success'] = "Registration successful! Please log in.";
            header("Location: ../login.php");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
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

<body class="registration-page">
    <div class="registration-container">
        <h2>User Registration</h2>

        <?php if (!empty($error)): ?>
            <p class="error"><?= $error ?></p>
        <?php endif; ?>

        <form method="post" id="registerForm">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" placeholder="Enter your full name" required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="password-wrapper">
                    <input type="password" id="password" name="password" placeholder="Enter password" required>
                    <i class="fa-solid fa-eye-slash password-toggle" id="togglePassword"></i>
                </div>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <div class="password-wrapper">
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm password" required>
                    <i class="fa-solid fa-eye-slash password-toggle" id="toggleConfirmPassword"></i>
                </div>
            </div>

            <input type="submit" value="Register" class="btn-register">
        </form>

        <div class="text-center">
            Already have an account? <a href="../login.php">Login</a>
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
        document.getElementById('registerForm').addEventListener('submit', function(event) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;

            if (password !== confirmPassword) {
                alert('Passwords do not match!');
                event.preventDefault();
            }
        });
    </script>

</body>

</html>