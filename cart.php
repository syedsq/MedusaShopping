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
    echo "<form action='remove_from_cart.php' method='POST' id='remove_form'>
        <input type='hidden' name='product_id' value='" . htmlspecialchars($product_id) . "'>
        <button type='submit' style='display: none;'>Submit</button>
      </form>
      <script>document.getElementById('remove_form').submit();</script>";
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            
        }

        
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 2px solid #ddd;
            border-radius:2px

        }

        th {
            background-color: #f4f4f4;
            font-weight: bold;
        }
        
        tr:hover {
            background-color: #f1f1f1;
        }

        button {
            background-color: #ff4f58;
            border: none;
            color: white;
            padding: 6px 12px;
            cursor: pointer;
            font-size: 14px;
        }

        button:hover {
            background-color: #d43f48;
        }


        /* Button styling for checkout */
        .checkout-button, .back-button {
            padding: 10px 20px;
            background-color: #3c8dbc;
            color: white;
            border-radius: 5px;
            text-decoration: none;
            margin-top: 20px;
        }

        .checkout-button:hover, .back-button:hover {
            background-color: #2b7aa1;
        }

        /* Responsive Cart Design */
        @media (max-width: 500px) {
            h2 {
                padding-top: 100px;
            }
            table {
                width: 100%;
                font-size: 14px;
            }
            .minimize{
                display:none;
            }
            th, td {
                padding: 10px;
            }

            .group1 {
                padding: 10px;
            }
        }
        .checkout-calculation {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            font-family: 'Arial', sans-serif;
            color: #333;
        }

        .checkout-calculation p {
            font-size: 1.1em;
            margin: 10px 0;
            justify-content: space-between;
            align-items: center;
        }

        .checkout-calculation p span {
            font-weight: bold;
            color: #333;
        }

        .checkout-calculation p:last-child {
            font-size: 1.3em;
            font-weight: bold;
            color: #00b4cc; 
        }

        .checkout-calculation .totals {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .checkout-calculation .totals p {
            font-size: 1.2em;
            color: #555;
        }

        /* Add hover effect on checkout-calculation box */
        .checkout-calculation:hover {
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
            transform: translateY(-5px);
            transition: all 0.3s ease;
        }
    </style>

    <!-- Navigation Bar -->
    <?php include 'navigation_bar.php'; ?>
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
                        <th>Picture</th>
                        <th>Product</th>
                        <th>Count</th>
                        <th class="minimize" >Price ($)</th>
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
                        $image_path = "Product-images/" . strtolower(str_replace([' ', '-'], '_', $name)) . ".jpg";
                    ?>
                        <tr>
                        <td>
                            <img src="<?php echo htmlspecialchars($image_path); ?>" 
                                 alt="<?php echo htmlspecialchars($image_path); ?>" 
                                 style="width: 50px; height: 50px; object-fit: cover;">
                        </td>
                        <td><?php echo htmlspecialchars($name); ?></td>
                            
                            <td><?php echo (int)$item['quantity']; ?></td>
                            
                            <td class="minimize"><?php echo number_format($price, 2); ?></td>
                            <td><?php echo number_format($item_total, 2); ?></td>
                            <td>
                            <form action="remove_from_cart.php" method="POST" style="display: inline;">
                                <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                                <button type="submit" name="remove_item" value="1">✖</button>
                            </form>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
<!--
            <div style="text-align: center; margin: 20px;">
                <input type="submit" name="update_cart" value="Update Cart">
            </div>
                    -->
        </form>

        <?php
        $tax_amount = $subtotal * $tax_rate;
        $total = $subtotal + $tax_amount;
        ?>

        <div style="text-align: center;">

            <div class="checkout-calculation">
                <p>Subtotal: $<?php echo number_format($subtotal, 2); ?></p>
                <p>Tax (8.25%): $<?php echo number_format($tax_amount, 2); ?></p>
                <p>Total: $<?php echo number_format($total, 2); ?></p>
            </div>
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
    <script src="JavaScript/cart.js"></script>
    <script src="JavaScript/toggle.js"></script>   
</body>
</html>