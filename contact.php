<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Electronic Store</title>
    <link rel="stylesheet" href="style.css" type="text/css" />
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
            background-color: #f9f9f9; /* Match the background of the contact section */
        }

        main {
            flex: 1;
        }

        footer {
            background-color: #222;
            color: #fff;
            text-align: center;
            padding: 20px 0;
        }

        .logo {
            display: block;
            margin: 20px auto;
            max-width: 400px;
        }
    </style>
</head>
<body>

    <header>
        <div class="container">
            <h1>Electronic Store</h1>
            <nav>
                <ul>
                    <li><a href="index.php#home">Home</a></li>
                    <li><a href="index.php#products">Products</a></li>
                    <li><a href="about.php">About</a></li>
                    <li><a href="contact.php">Contact</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <section class="contact">
            <h2>Contact Us</h2>
            <p>If you have any questions,<br>feel free to reach out to our team:<br><br></p>
            <div class="contact-list">
                <div class="contact-item">
                    <h3>Robert Cenar<br></h3>
                    <p>Co-Founder & Tech Specialist</p>
                    <p>Email: robert.cenar@store.com</p>
                    <p>Phone: +48 567 234 890<br><br></p>
                </div>
                <div class="contact-item">
                    <h3>Szymon Kuc<br></h3>
                    <p>Co-Founder & Customer Support Manager</p>
                    <p>Email: szymon.kuc@store.com</p>
                    <p>Phone: +48 567 234 891</p>
                </div>
            </div>
            <img src="images/logo.png" alt="Electronic Store Logo" class="logo">
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
