<?php
session_start();
require 'config.php';

// Get parameters from POST request
$product_id = $_POST['product_id'];
$new_quantity = $_POST['quantity'];
$session_id = session_id();

// Step 1: Get the current quantity in the cart (session)
$current_quantity = 0;

// Fetch the current quantity in the cart for the product
$query = $conn->prepare("SELECT quantity FROM cart WHERE product_id = ? AND session_id = ?");
$query->bind_param("is", $product_id, $session_id);
$query->execute();
$query->bind_result($current_quantity);
$query->fetch();
$query->close();

// Step 2: Compare the new quantity with the current quantity
if ($new_quantity > $current_quantity) {
    // Case 1: New quantity is greater than the current quantity
    $quantity_diff = $new_quantity - $current_quantity;
    
    // Update session cart with new quantity
    $_SESSION['cart'][$product_id]['quantity'] = $new_quantity;

    // Update the database: reduce product stock by the difference
    $update_cart = $conn->prepare("UPDATE cart SET quantity = ? WHERE product_id = ? AND session_id = ?");
    $update_cart->bind_param("iis", $new_quantity, $product_id, $session_id);
    $update_cart->execute();

    // Here, you would also update the product stock based on the difference if needed
    // Example: Reduce stock by $quantity_diff in the products table
    $reduce_stock = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
    $reduce_stock->bind_param("ii", $quantity_diff, $product_id);
    $reduce_stock->execute();
    
} elseif ($new_quantity < $current_quantity) {
    // Case 2: New quantity is less than the current quantity
    $quantity_diff = $current_quantity - $new_quantity;
    
    // Update session cart with new quantity
    $_SESSION['cart'][$product_id]['quantity'] = $new_quantity;

    // Update the database: increase product stock by the difference
    $update_cart = $conn->prepare("UPDATE cart SET quantity = ? WHERE product_id = ? AND session_id = ?");
    $update_cart->bind_param("iis", $new_quantity, $product_id, $session_id);
    $update_cart->execute();

    // Here, you would also update the product stock based on the difference if needed
    // Example: Increase stock by $quantity_diff in the products table
    $increase_stock = $conn->prepare("UPDATE products SET stock = stock + ? WHERE id = ?");
    $increase_stock->bind_param("ii", $quantity_diff, $product_id);
    $increase_stock->execute();
}

// Redirect back to the cart page after updating
header('Location: cart.php');
exit();
?>
