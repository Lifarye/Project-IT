<?php

$db_server = "localhost";
$db_user = "root";
$db_pass = ""; 
$db_name = "web page";
$db_port = 3306;

try {
    $dsn = "mysql:host=$db_server;dbname=$db_name;port=$db_port;charset=utf8mb4";
    $pdo = new PDO($dsn, $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Database connected successfully.";
} catch (PDOException $e) {
    error_log("Connection failed: " . $e->getMessage()); 
    die("Connection failed. Please try again later."); 
}

function getProductByCategory($category) {
    global $pdo;

    $category = trim($category); 

    $validCategories = ['laptops', 'computers', 'smartphones', 'accessories'];
    
    if (!in_array($category, $validCategories)) {
        throw new InvalidArgumentException("Invalid category.");
    }

    $stmt = $pdo->prepare("SELECT * FROM products WHERE category = :category");
    $stmt->bindParam(':category', $category, PDO::PARAM_STR);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
