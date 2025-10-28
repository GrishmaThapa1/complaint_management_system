<?php
session_start();
include __DIR__ . '/../includes/db.php';

// Check if admin is logged in
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: /complaint_management/login.php");
    exit;
}

$pageTitle = "Edit Admin Profile";
$admin_id = $_SESSION['admin_id'];

// Fetch current admin info
$stmt = $conn->prepare("SELECT * FROM admins WHERE id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$admin = $stmt->get_result()->fetch_assoc();

$profile_msg = "";
$profile_msg_type = "";

// Handle profile update
if (isset($_POST['update_profile'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $newImageName = $admin['image'];

    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['name'] !== "") {
        $imgName = $_FILES['profile_image']['name'];
        $imgTmp = $_FILES['profile_image']['tmp_name'];
        $imgExt = strtolower(pathinfo($imgName, PATHINFO_EXTENSION));
        $allowedExt = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($imgExt, $allowedExt)) {
            $newImageName = 'profile_' . $admin_id . '.' . $imgExt;
            move_uploaded_file($imgTmp, __DIR__ . '/../Image/' . $newImageName);
        } else {
            $profile_msg = "Invalid image format. Only JPG, PNG, GIF allowed.";
            $profile_msg_type = "error";
        }
    }

    if ($username === $admin['username'] && $email === $admin['email'] && $newImageName === $admin['image']) {
        if (!$profile_msg) {
            $profile_msg = "Please make changes before updating your profile.";
            $profile_msg_type = "error";
        }
    } else if (!$profile_msg) {
        $stmt = $conn->prepare("UPDATE admins SET username=?, email=?, image=? WHERE id=?");
        $stmt->bind_param("sssi", $username, $email, $newImageName, $admin_id);
        if ($stmt->execute()) {
            $profile_msg = "Profile updated successfully.";
            $profile_msg_type = "success";

            // Refresh admin data
            $stmt = $conn->prepare("SELECT * FROM admins WHERE id = ?");
            $stmt->bind_param("i", $admin_id);
            $stmt->execute();
            $admin = $stmt->get_result()->fetch_assoc();
        } else {
            $profile_msg = "Error updating profile.";
            $profile_msg_type = "error";
        }
    }
}

include __DIR__ . '/../includes/header.php';
?>

<body class="profile-page">
    <div class="container">
        <h2>Edit Admin Profile</h2>

        <?php if ($profile_msg): ?>
            <p id="profileMessage" class="<?php echo $profile_msg_type; ?>"><?php echo $profile_msg; ?></p>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($admin['username']); ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($admin['email']); ?>" required>
            </div>

            <div class="form-group">
                <label for="profile_image">Profile Image:</label>
                <?php if ($admin['image']): ?>
                    <div style="margin-bottom:10px;">
                        <img src="/complaint_management/Image/<?php echo htmlspecialchars($admin['image']); ?>"
                            alt="Current Profile Image" class="profile-img">
                    </div>
                <?php endif; ?>
                <input type="file" id="profile_image" name="profile_image" accept="image/*">
            </div>

            <button type="submit" name="update_profile">Update Profile</button>
        </form>
    </div>

   

    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>

</html>