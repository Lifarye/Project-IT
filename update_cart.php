<?php
session_start();

// Sprawdzamy, czy produkt_id i quantity są przesłane w żądaniu
if (isset($_POST['product_id']) && isset($_POST['quantity'])) {
    $productId = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);

    // Walidacja: czy ilość jest większa od zera
    if ($quantity <= 0) {
        echo json_encode(['success' => false, 'message' => 'Quantity must be greater than zero.']);
        exit;
    }

    // Flaga do sprawdzenia, czy produkt już jest w koszyku
    $productFound = false;

    // Jeśli koszyk istnieje w sesji
    if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as &$cart_item) {
            if ($cart_item['product_id'] === $productId) {
                $cart_item['quantity'] = $quantity; // Uaktualniamy ilość
                $productFound = true;
                break;
            }
        }

        // Jeśli produkt nie został znaleziony w koszyku, dodajemy go
        if (!$productFound) {
            $_SESSION['cart'][] = [
                'product_id' => $productId,
                'quantity' => $quantity
            ];
        }
    } else {
        // Jeśli koszyk jeszcze nie istnieje, tworzymy nowy i dodajemy produkt
        $_SESSION['cart'] = [
            [
                'product_id' => $productId,
                'quantity' => $quantity
            ]
        ];
    }

    // Przekierowanie z powrotem na stronę shopping_cart.php
    header('Location: shopping_cart.php');
    exit;
} else {
    echo json_encode(['success' => false, 'message' => 'No product ID or quantity received.']);
}
?>
