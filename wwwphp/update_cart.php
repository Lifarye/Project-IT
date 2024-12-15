<?php
session_start();

// Sprawdzamy, czy produkt_id i quantity są przesłane w żądaniu
if (isset($_POST['product_id']) && isset($_POST['quantity'])) {
    $productId = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);

    // Uaktualniamy ilość produktu w koszyku
    foreach ($_SESSION['cart'] as &$cart_item) {
        if ($cart_item['product_id'] == $productId) {
            $cart_item['quantity'] = $quantity; // Zmieniamy ilość
            break;
        }
    }

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'No product ID or quantity received']);
}
?>
