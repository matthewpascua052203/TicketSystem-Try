<?php
require_once 'config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $category = $_POST['category'];
    $priority = $_POST['priority'];
    $department = $_POST['department'];
    $description = $_POST['description'];

    $attachmentName = NULL;

    if(isset($_FILES['attachment']) && $_FILES['attachment']['error'] == 0) {

        $allowedTypes = ['jpg','jpeg','png','pdf','doc','docx'];
        $fileName = $_FILES['attachment']['name'];
        $fileTmp = $_FILES['attachment']['tmp_name'];
        $fileSize = $_FILES['attachment']['size'];

        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if(in_array($fileExt, $allowedTypes) && $fileSize <= 5 * 1024 * 1024) {

            $newFileName = time() . "_" . uniqid() . "." . $fileExt;
            $uploadPath = "uploads/" . $newFileName;

            if(move_uploaded_file($fileTmp, $uploadPath)) {
                $attachmentName = $newFileName;
            }
        }
    }

    $stmt = $conn->prepare("
        INSERT INTO tickets (problem_type, priority, department, description, attachment)
        VALUES (?, ?, ?, ?, ?)
    ");

    $stmt->bind_param("sssss", $category, $priority, $department, $description, $attachmentName);
    $stmt->execute();
    $stmt->close();

    echo "<script>alert('Ticket Submitted Successfully!'); window.location='view_tickets.php';</script>";
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Create Ticket</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="main-content">
<div class="content-wrapper">

<div class="ticket-header">
    <h1>Submit Ticket</h1>
    <p>Please provide detailed information to help us resolve your issue.</p>
</div>

<div class="ticket-card">
<form method="POST" enctype="multipart/form-data">

<div class="form-row">
    <div class="form-group">
        <label>Category</label>
        <select name="category" required>
            <option>Network Issue</option>
            <option>Hardware Issue</option>
            <option>Software Issue</option>
        </select>
    </div>

    <div class="form-group">
        <label>Priority</label>
        <select name="priority" required>
            <option>Low</option>
            <option>Medium</option>
            <option>High</option>
            <option>Critical</option>
        </select>
    </div>
</div>

<div class="form-row">
    <div class="form-group">
        <label>Department</label>
        <select name="department" required>
            <option>IT</option>
            <option>HR</option>
            <option>Marketing</option>
            <option>Sales</option>
            <option>Operations</option>
        </select>
    </div>
</div>

<div class="form-group full-width">
    <label>Description</label>
    <textarea name="description" rows="5" required></textarea>
</div>

<div class="form-group full-width">
    <label>Attachment</label>
    <input type="file" name="attachment">
    <small>Max 5MB (jpg,jpeg,png,pdf,doc,docx)</small>
</div>

<div class="form-footer">
    <button type="submit" class="primary-btn">Submit Ticket</button>
</div>

</form>
</div>

</div>
</div>

</body>
</html>
