<?php

include 'db.php';

$sql = "
    SELECT products.*, product_categories.category_name 
    FROM products 
    JOIN product_categories 
    ON products.category_id = product_categories.category_id 
    WHERE product_categories.category_name = :category";
$stmt = $pdo->prepare($sql);

$category = 'Computers'; 
$stmt->bindParam(':category', $category, PDO::PARAM_STR);

$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Computers - Electronic Store</title>
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
</head>
<body>

<header>
    <div class="container">
        <h1>Electronic Store</h1>
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
    <section id="computers" class="products">
        <h2>Our Computers</h2>
        <div class="product-list">
            <?php if (empty($products)): ?>
                <p class="no-products">Sorry, there are no computers available in this category right now. Please check back later!</p>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                    <div class="product-item">
                        <img src="<?= htmlspecialchars($product['image_url'] ?? 'default-image.jpg', ENT_QUOTES, 'UTF-8'); ?>" alt="<?= htmlspecialchars($product['NAME'], ENT_QUOTES, 'UTF-8'); ?>">
                        <h3><?= htmlspecialchars($product['NAME'], ENT_QUOTES, 'UTF-8'); ?></h3>

                        <button id="button-<?= $product['product_id']; ?>" onclick="toggleDetails(<?= $product['product_id']; ?>)">View Details</button>

                        <div id="details-<?= $product['product_id']; ?>" style="display: none;">
                            <p><?= htmlspecialchars($product['description'], ENT_QUOTES, 'UTF-8'); ?></p>
                            <p>$<?= number_format($product['price'], 2); ?></p>
                            
                            <button onclick="window.location.href='product-details.php?id=<?= urlencode($product['product_id']); ?>'">View Full Product</button>
                            <br>
                            <button onclick="addToCart(<?= $product['product_id']; ?>)">Add to Cart</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>
</main>

</body>
</html>
