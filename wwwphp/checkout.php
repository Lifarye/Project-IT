<?php
session_start();

if (!isset($_SESSION['cart']) || count($_SESSION['cart']) == 0) {
    header('Location: shopping_cart.php'); // Jeśli koszyk jest pusty, wróć do koszyka
    exit();
}

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_id = 1; // Zmienna sesji użytkownika, np. $_SESSION['customer_id']
    $address = $_POST['address'];
    $contact_info = $_POST['contact_info'];

    // Walidacja danych
    if (empty($address) || empty($contact_info)) {
        $error_message = "Please fill in all data.";
    } else {
        try {
            // 1. Utwórz nowe zamówienie
            $order_query = "INSERT INTO orders (customer_id, address, contact_info, status) 
                            VALUES (:customer_id, :address, :contact_info, 'In Progress')";
            $stmt = $pdo->prepare($order_query);
            $stmt->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);
            $stmt->bindParam(':address', $address, PDO::PARAM_STR);
            $stmt->bindParam(':contact_info', $contact_info, PDO::PARAM_STR);
            $stmt->execute();

            // 2. Pobierz ID ostatniego zamówienia
            $order_id = $pdo->lastInsertId();

            // 3. Dodaj produkty do zamówienia
            foreach ($_SESSION['cart'] as $cart_item) {
                $product_id = $cart_item['product_id'];
                $quantity = $cart_item['quantity'];

                // Pobierz cenę produktu
                $product_query = "SELECT unit_price FROM products WHERE product_id = :product_id";
                $stmt = $pdo->prepare($product_query);
                $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
                $stmt->execute();
                $product = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($product) {
                    $unit_price = $product['unit_price'];

                    // Dodaj szczegóły zamówienia do bazy
                    $order_details_query = "INSERT INTO order_details (order_id, product_id, quantity, unit_price) 
                                            VALUES (:order_id, :product_id, :quantity, :unit_price)";
                    $stmt = $pdo->prepare($order_details_query);
                    $stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
                    $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
                    $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
                    $stmt->bindParam(':unit_price', $unit_price, PDO::PARAM_STR);
                    $stmt->execute();
                }
            }

            // 4. Wyczyszczenie koszyka po złożeniu zamówienia
            unset($_SESSION['cart']);

            // Przekierowanie do strony potwierdzenia zamówienia
            header('Location: order_confirmation.php?order_id=' . $order_id);
            exit();
        } catch (PDOException $e) {
            error_log("Error while processing order: " . $e->getMessage());
            $error_message = "An error occurred while processing your order. Please try again later.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="style.css" type="text/css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Checkout</h1>
        </div>
    </header>

    <main>
        <div class="container">
            <h2>Finalize your order</h2>

            <?php if (isset($error_message)): ?>
                <p style="color: red;"><?php echo htmlspecialchars($error_message); ?></p>
            <?php endif; ?>

            <form action="checkout.php" method="POST">
                <div>
                    <label for="address">Delivery Address:</label>
                    <input type="text" id="address" name="address" required>
                </div>
                <div>
                    <label for="contact_info">Contact Information:</label>
                    <input type="text" id="contact_info" name="contact_info" required>
                </div>
                <div>
                    <button type="submit">Place Order</button>
                </div>
            </form>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2024 Electronic Store. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
