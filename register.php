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
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);  // Hash the password for security
    
    // Insert user data into the users table
    $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $username, $email, $password);
    
    if ($stmt->execute()) {
        echo "Registration successful! You can now <a href='login.php'>login</a>.";
    } else {
        echo "Error: " . $stmt->error;
    }
    
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="CSS/loginStyles.css">
    <style>
        <?php include 'CSS/styles.css'; ?>   
        .border {
        
            position: relative;
            text-wrap: wrap;
            width: 400px;
            height: 550px;
            
            /*background-image:linear-gradient(-225deg, #fafde3 50%, #ffe6e6 50%);
            */
            background: transparent;
            backdrop-filter: blur(20px);
            
            /*background: transparent;*/
            border: 2px solid rgba(255, 255,255 ,0.5);
            border-radius: 20px;
            display: flex;
            justify-content: center;
            align-items: center;}   
    </style>
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
                    <li><a class="NavLogin" href="login.php"><img class="login-icon" src="icon-image/login.png" alt="Login Icon" style= "vertical-align: middle">Login</a></li>
                    <li><a class="NavRegister" href="register.php">Register</a></li>
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
            <h2>Register a new account</h2>
            <form action="login.php" method="POST">
                <div class ="input-box">
                    <span class="icon"></span>
                    <input type = "email" name="email" required>
                    <label> Email</label>
                </div>
                <div class ="input-box">
                    <span class="icon"></span>
                    <input type ="text" name="username" required>
                    <label>Username</label>

                <div class ="input-box">
                    <span class="icon"></span>
                    <input type ="password" name="password" required >
                    <label>Password</label>
                </div>
                
                <button type="submit" class="btn">Register</button>
                <div class ="login-register">
                    <p>Already have an account?<a href="login.php" class="register-link"> Sign-in here</a></p>
                </div>
            </form>
            
        </div>
    </div>

    <div class="form-container">

    <script src="JavaScript/cart.js"></script>
</body>
</html>
