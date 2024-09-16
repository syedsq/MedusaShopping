<?php
session_start();

// Simulate admin login for testing (remove this once login system is in place)
$_SESSION['is_admin'] = true; // Simulated admin login

// Check if the user is an admin
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    echo "Access denied. You must be an admin to access this page.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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

        .admin-options {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }

        .admin-section {
            width: 30%;
            background-color: #007bff;
            color: white;
            padding: 15px;
            text-align: center;
            border-radius: 5px;
            text-decoration: none;
            font-size: 18px;
        }

        .admin-section:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <h1>Admin Dashboard</h1>
        <div class="admin-options">
            <a href="manage_items.php" class="admin-section">Manage Items</a>
            <a href="manage_discount_codes.php" class="admin-section">Manage Discount Codes</a>
            <a href="manage_sales_items.php" class="admin-section">Manage Sales Items</a>
        </div>

        <div class="admin-options">
            <a href="manage_users.php" class="admin-section">Manage Users</a>
            <a href="view_orders.php" class="admin-section">View Orders</a>
        </div>
    </div>
</body>
</html>
