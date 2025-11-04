<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include "../includes/db.php";
include "../includes/header.php";

// Get filter/search inputs
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';
$date = isset($_GET['date']) ? $_GET['date'] : ''; // single date

// Build SQL with filters
$sql = "SELECT complaints.id, users.username, complaints.complaint_text, complaints.status, complaints.created_at
        FROM complaints
        JOIN users ON complaints.user_id = users.id
        WHERE (complaints.id LIKE ? OR users.username LIKE ? OR complaints.complaint_text LIKE ?)";

$params = ["%%", "%%", "%%"]; // default

if (!empty($search)) {
    $search_param = "%$search%";
    $params = [$search_param, $search_param, $search_param];
}

if ($status === 'Pending' || $status === 'Resolved') {
    $sql .= " AND LOWER(complaints.status) = LOWER(?)";
    $params[] = $status;
}

if (!empty($date)) {
    $sql .= " AND DATE(complaints.created_at) = ?";
    $params[] = $date;
}

$sql .= " ORDER BY complaints.created_at DESC";

$stmt = $conn->prepare($sql);

// Bind parameters dynamically
$types = str_repeat('s', count($params));
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="admin-dashboard">
    <h2>All Complaints</h2>

    <!-- Search / Filter Form -->
    <form method="GET" class="search-filter-form" style="margin-bottom: 25px; display: flex; flex-wrap: wrap; gap: 10px; justify-content: center; align-items: center;">
        <input type="text" name="search" placeholder="Search by ID, Username, Complaint..." value="<?= htmlspecialchars($search) ?>" style="padding: 8px 12px; border-radius: 6px; border: 1px solid #ccc;">
        <select name="status" style="padding: 8px 12px; border-radius: 6px; border: 1px solid #ccc;">
            <option value="">All Status</option>
            <option value="Pending" <?= strtolower($status) === 'pending' ? 'selected' : '' ?>>Pending</option>
            <option value="Resolved" <?= strtolower($status) === 'resolved' ? 'selected' : '' ?>>Resolved</option>
        </select>
        <input type="date" name="date" value="<?= $date ?>" style="padding: 8px 12px; border-radius: 6px; border: 1px solid #ccc;">
        <button type="submit" style="padding: 8px 16px; background: #5563DE; color: #fff; border: none; border-radius: 6px; cursor: pointer;">Search</button>
    </form>

    <div class="view-complaints">
        <?php if ($result->num_rows > 0): ?>
            <div class="cards">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <?php
                    $status_class = strtolower($row['status']);
                    $status_icon = ($status_class === 'pending') ? 'fas fa-hourglass-half' : 'fas fa-check-circle';
                    ?>
                    <div class="card <?= $status_class ?>">
                        <!-- Card Header -->
                        <div class="card-header">
                            <div class="header-top" style="display:flex; justify-content:space-between; align-items:center;">
                                <h3>Complaint #<?= $row['id'] ?></h3>
                                <div class="status-block" style="display:flex; align-items:center; gap:5px;">
                                    <i class="<?= $status_icon ?>" style="color:<?= $status_class === 'pending' ? '#f0ad4e' : '#28a745' ?>;"></i>
                                    <span class="status <?= $status_class ?>"><?= ucfirst($status_class) ?></span>
                                </div>
                            </div>
                            <div class="header-bottom" style="display:flex; justify-content:space-between; font-size:14px; margin-top:5px;">
                                <p><i class="fas fa-user"></i> <?= htmlspecialchars($row['username']) ?></p>
                                <p><i class="fas fa-calendar-alt"></i> <?= date("d M Y", strtotime($row['created_at'])) ?></p>
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="card-body" style="margin-top:10px;">
                            <p><strong>Complaint:</strong></p>
                            <div class="complaint-text">
                                <?= htmlspecialchars($row['complaint_text']) ?>
                            </div>
                        </div>

                        <!-- Card Footer -->
                        <div class="card-footer" style="margin-top:10px;">
                            <a href="update_status.php?id=<?= $row['id'] ?>" class="btn">Update Status</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p class="no-complaints">No complaints found.</p>
        <?php endif; ?>
    </div>
</div>

<?php include "../includes/footer.php"; ?>
