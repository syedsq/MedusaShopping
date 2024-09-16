<?php
session_start();
require 'config.php';

$product_id = $_POST['product_id'];
$new_quantity = $_POST['quantity'];
$session_id = session_id();

// Update the cart quantity
$update_cart = $conn->prepare("UPDATE cart SET quantity = ? WHERE product_id = ? AND session_id = ?");
$update_cart->bind_param("iis", $new_quantity, $product_id, $session_id);
$update_cart->execute();

header('Location: cart.php');
?>
