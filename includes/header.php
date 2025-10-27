<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : "Complaint Management System"; ?></title>
    <link rel="stylesheet" href="/complaint_management/css/style.css">
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css">
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
</head>

<body>
    <header>
        <div class="navbar">
            <div class="logo">
                <a href="/complaint_management/index.php">
                    <img src="/complaint_management/Image/logo1.png" alt="Complaint Management System Logo">
                </a>
            </div>
            <div class="nav-links">
                <a href="/complaint_management/index.php">Home</a>
                <a href="/complaint_management/about.php">About Us</a>
                <a href="/complaint_management/contact.php">Contact</a>

                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <a href="/complaint_management/admin/dashboard.php">Dashboard</a>
                    <a href="/complaint_management/admin/logout.php">Logout (<?php echo $_SESSION['name']; ?>)</a>
                <?php elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'user'): ?>
                    <a href="/complaint_management/user/dashboard.php">Dashboard</a>
                    <a href="/complaint_management/user/logout.php">Logout (<?php echo $_SESSION['user_name']; ?>)</a>
                <?php else: ?>
                    <a href="/complaint_management/login.php">Login</a>
                    <a href="/complaint_management/user/register.php">Register</a>
                <?php endif; ?>





            </div>
        </div>
    </header>
    <main>