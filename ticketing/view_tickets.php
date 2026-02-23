<?php
require_once 'config/database.php';

/* ================= GET VALUES ================= */

$category   = $_GET['category']   ?? '';
$department = $_GET['department'] ?? '';
$priority   = $_GET['priority']   ?? '';
$status     = $_GET['status']     ?? '';
$search     = $_GET['search']     ?? '';

$sql = "
    SELECT employee_tickets.*, users.name, users.email
    FROM employee_tickets
    JOIN users ON employee_tickets.user_id = users.id
    WHERE 1
";

/* ================= FILTERS ================= */

if (!empty($category)) {
    $category = $conn->real_escape_string($category);
    $sql .= " AND employee_tickets.category = '$category'";
}

if (!empty($department)) {
    $department = $conn->real_escape_string($department);
    $sql .= " AND employee_tickets.department = '$department'";
}

if (!empty($priority)) {
    $priority = $conn->real_escape_string($priority);
    $sql .= " AND employee_tickets.priority = '$priority'";
}

/* 🔥 THIS WAS MISSING */
if (!empty($status)) {
    $status = $conn->real_escape_string($status);
    $sql .= " AND employee_tickets.status = '$status'";
}

if (!empty($search)) {
    $search = $conn->real_escape_string($search);
    $sql .= " AND (
        users.name LIKE '%$search%' OR
        users.email LIKE '%$search%' OR
        employee_tickets.subject LIKE '%$search%'
    )";
}

$sql .= " ORDER BY employee_tickets.created_at DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
<title>Submitted Tickets</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="main-content">
<div class="content-wrapper">

<h1 class="page-title">Submitted Tickets</h1>

<!-- ================= ONE LINE FILTER BAR ================= -->

<div class="filter-bar">
<form method="GET" id="filterForm">

<input type="text"
       name="search"
       id="searchInput"
       placeholder="Search name or email"
       value="<?= htmlspecialchars($search); ?>">

<select name="category" onchange="submitForm()">
    <option value="">All Categories</option>
    <option <?= $category=='Network Issue'?'selected':'' ?>>Network Issue</option>
    <option <?= $category=='Hardware Issue'?'selected':'' ?>>Hardware Issue</option>
    <option <?= $category=='Software Issue'?'selected':'' ?>>Software Issue</option>
    <option <?= $category=='Email Problem'?'selected':'' ?>>Email Problem</option>
</select>

<select name="department" onchange="submitForm()">
    <option value="">All Departments</option>
    <option <?= $department=='IT'?'selected':'' ?>>IT</option>
    <option <?= $department=='HR'?'selected':'' ?>>HR</option>
    <option <?= $department=='Marketing'?'selected':'' ?>>Marketing</option>
    <option <?= $department=='Admin'?'selected':'' ?>>Admin</option>
    <option <?= $department=='Technical'?'selected':'' ?>>Technical</option>
    <option <?= $department=='Accounting'?'selected':'' ?>>Accounting</option>
    <option <?= $department=='Supply Chain'?'selected':'' ?>>Supply Chain</option>
    <option <?= $department=='MPDC'?'selected':'' ?>>MPDC</option>
    <option <?= $department=='E-Comm'?'selected':'' ?>>E-Comm</option>
</select>

<select name="priority" onchange="submitForm()">
    <option value="">All Priorities</option>
    <option <?= $priority=='Low'?'selected':'' ?>>Low</option>
    <option <?= $priority=='Medium'?'selected':'' ?>>Medium</option>
    <option <?= $priority=='High'?'selected':'' ?>>High</option>
    <option <?= $priority=='Critical'?'selected':'' ?>>Critical</option>
</select>
<select name="status" onchange="submitForm()">
    <option value="">All Status</option>

    <option value="Open" <?= $status=='Open'?'selected':'' ?>>Open</option>

    <option value="In Progress" <?= $status=='In Progress'?'selected':'' ?>>
        In Progress
    </option>

    <option value="Resolved" <?= $status=='Resolved'?'selected':'' ?>>
        Resolved
    </option>

    
</select>

<a href="view_tickets.php" class="clear-btn">Clear</a>

</form>

</div>

<!-- ================= TABLE ================= -->

<div class="card">
<table>

<tr>
<th>ID</th>
<th>Requested By</th>
<th>Category</th>
<th>Priority</th>
<th>Department</th>
<th>Status</th>
<th>Attachment</th>
<th>Date</th>
</tr>

<?php while($row = $result->fetch_assoc()) { ?>
<tr>

<td>#<?= $row['id']; ?></td>

<td>
<strong><?= htmlspecialchars($row['name']); ?></strong><br>
<small><?= htmlspecialchars($row['email']); ?></small>
</td>

<td><?= htmlspecialchars($row['category']); ?></td>

<td>
<span class="badge badge-<?= strtolower($row['priority']); ?>">
<?= htmlspecialchars($row['priority']); ?>
</span>
</td>

<td><?= htmlspecialchars($row['department']); ?></td>

<td><?= htmlspecialchars($row['status']); ?></td>

<td>
<?php if(!empty($row['attachment'])) { ?>
<a href="uploads/<?= $row['attachment']; ?>" target="_blank">View</a>
<?php } else { ?>
No File
<?php } ?>
</td>

<td><?= date("M d, Y", strtotime($row['created_at'])); ?></td>

</tr>
<?php } ?>

</table>
</div>

</div>
</div>

<script>
let typingTimer;
const doneTypingInterval = 600; // 600ms delay

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

/* Dropdown auto-submit still works */
function submitForm(){
    document.getElementById("filterForm").submit();
}
</script>

</body>
</html>