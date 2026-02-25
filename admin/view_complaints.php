<?php
session_start();

// Prevent browser caching 
header("Cache-Control: no-store, no-cache, must-revalidate, private");
header("Pragma: no-cache");
header("Expires: 0");

// Redirect to login if not logged in as admin
if (!isset($_SESSION['admin_id']) || empty($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include "../includes/db.php";
include "../includes/header.php";

// Get filter/search inputs
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';
$date = isset($_GET['date']) ? $_GET['date'] : '';

// Build SQL with filters
$sql = "SELECT complaints.id, users.username, complaints.complaint_text, complaints.status, complaints.created_at, complaints.attachment
        FROM complaints
        JOIN users ON complaints.user_id = users.id
        WHERE (complaints.id LIKE ? OR users.username LIKE ? OR complaints.complaint_text LIKE ?)";

$params = ["%", "%", "%"];

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

if ($stmt) {
    $types = str_repeat('s', count($params));
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    die("SQL Prepare Error: " . $conn->error);
}
?>

<script>
    // Handle back button cache 
    window.onpageshow = function(event) {
        if (event.persisted || (window.performance && window.performance.getEntriesByType("navigation")[0].type === "back_forward")) {
            window.location.reload();
        }
    };
</script>

<div class="admin-dashboard">
    <h2>All Complaints</h2>

    <!-- Search / Filter Form -->
    <form method="GET" id="searchFilterForm" class="search-filter-form" style="margin-bottom: 25px; display: flex; flex-wrap: wrap; gap: 10px; justify-content: center; align-items: center;">
        <input type="text" id="searchInput" name="search" placeholder="Search by ID, Username, Complaint..." value="<?= htmlspecialchars($search) ?>" style="padding: 8px 12px; border-radius: 6px; border: 1px solid #ccc;">
        <select id="statusSelect" name="status" style="padding: 8px 12px; border-radius: 6px; border: 1px solid #ccc;">
            <option value="">All Status</option>
            <option value="Pending" <?= strtolower($status) === 'pending' ? 'selected' : '' ?>>Pending</option>
            <option value="Resolved" <?= strtolower($status) === 'resolved' ? 'selected' : '' ?>>Resolved</option>
        </select>
        <input type="date" id="dateInput" name="date" value="<?= htmlspecialchars($date) ?>"
            max="<?= date('Y-m-d'); ?>"
            style="padding: 8px 12px; border-radius: 6px; border: 1px solid #ccc;">
        <button type="submit" style="padding: 8px 16px; background: #5563DE; color: #fff; border: none; border-radius: 6px; cursor: pointer;">Search</button>
    </form>

    <div class="view-complaints">
        <?php if ($result && $result->num_rows > 0): ?>
            <div class="cards">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <?php $status_class = strtolower($row['status']); ?>
                    <div class="card <?= $status_class ?>">
                        <div class="card-header">
                            <div style="display:flex; justify-content:space-between; align-items:center;">
                                <h3>Complaint #<?= $row['id'] ?></h3>
                                <span class="status <?= $status_class ?>"><?= ucfirst($status_class) ?></span>
                            </div>
                            <div style="display:flex; justify-content:space-between; font-size:14px; margin-top:5px;">
                                <p><i class="fas fa-user"></i> <?= htmlspecialchars($row['username']) ?></p>
                                <p><i class="fas fa-calendar-alt"></i> <?= date("d M Y", strtotime($row['created_at'])) ?></p>
                            </div>
                        </div>

                        <div class="card-body" style="margin-top:10px;">
                            <p><strong>Complaint:</strong></p>
                            <div class="complaint-text"><?= htmlspecialchars($row['complaint_text']) ?></div>
                        </div>

                        <div class="card-footer">
                            <?php if (!empty($row['attachment'])): ?>
                                <a href="../uploads/<?= htmlspecialchars(basename($row['attachment'])) ?>" target="_blank" class="btn btn-attachment">View Attachment</a>
                            <?php endif; ?>
                            <a href="update_status.php?id=<?= $row['id'] ?>" class="btn btn-update">Update Status</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p class="no-complaints">No complaints found.</p>
        <?php endif; ?>
    </div>
</div>

<script>
    // Search/filter auto-update with debounce and URL params
    (function() {
        const searchInput = document.getElementById('searchInput');
        const statusSelect = document.getElementById('statusSelect');
        const dateInput = document.getElementById('dateInput');
        const form = document.getElementById('searchFilterForm');
        let debounceTimer;

        function updateURL() {
            const url = new URL(window.location.href);
            const search = searchInput.value.trim();
            const status = statusSelect.value;
            const date = dateInput.value;

            if (search !== '') url.searchParams.set('search', search);
            else url.searchParams.delete('search');

            if (status !== '') url.searchParams.set('status', status);
            else url.searchParams.delete('status');

            if (date !== '') url.searchParams.set('date', date);
            else url.searchParams.delete('date');

            window.location.href = url.toString();
        }

        searchInput.addEventListener('input', () => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(updateURL, 500);
        });
        statusSelect.addEventListener('change', updateURL);
        dateInput.addEventListener('change', updateURL);
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            updateURL();
        });
    })();
</script>

<?php include "../includes/footer.php"; ?>