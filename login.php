<?php
session_start();
include 'config.php';  // Include database connection

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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        .textbox {
            width: 90%;    
        }
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .form-container {
            background-color: #fff;
            padding: 20px; /* Increased padding */
            margin: 20px; /* Add margin */
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin: 10px 0 5px;
        }
        input[type="email"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        input[type="submit"] {
            background-color: #28a745;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            transition: background-color 0.3s;
        }
        input[type="submit"]:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    
    <div class="form-container">
        <h2>Login</h2>
        <form action="login.php" method="POST">
            <label>Email:</label>
            <input type="email" name="email" required>

            <label>Password:</label>
            <input class="textbox" type="password" name="password" required>

            <input class="textbox" type="submit" value="Login">
        </form>
        <p style="text-align:center;">Don't have an account? <a href="register.php">Register here</a></p>
    </div>
</body>
</html>
