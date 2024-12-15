<?php
session_start();

// Sprawdzamy, czy produkt_id jest przesłane w żądaniu
if (isset($_POST['product_id'])) {
    $productId = intval($_POST['product_id']);

    // Usuwamy produkt z koszyka
    foreach ($_SESSION['cart'] as $key => $cart_item) {
        if ($cart_item['product_id'] == $productId) {
            unset($_SESSION['cart'][$key]); // Usuwamy produkt
            break;
        }
    }

    // Przekształcamy indeksy tablicy
    $_SESSION['cart'] = array_values($_SESSION['cart']);

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'No product ID received']);
}
?>
