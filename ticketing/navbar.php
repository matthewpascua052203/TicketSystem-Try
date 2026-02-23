<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="navbar">
    <h2>Helpdesk</h2>

    <div class="nav-links">
        <a href="index.php" 
           class="<?= ($current_page == 'index.php') ? 'active' : '' ?>">
           Dashboard
        </a>

       

        <a href="view_tickets.php" 
           class="<?= ($current_page == 'view_tickets.php') ? 'active' : '' ?>">
           View Tickets
        </a>

        <a href="analytics.php" 
           class="<?= ($current_page == 'analytics.php') ? 'active' : '' ?>">
           Analytics
        </a>
    </div>

    <div class="nav-right dropdown">
        <a href="#">Login ▾</a>

        <div class="dropdown-menu">
            <a href="employee/employee_login.php">Employee Login</a>
            <a href="admin_login.php">Admin Login</a>
        </div>
    </div>
</div>
