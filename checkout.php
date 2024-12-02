<?php
session_start();
include 'config.php';  // Include database connection

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "You must be logged in to place an order.";
    exit();
}

$user_id = $_SESSION['user_id'];  // User's ID from session
$is_logged_in = isset($_SESSION['user_id']);

// Count the total number of items in the cart
$cart_item_count = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cart_item_count += $item['quantity'];  // Sum up quantities of all items
    }
}

// Initialize cart totals
$subtotal = 0;
$tax_rate = 0.0825;  // 8.25% tax rate
$discount_code = isset($_POST['discount_code']) ? $_POST['discount_code'] : null;
$discount_percentage = 0.0;  // Default discount percentage
$discount_message = "";  // To hold the discount message

// Calculate cart subtotal
foreach ($_SESSION['cart'] as $product_id => $item) {
    $sql = "SELECT price FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $stmt->bind_result($price);
    $stmt->fetch();
    $stmt->close();

    $subtotal += $price * $item['quantity'];
}

// Apply discount if a discount code is provided and valid
if ($discount_code) {
    $sql = "SELECT discount_percentage FROM discount_codes WHERE code = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $discount_code);
    $stmt->execute();
    $stmt->bind_result($discount_percentage);
    if ($stmt->fetch()) {
        $discount_message = "Discount code applied! " . ($discount_percentage * 100) . "% off.";
    } else {
        $discount_message = "Invalid discount code.";
        $discount_percentage = 0.0;
    }
    $stmt->close();
}

// Calculate tax and total
$discount_amount = $subtotal * $discount_percentage;
$subtotal_after_discount = $subtotal - $discount_amount;
$tax_amount = $subtotal_after_discount * $tax_rate;
$total_amount = $subtotal_after_discount + $tax_amount;

// Process the order when the user clicks "Place Order"
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['final_checkout'])) {
    // Insert the order into the orders table
    $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount) VALUES (?, ?)");
    $stmt->bind_param("id", $user_id, $total_amount);

    if ($stmt->execute()) {
        $order_id = $stmt->insert_id;  // Get the last inserted order ID
        
        // Insert the items into the order_items table
        foreach ($_SESSION['cart'] as $product_id => $item) {
            $quantity = $item['quantity'];
            $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiid", $order_id, $product_id, $quantity, $price);
            $stmt->execute();
        }

        // Insert shipping and payment details
        $stmt = $conn->prepare("INSERT INTO order_details (order_id, first_name, last_name, address, city, state, zip, credit_card_number, expiration_date, ccv)
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssssssss", 
            $order_id, 
            $_POST['f-name'], 
            $_POST['l-name'], 
            $_POST['address'], 
            $_POST['city'], 
            $_POST['state'], 
            $_POST['zip'], 
            $_POST['card-num'], 
            $_POST['expire'], 
            $_POST['security']
        );
        $stmt->execute();

        // Clear the cart after successful order placement
        unset($_SESSION['cart']);

        // Redirect to order summary
        header("Location: order_summary.php?order_id=" . $order_id);
        exit();
    } else {
        echo "Error placing the order: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <style>
        <?php include 'CSS/styles.css'; ?>
        <?php include 'CSS/checkout.css'; ?>
        body {
            background-repeat: no-repeat;
            background-size: cover;
            background-attachment: fixed;
            background-image: url('background/background3.jpeg');
            display: flex;
        }
        
    </style>
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
    
    <div class="checkout-container">
        <h1>Checkout</h1>
        <!-- Discount Code -->
        <div class="discount">
        <form action="checkout.php" method="POST">
            <label for="discount_code">Discount Code (optional):</label>
            <div class="input-group">
                <input type="text" class="discount-code-input" name="discount_code" placeholder="Enter discount code" value="<?php echo htmlspecialchars($discount_code); ?>">
                <button type="submit" class="apply-btn">Apply Discount</button>
            </div>
        </form>
            </div>
        <!-- Display discount message -->
        <?php if ($discount_message): ?>
            <p class="discount-message"><?php echo $discount_message; ?></p>
        <?php endif; ?>

        <!-- Order Summary -->
        <h2>Order Summary</h2>
        <div class="checkout-calculation">
            <p>Subtotal: $<?php echo number_format($subtotal, 2); ?></p>
            <p>Discount: -$<?php echo number_format($discount_amount, 2); ?></p>
            <p>Tax: $<?php echo number_format($tax_amount, 2); ?></p>
            <p><strong>Total: $<?php echo number_format($total_amount, 2); ?></strong></p>
        </div>

        <!-- Shipping and Payment Details -->
        <div class="wrapper">
    <div class="container">
        <form action="checkout.php" method="POST">
            <h1><i class="fas fa-shipping-fast"></i> Shipping Details</h1>
            <div class="name">
                <div>
                    <label for="f-name">First</label>
                    <input type="text" name="f-name" required>
                </div>
                <div>
                    <label for="l-name">Last</label>
                    <input type="text" name="l-name" required>
                </div>
            </div>

            <div class="street">
                <label for="address">Street</label>
                <input type="text" name="address" required>
            </div>

            <div class="address-info">
                <div>
                    <label for="city">City</label>
                    <input type="text" name="city" required>
                </div>
                <div>
                    <label for="state">State</label>
                    <input type="text" name="state" required>
                </div>
                <div>
                    <label for="zip">Zip</label>
                    <input type="text" name="zip" required>
                </div>
            </div>

            <h1><i class="far fa-credit-card"></i> Payment Information</h1>

            <div class="cc-num">
                <label for="card-num">Credit Card No.</label>
                <input type="text" name="card-num" required>
            </div>

            <div class="cc-info">
                <div>
                    <label for="expire">Exp</label>
                    <input type="text" name="expire" required>
                </div>
                <div>
                    <label for="security">CCV</label>
                    <input type="text" name="security" required>
                </div>
            </div>

            
                <input type="hidden" name="final_checkout" value="true">
                <input class="place-order" type="submit" value="Place Order">

        </form>
    </div>
</div>
    </div>
    <script src="JavaScript/cart.js"></script>
    <script src="JavaScript/toggle.js"></script>      
</body>
</html>
