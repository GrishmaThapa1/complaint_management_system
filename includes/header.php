<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : "Complaint Management System"; ?></title>
    <link rel="stylesheet" href="/complaint_management/css/style.css">
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css">
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

                <?php if (isset($_SESSION['role'])): ?>
                    <a href="/complaint_management/<?php echo $_SESSION['role'] === 'admin' ? 'admin/dashboard.php' : 'user/dashboard.php'; ?>">Dashboard</a>

                    <div class="user-dropdown">
                        <i class="fas fa-user-circle"></i>
                        <div class="dropdown-content">
                            <a href="/complaint_management/<?php echo $_SESSION['role'] === 'admin' ? 'admin/profile.php' : 'user/profile.php'; ?>">View Profile</a>
                            <a href="/complaint_management/<?php echo $_SESSION['role'] === 'admin' ? 'admin/logout.php' : 'user/logout.php'; ?>" class="logout-link">Logout</a>
                        </div>
                    </div>

                <?php else: ?>
                    <a href="/complaint_management/login.php">Login</a>
                    <a href="/complaint_management/user/register.php">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </header>


    <main>