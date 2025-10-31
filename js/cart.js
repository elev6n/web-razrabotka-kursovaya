function updateQuantity(productId, change) {
  const input = document.querySelector(
    `.cart-item[data-product-id="${productId}"] .quantity-input`
  );
  let newQuantity = parseInt(input.value) + change;

  const maxStock = parseInt(input.max);
  newQuantity = Math.max(1, Math.min(newQuantity, maxStock));

  input.value = newQuantity;
  updateCartItem(productId, newQuantity);
}

function updateQuantityInput(productId, quantity) {
  quantity = parseInt(quantity);
  const maxStock = parseInt(
    document.querySelector(
      `.cart-item[data-product-id="${productId}"] .quantity-input`
    ).max
  );

  if (quantity < 1) quantity = 1;
  if (quantity > maxStock) quantity = maxStock;

  updateCartItem(productId, quantity);
}

function updateCartItem(productId, quantity) {
  const formData = new FormData();
  formData.append("action", "update_quantity");
  formData.append("product_id", productId);
  formData.append("quantity", quantity);

  fetch("../includes/cart_actions.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        updateCartItemInUI(productId, quantity, data.cart_count);
        showNotification("Количество обновлено");
      } else {
        showNotification("Ошибка при обновлении корзины", "error");
        window.location.reload();
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      showNotification("Ошибка при обновлении корзины", "error");
      window.location.reload();
    });
}

function updateCartItemInUI(productId, quantity, cartCount) {
  const cartItem = document.querySelector(
    `.cart-item[data-product-id="${productId}"]`
  );
  if (cartItem) {
    const priceElement = cartItem.querySelector(".product-price");
    const itemTotalElement = cartItem.querySelector(".item-total");

    if (priceElement && itemTotalElement) {
      const price = parseInt(priceElement.textContent.replace(/[^\d]/g, ""));
      const itemTotal = price * quantity;
      itemTotalElement.textContent = `${itemTotal.toLocaleString("ru-RU")} ₽`;
    }

    recalculateCartTotal();

    updateCartCounter(cartCount);
  }
}

function removeFromCart(productId) {
  if (!confirm("Удалить товар из корзины?")) {
    return;
  }

  const formData = new FormData();
  formData.append("action", "remove_item");
  formData.append("product_id", productId);

  fetch("../includes/cart_actions.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        const cartItem = document.querySelector(
          `.cart-item[data-product-id="${productId}"]`
        );
        if (cartItem) {
          cartItem.style.transform = "translateX(100%)";
          cartItem.style.opacity = "0";

          setTimeout(() => {
            cartItem.remove();
            checkEmptyCart(data.cart_count);
            showNotification("Товар удален из корзины");
          }, 300);
        }
      } else {
        showNotification("Ошибка при удалении товара", "error");
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      showNotification("Ошибка при удалении товара", "error");
    });
}

function clearCart() {
  if (!confirm("Очистить всю корзину?")) {
    return;
  }

  const formData = new FormData();
  formData.append("action", "clear_cart");

  fetch("../includes/cart_actions.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        const cartItems = document.querySelectorAll(".cart-item");
        cartItems.forEach((item, index) => {
          setTimeout(() => {
            item.style.transform = "translateX(100%)";
            item.style.opacity = "0";
          }, index * 100);
        });

        setTimeout(() => {
          checkEmptyCart(0);
          showNotification("Корзина очищена");
        }, cartItems.length * 100 + 300);
      } else {
        showNotification("Ошибка при очистке корзины", "error");
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      showNotification("Ошибка при очистке корзины", "error");
    });
}

function checkEmptyCart(cartCount) {
  const cartContent = document.querySelector(".cart-content");
  const emptyCart = document.querySelector(".empty-cart");

  if (cartCount === 0 && cartContent && emptyCart) {
    cartContent.style.display = "none";
    emptyCart.style.display = "block";
  }

  updateCartCounter(cartCount);
  recalculateCartTotal();
}

function recalculateCartTotal() {
  let totalItems = 0;
  let totalPrice = 0;

  const cartItems = document.querySelectorAll(".cart-item");

  cartItems.forEach((item) => {
    const priceElement = item.querySelector(".product-price");
    const quantityElement = item.querySelector(".quantity-input");

    if (priceElement && quantityElement) {
      const price = parseInt(priceElement.textContent.replace(/[^\d]/g, ""));
      const quantity = parseInt(quantityElement.value);
      totalItems += quantity;
      totalPrice += price * quantity;
    }
  });

  updateCartSummary(totalItems, totalPrice);
}

function updateCartSummary(totalItems, totalPrice) {
  const itemsCountElement = document.querySelector(
    ".summary-row:first-child span:first-child"
  );
  if (itemsCountElement) {
    itemsCountElement.textContent = `Товары (${totalItems} шт.)`;
  }

  const itemsPriceElements = document.querySelectorAll(
    ".summary-row:first-child span:last-child"
  );
  itemsPriceElements.forEach((element) => {
    element.textContent = `${totalPrice.toLocaleString("ru-RU")} ₽`;
  });

  const totalPriceElements = document.querySelectorAll(
    ".summary-row.total span:last-child"
  );
  totalPriceElements.forEach((element) => {
    element.textContent = `${totalPrice.toLocaleString("ru-RU")} ₽`;
  });
}

document.addEventListener("DOMContentLoaded", function () {
  updateCartCounter();
});
