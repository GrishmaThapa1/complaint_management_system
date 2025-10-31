<?php
session_start();
include __DIR__ . '/../includes/db.php';

// Check if admin is logged in
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: /complaint_management/login.php");
    exit;
}

$pageTitle = "Admin Profile";
$admin_id = $_SESSION['admin_id'];

// Fetch current admin info
$stmt = $conn->prepare("SELECT * FROM admins WHERE id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$admin = $stmt->get_result()->fetch_assoc();

include __DIR__ . '/../includes/header.php';
?>

<body class="profile-page">
    <main class="profile-main">
        <div class="container">
            <h2>Admin Profile</h2>

            <?php if ($admin['image'] && file_exists(__DIR__ . '/../Image/' . $admin['image'])): ?>
                <div style="text-align:center; margin-bottom:15px;">
                    <img src="/complaint_management/Image/<?php echo htmlspecialchars($admin['image']); ?>"
                        alt="Profile Image" style="height:100px; border-radius:50%;">
                </div>
            <?php endif; ?>

            <p><strong>Username:</strong> <?php echo htmlspecialchars($admin['username']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($admin['email']); ?></p>

            <a href="edit_profile.php" class="btn">Edit Profile</a>
        </div>
    </main>

    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>

</html>