<?php
$host = '127.0.0.1';
$db = 'internet_shop';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['detail_id'])) {
    $detail_id = intval($_POST['detail_id']);
    $delete_query = "DELETE FROM order_details WHERE detail_id = $detail_id";
    $conn->query($delete_query);
}

$conn->close();
header('Location: cart.php'); // Powrót do koszyka
exit();
?>
