<?php
session_start();
include 'config.php';  // Include database connection

if (isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];

    // Check if product exists in the cart
    if (isset($_SESSION['cart'][$product_id])) {
        // Get the quantity of the product from the cart session
        $cart_quantity = $_SESSION['cart'][$product_id]['quantity'];

        // Fetch the current quantity of the product in the database
        $product_query = "SELECT quantity FROM products WHERE id = ?";
        $stmt = $conn->prepare($product_query);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $stmt->bind_result($current_quantity);
        $stmt->fetch();
        $stmt->close();

        // Refund the stock to the database
        if ($current_quantity !== null) {
            $new_quantity = $current_quantity + $cart_quantity;
            // Update the product's quantity in the database
            $update_query = "UPDATE products SET quantity = ? WHERE id = ?";
            $update_stmt = $conn->prepare($update_query);
            $update_stmt->bind_param("ii", $new_quantity, $product_id);
            $update_stmt->execute();
            $update_stmt->close();
        }

        // Remove the item from the session
        unset($_SESSION['cart'][$product_id]);
        // Redirect back to the cart page
         
        
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Removing Item...</title>
    <style>
        <?php include 'CSS/cart.css'; ?>
    </style>
</head>
<body>
    <div class="message">
        <p>Removing item from your cart...</p>
    </div>

    <!-- Redirect to the cart page after a 0.5-second delay -->
    <script>
        setTimeout(function () {
            window.location.href = "cart.php";
        }, 200); // Delay in milliseconds
    </script>
</body>
</html>