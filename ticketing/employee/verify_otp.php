<?php
require_once '../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['verify_email'])) {
    header("Location: register.php");
    exit();
}

$email = $_SESSION['verify_email'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $enteredOtp = trim($_POST['otp']);

    $stmt = $conn->prepare("
        SELECT otp_code FROM users 
        WHERE email = ? AND is_verified = 0
    ");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && $enteredOtp == $user['otp_code']) {

        $update = $conn->prepare("
            UPDATE users 
            SET is_verified = 1, otp_code = NULL 
            WHERE email = ?
        ");
        $update->bind_param("s", $email);
        $update->execute();
        $update->close();

        unset($_SESSION['verify_email']);

        header("Location: employee_login.php?registered=1");
        exit();

    } else {
        $error = "Invalid OTP code.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Verify OTP</title>
<link rel="stylesheet" href="../css/employee.css">
</head>
<body>

<div class="login-container">
<div class="login-card">

<h2>Email Verification</h2>

<?php if(isset($error)) echo "<div class='error'>$error</div>"; ?>

<form method="POST">
    <label>Enter OTP Code</label>
    <input type="text" name="otp" required>
    <button type="submit">Verify</button>
</form>

</div>
</div>

</body>
</html>