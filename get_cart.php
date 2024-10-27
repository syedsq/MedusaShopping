<?php
session_start();

// Check if there are items in the cart
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $cart_items = $_SESSION['cart'];
    $response = [];

    foreach ($cart_items as $id => $item) {
        $response[] = [
            'name' => $item['name'],
            'quantity' => $item['quantity'],
            'price' => $item['price']
        ];
    }

    // Return the cart items as JSON
    echo json_encode($response);
} else {
    // Return an empty array if there are no items
    echo json_encode([]);
}
?>
