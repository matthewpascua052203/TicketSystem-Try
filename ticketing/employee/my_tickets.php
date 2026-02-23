<?php
require_once '../config/database.php';

/* Protect page */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employee') {
    header("Location: employee_login.php");
    exit();
}

$user_id = (int) $_SESSION['user_id'];

/* Get only this employee's tickets */
$stmt = $conn->prepare("
    SELECT * FROM employee_tickets
    WHERE user_id = ?
    ORDER BY created_at DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
<title>My Tickets</title>
<link rel="stylesheet" href="../css/employee.css">
</head>
<body>

<?php include 'sidebar.php'; ?>

<!-- MAIN CONTENT -->
<div class="main-content">
<?php if(isset($_SESSION['success'])): ?>
    <div class="success-message">
        <?= $_SESSION['success']; ?>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>
    <h1>My Submitted Tickets</h1>

    <div class="recent">

        <table>
            <tr>
                <th>ID</th>
                <th>Category</th>
                <th>Priority</th>
                <th>Status</th>
                <th>Date</th>
                <th>Attachment</th>
            </tr>

            <?php if($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td>#<?php echo $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['category']); ?></td>
                    <td><?php echo htmlspecialchars($row['priority']); ?></td>
                    <td>
                        <span class="status-badge 
                        <?php echo strtolower(str_replace(' ', '-', $row['status'])); ?>">
                            <?php echo $row['status']; ?>
                        </span>
                    </td>
                    <td><?php echo date("M d, Y", strtotime($row['created_at'])); ?></td>
                    <td>
                        <?php if($row['attachment']): ?>
                            <a href="../uploads/<?php echo $row['attachment']; ?>" target="_blank">
                                View
                            </a>
                        <?php else: ?>
                            —
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">No tickets submitted yet.</td>
                </tr>
            <?php endif; ?>

        </table>

    </div>

</div>

</body>
</html>
