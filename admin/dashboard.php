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
    echo "<p class='success-message'>" . $_SESSION['success_message'] . "</p>";
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

<div class="admin-dashboard">
    <h2>Admin Dashboard</h2>

    <div class="cards">
        <div class="card total">
            <i class="fas fa-list-alt"></i>
            <h2 style="color:white;"><?php echo $total_complaints; ?></h2>
            <p>Total Complaints</p>
        </div>
        <div class="card pending">
            <i class="fas fa-clock"></i>
            <h2 style="color:white;"><?php echo $pending_complaints; ?></h2>
            <p>Pending Complaints</p>
        </div>
        <div class="card resolved">
            <i class="fas fa-check-circle"></i>
            <h2 style="color:white;"><?php echo $resolved_complaints; ?></h2>
            <p>Resolved Complaints</p>
        </div>
    </div>

    <div class="dashboard-buttons" style="margin-top:20px;">
        <a href="view_complaints.php" class="btn">View Complaints</a>
        <a href="view_feedback.php" class="btn" style="margin-left: 10px;">View Feedback</a>
    </div>
</div>

<?php include "../includes/footer.php"; ?>