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


function redirectToRemoveItem($product_id, $destination = 'remove_from_cart.php') {
    // Generate a hidden form and redirect using JavaScript
    echo "<form id='redirect_form' action='" . htmlspecialchars($destination) . "' method='POST' style='display: none;'>
            <input type='hidden' name='product_id' value='" . htmlspecialchars($product_id) . "'>
          </form>
          <script>document.getElementById('redirect_form').submit();</script>";
    exit();
}




// Initialize cart totals
$subtotal = 0;
$tax_rate = 0.0825;  // 8.25% tax rate

// Update cart items or remove items
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Update Cart
    //if (isset($_POST['update_cart'])) {
        // Ensure 'quantity' is an array before using it in foreach
       
            // Loop through each product's quantity in the cart
            foreach ($_POST['quantity'] as $product_id => $quantity) {
                

                $currentQuantity = $_SESSION['cart'][$product_id]['quantity'];

                // If quantity is set to 0, remove the item
                if ($quantity == 0) {
                    redirectToRemoveItem($product_id);
                } else {
                    // Update cart using the function
                    $_SESSION['cart'][$product_id]['quantity'] = $quantity;

                }

            }
    //}

    // Handle item removal action
    if (isset($_POST['remove_item'])) {
        $product_id = $_POST['product_id'];
    
        // Call the function to redirect
        redirectToRemoveItem($product_id);
    
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart</title>
    <style>
        <?php include 'CSS/styles.css'; ?>
        <?php include 'CSS/cart.css'; ?>
        
        
            

    </style>

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
<body>
    <div class="wrapper">
        

        <?php if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])): ?>
        <!-- Display empty cart message -->
        <div class="group1">
            <div class="motivation-poster">
                <img src= "background/motivationposter1.jpg">
            </div>
            <div class="empty-message">
                <h2>Your cart is empty</h2>
                <p>Looks like you haven't added anything yet. Are you going to give up that easily?</p>
                
                <a href="product.php">Go back to shopping</a>
            </div>
    <?php else: ?>
        <h1>Your Shopping Cart</h1>
        <!-- Display cart contents if there are items -->
        <form action="cart.php" method="POST">
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Price ($)</th>
                        <th>Total ($)</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($_SESSION['cart'] as $product_id => $item) {
                        $sql = "SELECT name, price FROM products WHERE id = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("i", $product_id);
                        $stmt->execute();
                        $stmt->bind_result($name, $price);
                        $stmt->fetch();
                        $stmt->close();

                        $item_total = $price * $item['quantity'];
                        $subtotal += $item_total;
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($name); ?></td>
                            <td>
                                <input type="number" name="quantity[<?php echo $product_id; ?>]" value="<?php echo $item['quantity']; ?>" min="0">
                            </td>
                            <td><?php echo number_format($price, 2); ?></td>
                            <td><?php echo number_format($item_total, 2); ?></td>
                            <td>
                            <form action="cart.php" method="POST" style="display: inline;">
                                <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                                <button type="submit" name="remove_item" value="1">Remove</button>
                            </form>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <div style="text-align: center; margin: 20px;">
                <input type="submit" name="update_cart" value="Update Cart">
            </div>
        </form>

        <?php
        $tax_amount = $subtotal * $tax_rate;
        $total = $subtotal + $tax_amount;
        ?>

        <div style="text-align: center;">
            <p>Subtotal: $<?php echo number_format($subtotal, 2); ?></p>
            <p>Tax (8.25%): $<?php echo number_format($tax_amount, 2); ?></p>
            <p>Total: $<?php echo number_format($total, 2); ?></p>
            
            <?php if ($is_logged_in): ?>
                <a href="checkout.php" class="checkout-button">Proceed to Checkout</a>
            <?php else: ?>
                <a href="login.php" class="checkout-button">Log in to complete your purchase</a>
            <?php endif; ?>
                <a href="index.php" class="back-button">Back to Home</a>
        </div>
    <?php endif; ?>

    <?php $conn->close(); ?>
    </div>  
</body>
</html>