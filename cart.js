async function updateCartPreview() {
    const response = await fetch('get_cart.php');
    const cartItems = await response.json();

    const cartItemsList = document.getElementById('cart-items');
    cartItemsList.innerHTML = ''; // Clear existing items

    if (cartItems.length > 0) {
        cartItems.forEach(item => {
            const li = document.createElement('li');
            li.textContent = `${item.name} (x${item.quantity}) - $${(item.price * item.quantity).toFixed(2)}`;
            cartItemsList.appendChild(li);
        });
    } else {
        const li = document.createElement('li');
        li.textContent = 'Your cart is empty.';
        cartItemsList.appendChild(li);
    }
}

// Event listener for showing the cart preview
document.querySelector('.cart').addEventListener('mouseenter', updateCartPreview);
