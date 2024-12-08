<?php
// Ustawienia bazy danych
$host = '127.0.0.1';
$db = 'internet_shop';
$user = 'root'; // Zmień na swoje dane użytkownika MySQL
$pass = ''; // Zmień na swoje hasło do MySQL

// Połączenie z bazą danych
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Połączenie z bazą danych nie powiodło się: " . $conn->connect_error);
}

// Pobierz aktywne zamówienie użytkownika (przykładowy `customer_id`)
$customer_id = 1; // Zmień na dynamiczne ID użytkownika
$order_query = "SELECT order_id FROM orders WHERE customer_id = $customer_id AND STATUS = 'In Cart' LIMIT 1";
$order_result = $conn->query($order_query);
$order_id = null;

if ($order_result && $order_result->num_rows > 0) {
    $order_id = $order_result->fetch_assoc()['order_id'];
}

// Pobierz szczegóły zamówienia
$cart_items = [];
$total_price = 0;

if ($order_id) {
    $details_query = "
        SELECT od.detail_id, p.NAME, od.quantity, od.unit_price, p.image_url 
        FROM order_details od
        JOIN products p ON od.product_id = p.product_id
        WHERE od.order_id = $order_id
    ";
    $details_result = $conn->query($details_query);

    while ($row = $details_result->fetch_assoc()) {
        $cart_items[] = $row;
        $total_price += $row['unit_price'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="style.css" type="text/css">
    <style>
        .cart-container { max-width: 800px; margin: 50px auto; padding: 20px; border: 1px solid #ccc; background-color: #f9f9f9; border-radius: 10px; }
        .cart-item { display: flex; align-items: center; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #ddd; }
        .cart-item img { width: 50px; height: 50px; border-radius: 5px; }
        .cart-item h4 { margin: 0; font-size: 16px; }
        .cart-item p { margin: 0; font-size: 14px; color: gray; }
        .cart-total { text-align: right; font-size: 18px; margin-top: 20px; font-weight: bold; }
        .checkout-button { display: block; width: 100%; padding: 10px; margin-top: 20px; background-color: #007bff; color: white; text-align: center; text-decoration: none; border-radius: 5px; font-size: 16px; }
        .checkout-button:hover { background-color: #0056b3; }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1>Shopping Cart</h1>
        </div>
    </header>

    <main>
        <div class="cart-container">
            <h2>Your Cart</h2>
            <?php if ($cart_items): ?>
                <div id="cart-items">
                    <?php foreach ($cart_items as $item): ?>
                        <div class="cart-item">
                            <div>
                                <img src="<?= htmlspecialchars($item['image_url'] ?? 'Images/default.jpg') ?>" alt="Product">
                            </div>
                            <div>
                                <h4><?= htmlspecialchars($item['NAME']) ?></h4>
                                <p>Quantity: <?= $item['quantity'] ?></p>
                                <p>Price: $<?= number_format($item['unit_price'], 2) ?></p>
                            </div>
                            <div>
                                <form method="POST" action="remove_from_cart.php">
                                    <input type="hidden" name="detail_id" value="<?= $item['detail_id'] ?>">
                                    <button type="submit">Remove</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <p class="cart-total">Total: $<?= number_format($total_price, 2) ?></p>
                <a href="checkout.php" class="checkout-button">Proceed to Checkout</a>
            <?php else: ?>
                <p>Your cart is empty.</p>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2024 Electronic Store. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
<?php $conn->close(); ?>
