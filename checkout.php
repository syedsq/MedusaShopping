<?php
session_start();
include 'config.php';  // Include database connection

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "You must be logged in to place an order.";
    exit();
}

$user_id = $_SESSION['user_id'];  // User's ID from session

// Check if the cart is empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo "Your cart is empty. <a href='index.php'>Go back to shopping</a>";
    exit();
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
        echo "Invalid discount code.";
        $discount_percentage = 0.0;
    }
    $stmt->close();
}

// Calculate tax and total
$discount_amount = $subtotal * $discount_percentage;
$subtotal_after_discount = $subtotal - $discount_amount;
$tax_amount = $subtotal_after_discount * $tax_rate;
$total_amount = $subtotal_after_discount + $tax_amount;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
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

        .checkout-container {
            width: 50%;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        input[type="text"], input[type="submit"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        input[type="submit"] {
            background-color: #28a745;
            color: white;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #218838;
        }

        p {
            font-size: 18px;
            text-align: center;
        }

        .summary {
            text-align: center;
            margin-bottom: 20px;
        }

        .discount-message {
            color: green;
            font-weight: bold;
            margin-bottom: 10px;
            text-align: center;
        }

        .payment-placeholder {
            text-align: center;
            margin-top: 20px;
            background-color: #f8f9fa;
            padding: 10px;
            border: 1px dashed #007bff;
        }

        .payment-placeholder p {
            font-style: italic;
            color: #007bff;
        }
    </style>
</head>
<body>

    <h1>Checkout</h1>

    <div class="checkout-container">
        <!-- Discount Code Form -->
        <form action="checkout.php" method="POST">
            <label for="discount_code">Discount Code (optional):</label>
            <input type="text" name="discount_code" id="discount_code" placeholder="Enter discount code">
            <input type="submit" value="Apply Discount & Checkout">
        </form>

        <!-- Display discount message -->
        <?php if ($discount_message): ?>
            <p class="discount-message"><?php echo $discount_message; ?></p>
        <?php endif; ?>

        <!-- Order Summary -->
        <div class="summary">
            <p>Subtotal: $<?php echo number_format($subtotal, 2); ?></p>
            <p>Discount: -$<?php echo number_format($discount_amount, 2); ?> (<?php echo $discount_percentage * 100; ?>%)</p>
            <p>Tax (8.25%): $<?php echo number_format($tax_amount, 2); ?></p>
            <p><strong>Total: $<?php echo number_format($total_amount, 2); ?></strong></p>
        </div>

        <!-- Payment Processing Placeholder -->
        <div class="payment-placeholder">
            <p>Payment processing integration coming soon...</p>
        </div>

        <form action="checkout.php" method="POST">
            <input type="hidden" name="final_checkout" value="true">
            <input type="submit" value="Place Order">
        </form>
    </div>

</body>
</html>

<?php
// Process the order after the user clicks "Place Order"
if (isset($_POST['final_checkout'])) {
    // Insert the order into the orders table
    $sql = "INSERT INTO orders (user_id, total_amount) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("id", $user_id, $total_amount);

    if ($stmt->execute()) {
        $order_id = $conn->insert_id;  // Get the last inserted order ID
        
        // Insert the items into the order_items table
        foreach ($_SESSION['cart'] as $product_id => $item) {
            $quantity = $item['quantity'];
            $sql = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iiid", $order_id, $product_id, $quantity, $price);
            $stmt->execute();
        }

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
