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

// Sorting and Filter Logic
$sortQuery = '';
$availableFilter = isset($_GET['show_available']) && $_GET['show_available'] == '1' ? 'AND quantity > 0' : '';

// Handle sorting
if (isset($_GET['sort_by'])) {
    switch ($_GET['sort_by']) {
        case 'price_asc':
            $sortQuery = 'ORDER BY price ASC';
            break;
        case 'price_desc':
            $sortQuery = 'ORDER BY price DESC';
            break;
        case 'name_asc':
            $sortQuery = 'ORDER BY name ASC';
            break;
        case 'name_desc':
            $sortQuery = 'ORDER BY name DESC';
            break;
        default:
            $sortQuery = '';
            break;
    }
}

// Modify the SQL query to include both search and sort options
$searchQuery = isset($_GET['search_query']) ? $_GET['search_query'] : '';
$searchCondition = $searchQuery ? "AND (name LIKE '%$searchQuery%' OR description LIKE '%$searchQuery%')" : '';

$sql = "SELECT id, name, description, price, image, quantity FROM products WHERE 1=1 $searchCondition $availableFilter $sortQuery";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product page</title>
    
    <!-- Include DataTables CSS and other styles -->
    <link rel="stylesheet" type="text/css" href="CSS/styles.css">
    
    <style>
        <?php include 'CSS/styles.css'; ?>
        <?php include 'CSS/product.css'; ?>
        body {
            background-repeat: no-repeat;
            background-size: cover;
            background-attachment: fixed;
            background-image: url('background/background4.jpeg');
            display: flex;
        }
    </style>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
    $(document).ready(function() {
        $('#itemstable').DataTable();
    });
    </script>

    <!-- Navigation Bar -->
    <nav class="navbar">
        <ul>
            <li class="logo">
                <a class="main_page" href="index.php">
                    <img class="image" src="icon-image/logo.png" alt="Logo">Medusa Gym</a>
            </li>
            <li class="toggle-button">
                <a href="#">
                    <img class="image" src="icon-image/toggle-icon.png" alt="toggle" style="vertical-align: middle">
                </a>
            </li>
            <div class="nav-items">
                <li><a class="NavButton" href="product.php">Browse</a></li>
                <?php if ($is_logged_in): ?>
                    <li><span class="login_welcome">Welcome, <?php echo $_SESSION['username']; ?>!</span></li>
                    <li><a class="NavUserProfile" href="user-profile.php">My profile</a></li>
                    <li><a class="NavLogout" href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a class="NavLogin" href="login.php"><img class="login-icon" src="icon-image/login.png" alt="Login Icon" style="vertical-align: middle">Login</a></li>
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
</head>
<body>
    <div class="body1">
        <div class="sidebar">
            <div class="search">
                <form method="GET" action="product.php">
                    <input type="text" class="searchTerm" name="search_query" value="<?php echo htmlspecialchars($searchQuery); ?>" placeholder="What are you looking for?">
                    <button type="submit" class="searchButton">
                        <img class="search-icon" src="icon-image/search.png" alt="🔍">
                    </button>
                </form>
            </div>

            <form method="GET" action="product.php">
                <input type="hidden" name="search_query" value="<?php echo htmlspecialchars($searchQuery); ?>">
                <div class="filter-group">
                    <label for="sort_by">Sort by:</label>
                    <select name="sort_by" id="sort_by" onchange="this.form.submit()">
                        <option value="">Select</option>
                        <option value="price_asc" <?php echo isset($_GET['sort_by']) && $_GET['sort_by'] == 'price_asc' ? 'selected' : ''; ?>>Price: Low to High</option>
                        <option value="price_desc" <?php echo isset($_GET['sort_by']) && $_GET['sort_by'] == 'price_desc' ? 'selected' : ''; ?>>Price: High to Low</option>
                        <option value="name_asc" <?php echo isset($_GET['sort_by']) && $_GET['sort_by'] == 'name_asc' ? 'selected' : ''; ?>>Name: A to Z</option>
                        <option value="name_desc" <?php echo isset($_GET['sort_by']) && $_GET['sort_by'] == 'name_desc' ? 'selected' : ''; ?>>Name: Z to A</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="show_available">
                        <input type="checkbox" name="show_available" id="show_available" value="1" <?php echo isset($_GET['show_available']) && $_GET['show_available'] == '1' ? 'checked' : ''; ?> onchange="this.form.submit()"> 
                        Show available items only
                    </label>
                </div>
            </form>
        </div>

        <div class="product-container">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="product-card">
                    <img src="Product-images/<?php echo $row['image']; ?>" alt="<?php echo $row['name']; ?>" class="product-image">
                    <div class="product-info">
                        <h2><?php echo $row['name']; ?></h2>
                        <p class="product-description"><?php echo $row['description']; ?></p>
                        <div class="price"><?php echo '$' . number_format($row['price'], 2); ?></div>
                        <p class="product-quantity"> 
                            <?php 
                                if ($row['quantity'] > 0) {
                                    echo "In Stock: " . $row['quantity'];
                                } else {
                                    echo "Out of Stock";
                                }
                            ?>
                        </p>
                        <form action="add_to_cart.php" method="post">
                            <input type="hidden" name="product_id" value="<?php echo (int)$row['id']; ?>">
                            <input type="number" name="quantity" value="1" min="1" max="<?php echo (int)$row['quantity']; ?>" <?php echo $row['quantity'] > 0 ? '' : 'disabled'; ?>>
                            <input class="button" type="submit" value="Add to Cart" <?php echo $row['quantity'] > 0 ? '' : 'disabled'; ?>>
                        </form>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
    <!-- Close the database connection -->
    <?php $conn->close(); ?>
    <script src="JavaScript/cart.js"></script>
    <script src="JavaScript/toggle.js"></script>
</body>
</html>
