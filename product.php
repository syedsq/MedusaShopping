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
    <title>Product Page</title>
    
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
        }

        .product-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            padding: 20px;
        }

        .product-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            width: 250px;
            margin: 10px;
            text-align: center;
            background: rgba(255, 255, 255, 0.8);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }

        .product-card:hover {
            transform: scale(1.05);
        }

        .product-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .product-info {
            padding: 10px;
        }

        .price, .quantity {
            font-size: 1.2em;
            color: #333;
            margin: 8px 0;
        }

        .product-card .button {
            background-color: #33b249;
            color: white;
            border: none;
            padding: 8px 16px;
            cursor: pointer;
            border-radius: 4px;
            margin-top: 8px;
            text-transform: uppercase;
            font-weight: bold;
        }

        .product-card .button:hover {
            background-color: #28a745;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <ul>
            <li class="logo">
                <a href="index.php">
                    <img src="icon-image/logo.png" alt="Logo"> Medusa Gym
                </a>
            </li>
            <div class="nav-items">
                <li><a href="product.php">Browse</a></li>
                <?php if ($is_logged_in): ?>
                    <li><span>Welcome, <?php echo $_SESSION['username']; ?>!</span></li>
                    <li><a href="user-profile.php">My Profile</a></li>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php">Register</a></li>
                <?php endif; ?>
                <li class="cart">
                    <a href="cart.php">
                        <img src="icon-image/cart.png" alt="Cart">
                        <?php if ($cart_item_count > 0): ?>
                            <div class="cart-count"><?php echo $cart_item_count; ?></div>
                        <?php endif; ?>
                    </a>
                </li>
            </div>
        </ul>
    </nav>

    <!-- Sidebar for Sorting and Filters -->
    <div class="sidebar">
        <div class="search">
            <form method="GET" action="product.php">
                <input type="text" name="search_query" placeholder="Search products" value="<?php echo htmlspecialchars($searchQuery); ?>">
                <button type="submit">Search</button>
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
                <label>
                    <input type="checkbox" name="show_available" value="1" <?php echo isset($_GET['show_available']) && $_GET['show_available'] == '1' ? 'checked' : ''; ?> onchange="this.form.submit()"> 
                    Show Available Only
                </label>
            </div>
        </form>
    </div>

    <!-- Product List -->
    <div class="product-container">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="product-card">
                <img src="Product-images/<?php echo $row['image']; ?>" alt="<?php echo $row['name']; ?>" class="product-image">
                <div class="product-info">
                    <h2><?php echo $row['name']; ?></h2>
                    <p class="product-description"><?php echo $row['description']; ?></p>
                    <div class="price">$<?php echo number_format($row['price'], 2); ?></div>
                    <p class="quantity">
                        <?php echo $row['quantity'] > 0 ? "In Stock: " . $row['quantity'] : "Out of Stock"; ?>
                    </p>
                    <form action="add_to_cart.php" method="post">
                        <input type="hidden" name="product_id" value="<?php echo (int)$row['id']; ?>">
                        <input type="number" name="quantity" value="1" min="1" max="<?php echo (int)$row['quantity']; ?>" <?php echo $row['quantity'] > 0 ? '' : 'disabled'; ?>>
                        <button class="button" type="submit" <?php echo $row['quantity'] > 0 ? '' : 'disabled'; ?>>Add to Cart</button>
                    </form>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

    <!-- Close database connection -->
    <?php $conn->close(); ?>
</body>
</html>
