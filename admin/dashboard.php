<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include "../includes/db.php";
include "../includes/header.php";

// Show success message if exists
if (isset($_SESSION['success_message'])) {
    echo "<p style='color:green; font-weight:bold;'>" . $_SESSION['success_message'] . "</p>";
    unset($_SESSION['success_message']);
}

// Count total complaints
$result = $conn->query("SELECT COUNT(*) as total FROM complaints");
$row = $result->fetch_assoc();
$total_complaints = $row['total'];

// Count pending complaints
$result = $conn->query("SELECT COUNT(*) as pending FROM complaints WHERE status='Pending'");
$row = $result->fetch_assoc();
$pending_complaints = $row['pending'];

// Count resolved complaints
$result = $conn->query("SELECT COUNT(*) as resolved FROM complaints WHERE status='Resolved'");
$row = $result->fetch_assoc();
$resolved_complaints = $row['resolved'];
?>

<div class="admin-dashboard dashboard-container">
    <h2>Admin Dashboard</h2>

    <?php if (isset($_SESSION['success_message'])): ?>
        <p class="success-message"><?php echo $_SESSION['success_message'];
                                    unset($_SESSION['success_message']); ?></p>
    <?php endif; ?>

    <div class="cards">
        <div class="card">
            <h2><?php echo $total_complaints; ?></h2>
            <p>Total Complaints</p>
        </div>
        <div class="card">
            <h2><?php echo $pending_complaints; ?></h2>
            <p>Pending Complaints</p>
        </div>
        <div class="card">
            <h2><?php echo $resolved_complaints; ?></h2>
            <p>Resolved Complaints</p>
        </div>
    </div>

    <div class="dashboard-buttons">
        <a href="view_complaints.php" class="btn">View Complaints</a>
        
    </div>
</div>
<?php include "../includes/footer.php"; ?>