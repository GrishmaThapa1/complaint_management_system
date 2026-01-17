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
    <style>
        .navbar {
            display: flex;
            align-items: center;
            flex-direction: row;
            gap: 15px;
        }

        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            font-size: 24px;
            color: #333;
            cursor: pointer;
            padding: 10px;
            flex-shrink: 0;
            order: -1
        }

        .logo {
            display: flex;
            align-items: center;
        }

        .mobile-sidebar {
            position: fixed;
            top: 0;
            left: -300px;
            width: 300px;
            height: 100vh;
            background: white;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            transition: left 0.3s ease;
            z-index: 1001;
            overflow-y: auto;
        }

        .mobile-sidebar.active {
            left: 0;
        }

        .mobile-sidebar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            border-bottom: 1px solid #eee;
        }

        .mobile-sidebar-close {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #333;
        }

        .mobile-sidebar-content {
            padding: 20px;
        }

        .mobile-sidebar-content a {
            display: block;
            padding: 12px 0;
            color: #333;
            text-decoration: none;
            border-bottom: 1px solid #f0f0f0;
            transition: color 0.3s;
        }

        .mobile-sidebar-content a:hover {
            color: #5563DE;
        }

        .mobile-user-info {
            padding: 15px 0;
            border-bottom: 2px solid #eee;
            margin-bottom: 10px;
        }

        .mobile-user-info i {
            font-size: 20px;
            margin-right: 10px;
            color: #5563DE;
        }

        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            z-index: 1000;
        }

        .sidebar-overlay.active {
            display: block;
        }

        @media (max-width: 768px) {
            .mobile-menu-btn {
                display: block;
            }

            .nav-links {
                display: none;
            }
        }
    </style>
</head>

<body>
    <header>
        <div class="navbar">
            <button class="mobile-menu-btn" id="mobileMenuBtn">
                <i class="fas fa-bars"></i>
            </button>

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

    <!-- Mobile Sidebar -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    <div class="mobile-sidebar" id="mobileSidebar">
        <div class="mobile-sidebar-header">
            <div class="logo">
                <img src="/complaint_management/Image/logo1.png" alt="Logo" style="height: 40px;">
            </div>
            <button class="mobile-sidebar-close" id="closeSidebar">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="mobile-sidebar-content">
            <?php if (isset($_SESSION['role'])): ?>
                <div class="mobile-user-info">
                    <i class="fas fa-user-circle"></i>
                    <strong><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?></strong>
                </div>
            <?php endif; ?>

            <a href="/complaint_management/index.php">
                <i class="fas fa-home"></i> Home
            </a>
            <a href="/complaint_management/about.php">
                <i class="fas fa-info-circle"></i> About Us
            </a>
            <a href="/complaint_management/contact.php">
                <i class="fas fa-envelope"></i> Contact
            </a>

            <?php if (isset($_SESSION['role'])): ?>
                <a href="/complaint_management/<?php echo $_SESSION['role'] === 'admin' ? 'admin/dashboard.php' : 'user/dashboard.php'; ?>">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="/complaint_management/<?php echo $_SESSION['role'] === 'admin' ? 'admin/profile.php' : 'user/profile.php'; ?>">
                    <i class="fas fa-user"></i> View Profile
                </a>
                <a href="/complaint_management/<?php echo $_SESSION['role'] === 'admin' ? 'admin/logout.php' : 'user/logout.php'; ?>" class="logout-link">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            <?php else: ?>
                <a href="/complaint_management/login.php">
                    <i class="fas fa-sign-in-alt"></i> Login
                </a>
                <a href="/complaint_management/user/register.php">
                    <i class="fas fa-user-plus"></i> Register
                </a>
            <?php endif; ?>
        </div>
    </div>

    <script>
        (function() {
            const mobileMenuBtn = document.getElementById('mobileMenuBtn');
            const mobileSidebar = document.getElementById('mobileSidebar');
            const closeSidebar = document.getElementById('closeSidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');

            function openSidebar() {
                mobileSidebar.classList.add('active');
                sidebarOverlay.classList.add('active');
                document.body.style.overflow = 'hidden';
            }

            function closeSidebarFunc() {
                mobileSidebar.classList.remove('active');
                sidebarOverlay.classList.remove('active');
                document.body.style.overflow = '';
            }

            mobileMenuBtn.addEventListener('click', openSidebar);
            closeSidebar.addEventListener('click', closeSidebarFunc);
            sidebarOverlay.addEventListener('click', closeSidebarFunc);

            // Close sidebar when clicking on a link
            const sidebarLinks = mobileSidebar.querySelectorAll('a');
            sidebarLinks.forEach(function(link) {
                link.addEventListener('click', closeSidebarFunc);
            });
        })();
    </script>

    <main>