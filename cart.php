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


// Check if cart is empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo "<p>Your cart is empty. <a href='index.php'>Go back to shopping</a></p>";
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
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        h1 {
            text-align: center;
            margin: 20px 0;
        }

        table {
            width: 80%;
            max-width: 1000px;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        table th, table td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        table th {
            background-color: #007bff;
            color: white;
        }

        table tr:hover {
            background-color: #f1f1f1;
        }

        input[type="number"] {
            width: 50px;
            padding: 5px;
        }

        input[type="submit"], .checkout-button, .back-button {
            background-color: #28a745;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
            display: inline-block;
            text-decoration: none;
        }

        input[type="submit"]:hover, .checkout-button:hover, .back-button:hover {
            background-color: #218838;
        }

        .checkout-button, .back-button {
            display: block;
            text-align: center;
            margin: 20px auto;
            width: 200px;
        }

        .back-button {
            background-color: #007bff;
        }

        .back-button:hover {
            background-color: #0056b3;
        }
        
            

    </style>

    <!-- Navigation Bar -->
    
</head>
<body>
    <h1>Your Shopping Cart</h1>

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
                // Fetch product details from the session cart
                foreach ($_SESSION['cart'] as $product_id => $item) {
                    $sql = "SELECT name, price FROM products WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $product_id);
                    $stmt->execute();
                    $stmt->bind_result($name, $price);
                    $stmt->fetch();
                    $stmt->close();

                    // Calculate the total for each item
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
                            <button type="submit" name="remove_item" value="1">Remove</button>
                            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
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
    // Calculate tax and total
    $tax_amount = $subtotal * $tax_rate;
    $total = $subtotal + $tax_amount;
    ?>

    <div style="text-align: center;">
        <p>Subtotal: $<?php echo number_format($subtotal, 2); ?></p>
        <p>Tax (8.25%): $<?php echo number_format($tax_amount, 2); ?></p>
        <p>Total: $<?php echo number_format($total, 2); ?></p>

        <a href="checkout.php" class="checkout-button">Proceed to Checkout</a>
        <a href="index.php" class="back-button">Back to Home</a>
    </div>

    <?php $conn->close(); ?>
</body>
</html>