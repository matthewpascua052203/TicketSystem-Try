<div class="sidebar">
    <h2>Employee Panel</h2>

    <a href="dashboard.php" 
       class="<?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>">
        Dashboard
    </a>

    <a href="request_ticket.php"
       class="<?= basename($_SERVER['PHP_SELF']) == 'request_ticket.php' ? 'active' : '' ?>">
        Request Ticket
    </a>

    <a href="my_tickets.php"
       class="<?= basename($_SERVER['PHP_SELF']) == 'my_tickets.php' ? 'active' : '' ?>">
        My Tickets
    </a>

    <a href="logout.php">Logout</a>
</div>