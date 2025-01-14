<?php

include 'db.php';

session_start();

if ($_GET['csrf_token'] !== $_SESSION['csrf_token']) {
    die('Invalid CSRF token');
}

$searchQuery = isset($_GET['searchQuery']) ? $_GET['searchQuery'] : '';
$searchQuery = strtolower($searchQuery);

$sql = "SELECT p.product_id, p.NAME, p.description, p.price, p.image_url, c.category_name 
        FROM products p 
        JOIN product_categories c ON p.category_id = c.category_id 
        WHERE LOWER(p.NAME) LIKE :searchQuery OR LOWER(c.category_name) LIKE :searchQuery";

$stmt = $pdo->prepare($sql);
try {
    $stmt->execute(['searchQuery' => "%$searchQuery%"]);
} catch (PDOException $e) {
    error_log($e->getMessage());
    echo '<p>An error occurred while fetching the products. Please try again later.</p>';
    exit;
}
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results</title>
    <link rel="stylesheet" href="style.css" type="text/css" />
    <script>
        function toggleDetails(productId) {
            var details = document.getElementById('details-' + productId);
            var button = document.getElementById('button-' + productId);
            if (details.style.display === "none") {
                details.style.display = "block";
                button.textContent = "Hide Details";
            } else {
                details.style.display = "none";
                button.textContent = "View Details";
            }
        }

        function addToCart(productId) {
            alert('Product ' + productId + ' added to cart!');
        }
    </script>
    <style>
        .product-list input[type="number"] {
            width: 60px;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1>Search Results for <?php echo ucfirst(htmlspecialchars($searchQuery, ENT_QUOTES, 'UTF-8')); ?></h1>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="laptops.php">Laptops</a></li>
                    <li><a href="computers.php">Computers</a></li>
                    <li><a href="smartphones.php">Smartphones</a></li>
                    <li><a href="accessories.php">Accessories</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <section id="search-results" class="products">
            <h2>Products Found</h2>
            <div class="product-list">
                <?php if (empty($products)): ?>
                    <p class="no-products">No products found matching your search.</p>
                <?php else: ?>
                    <?php foreach ($products as $product): ?>
                        <div class="product-item">
                            <img src="<?= htmlspecialchars($product['image_url'] ?? 'default-image.jpg', ENT_QUOTES, 'UTF-8'); ?>" alt="<?= htmlspecialchars($product['NAME'], ENT_QUOTES, 'UTF-8'); ?>">
                            <h3><?= htmlspecialchars($product['NAME'], ENT_QUOTES, 'UTF-8'); ?></h3>

                            <button id="button-<?= $product['product_id']; ?>" onclick="toggleDetails(<?= $product['product_id']; ?>)">View Details</button>

                            <div id="details-<?= $product['product_id']; ?>" style="display: none;">
                                <p><?= htmlspecialchars($product['description'], ENT_QUOTES, 'UTF-8'); ?></p>
                                <p><strong>Price: $<?= number_format($product['price'], 2); ?></strong></p>
                                <br>
                                <form action="add_to_cart.php" method="post">
                                    <input type="hidden" name="product_id" value="<?= $product['product_id']; ?>">
                                    <input type="hidden" name="customer_id" value="<?= htmlspecialchars($_SESSION['customer_id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                    <input type="number" name="quantity" value="1" min="1">
                                    <button type="submit" onclick="addToCart(<?= $product['product_id']; ?>)">Add to Cart</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2024 Electronic Store. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
