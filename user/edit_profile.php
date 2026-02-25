<?php
session_start();

// Prevent browser caching
header("Cache-Control: no-store, no-cache, must-revalidate, private");
header("Pragma: no-cache");
header("Expires: 0");

include __DIR__ . '/../includes/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: /complaint_management/login.php");
    exit;
}

$pageTitle = "Edit Your Profile";
$user_id = $_SESSION['user_id'];

// Fetch current user info
$stmt = $conn->prepare("SELECT * FROM users WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$profile_msg = "";
$profile_msg_type = "";

// Handle profile update
if (isset($_POST['update_profile'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $newImageName = $user['image']; 

    // Handle image removal
    if (isset($_POST['remove_image']) && $_POST['remove_image'] == 1) {
        if ($user['image'] && file_exists(__DIR__ . '/../Image/' . $user['image'])) {
            unlink(__DIR__ . '/../Image/' . $user['image']); 
        }
        $newImageName = ''; 
    }

    // Handle image upload
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['name'] !== "") {
        $imgName = $_FILES['profile_image']['name'];
        $imgTmp = $_FILES['profile_image']['tmp_name'];
        $imgExt = strtolower(pathinfo($imgName, PATHINFO_EXTENSION));
        $allowedExt = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($imgExt, $allowedExt)) {
            $newImageName = 'profile_' . $user_id . '.' . $imgExt;
            move_uploaded_file($imgTmp, __DIR__ . '/../Image/' . $newImageName);
        } else {
            $profile_msg = "Invalid image format. Only JPG, PNG, GIF allowed.";
            $profile_msg_type = "error";
        }
    }

    // Check if at least one field changed
    if ($username === $user['username'] && $email === $user['email'] && $newImageName === $user['image']) {
        if (!$profile_msg) {
            $profile_msg = "Please make changes before updating your profile.";
            $profile_msg_type = "error";
        }
    } else if (!$profile_msg) {
        $stmt = $conn->prepare("UPDATE users SET username=?, email=?, image=? WHERE id=?");
        $stmt->bind_param("sssi", $username, $email, $newImageName, $user_id);
        if ($stmt->execute()) {
            $profile_msg = "Profile updated successfully.";
            $profile_msg_type = "success";

            // Refresh user data
            $stmt = $conn->prepare("SELECT * FROM users WHERE id=?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();

            echo '<script>
                setTimeout(() => { window.location.href = "profile.php"; }, 2500);
            </script>';
        } else {
            $profile_msg = "Error updating profile.";
            $profile_msg_type = "error";
        }
    }
}

include __DIR__ . '/../includes/header.php';
?>

<body class="profile-page">
 <main class="profile-main">
 <div class="container">
      <h2>Edit Your Profile</h2>

 <?php if ($profile_msg): ?>
 <p id="profileMessage" class="<?php echo $profile_msg_type; ?>"><?php echo $profile_msg; ?></p>
            <?php endif; ?>

     <form method="post" enctype="multipart/form-data">
      <div class="form-group">
          <label for="username">Username:</label>
     <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                </div>

     <div class="form-group">
      <label for="email">Email:</label>
     <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
         </div>

     <div class="form-group">
     <label for="profile_image">Profile Image:</label>

    <?php if ($user['image'] && file_exists(__DIR__ . '/../Image/' . $user['image'])): ?>
             <div style="margin-bottom:10px; text-align:center;">
              <img src="/complaint_management/Image/<?php echo htmlspecialchars($user['image']); ?>"
          alt="Current Profile Image" class="profile-img" style="height:100px; border-radius:50%;">
        </div>
     <div style="margin-bottom:10px; text-align:center;">
      <label style="display:inline-flex; align-items:center; gap:5px;">
      <input type="checkbox" id="remove_image" name="remove_image" value="1">
                                Remove Profile Image
                </label>
            </div>
                    <?php endif; ?>

                    <input type="file" id="profile_image" name="profile_image" accept="image/*">
                </div>

                <button type="submit" name="update_profile">Update Profile</button>
            </form>
        </div>
    </main>

    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>

</html>