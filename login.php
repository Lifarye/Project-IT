<?php
session_start();
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Debugowanie: wypisz zawartość $_POST
    //var_dump($_POST); //debugowanie przez wyświetlanie zawartości zmiennej
    
    // Sprawdzenie, czy pola istnieją
    if (isset($_POST['email']) && isset($_POST['password'])) {
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);

        try {// Przygotowanie i wykonanie zapytania SQL
            $stmt = $pdo->prepare("SELECT * FROM customers WHERE email = :email");
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);// Sprawdzanie poprawności hasła
			
            if ($user && password_verify($password, $user['PASSWORD'])) {
                $_SESSION['user_id'] = $user['customer_id'];
                $_SESSION['user_role'] = $user['role']; // Jeśli kolumna role istnieje
                header("Location: index.php");
                exit;
            } else {
                $error = "Incorrect email or password.";
            }
        } catch (Exception $e) {
            $error = "Server error: " . $e->getMessage();
        }
    } else {
        $error = "Please fill in all fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 300px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        label {
            font-weight: bold;
            display: block;
            margin: 10px 0 5px;
        }
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        .error {
            color: red;
            text-align: center;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Admin Login</h2>
    <?php if (isset($error)): ?>
        <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="POST">
		<label for="email">Email</label>
		<input type="text" id="email" name="email" required>

		<label for="password">Password</label>
		<input type="password" id="password" name="password" required>

		<button type="submit">Login</button>
	</form>
</div>

</body>
</html>
