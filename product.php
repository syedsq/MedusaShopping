
<?php
session_start();
include 'config.php';  // Include database connection

// Check if the user is logged in
$is_logged_in = isset($_SESSION['user_id']);

// Count the total number of items in the cart
$cart_item_count = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cart_item_count += $item['quantity'];  // Sum up quantities of all items
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medusa Gym</title>
    
    <!-- Include DataTables CSS and other styles -->
    <link rel="stylesheet" type="text/css" href="CSS/styles.css">
    
    <style>
        <?php include 'CSS/styles.css'; ?>
        body {
            background-image: url('background/gymbackground.jpeg');
        }
        .product-container {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            justify-content: center;
            padding: 20px;
        }

    
        .product-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            width: 250px;
            text-align: center;
            background: transparent;
            backdrop-filter: blur(20px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }

        .product-card:hover {
            transform: scale(1.05);
        }

        .product-image {
            width: 250px;
            height: 250px;
        }

        .product-info {
            padding: 16px;
        }

        .price {
            font-size: 1.2em;
            color: #555;
            margin: 8px 0;
        }

        .product-card button {
            background-color: white;
            color: white;
            border: none;
            padding: 8px 16px;
            cursor: pointer;
            border-radius: 4px;
            margin-top: 8px;
        }

        .product-card button:hover {
            background-color: black;
        }
    
    </style>

    <!-- Include jQuery and DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>

    <!-- Initialize DataTables -->
    <script>
    $(document).ready(function() {
        $('#itemsTable').DataTable();
    });
    </script>
    
    
</head>
<body >
    
    <!-- Navigation Bar -->
        <!-- Navigation Bar -->
        <nav class="navbar">
        <ul>
            <!-- Logo on the left -->
            <li class="logo">
                <a class="main_page" href="index.php">
                    <img class="image" src="icon-image/logo.png" alt="Logo">Medusa Gym</a>
            </li>
            <!-- Links on the right -->
            <div class="nav-items">
                <li><a class="NavButton" href="product.php">Browse</a></li>
                <?php if ($is_logged_in): ?>
                    <li><span class="login_welcome">Welcome, <?php echo $_SESSION['username']; ?>!</span></li>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a class="NavLogin" href="login.php"><img class="login-icon" src="icon-image/login.png" alt="Login Icon" style= "vertical-align: middle">Login</a></li>
                    <li><a class="NavRegister" href="register.php">Register</a></li>
                <?php endif; ?>
                <li class="cart">
                    <a href="cart.php">
                        <img src="icon-image/cart.png" alt="Cart">
                        <?php if ($cart_item_count > 0): ?>
                            <div class="cart-count"><?php echo $cart_item_count; ?></div>
                        <?php endif; ?>
                    </a>
                    <div class="cart-preview" id="cart-preview">
                    <h3>Cart Preview</h3>
                    <ul id="cart-items">
                        <!-- Dynamically generated cart items will go here -->
                    </ul>
                    <?php if ($cart_item_count > 0): ?>
                    <a href="cart.php" class="view-cart">View Cart</a>
                    <?php else: ?>
                    <a href="product.php" class="view-cart">Browse our product</a>
                    <?php endif; ?>
                    </div>
                </li>
                
            </div>
        </ul>
    </nav>

    <!-- Product Table -->
    <h1>Welcome to Our Online Store</h1>



        <div class="product-container">
            <?php
            // Fetch products from the database
            $sql = "SELECT id, name, description, price, image FROM products";
            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0):
                while ($row = $result->fetch_assoc()):
            ?>
            <div class="product-card">
                <img class="product-image" src="Product-images/<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                <div class="product-info">    
                <h2><?php echo htmlspecialchars($row['name']); ?></h2>
                <p><?php echo htmlspecialchars($row['description']); ?></p>
                <div class="price"><?php echo number_format($row['price'], 2); ?></div>
                
                <form action="add_to_cart.php" method="post">
                    <input type="hidden" name="product_id" value="<?php echo (int)$row['id']; ?>">
                    <input type="number" name="quantity" value="1" min="1" max="10">
                    <input type="submit" value="Add to Cart">
                </form>
                </div>
                </div>
            <?php endwhile; else: ?>
                <p>No products found.</p>
            <?php endif; ?>
        
        </div>

    <!-- Close the database connection -->
    <?php $conn->close(); ?>
</body>
</html>
