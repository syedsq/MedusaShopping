<?php
session_start();
include 'config.php';  // Database connection

// Check if product_id and quantity are passed
if (isset($_POST['product_id']) && isset($_POST['quantity'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    // Fetch product details from the database
    $sql = "SELECT id, name, price FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    
    if ($product) {
        // Store the product in the session cart
        if (isset($_SESSION['cart'][$product_id])) {
            // Update quantity if product already in cart
            $_SESSION['cart'][$product_id]['quantity'] += $quantity;
        } else {
            // Add product to cart if not already added
            $_SESSION['cart'][$product_id] = [
                'name' => $product['name'],
                'price' => $product['price'],
                'quantity' => $quantity
            ];
        }
        
        // Redirect back to the homepage (index.php) after adding to cart
        header("Location: product.php");
        exit();
    } else {
        echo "Product not found.";
    }

    $stmt->close();
    $conn->close();
} else {
    if (isset($_POST['product_id'])) {
        echo "Invalid Product id";
    }
    if (isset($_POST['quantity'])) {
        echo "Error quantity.";
    }else {
        echo "no idea";
    }
}
?>
