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
    <title>Medusa Gym</title>
    
    <!-- Include DataTables CSS and other styles -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        /* Hero Section */
        .hero {
            background-image: url('https://images.pexels.com/photos/1552252/pexels-photo-1552252.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1');
            background-size: cover;
            background-position: center;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            text-align: center;
            position: relative;
        }

        .hero-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .hero h1 {
            font-size: 3.5rem;
            margin-bottom: 10px;
            z-index: 1;
        }

        .hero .cta {
            background-color: #ff4500;
            padding: 10px 20px;
            border: none;
            color: white;
            border-radius: 30px;
            cursor: pointer;
            font-size: 1.2rem;
            z-index: 1;
        }

        /* Navigation Bar */
        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: rgba(0, 0, 0, 0.8);
            padding: 10px 20px;
            color: white;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 100;
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

        /* Product Table */
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
        <div>
            <a href="#">Medusa Gym</a>
        </div>
        <div>
            <?php if ($is_logged_in): ?>
                <span>Welcome, <?php echo $_SESSION['username']; ?>!</span>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
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

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-overlay"></div>
        <div>
            <h1>It’s gym season, Gymrat!</h1>
            <p>Ready for the next step?</p>
            <button class="cta">Shop this sale</button>
        </div>
    </section>

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
