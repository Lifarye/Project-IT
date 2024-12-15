<?php
session_start();

// Sprawdzenie, czy użytkownik jest zalogowany jako admin
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
</head>
<body>
    <h1>Welcome, Admin!</h1>
    <nav>
        <ul>
            <li><a href="manage_products.php">Manage Products</a></li>
            <li><a href="manage_users.php">Manage Users</a></li>
            <li><a href="manage_orders.php">Manage Orders</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
</body>
</html>
