<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: login.php');
    exit;
}

// Dodawanie nowego produktu
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $image = $_POST['image']; // W rzeczywistości powinno to być uploadowane
    $category = $_POST['category'];
    $stock = $_POST['stock'];

    $query = "INSERT INTO products (title, description, price, image, category, stock) 
              VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssdsdi", $title, $description, $price, $image, $category, $stock);
    $stmt->execute();
    echo "Product added successfully!";
}

// Wyświetlanie produktów
$query = "SELECT * FROM products";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products</title>
</head>
<body>
    <h1>Manage Products</h1>

    <h2>Add New Product</h2>
    <form method="POST">
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" required><br>

        <label for="description">Description:</label>
        <input type="text" id="description" name="description" required><br>

        <label for="price">Price:</label>
        <input type="number" id="price" name="price" required><br>

        <label for="image">Image URL:</label>
        <input type="text" id="image" name="image" required><br>

        <label for="category">Category:</label>
        <select id="category" name="category">
            <option value="laptops">Laptops</option>
            <option value="computers">Computers</option>
            <option value="smartphones">Smartphones</option>
            <option value="accessories">Accessories</option>
        </select><br>

        <label for="stock">Stock:</label>
        <input type="number" id="stock" name="stock" required><br>

        <button type="submit" name="add_product">Add Product</button>
    </form>

    <h2>Product List</h2>
    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>Description</th>
                <th>Price</th>
                <th>Category</th>
                <th>Stock</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($product = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $product['title']; ?></td>
                    <td><?php echo $product['description']; ?></td>
                    <td><?php echo $product['price']; ?></td>
                    <td><?php echo $product['category']; ?></td>
                    <td><?php echo $product['stock']; ?></td>
                    <td>
                        <a href="edit_product.php?id=<?php echo $product['id']; ?>">Edit</a> |
                        <a href="delete_product.php?id=<?php echo $product['id']; ?>">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
