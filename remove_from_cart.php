<?php
session_start();
require 'config.php';

$product_id = $_POST['product_id'];
$session_id = session_id();

// Remove product from the cart
$remove_item = $conn->prepare("DELETE FROM cart WHERE product_id = ? AND session_id = ?");
$remove_item->bind_param("is", $product_id, $session_id);
$remove_item->execute();

header('Location: cart.php');
?>
