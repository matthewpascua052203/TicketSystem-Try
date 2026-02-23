<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config/database.php';
require_once '../vendor/phpmailer/src/Exception.php';
require_once '../vendor/phpmailer/src/PHPMailer.php';
require_once '../vendor/phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}




/* If already logged in */
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name       = trim($_POST['name']);
    $email      = trim($_POST['email']);
    $department = trim($_POST['department']);
    $password   = trim($_POST['password']);

    if (!empty($name) && !empty($email) && !empty($department) && !empty($password)) {

        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error = "Email already registered.";
        } else {

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $otp = rand(100000, 999999);

            $stmt = $conn->prepare("
                INSERT INTO users 
                (name, email, department, password, role, otp_code, is_verified)
                VALUES (?, ?, ?, ?, 'employee', ?, 0)
            ");

            $stmt->bind_param("sssss", $name, $email, $department, $hashedPassword, $otp);

            if ($stmt->execute()) {

                /* ===== SEND OTP EMAIL ===== */

                $mail = new PHPMailer(true);

                try {

                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com';
                    $mail->SMTPAuth   = true;
                    $mail->Username   = 'matthewpascua052203@gmail.com';
                    $mail->Password   = 'tmwtjqjvadsmgzje';
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port       = 587;

                    $mail->SMTPOptions = [
                        'ssl' => [
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true
                        ]
                    ];

                    $mail->setFrom('matthewpascua052203@gmail.com', 'IT Helpdesk System');
                    $mail->addAddress($email);

                    $mail->isHTML(true);
                    $mail->Subject = "Verify Your Email - OTP Code";

                    $mail->Body = "
                        <div style='font-family:Segoe UI;padding:20px'>
                            <h2 style='color:#2563EB'>Email Verification</h2>
                            <p>Hello <strong>$name</strong>,</p>
                            <p>Your OTP Code is:</p>
                            <h1 style='color:#2563EB'>$otp</h1>
                            <p>Please enter this code to activate your account.</p>
                        </div>
                    ";

                    $mail->send();

                    $_SESSION['verify_email'] = $email;

                    header("Location: verify_otp.php");
                    exit();

                } catch (Exception $e) {
                    $error = "Email could not be sent. " . $mail->ErrorInfo;
                }

            } else {
                $error = "Registration failed.";
            }

            $stmt->close();
        }

        $check->close();
    } else {
        $error = "Please fill in all fields.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Create Employee Account</title>
    <link rel="stylesheet" href="../css/employee.css">
</head>
<body>

<div class="login-container">
    <div class="login-card">

        <h2>Create Account</h2>

        <?php if(isset($error)) : ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">

            <label>Full Name</label>
            <input type="text" name="name" required>

            <label>Email</label>
            <input type="email" name="email" required>

            <label>Department</label>
            <select name="department" required>
                <option value="">Select Department</option>
                <option>IT</option>
                <option>HR</option>
                <option>Marketing</option>
                <option>Admin</option>
                <option>Technical</option>
                <option>Accounting</option>
                <option>Supply Chain</option>
                <option>MPDC</option>
                <option>E-Comm</option>
            </select>

            <label>Password</label>
            <input type="password" name="password" required>

            <button type="submit">Create Account</button>

        </form>

        <div class="signup-link">
            Already have an account?
            <a href="employee_login.php">Login here</a>
        </div>

    </div>
</div>

</body>
</html>