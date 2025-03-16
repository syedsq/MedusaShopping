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
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT id, username, password FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($user_id, $username, $hashed_password);

    if ($stmt->fetch()) {
        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $username;
            header("Location: index.php");
            exit();
        } else {
            $error_message = "Incorrect password. Please try again.";
        }
    } else {
        $error_message = "No account found with that email. Please <a href='register.php'>register</a>.";
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
    <?php include 'navigation_bar.php'; ?>
</head>
<body>
    <!-- Navigation Bar -->
    
    <div class="login-register-operation">                   
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
                <button type="submit" value ="Login" class="btn">Login</button>
                <div class ="login-register">
                    <p>Don't have an account?<a href="register.php" class="register-link"> Register here</a></p>
                </div>
            </form>
        </div>
    </div>
    <?php if (!empty($error_message)): ?>
    <div class="error-message">
        <?php echo $error_message; ?>
    </div>
    <?php endif; ?>
    </div>
</body>
<script src="JavaScript/toggle.js"></script>
<script src="JavaScript/cart.js"></script>
</html>
