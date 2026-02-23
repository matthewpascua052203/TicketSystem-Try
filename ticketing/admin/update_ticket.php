<?php
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin_login.php");
    exit();
}

if (!isset($_GET['id'])) {
    die("Invalid Ticket ID");
}

$id = (int) $_GET['id'];

$conn->query("UPDATE employee_tickets SET is_read = 1 WHERE id = $id");

/* Get full ticket + employee info */
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

if (!$ticket) {
    die("Ticket not found.");
}

/* Update status & department */
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $new_status = $_POST['status'];
    $new_department = $_POST['assigned_department'];

    $update = $conn->prepare("
        UPDATE employee_tickets
        SET status = ?, assigned_department = ?
        WHERE id = ?
    ");
    $update->bind_param("ssi", $new_status, $new_department, $id);
    $update->execute();
    $update->close();

    $_SESSION['success'] = "Ticket #$id successfully updated.";
header("Location: all_tickets.php");
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
    <a href="all_tickets.php" class="active">All Tickets</a>
    <a href="logout.php">Logout</a>
</div>

<div class="main-content">

<h1>Ticket #<?= $ticket['id']; ?></h1>

<div class="recent">

<!-- ===== EMPLOYEE INFO ===== -->
<h3>Employee Information</h3>
<p><strong>Name:</strong> <?= htmlspecialchars($ticket['name']); ?></p>
<p><strong>Email:</strong> <?= htmlspecialchars($ticket['email']); ?></p>
<p><strong>Original Department:</strong> <?= htmlspecialchars($ticket['department']); ?></p>

<hr style="margin:20px 0;">

<!-- ===== TICKET INFO ===== -->
<h3>Ticket Details</h3>
<p><strong>Subject:</strong> <?= htmlspecialchars($ticket['subject']); ?></p>
<p><strong>Category:</strong> <?= htmlspecialchars($ticket['category']); ?></p>
<p><strong>Priority:</strong> <?= htmlspecialchars($ticket['priority']); ?></p>
<p><strong>Status:</strong> <?= htmlspecialchars($ticket['status']); ?></p>
<p><strong>Assigned Department:</strong> <?= htmlspecialchars($ticket['assigned_department']); ?></p>
<p><strong>Date Created:</strong> <?= date("M d, Y h:i A", strtotime($ticket['created_at'])); ?></p>

<?php if (!empty($ticket['description'])) { ?>
    <p><strong>Description:</strong><br>
    <?= nl2br(htmlspecialchars($ticket['description'])); ?></p>
<?php } ?>

<?php if (!empty($ticket['attachment'])) { ?>
    <p><strong>Attachment:</strong>
        <a href="../uploads/<?= $ticket['attachment']; ?>" target="_blank">
            View Attachment
        </a>
    </p>
<?php } ?>

<hr style="margin:25px 0;">

<!-- ===== UPDATE SECTION ===== -->
<h3>Update Ticket</h3>

<form method="POST">

    <label>Status</label>
    <select name="status">
        <option <?= $ticket['status']=='Open'?'selected':'' ?>>Open</option>
        <option <?= $ticket['status']=='In Progress'?'selected':'' ?>>In Progress</option>
        <option <?= $ticket['status']=='Resolved'?'selected':'' ?>>Resolved</option>
    </select>

    <label>Assign To Department</label>
<select name="assigned_department">
    <option <?= $ticket['assigned_department']=='IT'?'selected':'' ?>>IT</option>
    <option <?= $ticket['assigned_department']=='HR'?'selected':'' ?>>HR</option>
    <option <?= $ticket['assigned_department']=='Marketing'?'selected':'' ?>>Marketing</option>
    <option <?= $ticket['assigned_department']=='Admin'?'selected':'' ?>>Admin</option>
    <option <?= $ticket['assigned_department']=='Technical'?'selected':'' ?>>Technical</option>
    <option <?= $ticket['assigned_department']=='Accounting'?'selected':'' ?>>Accounting</option>
    <option <?= $ticket['assigned_department']=='Supply Chain'?'selected':'' ?>>Supply Chain</option>
    <option <?= $ticket['assigned_department']=='MPDC'?'selected':'' ?>>MPDC</option>
    <option <?= $ticket['assigned_department']=='E-Comm'?'selected':'' ?>>E-Comm</option>
</select>

    <br><br>
    <button type="submit">Update Ticket</button>

</form>

</div>
</div>

</body>
</html>