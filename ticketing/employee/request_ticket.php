

<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config/database.php';

require '../vendor/phpmailer/src/PHPMailer.php';
require '../vendor/phpmailer/src/SMTP.php';
require '../vendor/phpmailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employee') {
    header("Location: employee_login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $user_id = $_SESSION['user_id'];
    $subject = $_POST['subject'];
    $category = $_POST['category'];
    $priority = $_POST['priority'];
    $department = $_SESSION['department'];
    $assigned_department = $department;
    $description = !empty($_POST['description']) ? $_POST['description'] : NULL;

    $attachmentName = NULL;

    /* ================= FILE UPLOAD ================= */

    if(isset($_FILES['attachment']) && $_FILES['attachment']['error'] == 0) {

        $allowedTypes = ['jpg','jpeg','png','pdf','doc','docx'];
        $fileName = $_FILES['attachment']['name'];
        $fileTmp = $_FILES['attachment']['tmp_name'];
        $fileSize = $_FILES['attachment']['size'];

        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if(in_array($fileExt, $allowedTypes) && $fileSize <= 5 * 1024 * 1024) {

            $newFileName = time() . "_" . uniqid() . "." . $fileExt;
            $uploadPath = "../uploads/" . $newFileName;

            if(move_uploaded_file($fileTmp, $uploadPath)) {
                $attachmentName = $newFileName;
            }
        }
    }

    /* ================= INSERT INTO DATABASE ================= */

    $stmt = $conn->prepare("
        INSERT INTO employee_tickets
        (user_id, subject, category, priority, department, assigned_department, description, attachment)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "isssssss",
        $user_id,
        $subject,
        $category,
        $priority,
        $department,
        $assigned_department,
        $description,
        $attachmentName
    );

    $stmt->execute();
    $stmt->close();

    /* ================= SEND EMAIL ================= */

    $mail = new PHPMailer(true);

    try {

        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'matthewpascua052203@gmail.com';
        $mail->Password = 'tmwtjqjvadsmgzje'; // your app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ];

        $mail->setFrom('matthewpascua052203@gmail.com', 'IT Helpdesk System');

        /* Add all admin emails */
        $admins = $conn->query("SELECT email FROM users WHERE role='admin'");
        while($admin = $admins->fetch_assoc()){
            $mail->addAddress($admin['email']);
        }

        /* Attach file if exists */
        if (!empty($attachmentName)) {
            $mail->addAttachment('../uploads/' . $attachmentName);
        }

        $mail->isHTML(true);
        $mail->Subject = "New Ticket Submitted - $subject";

        $mail->Body = "
            <div style='font-family:Segoe UI; padding:15px'>
                <h2 style='color:#2563EB'>New Ticket Submitted</h2>
                <hr>
                <p><strong>Employee:</strong> {$_SESSION['name']}</p>
                <p><strong>Email:</strong> {$_SESSION['email']}</p>
                <p><strong>Subject:</strong> $subject</p>
                <p><strong>Category:</strong> $category</p>
                <p><strong>Priority:</strong> $priority</p>
                <p><strong>Department:</strong> $department</p>
                <p><strong>Description:</strong><br>" . ($description ?? 'None') . "</p>
                <p><strong>Attachment:</strong> " . ($attachmentName ? "Included in this email" : "None") . "</p>
                <hr>
                <p style='font-size:12px;color:#64748B'>
                    This is an automated message from IT Helpdesk System.
                </p>
            </div>
        ";

        $mail->send();

    } catch (Exception $e) {
        echo "Mailer Error: " . $mail->ErrorInfo;
        exit();
    }

    /* ================= SUCCESS MESSAGE ================= */

    $_SESSION['success'] = "Ticket successfully submitted!";
    header("Location: my_tickets.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Request Ticket</title>
<link rel="stylesheet" href="../css/employee.css">
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="main-content">

<h1>Submit Ticket</h1>
<p>Please provide detailed information to help us resolve your issue as quickly as possible.</p>

<form method="POST" enctype="multipart/form-data">

    <label>Subject</label>
    <input type="text" name="subject" placeholder="Enter a brief title for the issue" required>

    <label>Priority</label>
    <select name="priority" required>
        <option value="">Select Priority</option>
        <option>Low</option>
        <option>Medium</option>
        <option>High</option>
        <option>Critical</option>
    </select>

    <label>Category</label>
    <select name="category" required>
        <option value="">Select Category</option>
        <option>Network Issue</option>
        <option>Hardware Issue</option>
        <option>Software Issue</option>
        <option>Email Problem</option>
        <option>Account Access</option>
    </select>

    <label>Department</label>
<input type="text" value="<?= $_SESSION['department']; ?>" readonly>

    <label>Description (Optional)</label>
    <textarea name="description" rows="5" placeholder="Describe your issue in detail..."></textarea>

    <label>Attachment</label>
    <input type="file" name="attachment">
    <small>Max 5MB (jpg, jpeg, png, pdf, doc, docx)</small>

    <br><br>
    <button type="submit">Submit Ticket</button>

</form>

</div>
</body>
</html>