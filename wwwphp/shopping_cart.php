<?php

session_start();
$db_server = "localhost";
$db_user = "root";
$db_pass = ""; 
$db_name = "web page"; 
$db_port = 3306;

try {
    $dsn = "mysql:host=$db_server;dbname=$db_name;port=$db_port;charset=utf8mb4";
    $pdo = new PDO($dsn, $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    error_log("Connection failed: " . $e->getMessage());
    die("Connection failed. Please try again later.");
}

// Pobierz produkty z koszyka w sesji
$cart_items = [];
$total_price = 0;

if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
    foreach ($_SESSION['cart'] as $cart_item) {
        $product_id = $cart_item['product_id'];
        $quantity = $cart_item['quantity'];

        // Pobierz szczegóły produktu
        $product_query = "SELECT name, unit_price, image_url FROM products WHERE product_id = :product_id LIMIT 1";
        $stmt = $pdo->prepare($product_query);
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->execute();

        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($product) {
            $product['quantity'] = $quantity;
            $cart_items[] = $product;
            $total_price += $product['unit_price'] * $quantity; // Oblicz całkowitą cenę (unit_price * quantity)
        }
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
                                <h4><?= htmlspecialchars($item['name']) ?></h4>
                                <p>Quantity: <?= htmlspecialchars($item['quantity']) ?></p>
                                <p>Price: $<?= number_format($item['unit_price'], 2) ?></p>
                            </div>
                            <div>
                                <form method="POST" action="update_cart.php">
                                    <input type="hidden" name="product_id" value="<?= htmlspecialchars($item['product_id']) ?>">
                                    <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1">
                                    <button type="submit">Update Quantity</button>
                                </form>
                                <form method="POST" action="remove_from_cart.php">
                                    <input type="hidden" name="product_id" value="<?= htmlspecialchars($item['product_id']) ?>">
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

<?php $pdo = null; ?>
