<?php
session_start();
include 'config.php';  // Database connection

// Check if the user is an admin
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    echo "Access denied. You must be an admin to access this page.";
    exit();
}

// Handle deletion of an order
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $order_id = $_POST['order_id'];
    
    // First, delete related items from the order_items table
    $delete_items_sql = "DELETE FROM order_items WHERE order_id = ?";
    $stmt = $conn->prepare($delete_items_sql);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $stmt->close();

    // Then, delete the order from the orders table
    $delete_order_sql = "DELETE FROM orders WHERE id = ?";
    $stmt = $conn->prepare($delete_order_sql);
    $stmt->bind_param("i", $order_id);

    if ($stmt->execute()) {
        echo "<p>Order deleted successfully.</p>";
    } else {
        echo "<p>Error deleting order: " . $conn->error . "</p>";
    }

    $stmt->close();
}

// Fetch all orders with sorting options
$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'created_at';
$order_direction = isset($_GET['order_direction']) ? $_GET['order_direction'] : 'DESC';

$sql = "SELECT orders.*, users.username FROM orders JOIN users ON orders.user_id = users.id ORDER BY $sort_by $order_direction";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Orders</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .admin-container {
            width: 80%;
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
        }

        table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }

        table th, table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        table th {
            background-color: #007bff;
            color: white;
        }

        .sort {
            margin-bottom: 20px;
        }

        .delete-btn {
            color: #fff;
            background-color: #dc3545;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <h1>View Orders</h1>

        <!-- Sort Orders -->
        <div class="sort">
            <form action="view_orders.php" method="GET">
                <label for="sort_by">Sort By:</label>
                <select name="sort_by">
                    <option value="created_at">Order Date</option>
                    <option value="username">Customer</option>
                    <option value="total_amount">Order Size</option>
                </select>

                <label for="order_direction">Order:</label>
                <select name="order_direction">
                    <option value="ASC">Ascending</option>
                    <option value="DESC">Descending</option>
                </select>

                <input type="submit" value="Sort">
            </form>
        </div>

        <!-- Display Orders -->
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Total Amount ($)</th>
                    <th>Order Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                            <td><?php echo number_format($row['total_amount'], 2); ?></td>
                            <td><?php echo date('F j, Y, g:i a', strtotime($row['created_at'])); ?></td>
                            <td>
                                <!-- Delete Button -->
                                <form action="view_orders.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($row['id']); ?>">
                                    <input type="submit" name="delete" value="Delete" class="delete-btn" onclick="return confirm('Are you sure you want to delete this order?');">
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5">No orders found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
$conn->close();
?>