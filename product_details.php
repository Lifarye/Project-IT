<?php
include 'db.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $productId = (int)$_GET['id']; 
    
    $sql = "SELECT * FROM products WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    
    $stmt->bindParam(':id', $productId, PDO::PARAM_INT);
    
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        echo "Product not found.";
        exit;
    }
} else {
    echo "Invalid product ID.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details - <?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?> | Electronic Store</title>
    <link rel="stylesheet" href="style.css" type="text/css" />
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
    <section id="product-details" class="product">
        <div class="product-item">
            <img src="<?= htmlspecialchars($product['image_url'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?>">
            <h2><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?></h2>
            <p><?= htmlspecialchars($product['description'], ENT_QUOTES, 'UTF-8'); ?></p>
            <p><strong>Price: </strong>$<?= number_format($product['price'], 2); ?></p>
        </div>
    </section>
</main>

</body>
</html>
