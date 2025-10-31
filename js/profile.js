document.addEventListener("DOMContentLoaded", function () {
  if (window.location.search.includes("tab=wishlist")) {
    loadWishlist();
  }

  const navItems = document.querySelectorAll(".nav-item");
  navItems.forEach((item) => {
    item.addEventListener("click", function () {
      const href = this.getAttribute("href");
      if (href && href.includes("tab=wishlist")) {
        setTimeout(loadWishlist, 100);
      }
    });
  });
});

function loadWishlist() {
  const wishlistContent = document.getElementById("wishlist-content");
  if (!wishlistContent) return;

  wishlistContent.innerHTML = `
        <div class="loading-state">
            <div class="loading-spinner"></div>
            <p>Загрузка избранного...</p>
        </div>
    `;

  let actionUrl;
  if (window.location.pathname.includes("/pages/")) {
    actionUrl = "../includes/wishlist_actions.php";
  } else {
    actionUrl = "includes/wishlist_actions.php";
  }

  console.log("Loading wishlist from:", actionUrl);

  const formData = new FormData();
  formData.append("action", "get_wishlist");

  fetch(actionUrl, {
    method: "POST",
    body: formData,
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      return response.json();
    })
    .then((products) => {
      console.log("Wishlist products:", products);

      if (!products || products.length === 0) {
        wishlistContent.innerHTML = `
                <div class="empty-state">
                    <div class="empty-icon">❤️</div>
                    <h3>В избранном пока пусто</h3>
                    <p>Добавляйте товары в избранное, чтобы не потерять их</p>
                    <a href="products.php" class="btn btn-primary">Перейти к покупкам</a>
                </div>
            `;
        return;
      }

      let html = `<div class="wishlist-grid">`;

      products.forEach((product) => {
        let imagePath;
        if (window.location.pathname.includes("/pages/")) {
          imagePath = product.image
            ? "../images/" + product.image
            : "../images/placeholder.png";
        } else {
          imagePath = product.image
            ? "images/" + product.image
            : "images/placeholder.png";
        }

        html += `
                <div class="wishlist-item" data-product-id="${product.id}">
                    <div class="wishlist-item-image">
                        <img src="${imagePath}" 
                            alt="${product.name}"
                            onerror="this.src='${
                              window.location.pathname.includes("/pages/")
                                ? "../images/placeholder.png"
                                : "images/placeholder.png"
                            }'">
                    </div>
                    <div class="wishlist-item-info">
                        <h4>${product.name}</h4>
                        <p class="product-category">${product.category_name}</p>
                        <p class="wishlist-item-price">${formatPrice(
                          product.price
                        )} ₽</p>
                        <div class="wishlist-item-stock ${
                          product.stock > 0 ? "in-stock" : "out-of-stock"
                        }">
                            ${
                              product.stock > 0
                                ? "✓ В наличии"
                                : "✗ Нет в наличии"
                            }
                        </div>
                        <div class="wishlist-item-actions">
                            <a href="product.php?id=${
                              product.id
                            }" class="btn btn-outline">Подробнее</a>
                            <button class="btn btn-primary" onclick="addToCart(${
                              product.id
                            })" ${product.stock <= 0 ? "disabled" : ""}>
                                В корзину
                            </button>
                            <button class="btn btn-remove" onclick="removeFromWishlist(${
                              product.id
                            })" title="Удалить из избранного">
                                🗑️ Удалить
                            </button>
                        </div>
                    </div>
                </div>
            `;
      });

      html += `</div>`;
      wishlistContent.innerHTML = html;
    })
    .catch((error) => {
      console.error("Error loading wishlist:", error);
      wishlistContent.innerHTML = `
            <div class="error-state">
                <div class="error-icon">⚠️</div>
                <h3>Ошибка при загрузке избранного</h3>
                <p>${error.message}</p>
                <button onclick="loadWishlist()" class="btn btn-primary">Попробовать снова</button>
            </div>
        `;
    });
}

function formatPrice(price) {
  return price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
}

function validateProfileForm() {
  const name = document.getElementById("name").value.trim();
  const email = document.getElementById("email").value.trim();
  const newPassword = document.getElementById("new_password").value;
  const currentPassword = document.getElementById("current_password").value;

  if (name.length < 2) {
    showNotification("Имя должно содержать минимум 2 символа", "error");
    return false;
  }

  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(email)) {
    showNotification("Введите корректный email", "error");
    return false;
  }

  if (newPassword && newPassword.length < 6) {
    showNotification(
      "Новый пароль должен содержать минимум 6 символов",
      "error"
    );
    return false;
  }

  if (newPassword && !currentPassword) {
    showNotification("Для смены пароля введите текущий пароль", "error");
    return false;
  }

  return true;
}

document.addEventListener("DOMContentLoaded", function () {
  const profileForm = document.querySelector(".profile-form");
  if (profileForm) {
    profileForm.addEventListener("submit", function (e) {
      if (!validateProfileForm()) {
        e.preventDefault();
      }
    });
  }
});

function addToCart(productId) {
    const formData = new FormData();
    formData.append('action', 'add_to_cart');
    formData.append('product_id', productId);

    let actionUrl;
    if (window.location.pathname.includes('/pages/')) {
        actionUrl = '../includes/cart_actions.php';
    } else {
        actionUrl = 'includes/cart_actions.php';
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
            showNotification('Ошибка при добавлении в корзину', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Ошибка при добавлении в корзину', 'error');
    });
}

function removeFromWishlist(productId) {
    const formData = new FormData();
    formData.append('action', 'remove_from_wishlist');
    formData.append('product_id', productId);

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
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message || 'Товар удален из избранного');
            loadWishlist();
        } else {
            showNotification('Ошибка при удалении из избранного', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Ошибка при удалении из избранного', 'error');
    });
}

function updateCartCounter(count = null) {
    if (count === null) {
        const formData = new FormData();
        formData.append('action', 'get_cart_count');
        
        let actionUrl;
        if (window.location.pathname.includes('/pages/')) {
            actionUrl = '../includes/cart_actions.php';
        } else {
            actionUrl = 'includes/cart_actions.php';
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

function displayCartCounter(count) {
    const counters = document.querySelectorAll('.cart-counter');
    counters.forEach(counter => {
        counter.textContent = `(${count})`;
    });

    if (counters.length === 0) {
        const cartLinks = document.querySelectorAll('a[href*="cart.php"]');
        cartLinks.forEach(link => {
            let counter = link.querySelector('.cart-counter');
            if (!counter) {
                counter = document.createElement('span');
                counter.className = 'cart-counter';
                counter.style.cssText = 'color: #e74c3c; font-weight: bold; margin-left: 0.25rem;';
                link.appendChild(counter);
            }
            counter.textContent = `(${count})`;
        });
    }
}

function showNotification(message, type = 'success') {
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(notification => notification.remove());

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
        z-index: 10000;
        animation: slideIn 0.3s ease;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    `;

    document.body.appendChild(notification);

    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}
