<?php
session_start();
include 'config.php';  // Include database connection

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "You must be logged in to view your order summary.";
    exit();
}

$user_id = $_SESSION['user_id'];  // User's ID from session
$order_id = isset($_GET['order_id']) ? $_GET['order_id'] : null;

// Check if the order ID is provided
if (!$order_id) {
    echo "Invalid order ID.";
    exit();
}

// Fetch order details from the orders table
$sql = "SELECT total_amount, created_at FROM orders WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$stmt->bind_result($total_amount, $order_date);
if (!$stmt->fetch()) {
    echo "Order not found.";
    exit();
}
$stmt->close();

// Fetch order items from the order_items table
$sql = "SELECT products.name, order_items.quantity, order_items.price FROM order_items 
        JOIN products ON order_items.product_id = products.id 
        WHERE order_items.order_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();

$order_items = [];
while ($row = $result->fetch_assoc()) {
    $order_items[] = $row;  // Store each item
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Summary</title>
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

        .order-summary-container {
            width: 60%;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table th, table td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }

        table th {
            background-color: #007bff;
            color: white;
        }

        .summary-details {
            text-align: center;
            margin-top: 20px;
        }

        .summary-details p {
            font-size: 18px;
            margin-bottom: 10px;
        }

        .back-button {
            display: block;
            margin: 20px auto;
            text-align: center;
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .back-button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>

    <h1>Order Summary</h1>

    <div class="order-summary-container">
        <h2>Order #<?php echo $order_id; ?></h2>
        <p><strong>Date:</strong> <?php echo date('F j, Y, g:i a', strtotime($order_date)); ?></p>

        <!-- Order Items Table -->
        <table>
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Price per Unit</th>
                    <th>Total Price</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $subtotal = 0;
                foreach ($order_items as $item) {
                    $total_price = $item['quantity'] * $item['price'];
                    $subtotal += $total_price;
                ?>
                <?php $subtotal = $subtotal+ $subtotal* 0.0825 ;?>
                <tr>
                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                    <td><?php echo (int)$item['quantity']; ?></td>
                    <td>$<?php echo number_format($item['price'], 2); ?></td>
                    <td>$<?php echo number_format($total_price, 2); ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>

        <!-- Order Summary Details -->
        <div class="summary-details">
            <p><strong>Subtotal before discount:</strong> $<?php echo number_format($subtotal, 2); ?></p>
            <p><strong>Total:</strong> $<?php echo number_format($total_amount -$total_amount*0.25, 2); ?></p>
        </div>

        <!-- Back to Home Button -->
        <a href="index.php" class="back-button">Back to Home</a>
    </div>

</body>
</html>
