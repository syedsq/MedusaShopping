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
    <link rel="stylesheet" type="text/css" href="CSS/styles.css">
    
    <style>
        <?php include 'CSS/styles.css'; ?>
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
    <nav class="navbar">
    <ul>
        <!-- Logo on the left -->
        <li class="logo">
            <a class="main_page" href="index.php">
                <img class="image" src="icon-image/logo.png" alt="Logo">Medusa Gym</a>
        </li>
        <!-- Links on the right -->
        <div class="nav-items">
            <li><a class="NavButton" href="product.php">Browse</a></li>
            <?php if ($is_logged_in): ?>
                <li><span class="login_welcome">Welcome, <?php echo $_SESSION['username']; ?>!</span></li>
                <li><a href="logout.php">Logout</a></li>
            <?php else: ?>
                <li><a class="NavButton" href="login.php">Login</a></li>
                <li><a class="NavButton" href="register.php">Register</a></li>
            <?php endif; ?>
            <li class="cart">
                <a href="cart.php">
                    <img src="icon-image/cart.png" alt="Cart">
                    <?php if ($cart_item_count > 0): ?>
                        <div class="cart-count"><?php echo $cart_item_count; ?></div>
                    <?php endif; ?>
                </a>
                <div class="cart-preview" id="cart-preview">
                <h3>Cart Preview</h3>
                <ul id="cart-items">
                    <!-- Dynamically generated cart items will go here -->
                </ul>
                <?php if ($cart_item_count > 0): ?>
                <a href="cart.php" class="view-cart">View Cart</a>
                <?php else: ?>
                <a href="product.php" class="view-cart">Browse our product</a>
                <?php endif; ?>
                </div>
            </li>
            <li><p>'\t'</p></li>
        </div>
    </ul>
</nav>



    <!-- background Section -->
    <section class="background">
        <div class="background-overlay"></div>
        <div>
            <h1>It’s gym season, Gymrat!</h1>
            <p>Ready for the next step?</p>
            <button class="browse"><a href="product.php">Shop this sale </a></button>
        </div>
    </section>

   
            
    <!-- Close the database connection -->
    <?php $conn->close(); ?>
    <script src="cart.js"></script>

</body>
</html>
