<?php
session_start();

// Prevent browser caching
header("Cache-Control: no-store, no-cache, must-revalidate, private");
header("Pragma: no-cache");
header("Expires: 0");

include __DIR__ . '/../includes/db.php';

// Check if admin is logged in
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

$pageTitle = "Admin Profile";
$admin_id = $_SESSION['admin_id'];

// Fetch current admin info from users table
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ? AND role = 'admin'");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$admin = $stmt->get_result()->fetch_assoc();

include __DIR__ . '/../includes/header.php';
?>

<script>
  window.onpageshow = function(event) {
    if (event.persisted) {
      window.location.reload();
    }
  };
</script>

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