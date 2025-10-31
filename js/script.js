function addToCart(productId, quantity = 1) {
  let cart = JSON.parse(localStorage.getItem("cart")) || [];

  const existingItem = cart.find((item) => item.id === productId);

  if (existingItem) {
    existingItem.quantity += quantity;
  } else {
    cart.push({
      id: productId,
      quantity: 1,
    });
  }

  localStorage.setItem("cart", JSON.stringify(cart));
  updateCartCounter();
  showNotification("Товар добавлен в корзину!");
}

function updateCartCounter() {
  const cart = JSON.parse(localStorage.getItem("cart")) || [];
  const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);

  const counters = document.querySelectorAll(".cart-counter");
  counters.forEach(counter => {
    counter.textContent = `(${totalItems})`;
  });

  if (counters.length === 0) {
    const cartLinks = document.querySelectorAll('a[href*="cart.php"]');
    cartLinks.forEach(link => {
      if (!link.querySelector('.cart-counter')) {
        const counter = document.createElement('span');
        counter.className = 'cart-counter';
        counter.textContent = `(${totalItems})`;
        counter.style.cssText = 'color: #e74c3c; font-weight: bold; margin-left: 0.25rem;';
        link.appendChild(counter)
      }
    })
  }
}

function showNotification(message, type = "success") {
  const notification = document.createElement("div");
  notification.className = `notification notification-${type}`;
  notification.textContent = message;
  notification.style.cssText = `
    position: fixed;
    top: 20px;
    right: 20px;
    background: ${type === "success" ? "#2ecc71" : "#e74c3c"};
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

document.addEventListener("DOMContentLoaded", function () {
  updateCartCounter();
});
