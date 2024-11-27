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
    <title>Profile</title>
    <style>
        <?php include 'CSS/profile.css'; ?>
        <?php include 'CSS/styles.css'; ?>
        body {
            background-repeat:no-repeat ;
            background-size: cover;
            background-attachment: fixed;
            background-image: url('background/user-background1.jpg');
        }
        <?php include 'CSS/styles.css'; ?>

    </style>

    <!-- Navigation Bar -->
    <nav class="navbar">
        
        <ul>
            <!-- Logo on the left -->
            <li class="logo">
                <a class="main_page" href="index.php">
                    <img class="image" src="icon-image/logo.png" alt="Logo">Medusa Gym</a>
            </li>
            <!-- Links on the right -->
            <li class="toggle-button">
                <a href="#">
                    <img class= "image" src="icon-image/toggle-icon.png" alt="toggle" style= "vertical-align: middle">
                </a>
            </li>
            <div class="nav-items">
                <li><a class="NavButton" href="product.php">Browse</a></li>
                <?php if ($is_logged_in): ?>
                    <li><span class="login_welcome">Welcome, <?php echo $_SESSION['username']; ?>!</span></li>
                    <li><a class ="NavUserProfile" href="user-profile.php">My profile</a></li>
                    <li><a class ="NavLogout" href="logout.php">Logout</a></li>
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
</head>

<body>

        
        <div class="user-profile-picture">
            <img class="user-photo" width="150px">
            <span class="user-name">Name</span>
            <a class="display email" href="https://www.youtube.com/watch?v=dQw4w9WgXcQ" >email@gmail.com</a>
        </div>
        
        
        <div class="Profile">
            <div class="title">
                <h4 class="">Profile Settings</h4>
            </div>
            <div class="name">
                <div class="firstname"><label class="labels">Name</label><input type="text" class="form-control" placeholder="First name" value=""></div>
                <div class="lastname"><label class="labels">Surname</label><input type="text" class="form-control" value="" placeholder="Last Name"></div>
            </div>
            <div class="personal-info">
                <div class="col-md-12"><label class="labels">Email</label><input type="email" class="form-control" placeholder="enter email id" value=""></div>
                <div class="phone"><label class="labels">Mobile Number</label><input type="text" class="form-control" placeholder="Enter phone number" value=""></div>
                <div class="address"><label class="labels">Address Line 1</label><input type="text" class="form-control" placeholder="Enter address line" value=""></div>
                <div class="zipcode"><label class="labels">zipcode</label><input type="text" class="form-control" placeholder="Enter the zipcode" value=""></div>
                <div class="city"><label class="labels">city</label><input type="text" class="form-control" placeholder="Enter your city" value=""></div>
            </div>
            <div class="row mt-3">
                <div class="country"><label class="labels">Country</label><input type="text" class="form-control" placeholder="country" value=""></div>
                <div class="state"><label class="labels">State/Region</label><input type="text" class="form-control" value="" placeholder="state"></div>
            </div>
            <div class="update-profile-button">
                <button class="submit" type="submit" ><a href="https://www.youtube.com/watch?v=dQw4w9WgXcQ">Save Profile</a></button>
            </div>
        </div>
        
    
        <div class="past-order">
            <h4 class="">Past order</h4>
            <p>This part display past order</p>
        </div>
    

    
</body>