<?php
session_start();
include __DIR__ . '/../includes/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: /complaint_management/login.php");
    exit;
}

$pageTitle = "Your Profile";
$user_id = $_SESSION['user_id'];

// Fetch user info
$stmt = $conn->prepare("SELECT * FROM users WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

include __DIR__ . '/../includes/header.php';
?>

<body class="profile-page">
    <div class="container">
        <h2>Your Profile</h2>

        <div style="margin-bottom:20px;">
            <img src="/complaint_management/Image/<?php echo $user['image'] ?: 'default.png'; ?>"
                alt="Profile Image" class="profile-img">
        </div>

        <p><strong>Name:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>

        <a href="edit_profile.php" class="btn">Edit Profile</a>
    </div>

    <script src="/complaint_management/Js/script.js"></script>
    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>