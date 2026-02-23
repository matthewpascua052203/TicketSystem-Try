<?php
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin_login.php");
    exit();
}

/* ================= GET VALUES ================= */

$category   = $_GET['category']   ?? '';
$department = $_GET['department'] ?? '';
$priority   = $_GET['priority']   ?? '';
$status     = $_GET['status']     ?? '';
$search     = $_GET['search']     ?? '';

$query = "
SELECT employee_tickets.*, users.name, users.email
FROM employee_tickets
JOIN users ON employee_tickets.user_id = users.id
WHERE 1
";

/* ================= FILTERS ================= */

if (!empty($category)) {
    $category = $conn->real_escape_string($category);
    $query .= " AND employee_tickets.category = '$category'";
}

if (!empty($department)) {
    $department = $conn->real_escape_string($department);
    $query .= " AND employee_tickets.department = '$department'";
}

if (!empty($priority)) {
    $priority = $conn->real_escape_string($priority);
    $query .= " AND employee_tickets.priority = '$priority'";
}

if (!empty($status)) {

    if ($status === 'unread') {
        $query .= " AND employee_tickets.is_read = 0";
    } else {
        $status = $conn->real_escape_string($status);
        $query .= " AND employee_tickets.status = '$status'";
    }
}

if (!empty($search)) {
    $search = $conn->real_escape_string($search);
    $query .= " AND (
        users.name LIKE '%$search%' OR
        users.email LIKE '%$search%' OR
        employee_tickets.category LIKE '%$search%'
    )";
}

$query .= " ORDER BY employee_tickets.created_at DESC";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
<title>All Tickets</title>
<link rel="stylesheet" href="../css/employee.css">
</head>
<body>

<div class="sidebar">
    <h2>Admin Panel</h2>
    <a href="dashboard.php">Dashboard</a>
    <a href="all_tickets.php" class="active">All Tickets</a>
    <a href="logout.php">Logout</a>
</div>

<div class="main-content">

<?php if(isset($_SESSION['success'])): ?>
    <div class="success-message">
        <?= $_SESSION['success']; ?>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<h1>All Tickets</h1>

<!-- ONE LINE FILTER BAR -->
<div class="filter-bar">
<form method="GET" id="filterForm">

<input type="text"
       name="search"
       id="searchInput"
       placeholder="Search name or email"
       value="<?= htmlspecialchars($search); ?>">

<select name="category" onchange="submitForm()">
    <option value="">All Category</option>
    <option value="Network Issue" <?= $category=='Network Issue'?'selected':'' ?>>Network Issue</option>
    <option value="Hardware Issue" <?= $category=='Hardware Issue'?'selected':'' ?>>Hardware Issue</option>
    <option value="Software Issue" <?= $category=='Software Issue'?'selected':'' ?>>Software Issue</option>
</select>

<select name="department" onchange="submitForm()">
    <option value="">All Department</option>
    <option value="IT" <?= $department=='IT'?'selected':'' ?>>IT</option>
    <option value="HR" <?= $department=='HR'?'selected':'' ?>>HR</option>
    <option value="Marketing" <?= $department=='Marketing'?'selected':'' ?>>Marketing</option>
    <option value="Admin" <?= $department=='Admin'?'selected':'' ?>>Admin</option>
    <option value="Technical" <?= $department=='Technical'?'selected':'' ?>>Technical</option>
    <option value="Accounting" <?= $department=='Accounting'?'selected':'' ?>>Accounting</option>
    <option value="Supply Chain" <?= $department=='Supply Chain'?'selected':'' ?>>Supply Chain</option>
    <option value="MPDC" <?= $department=='MPDC'?'selected':'' ?>>MPDC</option>
    <option value="E-Comm" <?= $department=='E-Comm'?'selected':'' ?>>E-Comm</option>
</select>

<select name="priority" onchange="submitForm()">
    <option value="">All Priority</option>
    <option value="Low" <?= $priority=='Low'?'selected':'' ?>>Low</option>
    <option value="Medium" <?= $priority=='Medium'?'selected':'' ?>>Medium</option>
    <option value="High" <?= $priority=='High'?'selected':'' ?>>High</option>
    <option value="Critical" <?= $priority=='Critical'?'selected':'' ?>>Critical</option>
</select>

<select name="status" onchange="submitForm()">
    <option value="">All Status</option>
    <option value="Open" <?= $status=='Open'?'selected':'' ?>>Open</option>
    <option value="In Progress" <?= $status=='In Progress'?'selected':'' ?>>In Progress</option>
    <option value="Resolved" <?= $status=='Resolved'?'selected':'' ?>>Resolved</option>
    <option value="unread" <?= $status=='unread'?'selected':'' ?>>Unread</option>
</select>

<a href="all_tickets.php" class="clear-btn">Clear</a>

</form>
</div>
<table>
<tr>
    <th>ID</th>
    <th>Requested By</th>
    <th>Original Dept</th>
    <th>Assigned Dept</th>
    <th>Priority</th>
    <th>Status</th>
    <th>Date</th>
</tr>

<?php while($row = $result->fetch_assoc()) { ?>
<tr 
onclick="window.location='update_ticket.php?id=<?= $row['id']; ?>'"
style="cursor:pointer; <?= $row['is_read'] == 0 ? 'background:#EFF6FF; font-weight:600;' : ''; ?>">

    <td>#<?= $row['id']; ?></td>

    <td>
        <strong><?= htmlspecialchars($row['name']); ?></strong><br>
        <small><?= htmlspecialchars($row['email']); ?></small>
    </td>

    

    <td><?= htmlspecialchars($row['department']); ?></td>
<td><?= htmlspecialchars($row['assigned_department']); ?></td>

    <td><?= htmlspecialchars($row['priority']); ?></td>

    <td>
    <?= htmlspecialchars($row['status']); ?>
    <?php if($row['is_read'] == 0): ?>
        <span style="background:#2563EB;color:white;padding:3px 8px;border-radius:12px;font-size:11px;margin-left:6px;">
            NEW
        </span>
    <?php endif; ?>
</td>

    <td><?= date("M d, Y", strtotime($row['created_at'])); ?></td>

</tr>

<?php } ?>

</table>

</div>

<script>
let typingTimer;
const doneTypingInterval = 600;

const searchInput = document.getElementById("searchInput");

searchInput.addEventListener("keyup", function () {
    clearTimeout(typingTimer);
    typingTimer = setTimeout(doneTyping, doneTypingInterval);
});

searchInput.addEventListener("keydown", function () {
    clearTimeout(typingTimer);
});

function doneTyping() {
    document.getElementById("filterForm").submit();
}

function submitForm(){
    document.getElementById("filterForm").submit();
}
</script>
</body>
</html>
