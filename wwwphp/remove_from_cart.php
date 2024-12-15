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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['detail_id'])) {
    $detail_id = intval($_POST['detail_id']); 

    try {
        $delete_query = "DELETE FROM order_details WHERE detail_id = :detail_id";
        $stmt = $pdo->prepare($delete_query);
        $stmt->bindParam(':detail_id', $detail_id, PDO::PARAM_INT);
        $stmt->execute();

        header('Location: cart.php'); 
        exit();
    } catch (PDOException $e) {
        error_log("Deletion failed: " . $e->getMessage());
        die("An error occurred. Please try again later."); 
    }
}

$pdo = null;

?>
