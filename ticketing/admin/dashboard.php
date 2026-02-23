<?php
require_once '../config/database.php';

$newTickets = $conn->query("
    SELECT COUNT(*) AS count 
    FROM employee_tickets 
    WHERE status='Open'
")->fetch_assoc()['count'];

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin_login.php");
    exit();
}

/* Summary Counts */
$total = $conn->query("SELECT COUNT(*) AS count FROM employee_tickets")
              ->fetch_assoc()['count'];

$open = $conn->query("SELECT COUNT(*) AS count FROM employee_tickets WHERE status='Open'")
             ->fetch_assoc()['count'];

$progress = $conn->query("SELECT COUNT(*) AS count FROM employee_tickets WHERE status='In Progress'")
                 ->fetch_assoc()['count'];

$resolved = $conn->query("SELECT COUNT(*) AS count FROM employee_tickets WHERE status='Resolved'")
                 ->fetch_assoc()['count'];
/* ===== DEPARTMENT DATA ===== */

$deptQuery = $conn->query("
    SELECT assigned_department, COUNT(*) as count
    FROM employee_tickets
    GROUP BY assigned_department
");

$departments = [];
$deptCounts = [];

while($row = $deptQuery->fetch_assoc()) {
    $departments[] = $row['assigned_department'];
    $deptCounts[] = $row['count'];
}

/* ===== PRIORITY DATA ===== */

$priorityQuery = $conn->query("
    SELECT priority, COUNT(*) as count
    FROM employee_tickets
    GROUP BY priority
");

$priorities = [];
$priorityCounts = [];

while($row = $priorityQuery->fetch_assoc()) {
    $priorities[] = $row['priority'];
    $priorityCounts[] = $row['count'];
}
?>


<!DOCTYPE html>
<html>
<head>
<title>Admin Dashboard</title>
<link rel="stylesheet" href="../css/employee.css">
</head>
<body>

<div class="sidebar">
    <h2>Admin Panel</h2>
    <a href="dashboard.php" class="active">Dashboard</a>
    <a href="all_tickets.php">All Tickets</a>
    <a href="logout.php">Logout</a>
</div>

<div class="main-content">

<h1>Admin Dashboard</h1>



<?php if($newTickets > 0): ?>
<div class="success-message">
    🔔 You have <?= $newTickets ?> new ticket(s)
</div>
<?php endif; ?>

<div class="cards">
    <div class="card blue"><h3>Total</h3><h2><?= $total ?></h2></div>
    <div class="card orange"><h3>Open</h3><h2><?= $open ?></h2></div>
    <div class="card yellow"><h3>In Progress</h3><h2><?= $progress ?></h2></div>
    <div class="card green"><h3>Resolved</h3><h2><?= $resolved ?></h2></div>
</div>

<div class="recent">
    <h2 style="margin-bottom:20px;">Analytics </h2>

    <div style="display:flex; gap:40px;">

        <div style="width:50%;">
            <h3>Tickets by Department</h3>
            <canvas id="deptChart"></canvas>
        </div>

        <div style="width:50%;">
            <h3>Tickets by Priority</h3>
            <canvas id="priorityChart"></canvas>
        </div>

    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
new Chart(document.getElementById('deptChart'), {
    type: 'bar',
    data: {
        labels: <?= json_encode($departments); ?>,
        datasets: [{
            data: <?= json_encode($deptCounts); ?>,
            backgroundColor: '#2563EB',
            borderRadius: 6
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true } }
    }
});

new Chart(document.getElementById('priorityChart'), {
    type: 'pie',
    data: {
        labels: <?= json_encode($priorities); ?>,
        datasets: [{
            data: <?= json_encode($priorityCounts); ?>,
            backgroundColor: [
                '#16A34A',   // Low
                '#FACC15',   // Medium
                '#F97316',   // High
                '#DC2626'    // Critical
            ]
        }]
    }
});
</script>
</body>
</html>
