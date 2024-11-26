
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
    <title>Product page</title>
    
    <!-- Include DataTables CSS and other styles -->
    <link rel="stylesheet" type="text/css" href="CSS/styles.css">
    
    <style>
        <?php include 'CSS/styles.css'; ?>
        body {
            background-repeat:no-repeat ;
            background-size: cover;
            background-attachment: fixed;
            background-image: url('background/gymbackground.jpeg');
            display: flex;

        }
        .body1{
            display: grid;  
        }
        h1 {
            margin-bottom: 20px;
        }
        .welcome-statement{
            position: absolute;
            justify-content: center;
            z-index: 3;
            left:0
        }
        .product-container {
            display: flex;
            flex-wrap: wrap;
            /*lex-direction: column;*/
            gap: 16px;
            z-index:2;
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

        .product-card .button {
            background-color:#33b249 ;
            color: white;
            border: none;
            padding: 8px 16px;
            cursor: pointer;
            border-radius: 4px;
            margin-top: 8px;
        }
        
        .product-card p{
            display: none;
        }
        .product-card:hover p {
            display: block;
        }
        .product-card .button:hover {
            background-color: darkcyan;
        }
        
        .search {
            width: 100%;
            position: relative;
            display: flex;
            text-align: center;
        }

        .searchTerm {
            width: 160px;
            border: 3px solid #00B4CC;
            border-right: none;
            

            height: 40px;
            border-radius: 5px 0 0 5px;
            outline: none;
            color: #9DBFAF;
            margin:auto;
        }

        .searchTerm:focus{
        color: black;
        }

        .searchButton {
            width: 40px;
            height: 40px;
            
            border: 1px solid #00B4CC;
            background: #00B4CC;
            text-align: center;
            color: #fff;
            border-radius:  0 5px 5px  0;


        }
        .search-icon{
            width: 15px;
            height: 15px;
            text-align: center;
        }
        /*Resize the wrap to see the search bar change!*/
        .product-quantity {
            font-size: 14px;
            color: #333;
            margin-bottom: 10px;
        }

    </style>

    <!-- Include jQuery and DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>

    <!-- Initialize DataTables -->
    <script>
    $(document).ready(function() {
        $('#itemstable').DataTable();
    });
    </script>
    <!-- Navigation Bar -->
    <nav class="navbar">
        
        <ul>
            <!-- Logo on the left -->
            <li class="logo">
                <a class="main_page" href="index.php">
                    <img class="image" src="icon-image/logo.png" alt="Logo">Medusa Gym</a>
            </li>
            <!-- Links on the right -->
            <li class="toggle-button">
                <a href="#">
                    <img class= "image" src="icon-image/toggle-icon.png" alt="toggle" style= "vertical-align: middle">
                </a>
            </li>
            <div class="nav-items">
                <li><a class="NavButton" href="product.php">Browse</a></li>
                <?php if ($is_logged_in): ?>
                    <li><span class="login_welcome">Welcome, <?php echo $_SESSION['username']; ?>!</span></li>
                    <li><a class ="NavUserProfile" href="user-profile.php">My profile</a></li>
                    <li><a class ="NavLogout" href="logout.php">Logout</a></li>
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
    
</head>
<body >
    
    
   
    
    <div class="body1">
            <!-- Add the Search Form above the product table -->
        
            <div class="search">
                <form method="GET" action="product.php">
                    <input type="text" class ="searchTerm" name="search_query" placeholder="What are you looking for?">
                    <button type="submit" class="searchButton"> <img class="search-icon" src="icon-image/search.png" alt="🔍"></i></button>
                </form>
            </div>
        
            
        

        <!-- Product Table -->
        <div class="product-container">
    <Table id="itemstable" class="display">
        <?php
        // Check if a search query is provided
        $searchQuery = isset($_GET['search_query']) ? $_GET['search_query'] : '';

        // Database connection (replace with your actual database credentials)
        $servername = "localhost";
        $sql = "SELECT id, name, description, price, image, quantity FROM products";

        $result = $conn->query($sql);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // SQL query to search products based on the search term or show all products if no search term is entered
        if ($searchQuery) {
            $sql = "SELECT id, name, description, price, image, quantity FROM products WHERE name LIKE ? OR description LIKE ?";
            $stmt = $conn->prepare($sql);
            $searchTerm = "%" . $searchQuery . "%";
            $stmt->bind_param("ss", $searchTerm, $searchTerm);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            // If no search term is entered, show all products
            $sql = "SELECT id, name, description, price, image, quantity FROM products";
            $result = $conn->query($sql);
        }

        if ($result && $result->num_rows > 0):
            while ($row = $result->fetch_assoc()):
        ?>
        <div class="product-card">
            <!-- Display product quantity at the top -->
            
            <img class="product-image" src="Product-images/<?php echo htmlspecialchars($row['image']); ?>" alt="Product-images/<?php echo htmlspecialchars($row['image']); ?>"> 
            <div class="product-info">    
                <h2><?php echo htmlspecialchars($row['name']); ?></h2>
                <div class="product-quantity">
                    <p>Available Quantity: <?php echo (int)$row['quantity']; ?></p>
                </div>
                <p><?php echo htmlspecialchars($row['description']); ?></p>
                <div class="price"><?php echo number_format($row['price'], 2); ?></div>
                
                <form action="add_to_cart.php" method="post">
                    <input type="hidden" name="product_id" value="<?php echo (int)$row['id']; ?>">
                    <input type="number" name="quantity" value="1" min="1" max="<?php echo (int)$row['quantity']; ?>" 
                           <?php echo $row['quantity'] > 0 ? '' : 'disabled'; ?>>
                    <input class="button" type="submit" value="Add to Cart" 
                           <?php echo $row['quantity'] > 0 ? '' : 'disabled'; ?>>
                </form>
            </div>
        </div>
        <?php 
            endwhile;
        else: 
        ?>
            <p>No products found.</p>
        <?php 
        endif;

        if ($searchQuery) $stmt->close();
        ?>
    </Table>
</div>

    <!-- Close the database connection -->
    <?php $conn->close(); ?>
    <script src="JavaScript/cart.js"></script>
    <script src="JavaScript/toggle.js"></script>  
</body>
</html>
