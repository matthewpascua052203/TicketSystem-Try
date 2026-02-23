<?php
require_once '../config/database.php';

/* Protect page */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employee') {
    header("Location: employee_login.php");
    exit();
}

$user_id = (int) $_SESSION['user_id'];

/* Ticket Counts (ONLY this employee) */
$total = $conn->query("
    SELECT COUNT(*) AS count 
    FROM employee_tickets 
    WHERE user_id = $user_id
")->fetch_assoc()['count'];

$open = $conn->query("
    SELECT COUNT(*) AS count 
    FROM employee_tickets 
    WHERE user_id = $user_id AND status='Open'
")->fetch_assoc()['count'];

$progress = $conn->query("
    SELECT COUNT(*) AS count 
    FROM employee_tickets 
    WHERE user_id = $user_id AND status='In Progress'
")->fetch_assoc()['count'];

$resolved = $conn->query("
    SELECT COUNT(*) AS count 
    FROM employee_tickets 
    WHERE user_id = $user_id AND status='Resolved'
")->fetch_assoc()['count'];

/* Recent Tickets */
$recent = $conn->query("
    SELECT * 
    FROM employee_tickets 
    WHERE user_id = $user_id 
    ORDER BY created_at DESC 
    LIMIT 5
");
?>

<!DOCTYPE html>
<html>
<head>
<title>Employee Dashboard</title>
<link rel="stylesheet" href="../css/employee.css">
</head>
<body>

<?php include 'sidebar.php'; ?>

<!-- MAIN CONTENT -->
<div class="main-content">

    <h1>Welcome, <?= htmlspecialchars($_SESSION['name']); ?> 👋</h1>
    <p><strong>Department:</strong> <?= htmlspecialchars($_SESSION['department']); ?></p>

    <!-- SUMMARY CARDS -->
    <div class="cards">

        <div class="card blue">
            <h3>Total Tickets</h3>
            <h2><?= $total ?></h2>
        </div>

        <div class="card orange">
            <h3>Open</h3>
            <h2><?= $open ?></h2>
        </div>

        <div class="card yellow">
            <h3>In Progress</h3>
            <h2><?= $progress ?></h2>
        </div>

        <div class="card green">
            <h3>Resolved</h3>
            <h2><?= $resolved ?></h2>
        </div>

    </div>

    <!-- RECENT TICKETS -->
    <div class="recent">
        <h2>My Recent Tickets</h2>

        <table>
            <tr>
                <th>ID</th>
                <th>Subject</th>
                <th>Category</th>
                <th>Priority</th>
                <th>Status</th>
                <th>Date</th>
            </tr>

            <?php while($row = $recent->fetch_assoc()) { ?>
            <tr>
                <td>#<?= $row['id']; ?></td>
                <td><?= htmlspecialchars($row['subject']); ?></td>
                <td><?= htmlspecialchars($row['category']); ?></td>

                <td class="priority-<?= strtolower($row['priority']); ?>">
                    <?= htmlspecialchars($row['priority']); ?>
                </td>

                <td>
                    <span class="status-badge <?= strtolower(str_replace(' ', '-', $row['status'])); ?>">
                        <?= htmlspecialchars($row['status']); ?>
                    </span>
                </td>

                <td><?= date("M d, Y", strtotime($row['created_at'])); ?></td>
            </tr>
            <?php } ?>

        </table>
    </div>

</div>

</body>
</html>