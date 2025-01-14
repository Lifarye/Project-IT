<?php
require 'db.php'; // Połączenie z bazą danych

try {
    // Pobierz wszystkie rekordy z tabeli customers
    $stmt = $pdo->query("SELECT customer_id, PASSWORD FROM customers");
    $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Iteruj przez każdego użytkownika i aktualizuj hasło
    foreach ($customers as $customer) {
        $customerId = $customer['customer_id'];
        $plainPassword = $customer['PASSWORD'];

        // Hashowanie hasła
        $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

        // Aktualizacja hasła w bazie danych
        $updateStmt = $pdo->prepare("UPDATE customers SET PASSWORD = :hashedPassword WHERE customer_id = :id");
        $updateStmt->execute(['hashedPassword' => $hashedPassword, 'id' => $customerId]);
    }

    echo "Hasła zostały pomyślnie zhashowane!";
} catch (Exception $e) {
    echo "Wystąpił błąd: " . $e->getMessage();
}
?>
