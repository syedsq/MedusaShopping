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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Shopping Site</title>
    <!-- Include DataTables CSS and other styles -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #007bff;
            padding: 10px 20px;
            color: white;
        }

        nav a {
            color: white;
            text-decoration: none;
            margin-left: 10px;
            font-weight: bold;
        }

        nav a:hover {
            text-decoration: underline;
        }

        nav .cart {
            position: relative;
            display: inline-block;
            margin-left: 20px;
        }

        nav .cart img {
            width: 30px;
        }

        nav .cart .cart-count {
            position: absolute;
            top: -8px;
            right: -10px;
            background-color: red;
            color: white;
            font-size: 12px;
            width: 20px;
            height: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 50%;
        }

        h1 {
            text-align: center;
            margin: 20px 0;
        }

        table {
            width: 90%;
            max-width: 1200px;
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

        input[type="submit"] {
            background-color: #28a745;
            color: white;
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #218838;
        }
    </style>

    <!-- Include jQuery and DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>

    <!-- Initialize DataTables -->
    <script>
    $(document).ready(function() {
        $('#itemsTable').DataTable();
    });
    </script>
</head>
<body>
    <!-- Navigation Bar -->
    <nav>
        <h1>Online Shopping</h1>
        <div>
            <?php if ($is_logged_in): ?>
                <span>Welcome, <?php echo $_SESSION['username']; ?>!</span>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            <?php endif; ?>

            <!-- Cart Icon with Item Count -->
            <div class="cart">
                <a href="cart.php">
                    <img src="cart-icon.png" alt="Cart">
                    <?php if ($cart_item_count > 0): ?>
                        <div class="cart-count"><?php echo $cart_item_count; ?></div>
                    <?php endif; ?>
                </a>
            </div>
        </div>
    </nav>

    <!-- Product Table -->
    <h1>Welcome to Our Online Store</h1>
    <table id="itemsTable" class="display">
        <thead>
            <tr>
                <th>Image</th>
                <th>Name</th>
                <th>Description</th>
                <th>Price ($)</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Fetch products from the database
            $sql = "SELECT id, name, description, price, image FROM products";
            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0):
                while ($row = $result->fetch_assoc()):
            ?>
                <tr>
                    <td><img src="<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>" width="100"></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['description']); ?></td>
                    <td><?php echo number_format($row['price'], 2); ?></td>
                    <td>
                        <form action="add_to_cart.php" method="post">
                            <input type="hidden" name="product_id" value="<?php echo (int)$row['id']; ?>">
                            <input type="number" name="quantity" value="1" min="1" max="10">
                            <input type="submit" value="Add to Cart">
                        </form>
                    </td>
                </tr>
            <?php endwhile; else: ?>
                <tr><td colspan="5">No products found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Close the database connection -->
    <?php $conn->close(); ?>
</body>
</html>
