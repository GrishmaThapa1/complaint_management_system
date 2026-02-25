<?php
session_start();

// prevent browser caching
header("Cache-Control: no-store, no-cache, must-revalidate, private");
header("Pragma: no-cache");
header("Expires: 0");

// Session validation
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: ../login.php");
    exit;
}

include "../includes/db.php";
include "../includes/header.php";

// User info
$user_id = intval($_SESSION['user_id']);
$user_name = $_SESSION['name'] ?? 'User';

// Get search and status filter
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$status_filter = isset($_GET['status']) ? strtolower(trim($_GET['status'])) : 'all';

// Build query
$sql = "SELECT id, subject, complaint_text, status, created_at, admin_remarks 
        FROM complaints 
        WHERE user_id=? ";
$params = [$user_id];
$types = "i";

// Search filter
if (!empty($search)) {
    $sql .= " AND (subject LIKE ? OR complaint_text LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "ss";
}

// Status filter
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

//  Fetch unread notifications 
$count_sql = "SELECT COUNT(*) FROM notification WHERE user_id = ? AND status = 'unread'";
$count_stmt = $conn->prepare($count_sql);
$count_stmt->bind_param("i", $user_id);
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$count_row = $count_result->fetch_row();
$unread_count = $count_row[0];
?>

<!-- Prevent caching -->
<script>
    window.onpageshow = function(event) {
        if (event.persisted) {
            window.location.reload();
        }
    };
</script>

<div class="dashboard-wrapper">
    <div class="dashboard-container">
        <div class="dashboard-inner">

<!-- Header -->
<div class="dashboard-header-inner" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
     <h1>Welcome, <?= htmlspecialchars($user_name) ?></h1>
     <div style="display:flex; gap:15px; align-items:center;">
 <a href="notifications.php" class="bell-icon" style="position:relative; display:inline-block;">
  <i class="fas fa-bell" style="font-size:24px; color:#555;"></i>
     <?php if ($unread_count > 0): ?>
      <span class="badge" style="position:absolute; top:-5px; right:-10px; background:red; color:white; border-radius:50%; padding:2px 6px; font-size:12px;">
     <?= $unread_count ?>
        </span>
      <?php endif; ?>
    </a>
    <a href="submit_complaint.php" class="btn" style="float:right;">Submit Complaint</a>
    </div>
            </div>

    <!-- Filter Buttons -->
    <div class="filter-buttons" style="margin-bottom:20px; justify-content:center; gap:10px;">
     <a href="?status=all" class="filter-btn <?= $status_filter === 'all' ? 'active' : '' ?>">All</a>
     <a href="?status=pending" class="filter-btn <?= $status_filter === 'pending' ? 'active' : '' ?>">Pending</a>
    <a href="?status=resolved" class="filter-btn <?= $status_filter === 'resolved' ? 'active' : '' ?>">Resolved</a>
     </div>

 <!-- Search Form -->
<form method="GET" id="searchForm" style="display:flex; justify-content:center; gap:10px; margin-bottom:25px; flex-wrap:wrap;">
     <input type="text" name="search" id="searchInput" placeholder="Search by Subject or Complaint..." value="<?= htmlspecialchars($search) ?>" style="padding:8px 12px; border-radius:6px; border:1px solid #ccc;">
     <button type="submit" style="padding:8px 16px; background:#5563DE; color:white; border:none; border-radius:6px; cursor:pointer;">Search</button>
     </form>

 <!-- Complaints Section -->
    <section class="complaints-section">
     <h2>Your Complaints</h2>
 <div class="complaints-table" style="display:grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap:15px;">
     <?php if ($complaints && $complaints->num_rows > 0): ?>
     <?php while ($row = $complaints->fetch_assoc()):
       $status = strtolower(trim($row['status']));
     $admin_remark = !empty($row['admin_remarks']) ? $row['admin_remarks'] : "Your complaint has been received and is under review.";
         ?>
       <div class="complaint-card <?= $status ?>" style="border:1px solid #ccc; padding:15px; border-radius:8px; display:flex; flex-direction:column; justify-content:space-between; height:100%;">

     <!-- Card Header -->
        <div class="complaint-header" style="display:flex; align-items:center; gap:8px;">
              <span class="<?= $status === 'pending' ? 'icon-pending' : 'icon-resolved' ?>">
               <?= $status === 'pending' ? '⏳' : '✅' ?>
             </span>
         <h3><?= htmlspecialchars($row['subject']) ?></h3>
             </div>

    <!-- Status & Date -->
            <div style="margin-top:10px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap;">
         <span style="background:<?= $status === 'pending' ? '#f0ad4e' : '#28a745' ?>; color:white; padding:5px 12px; border-radius:6px; font-weight:500;">
         <?= ucfirst($status) ?>
    </span>
  <p style="font-size:14px; color:#555;">Created: <?= date("d M Y", strtotime($row['created_at'])) ?></p>
 </div>

      <!-- Admin Remark -->
     <p style="margin-top:5px; font-size:13px; color:#444;">📝 Admin Remark: <?= htmlspecialchars(strlen($admin_remark) > 50 ? substr($admin_remark, 0, 50) . '...' : $admin_remark) ?></p>

    <!-- View Details Button -->
     <div style="display:flex; justify-content:center; margin-top:10px;">
       <a href="view_complaint.php?id=<?= $row['id'] ?>" class="btn-view" style="text-decoration:none; background:#5563DE; color:white; padding:8px 12px; border-radius:6px; text-align:center;">
                                        View Details
                                    </a>
                                </div>
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

<script>
    const searchInput = document.getElementById('searchInput');

    searchInput.addEventListener('input', function() {
        clearTimeout(this.searchTimer);
        this.searchTimer = setTimeout(() => {
            document.getElementById('searchForm').submit();
        }, 400);
    });
</script>

<?php include "../includes/footer.php"; ?>