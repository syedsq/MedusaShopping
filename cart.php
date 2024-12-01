<?php
session_start();
include 'config.php';  // Include database connection

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "You must be logged in to view your cart.";
    exit();
}

// Initialize cart totals
$subtotal = 0;
$tax_rate = 0.0825;  // 8.25% tax rate
$cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];  // Fetch cart items from session

// Fetch cart details and calculate totals
$cart_details = [];
if (!empty($cart_items)) {
    foreach ($cart_items as $product_id => $item) {
        $sql = "SELECT name, price FROM products WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $stmt->bind_result($name, $price);
        $stmt->fetch();
        $stmt->close();

        $item_total = $price * $item['quantity'];
        $subtotal += $item_total;

        $cart_details[] = [
            'name' => $name,
            'quantity' => $item['quantity'],
            'price' => $price,
            'total_price' => $item_total
        ];
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
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }

        .cart-container {
            width: 80%;
            max-width: 900px;
            margin: 50px auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .cart-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .cart-header h1 {
            margin: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        table th, table td {
            padding: 12px;
            text-align: center;
            border: 1px solid #ddd;
        }

        table th {
            background-color: #007bff;
            color: white;
        }

        table tr:hover {
            background-color: #f1f1f1;
        }

        .summary {
            text-align: right;
            margin-top: 20px;
        }

        .summary p {
            margin: 5px 0;
            font-size: 16px;
        }

        .checkout-button, .back-to-home-button {
            display: block;
            text-align: center;
            margin: 20px auto;
            padding: 10px 15px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            width: 200px;
        }

        .checkout-button:hover {
            background-color: #218838;
        }

        .back-to-home-button {
            background-color: #007bff;
        }

        .back-to-home-button:hover {
            background-color: #0056b3;
        }

        .empty-cart-message {
            text-align: center;
            margin-top: 20px;
            font-size: 18px;
            color: gray;
        }
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
                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                            <td><?php echo (int)$item['quantity']; ?></td>
                            <td><?php echo number_format($item['price'], 2); ?></td>
                            <td><?php echo number_format($item['total_price'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="summary">
                <p>Subtotal: $<?php echo number_format($subtotal, 2); ?></p>
                <p>Tax (8.25%): $<?php echo number_format($tax, 2); ?></p>
                <p><strong>Total: $<?php echo number_format($total, 2); ?></strong></p>
            </div>

            <a href="checkout.php" class="checkout-button">Proceed to Checkout</a>
        <?php endif; ?>

        <a href="index.php" class="back-to-home-button">Back to Home</a>
    </div>
</body>
</html>
