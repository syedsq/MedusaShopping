const toggleButton = document.querySelector('.toggle-button');
const navItems = document.querySelector('.nav-items');

toggleButton.addEventListener('click', () => {
    navItems.classList.toggle('active');
});