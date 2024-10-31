<!--backend logic -->
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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the form data
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // Check if the user exists
    $sql = "SELECT id, username, password FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($user_id, $username, $hashed_password);
    
    if ($stmt->fetch() && password_verify($password, $hashed_password)) {
        // Set session and redirect
        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $username;
        header("Location: index.php");  // Redirect to homepage after successful login
        exit();
    } else {
        echo "Invalid email or password.";
    }
    
    $stmt->close();
    $conn->close();
}
?>


 <!--front end -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        <?php include 'CSS/styles.css'; ?>
    </style>
    <link rel="stylesheet" href="CSS/loginStyles.css">
    <!-- Include jQuery and DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>

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
            </div>
        </ul>
    </nav>



    <div class = "border">
        <div class="form-box-login">
            <h2>Login to your account</h2>
            <form action="login.php" method="POST">
                <div class ="input-box">
                    <span class="icon"></span>
                    <input type = "email" name="email" required>
                    <label> Email</label>
                </div>
                <div class ="input-box">
                    <span class="icon"></span>
                    <input type ="password" name="password" required >
                    <label>Password</label>
                </div>
                <div class="remember-forgot">
                    <label><input type= "checkbox">Remember my account</label>
                </div>
                <button type="submit" class="btn">login</button>
                <div class ="login-register">
                    <p>Don't have an account?<a href="register.php" class="register-link"> Register here</a></p>
                </div>
            </form>
            
        </div>
    </div>
</body>

<script src="JavaScript/cart.js"></script>
</html>
