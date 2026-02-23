<?php
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin_login.php");
    exit();
}

$id = $_GET['id'];

$stmt = $conn->prepare("
SELECT employee_tickets.*, users.name, users.email
FROM employee_tickets
JOIN users ON employee_tickets.user_id = users.id
WHERE employee_tickets.id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$ticket = $result->fetch_assoc();

/* Update Status */
if (isset($_POST['status'])) {
    $newStatus = $_POST['status'];
    $conn->query("UPDATE employee_tickets SET status='$newStatus' WHERE id=$id");
    header("Location: view_ticket.php?id=$id");
    exit();
}

/* Reassign */
if (isset($_POST['assigned_department'])) {
    $newDept = $_POST['assigned_department'];
    $conn->query("UPDATE employee_tickets SET assigned_department='$newDept' WHERE id=$id");
    header("Location: view_ticket.php?id=$id");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Ticket Details</title>
<link rel="stylesheet" href="../css/employee.css">
</head>
<body>

<div class="sidebar">
    <h2>Admin Panel</h2>
    <a href="dashboard.php">Dashboard</a>
    <a href="all_tickets.php">All Tickets</a>
    <a href="logout.php">Logout</a>
</div>

<div class="main-content">

<h1>Ticket #<?= $ticket['id'] ?></h1>

<p><strong>Requested by:</strong> <?= $ticket['name'] ?></p>
<p><strong>Email:</strong> <?= $ticket['email'] ?></p>
<p><strong>Original Dept:</strong> <?= $ticket['department'] ?></p>
<p><strong>Assigned Dept:</strong> <?= $ticket['assigned_department'] ?? 'Not Assigned' ?></p>
<p><strong>Category:</strong> <?= $ticket['category'] ?></p>
<p><strong>Priority:</strong> <?= $ticket['priority'] ?></p>
<p><strong>Status:</strong> <?= $ticket['status'] ?></p>
<p><strong>Description:</strong><br><?= $ticket['description'] ?></p>

<?php if($ticket['attachment']): ?>
<p><a href="../uploads/<?= $ticket['attachment'] ?>" target="_blank">View Attachment</a></p>
<?php endif; ?>

<hr>

<h3>Update Status</h3>
<form method="POST">
    <select name="status">
        <option>Open</option>
        <option>In Progress</option>
        <option>Resolved</option>
    </select>
    <button type="submit">Update</button>
</form>

<h3>Reassign Department</h3>
<form method="POST">
    <select name="assigned_department">
        <option>IT</option>
        <option>HR</option>
        <option>Finance</option>
        <option>Marketing</option>
    </select>
    <button type="submit">Reassign</button>
</form>

</div>
</body>
</html>
