<?php
session_start();


if (isset($_POST['product_id'])) {
    $productId = intval($_POST['product_id']);

    if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $key => $cart_item) {
            if ($cart_item['product_id'] === $productId) {
                unset($_SESSION['cart'][$key]); // Delete product
                break;
            }
        }
        $_SESSION['cart'] = array_values($_SESSION['cart']);
    }
    header('Location: shopping_cart.php');
    exit;
} else {
    echo json_encode(['success' => false, 'message' => 'No product ID received']);
}
?>
