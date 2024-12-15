<?php
session_start();

// Sprawdzamy, czy produkt został przesłany w żądaniu
if (isset($_POST['product_id']) && isset($_POST['quantity'])) {
    $productId = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);

    // Tworzymy tablicę, jeśli nie istnieje, aby przechować produkty w koszyku
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Sprawdzamy, czy produkt już jest w koszyku
    $found = false;
    foreach ($_SESSION['cart'] as &$cart_item) {
        if ($cart_item['product_id'] == $productId) {
            $cart_item['quantity'] += $quantity; // Zwiększ ilość produktu
            $found = true;
            break;
        }
    }

    // Jeśli produkt nie jest w koszyku, dodajemy go
    if (!$found) {
        $_SESSION['cart'][] = ['product_id' => $productId, 'quantity' => $quantity];
    }

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'No product ID or quantity received']);
}
?>
