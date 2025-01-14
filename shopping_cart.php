<?php

session_start();
include 'db.php';

// Pobierz produkty z koszyka w sesji
$cart_items = [];
$total_price = 0;

if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
    foreach ($_SESSION['cart'] as $cart_item) {
        $product_id = $cart_item['product_id'];
        $quantity = $cart_item['quantity'];

        try {
            // Pobierz szczegóły produktu
            $product_query = "SELECT name, price, image_url FROM products WHERE product_id = :product_id LIMIT 1";
            $stmt = $pdo->prepare($product_query);
            $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
            $stmt->execute();

            $product = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($product) {
                $product['quantity'] = $quantity;
                $product['product_id'] = $product_id; // Dodanie id produktu do tablicy produktu
                $cart_items[] = $product;
                $total_price += $product['price'] * $quantity; // Oblicz całkowitą cenę (price * quantity)
            }
        } catch (PDOException $e) {
            // Loguj błąd do pliku logów, ale nie pokazuj go użytkownikowi
            error_log("Database error: " . $e->getMessage());
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
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            color: #333;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        header {
            background-color: #222;
            color: #fff;
            padding: 20px 0;
        }
        header .container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
        }
        header h1 {
            margin: 0;
            font-size: 24px;
        }
        header a {
            color: #fff;
            text-decoration: none;
            font-size: 16px;
            font-weight: bold;
        }
        header a:hover {
            text-decoration: underline;
        }
        .cart-container { max-width: 800px; margin: 50px auto; padding: 20px; border: 1px solid #ccc; background-color: #f9f9f9; border-radius: 10px; }
        .cart-item { display: flex; align-items: center; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #ddd; }
        .cart-item img { width: 50px; height: 50px; border-radius: 5px; }
        .cart-item h4 { margin: 0; font-size: 16px; }
        .cart-item p { margin: 0; font-size: 14px; color: gray; }
        .cart-total { text-align: right; font-size: 18px; margin-top: 20px; font-weight: bold; }
        .checkout-button { display: block; width: 100%; padding: 10px; margin-top: 20px; background-color: #007bff; color: white; text-align: center; text-decoration: none; border-radius: 5px; font-size: 16px; }
        .checkout-button:hover { background-color: #0056b3; }
        footer {
            background-color: #222;
            color: #fff;
            padding: 20px 0;
            text-align: center;
            margin-top: auto;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1>Shopping Cart</h1>
            <a href="index.php">Return to Home Page</a>
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
                                <p>Price: $<?= number_format($item['price'], 2) ?></p>
                            </div>
                            <div>
                                <form method="POST" action="update_cart.php">
                                    <input type="hidden" name="product_id" value="<?= htmlspecialchars($item['product_id']) ?>">
                                    <input type="number" name="quantity" value="<?= htmlspecialchars($item['quantity']) ?>" min="1">
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
