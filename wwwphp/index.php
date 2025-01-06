<?php  
include 'db.php'; 
session_start();

// Sprawdzamy, czy użytkownik jest zalogowany jako administrator
$is_admin_logged_in = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));  
}

if (isset($_SESSION['user_id'])) {
    echo '<div class="user-info">';
    echo '<img src="user_icon.png" >'; // Dodaj ikonę
    echo '<span> Hello, user ID: ' . $_SESSION['user_id'] . '</span>';
    echo '</div>';
}

// Lista produktów polecanych
$featured_products = [
    [
        'title' => 'Premium Laptops',
        'image' => 'Images/laptop.avif',
        'url' => 'laptops.php',
    ],
    [
        'title' => 'Smartphones',
        'image' => 'Images/smartphone.avif',
        'url' => 'smartphones.php',
    ],
    [
        'title' => 'High Performance Computers',
        'image' => 'Images/computer.avif',
        'url' => 'computers.php',
    ],
    [
        'title' => 'Tech Accessories',
        'image' => 'Images/accessories.avif',
        'url' => 'accessories.php',
    ]
];

shuffle($featured_products);
$featured_to_display = array_slice($featured_products, 0, 2);

// Obsługuje logowanie
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'], $_POST['password'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = "Please fill in both username and password.";
    } else {
        try {
            // Zapytanie do bazy danych w celu sprawdzenia użytkownika o roli admin
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username AND role = 'admin'");
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_username'] = $username;
                header('Location: admin_dashboard.php');
                exit;
            } else {
                $error = "Invalid username or password.";
            }
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            $error = "An error occurred while trying to log you in. Please try again later.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-commerce Store for Computers, Laptops, Smartphones and Electronic Accessories</title>
    <link rel="stylesheet" href="style.css" type="text/css" />
    <script>
        function searchProduct() {
            const query = document.getElementById("searchInput").value.trim().toLowerCase();

            if (!query) {
                alert("Please enter a query.");
                return;
            }

            // Jeśli użytkownik podał kategorię, np. 'laptops', 'smartphones', 'computers', 'accessories'
            const validCategories = ['laptops', 'computers', 'smartphones', 'accessories'];
            let category = null;

            validCategories.forEach(cat => {
                if (query.includes(cat)) {
                    category = cat;
                }
            });

            // Jeśli podano kategorię
            if (category) {
                window.location.href = `search.php?searchQuery=${encodeURIComponent(query)}&category=${category}&csrf_token=<?php echo $_SESSION['csrf_token']; ?>`;
            } else {
                // Jeśli nie podano kategorii, traktujemy wyszukiwanie jako produkt
                window.location.href = `search.php?searchQuery=${encodeURIComponent(query)}&csrf_token=<?php echo $_SESSION['csrf_token']; ?>`;
            }
        }

        function handleEnterKey(event) {
            if (event.key === "Enter") {
                searchProduct();
            }
        }
    </script>
</head>
<body>
    
    <header>
        <div class="container">
            <h1>Electronic Store</h1>
            <nav>
                <ul>
                    <li><a href="#home">Home</a></li>
                    <li><a href="#products">Products</a></li>
                    <li><a href="about.php">About</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    <?php if ($is_admin_logged_in): ?>
                        <li><a href="admin_dashboard.php">Admin Panel</a></li>
					<?php elseif (isset($_SESSION['user_id'])): ?>
						<li><a href="logout.php">Logout</a></li>
                    <?php else: ?>
                        <li><a href="login.php">Login</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
            <div class="search-bar">
                <input type="text" id="searchInput" placeholder="Search products or categories..." onkeydown="handleEnterKey(event)">
                <button onclick="searchProduct()">Search</button>
            </div>
            <div class="cart">
                <a href="shopping_cart.php">
                    <img src="Images/cart-icon.png" alt="Shopping Cart" style="width: 30px; height: 30px;">
                    <span id="cart-count">0</span>
                </a>
            </div>
        </div>
    </header>

    <main>
        <section id="home" class="banner">
            <h2>Welcome to Modern Electronic Store</h2>
            <p>Find the best laptops, phones, accessories and more!</p>
            <a href="#products" class="button">Browse products</a>
        </section>

        <section id="featured" class="featured">
            <h2>Featured Products</h2>
            <div class="featured-list">
                <?php foreach ($featured_to_display as $product): ?>
                    <div class="featured-item">
                        <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['title']; ?>">
                        <h3><?php echo $product['title']; ?></h3>
                        <button onclick="window.location.href='<?php echo $product['url']; ?>'">Go to selection of <?php echo strtolower($product['title']); ?></button>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section id="products" class="products">
            <h2>Our Products</h2>
            <div class="product-list">
                <div class="product-item">
                    <img src="Images/laptop.avif" alt="Laptop">
                    <h3>Laptops</h3>
                    <p>The best models on the market.</p>
                    <button onclick="window.location.href='laptops.php'">Go to selection of laptops</button>
                </div>
                <div class="product-item">
                    <img src="Images/computer.avif" alt="Computer">
                    <h3>Computers</h3>
                    <p>Efficient units for professionals.</p>
                    <button onclick="window.location.href='computers.php'">Go to selection of computers</button>
                </div>
                <div class="product-item">
                    <img src="Images/smartphone.avif" alt="Smartphone">
                    <h3>Smartphones</h3>
                    <p>Modern smartphones for every budget.</p>
                    <button onclick="window.location.href='smartphones.php'">Go to selection of smartphones</button>
                </div>
                <div class="product-item">
                    <img src="Images/accessories.avif" alt="Accessories">
                    <h3>Accessories</h3>
                    <p>Complement your devices with additional accessories.</p>
                    <button onclick="window.location.href='accessories.php'">Go to selection of accessories</button>
                </div>
            </div>
        </section>

    </main>

    <footer>
        <div class="container">
            <p>&copy; 2024 Electronic Store. All rights reserved.</p>
            <p>Contact: info@store.com</p>
        </div>
    </footer>
</body>
</html>
