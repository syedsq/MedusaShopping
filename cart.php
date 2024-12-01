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

// Calculate tax and total
$tax = $subtotal * $tax_rate;
$total = $subtotal + $tax;
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
</head>
<body>
    <div class="cart-container">
        <div class="cart-header">
            <h1>Your Shopping Cart</h1>
        </div>

        <?php if (empty($cart_items)): ?>
            <p class="empty-cart-message">Your cart is empty. <a href="product.php">Browse Products</a></p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Price per Unit ($)</th>
                        <th>Total Price ($)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart_details as $item): ?>
                        <tr>

                            <td><?php echo htmlspecialchars($name); ?></td>
                            
                            <td><?php echo (int)$item['quantity']; ?></td>
                            
                            <td><?php echo number_format($price, 2); ?></td>
                            <td><?php echo number_format($item_total, 2); ?></td>
                            <td>
                            <form action="remove_from_cart.php" method="POST" style="display: inline;">
                                <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                                <button type="submit" name="remove_item" value="1">Remove</button>
                            </form>
                            </td>

                        </tr>
                    <?php endforeach; ?>
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

            <a href="checkout.php" class="checkout-button">Proceed to Checkout</a>
        <?php endif; ?>

        <a href="index.php" class="back-to-home-button">Back to Home</a>
    </div>
</body>
</html>
