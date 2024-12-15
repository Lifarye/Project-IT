<?php  
include 'db.php'; 

session_start();

if ($_GET['csrf_token'] !== $_SESSION['csrf_token']) {
    die('Invalid CSRF token');
}

$searchQuery = isset($_GET['searchQuery']) ? $_GET['searchQuery'] : '';
$searchQuery = strtolower($searchQuery);

$sql = "SELECT p.product_id, p.NAME, p.description, p.price, c.category_name 
        FROM products p 
        JOIN product_categories c ON p.category_id = c.category_id 
        WHERE LOWER(p.NAME) LIKE :searchQuery OR LOWER(c.category_name) LIKE :searchQuery";

$stmt = $pdo->prepare($sql);
$stmt->execute(['searchQuery' => "%$searchQuery%"]);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results</title>
    <link rel="stylesheet" href="style.css" type="text/css" />
</head>
<body>
    <header>
        <div class="container">
            <h1>Search Results for <?php echo ucfirst($searchQuery); ?></h1>
        </div>
    </header>

    <main>
        <section id="search-results" class="search-results">
            <h2>Products Found</h2>
            <div class="product-list">
                <?php if (empty($products)): ?>
                    <p>No products found matching your search.</p>
                <?php else: ?>
                    <?php foreach ($products as $product): ?>
                        <div class="product-item">
                            <h3><?php echo $product['NAME']; ?></h3>
                            <p><?php echo $product['description']; ?></p>
                            <p><strong>Price: $<?php echo number_format($product['price'], 2); ?></strong></p>
                            <button onclick="window.location.href='product.php?id=<?php echo $product['product_id']; ?>'">View Product</button>
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
