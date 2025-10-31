<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}
include "../includes/db.php";
include "../includes/header.php";

$user_id = intval($_SESSION['user_id']);

// Get search and status filter
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$status_filter = isset($_GET['status']) ? strtolower(trim($_GET['status'])) : 'all';

// Build query
$sql = "SELECT * FROM complaints WHERE user_id=? ";
$params = [$user_id];
$types = "i";

// Add search
if (!empty($search)) {
    $sql .= " AND (subject LIKE ? OR complaint_text LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "ss";
}

// Add status filter
if ($status_filter === 'pending' || $status_filter === 'resolved') {
    $sql .= " AND LOWER(TRIM(status))=?";
    $params[] = $status_filter;
    $types .= "s";
}

$sql .= " ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$complaints = $stmt->get_result();

// Fetch unread notification count
$count_sql = "SELECT COUNT(*) FROM notifications WHERE user_id = ? AND status = 'unread'";
$count_stmt = $conn->prepare($count_sql);
$count_stmt->bind_param("i", $user_id);
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$count_row = $count_result->fetch_row();
$unread_count = $count_row[0];
?>

<div class="dashboard-wrapper">
    <div class="dashboard-container">
        <div class="dashboard-inner">
            <!-- Header -->
            <div class="dashboard-header-inner" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h1>Welcome, <?= htmlspecialchars($_SESSION['user_name']) ?></h1>

                <!-- Bell Icon linking to notifications page -->
                <a href="notifications.php" class="bell-icon" style="position: relative; display: inline-block;">
                    <i class="fas fa-bell" style="font-size: 24px; color: #555;"></i>
                    <?php if ($unread_count > 0): ?>
                        <span class="badge" style="position: absolute; top: -5px; right: -10px; background: red; color: white; border-radius: 50%; padding: 2px 6px; font-size: 12px;"><?= $unread_count ?></span>
                    <?php endif; ?>
                </a>

                <!-- Submit Complaint button -->
                <a href="submit_complaint.php" class="btn" style="float: right;">Submit Complaint</a>
            </div>

            <!-- Filter Buttons -->
            <div class="filter-buttons" style="margin-bottom:20px; justify-content:center; gap:10px;">
                <a href="?status=all" class="filter-btn <?= $status_filter === 'all' ? 'active' : '' ?>">All</a>
                <a href="?status=pending" class="filter-btn <?= $status_filter === 'pending' ? 'active' : '' ?>">Pending</a>
                <a href="?status=resolved" class="filter-btn <?= $status_filter === 'resolved' ? 'active' : '' ?>">Resolved</a>
            </div>

            <!-- Search Form -->
            <form method="GET" style="display:flex; justify-content:center; gap:10px; margin-bottom:25px; flex-wrap:wrap;">
                <input type="text" name="search" placeholder="Search by Subject or Complaint..." value="<?= htmlspecialchars($search) ?>" style="padding:8px 12px; border-radius:6px; border:1px solid #ccc;">
                <button type="submit" style="padding:8px 16px; background:#5563DE; color:white; border:none; border-radius:6px; cursor:pointer;">Search</button>
            </form>

            <!-- Complaints Section -->
            <section class="complaints-section">
                <h2>Your Complaints</h2>
                <div class="complaints-table">
                    <?php if ($complaints && $complaints->num_rows > 0): ?>
                        <?php while ($row = $complaints->fetch_assoc()): ?>
                            <?php $status = strtolower(trim($row['status'])); ?>
                            <div class="complaint-card <?= $status ?>">
                                <!-- Card Header -->
                                <div class="complaint-header" style="display:flex; align-items:center; gap:8px;">
                                    <?php if ($status === 'pending'): ?>
                                        <span class="icon-pending">⏳</span>
                                    <?php else: ?>
                                        <span class="icon-resolved">✅</span>
                                    <?php endif; ?>
                                    <h3><?= htmlspecialchars($row['subject']) ?></h3>
                                </div>

                                <!-- Card Footer -->
                                <div class="complaint-footer" style="margin-top:10px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap;">
                                    <span class="status <?= $status ?>">Status: <?= htmlspecialchars(ucfirst($row['status'])) ?></span>
                                    <p class="created-line">Created: <?= date("d M Y", strtotime($row['created_at'])) ?></p>
                                </div>

                                <!-- View Details Button -->
                                <a href="view_complaint.php?id=<?= $row['id'] ?>" class="btn-view">View Details</a>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="no-complaints">
                            <p><strong>No complaints found.</strong></p>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>