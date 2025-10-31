function addToCart(productId) {
  let cart = JSON.parse(localStorage.getItem('cart')) || [];

  const existingItem = cart.find(item => item.id === productId);

  if (existingItem) {
    existingItem.quantity += 1;
  } else {
    cart.push({
      id: productId,
      quantity: 1
    });
  }

  localStorage.setItem('cart', JSON.stringify(cart));
  updateCartCounter();
  showNotification('Товар добавлен в корзину!');
}

function updateCartCounter() {
  const cart = JSON.parse(localStorage.getItem('cart')) || [];
  const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);

  let counter = document.querySelector('.cart-counter');
  if (!counter) {
    const nav = document.querySelector('.nav-links');
    const cartLink = Array.from(nav.children).find(li => 
      li.querySelector('a[href*="cart.php"]')
    );

    if (cartLink) {
      counter = document.createElement('span');
      counter.className = 'cart-counter';
      cartLink.appendChild(counter);
    }
  }

  if (counter) {
    counter.textContent = ` (${totalItems})`;
    counter.style.color = '#e74c3c';
    counter.style.fontWeight = 'bold';
  }
}

function showNotification(message, type = 'success') {
  const notification = document.createElement('div');
  notification.className = `notification notification-${type}`;
  notification.textContent = message;
  notification.style.cssText = `
    position: fixed;
    top: 20px;
    right: 20px;
    background: ${type === 'success' ? '#2ecc71' : '#e74c3c'};
    color: white;
    padding: 1rem 2rem;
    border-radius: 4px;
    z-index: 1000,
    animation: slineIn 0.3s ease;
  `;

  document.body.appendChild(notification);

  setTimeout(() => {
    notification.remove();
  }, 3000);
}

document.addEventListener('DOMContentLoaded', function() {
  updateCartCounter();
})