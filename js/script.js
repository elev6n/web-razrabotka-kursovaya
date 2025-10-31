function addToCart(productId, quantity = 1) {
    const button = event?.target;
    if (button) {
        const originalText = button.innerHTML;
        button.innerHTML = 'Добавляем...';
        button.disabled = true;
        
        setTimeout(() => {
            button.innerHTML = originalText;
            button.disabled = false;
        }, 1000);
    }

    const formData = new FormData();
    formData.append('action', 'add_to_cart');
    formData.append('product_id', productId);
    formData.append('quantity', quantity);

    let actionUrl = 'includes/cart_actions.php';
    if (window.location.pathname.includes('/pages/')) {
        actionUrl = '../includes/cart_actions.php';
    }

    fetch(actionUrl, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateCartCounter(data.cart_count);
            showNotification(data.message || 'Товар добавлен в корзину!');
        } else {
            showNotification(data.error || 'Ошибка при добавлении в корзину', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Ошибка при добавлении в корзину', 'error');
    });
}

function updateCartCounter(count = null) {
    if (count === null) {
        const formData = new FormData();
        formData.append('action', 'get_cart_count');
        
        let actionUrl = 'includes/cart_actions.php';
        if (window.location.pathname.includes('/pages/')) {
            actionUrl = '../includes/cart_actions.php';
        }
        
        fetch(actionUrl, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayCartCounter(data.cart_count);
            }
        })
        .catch(error => {
            console.error('Error getting cart count:', error);
        });
    } else {
        displayCartCounter(count);
    }
}

function syncLocalStorageWithServer() {
    const formData = new FormData();
    formData.append('action', 'get_cart_count');
    
    let actionUrl = 'includes/cart_actions.php';
    if (window.location.pathname.includes('/pages/')) {
        actionUrl = '../includes/cart_actions.php';
    }
    
    fetch(actionUrl, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            localStorage.setItem('cart_count', data.cart_count.toString());
        }
    });
}

function displayCartCounter(count) {
  const counters = document.querySelectorAll(".cart-counter");
  counters.forEach((counter) => {
    counter.textContent = `(${count})`;
  });

  if (counters.length === 0) {
    const cartLinks = document.querySelectorAll('a[href*="cart.php"]');
    cartLinks.forEach((link) => {
      let counter = link.querySelector(".cart-counter");
      if (!counter) {
        counter = document.createElement("span");
        counter.className = "cart-counter";
        counter.style.cssText =
          "color: #e74c3c; font-weight: bold; margin-left: 0.25rem;";
        link.appendChild(counter);
      }
      counter.textContent = `(${count})`;
    });
  }
}

function showNotification(message, type = "success", duration = 3000) {
  // Удаляем существующие уведомления
  const existingNotifications = document.querySelectorAll(".notification");
  existingNotifications.forEach((notification) => {
    notification.remove();
  });

  const notification = document.createElement("div");
  notification.className = `notification notification-${type}`;
  notification.innerHTML = `
        <span>${message}</span>
        <button class="notification-close" onclick="this.parentElement.remove()">×</button>
    `;

  notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${getNotificationColor(type)};
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        z-index: 10000;
        display: flex;
        align-items: center;
        gap: 1rem;
        animation: slideInRight 0.3s ease;
        max-width: 400px;
    `;

  const closeBtn = notification.querySelector(".notification-close");
  closeBtn.style.cssText = `
        background: none;
        border: none;
        color: white;
        font-size: 1.2rem;
        cursor: pointer;
        padding: 0;
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
    `;

  document.body.appendChild(notification);

  // Автоматическое удаление
  setTimeout(() => {
    if (notification.parentElement) {
      notification.style.animation = "slideOutRight 0.3s ease";
      setTimeout(() => notification.remove(), 300);
    }
  }, duration);
}

function getNotificationColor(type) {
  const colors = {
    success: "#27ae60",
    error: "#e74c3c",
    warning: "#f39c12",
    info: "#3498db",
  };
  return colors[type] || colors.info;
}

const style = document.createElement("style");
style.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

// Функции для работы с избранным
function addToWishlist(productId) {
    const formData = new FormData();
    formData.append('action', 'add_to_wishlist');
    formData.append('product_id', productId);

    // Определяем правильный путь
    let actionUrl = 'includes/wishlist_actions.php';
    if (window.location.pathname.includes('/pages/')) {
        actionUrl = '../includes/wishlist_actions.php';
    }

    fetch(actionUrl, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message || 'Товар добавлен в избранное!');
        } else {
            showNotification(data.message || 'Товар уже в избранном', 'info');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Ошибка при добавлении в избранное', 'error');
    });
}

// Функции для работы с избранным
function addToWishlist(productId) {
    const formData = new FormData();
    formData.append('action', 'add_to_wishlist');
    formData.append('product_id', productId);

    // Исправляем определение пути
    let actionUrl;
    if (window.location.pathname.includes('/pages/')) {
        actionUrl = '../includes/wishlist_actions.php';
    } else {
        actionUrl = 'includes/wishlist_actions.php';
    }

    fetch(actionUrl, {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showNotification(data.message || 'Товар добавлен в избранное!');
        } else {
            showNotification(data.message || 'Товар уже в избранном', 'info');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Ошибка при добавлении в избранное', 'error');
    });
}

function removeFromWishlist(productId) {
    const formData = new FormData();
    formData.append('action', 'remove_from_wishlist');
    formData.append('product_id', productId);

    // Исправляем определение пути
    let actionUrl;
    if (window.location.pathname.includes('/pages/')) {
        actionUrl = '../includes/wishlist_actions.php';
    } else {
        actionUrl = 'includes/wishlist_actions.php';
    }

    fetch(actionUrl, {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showNotification(data.message || 'Товар удален из избранного');
            // Обновляем список избранного
            if (window.location.search.includes('tab=wishlist')) {
                loadWishlist();
            }
        } else {
            showNotification('Ошибка при удалении из избранного', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Ошибка при удалении из избранного', 'error');
    });
}