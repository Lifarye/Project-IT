<?php
session_start();
if (!isset($_GET['order_id'])) {
    die("Order ID is missing.");
}

$order_id = $_GET['order_id'];

$db_server = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "web page"; 
$db_port = 3306;

try {
    $dsn = "mysql:host=$db_server;dbname=$db_name;port=$db_port;charset=utf8mb4";
    $pdo = new PDO($dsn, $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Pobierz dane zamówienia
    $order_query = "SELECT * FROM orders WHERE order_id = :order_id LIMIT 1";
    $stmt = $pdo->prepare($order_query);
    $stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
    $stmt->execute();
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        die("Order not found.");
    }

    // Pobierz szczegóły zamówienia
    $details_query = "SELECT p.name, od.quantity, od.unit_price FROM order_details od
                      JOIN products p ON od.product_id = p.product_id WHERE od.order_id = :order_id";
    $stmt = $pdo->prepare($details_query);
    $stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
    $stmt->execute();
    $order_details = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $total_price = 0;
    foreach ($order_details as $item) {
        $total_price += $item['unit_price'] * $item['quantity'];
    }
} catch (PDOException $e) {
    error_log("Error while retrieving order data: " . $e->getMessage());
    die("An error occurred. Please try again later.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
</head>
<body>
    <h1>Order Confirmation</h1>
    <p>Thank you for your order! Your order ID is: <?php echo htmlspecialchars($order_id); ?></p>
    <p>Status: <?php echo htmlspecialchars($order['status']); ?></p>
    <h3>Order Details</h3>
    <ul>
        <?php foreach ($order_details as $item): ?>
            <li><?php echo htmlspecialchars($item['name']) . ' - Quantity: ' . $item['quantity'] . ' - Price: ' . number_format($item['unit_price'], 2); ?></li>
        <?php endforeach; ?>
    </ul>
    <p>Total Price: <?php echo number_format($total_price, 2); ?> PLN</p>
</body>
</html>
