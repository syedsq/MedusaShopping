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
    
    <style>
        
        body{
            display: flex;
            justify-content: center;
            align-items: center;
            background-image: url('https://images.unsplash.com/photo-1534438327276-14e5300c3a48?fm=jpg&q=60&w=3000&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8M3x8Z3ltfGVufDB8fDB8fHww');
            
            background-repeat:no-repeat ;
            background-size: cover;
            background-attachment: fixed;
        }
        <?php include 'CSS/styles.css'; ?>
        
    </style>

    <!-- Include jQuery and DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
    <!-- Navigation Bar -->
    <?php include 'navigation_bar.php'; ?>
</head>
<body class ="main-page-body">
    <!-- background Section -->
    <section class="cardview">
        <div class="cardview-overlay"></div>
        <div class="mainpage-cardview">
            <h1>It’s gym season, Gymrat!</h1>
            <p>Ready for the next step?</p>
            <a class= "browse" href="product.php">Shop this sale </a>
        </div>
        
    </section>

   
            
    <!-- Close the database connection -->
    <?php $conn->close(); ?>
    <script src="JavaScript/cart.js"></script>
    <script src="JavaScript/toggle.js"></script>                   
</body>
</html>
