<?php
require_once 'db.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo "No products in the cart. Make sure products are added to the cart.";
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user details
$stmt = $pdo->prepare("SELECT * FROM customers WHERE customer_id = :user_id");
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "User not found.";
    exit;
}

// Validate session cart format
foreach ($_SESSION['cart'] as $key => $value) {
    if (!is_array($value) || !isset($value['product_id'], $value['quantity'])) {
        echo "Error in cart data. Make sure the products were added correctly.";
        exit;
    }
}

// Fetch products from session cart
$cartProducts = [];
$total_price = 0;

foreach ($_SESSION['cart'] as $item) {
    if (empty($item['product_id']) || empty($item['quantity'])) {
        error_log("Invalid product data: " . print_r($item, true));
        continue;
    }
    $product_id = $item['product_id'];
    $quantity = $item['quantity'];

    $stmt = $pdo->prepare("SELECT name, price, image_url FROM products WHERE product_id = :product_id");
    $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        $product['quantity'] = $quantity;
        $product['total_price'] = $product['price'] * $quantity;
        $product['product_id'] = $product_id; // Dodanie product_id
        $cartProducts[] = $product;
        $total_price += $product['total_price'];
    } else {
        error_log("Product not found for ID: $product_id");
        continue;
    }
}

if (empty($cartProducts)) {
    echo "Brak produktów w koszyku.";
    exit;
}

// Insert order into orders table
$order_stmt = $pdo->prepare("INSERT INTO orders (customer_id, STATUS, total_price) VALUES (:customer_id, 'Placed', :total_price)");
$order_stmt->bindParam(':customer_id', $user_id, PDO::PARAM_INT);
$order_stmt->bindParam(':total_price', $total_price, PDO::PARAM_STR);
$order_stmt->execute();
$order_id = $pdo->lastInsertId();

// Insert order details into order_details table
$order_detail_stmt = $pdo->prepare("INSERT INTO order_details (order_id, product_id, quantity, unit_price) VALUES (:order_id, :product_id, :quantity, :unit_price)");

foreach ($cartProducts as $item) {
    if (empty($item['product_id'])) {
        error_log("Invalid product_id: " . print_r($item, true));
        continue;
    }

    $unit_price = $item['price']; // Cena jednostkowa produktu

    try {
        // Debugowanie danych przed wstawieniem
        error_log("Inserting order details: Order ID: $order_id, Product ID: {$item['product_id']}, Quantity: {$item['quantity']}, Unit Price: $unit_price");

        $order_detail_stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
        $order_detail_stmt->bindParam(':product_id', $item['product_id'], PDO::PARAM_INT);
        $order_detail_stmt->bindParam(':quantity', $item['quantity'], PDO::PARAM_INT);
        $order_detail_stmt->bindParam(':unit_price', $unit_price, PDO::PARAM_STR);
        $order_detail_stmt->execute();
    } catch (PDOException $e) {
        error_log("Error inserting into order_details: " . $e->getMessage());
    }
}

// Clear cart after order confirmation
unset($_SESSION['cart']);

// Display summary
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Summary</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            color: #333;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .main-header {
            background-color: #222;
            color: white;
            padding: 20px 0;
            text-align: center;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .user-info, .order-summary, .confirmation {
            background: white;
            margin: 20px 0;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        h2 {
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }
        .summary-table th, .summary-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        .summary-table th {
            background-color: #333;
            color: white;
        }
        .product-image {
            max-width: 50px;
            max-height: 50px;
        }
        .total-price {
            font-size: 20px;
            text-align: right;
            margin-top: 20px;
            font-weight: bold;
        }
        .main-footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 10px 0;
            margin-top: auto;
        }
    </style>
</head>
<body>
    <header class="main-header">
        <div class="container">
            <h1>Order Summary</h1>
            <nav>
                <a href="index.php" style="color: white; text-decoration: none; margin-top: 10px; display: inline-block; font-weight: bold;">Return to Home Page</a>
            </nav>
        </div>
    </header>
    <main class="main-content">
        <section class="user-info">
            <div class="container">
                <h2>Customer Details</h2>
                <p><strong>Name:</strong> <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></p>
                <p><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone']); ?></p>
                <p><strong>Address:</strong> <?php echo htmlspecialchars($user['address']); ?></p>
            </div>
        </section>

        <section class="order-summary">
            <div class="container">
                <h2>Order Details</h2>
                <table class="summary-table">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cartProducts as $item): ?>
                        <tr>
                            <td><img class="product-image" src="<?php echo htmlspecialchars($item['image_url'] ?? 'default.jpg'); ?>" alt="Produkt"></td>
                            <td><?php echo htmlspecialchars($item['name'] ?? "N/A"); ?></td>
                            <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                            <td><?php echo number_format($item['price'], 2, ',', ' '); ?> zł</td>
                            <td><?php echo number_format($item['total_price'], 2, ',', ' '); ?> zł</td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <p class="total-price"><strong>Total Amount: <?php echo number_format($total_price, 2, ',', ' '); ?> zł</strong></p>
            </div>
        </section>

        <section class="confirmation">
            <div class="container">
                <p>Thank you for placing your order! You will receive a confirmation email shortly.</p>
            </div>
        </section>
    </main>
    <footer class="main-footer">
        <div class="container">
            <p>&copy; 2024 Electronic Store. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
