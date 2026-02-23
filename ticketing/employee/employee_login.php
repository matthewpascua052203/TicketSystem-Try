<?php
require_once '../config/database.php';

/* If already logged in, redirect */
if (isset($_SESSION['user_id']) && $_SESSION['role'] == 'employee') {
    header("Location: dashboard.php");
    exit();
}

if(isset($_GET['registered'])) {
    echo "<div class='success'>Account created successfully! Please login.</div>";
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!empty($email) && !empty($password)) {

       $stmt = $conn->prepare("
    SELECT * FROM users 
    WHERE email = ? AND role = 'employee' AND is_verified = 1
");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {

            if (password_verify($password, $user['password'])) {

                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['department'] = $user['department'];
                $_SESSION['role'] = $user['role'];

                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Incorrect password.";
            }

        } else {
            $error = "No account found with that email.";
        }

        $stmt->close();

    } else {
        $error = "Please fill in all fields.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Employee Login</title>
<link rel="stylesheet" href="../css/employee.css">
</head>
<body>

<div class="login-container">
    <div class="login-card">

        <h2>Employee Login</h2>

        <?php if(isset($error)) : ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">

            <label>Email</label>
            <input type="email" name="email" required>

            <label>Password</label>
            <input type="password" name="password" required>

            <button type="submit">Login</button>

        </form>

        <div class="signup-link">
            Don’t have an account?
            <a href="register.php">Sign up</a>
        </div>

    </div>
</div>

</body>
</html>
