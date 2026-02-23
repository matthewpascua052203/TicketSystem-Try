<?php
require_once 'config/database.php';

/* ================= TOTAL ================= */

$total_result = $conn->query("
    SELECT COUNT(*) AS total 
    FROM employee_tickets
");
$total = $total_result->fetch_assoc()['total'];

/* ================= DEPARTMENT DATA ================= */

$dept_query = $conn->query("
    SELECT department, COUNT(*) AS count 
    FROM employee_tickets 
    GROUP BY department
");

$departments = [];
$dept_counts = [];

while($row = $dept_query->fetch_assoc()) {
    $departments[] = $row['department'];
    $dept_counts[] = $row['count'];
}

/* ================= PRIORITY DATA ================= */

$priority_query = $conn->query("
    SELECT priority, COUNT(*) AS count 
    FROM employee_tickets 
    GROUP BY priority
");

$priorities = [];
$priority_counts = [];
$priority_colors = [];

while($row = $priority_query->fetch_assoc()) {

    $priorities[] = $row['priority'];
    $priority_counts[] = $row['count'];

    /* Match your badge colors */
    switch($row['priority']) {
        case 'Low':
            $priority_colors[] = '#10B981'; // green
            break;
        case 'Medium':
            $priority_colors[] = '#FACC15'; // yellow
            break;
        case 'High':
            $priority_colors[] = '#F97316'; // orange
            break;
        case 'Critical':
            $priority_colors[] = '#DC2626'; // red
            break;
        default:
            $priority_colors[] = '#94A3B8';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Analytics Dashboard</title>
<link rel="stylesheet" href="css/style.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
.graph-container {
    display: flex;
    gap: 25px;
    flex-wrap: wrap;
}

.graph-card {
    flex: 1;
    min-width: 350px;
    background: white;
    padding: 25px;
    border-radius: 16px;
    box-shadow: 0 15px 35px rgba(0,0,0,0.05);
}

canvas {
    max-height: 300px;
}
</style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="main-content">
<div class="content-wrapper">

<h1 class="page-title">Analytics Dashboard</h1>

<div class="card" style="margin-bottom:25px;">
    <h3>Total Tickets</h3>
    <h2><?php echo $total; ?></h2>
</div>

<!-- ================= GRAPHS ================= -->

<div class="graph-container">

    <div class="graph-card">
        <h3>Tickets by Department</h3>
        <canvas id="departmentChart"></canvas>
    </div>

    <div class="graph-card">
        <h3>Priority Distribution</h3>
        <canvas id="priorityChart"></canvas>
    </div>

</div>

</div>
</div>

<script>

/* Department Bar Chart */
new Chart(document.getElementById('departmentChart'), {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($departments); ?>,
        datasets: [{
            data: <?php echo json_encode($dept_counts); ?>,
            backgroundColor: '#2563EB',
            borderRadius: 8
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true }
        }
    }
});

/* Priority Pie Chart */
new Chart(document.getElementById('priorityChart'), {
    type: 'pie',
    data: {
        labels: <?php echo json_encode($priorities); ?>,
        datasets: [{
            data: <?php echo json_encode($priority_counts); ?>,
            backgroundColor: <?php echo json_encode($priority_colors); ?>
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});

</script>

</body>
</html>