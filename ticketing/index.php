<?php
require_once 'config/database.php';

/* ================= COUNTS ================= */

$total = $conn->query("
    SELECT COUNT(*) AS count 
    FROM employee_tickets
")->fetch_assoc()['count'];

$open = $conn->query("
    SELECT COUNT(*) AS count 
    FROM employee_tickets 
    WHERE status='Open'
")->fetch_assoc()['count'];

$progress = $conn->query("
    SELECT COUNT(*) AS count 
    FROM employee_tickets 
    WHERE status='In Progress'
")->fetch_assoc()['count'];

$resolved = $conn->query("
    SELECT COUNT(*) AS count 
    FROM employee_tickets 
    WHERE status='Resolved'
")->fetch_assoc()['count'];

/* ================= RECENT TICKETS ================= */

$recent = $conn->query("
    SELECT 
        employee_tickets.*,
        users.name,
        users.email
    FROM employee_tickets
    JOIN users ON employee_tickets.user_id = users.id
    ORDER BY employee_tickets.created_at DESC
    LIMIT 5
");
?>

<!DOCTYPE html>
<html>
<head>
<title>Dashboard</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="main-content">
<div class="content-wrapper">

<h1 class="page-title">Dashboard</h1>

<div class="dashboard-welcome">
    <h2>Welcome to the IT Helpdesk Support Ticket System</h2>
    <p>
        Monitor ticket activity, track ongoing issues, and manage support requests efficiently.
        Use the dashboard below to get a quick overview of system performance.
    </p>
</div>

<!-- ================= SUMMARY CARDS ================= -->

<div class="dashboard-cards">

    <div class="dash-card blue">
        <h3>Total Tickets</h3>
        <h2><?= $total; ?></h2>
    </div>

    <div class="dash-card red">
        <h3>Open Tickets</h3>
        <h2><?= $open; ?></h2>
    </div>

    <div class="dash-card orange">
        <h3>In Progress</h3>
        <h2><?= $progress; ?></h2>
    </div>

    <div class="dash-card green">
        <h3>Resolved Tickets</h3>
        <h2><?= $resolved; ?></h2>
    </div>

</div>

<!-- ================= RECENT TICKETS ================= -->

<div class="card">
    <h2 style="margin-bottom:20px;">Recent Tickets</h2>

    <table>
        <tr>
            <th>ID</th>
            <th>Requested By</th>
            <th>Category</th>
            <th>Priority</th>
            <th>Department</th>
            <th>Status</th>
            <th>Date</th>
        </tr>

        <?php while($row = $recent->fetch_assoc()) { ?>
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

            <td>
                <span class="status-badge status-<?= strtolower(str_replace(' ', '-', $row['status'])); ?>">
                    <?= htmlspecialchars($row['status']); ?>
                </span>
            </td>

            <td><?= date("M d, Y", strtotime($row['created_at'])); ?></td>

        </tr>
        <?php } ?>

    </table>
</div>

</div>
</div>

</body>
</html>