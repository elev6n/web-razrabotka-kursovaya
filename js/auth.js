// Валидация формы регистрации
function validateRegisterForm() {
  let isValid = true;

  // Очищаем предыдущие ошибки
  clearErrors();

  // Валидация имени
  const name = document.getElementById("name");
  if (name.value.trim().length < 2) {
    showError("name-error", "Имя должно содержать минимум 2 символа");
    name.classList.add("error");
    isValid = false;
  }

  // Валидация email
  const email = document.getElementById("email");
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(email.value)) {
    showError("email-error", "Введите корректный email");
    email.classList.add("error");
    isValid = false;
  }

  // Валидация пароля
  const password = document.getElementById("password");
  if (password.value.length < 6) {
    showError("password-error", "Пароль должен содержать минимум 6 символов");
    password.classList.add("error");
    isValid = false;
  }

  // Валидация подтверждения пароля
  const confirmPassword = document.getElementById("confirm_password");
  if (password.value !== confirmPassword.value) {
    showError("confirm-password-error", "Пароли не совпадают");
    confirmPassword.classList.add("error");
    isValid = false;
  }

  return isValid;
}

// Валидация формы входа
function validateLoginForm() {
  let isValid = true;

  // Очищаем предыдущие ошибки
  clearErrors();

  // Валидация email
  const email = document.getElementById("email");
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(email.value)) {
    showError("email-error", "Введите корректный email");
    email.classList.add("error");
    isValid = false;
  }

  // Валидация пароля
  const password = document.getElementById("password");
  if (password.value.length === 0) {
    showError("password-error", "Введите пароль");
    password.classList.add("error");
    isValid = false;
  }

  return isValid;
}

// Функция для показа ошибки
function showError(elementId, message) {
  const errorElement = document.getElementById(elementId);
  if (errorElement) {
    errorElement.textContent = message;
  }
}

// Функция для очистки ошибок
function clearErrors() {
  const errorMessages = document.querySelectorAll(".error-message");
  errorMessages.forEach((element) => {
    element.textContent = "";
  });

  const errorInputs = document.querySelectorAll(".error");
  errorInputs.forEach((element) => {
    element.classList.remove("error");
  });
}

// Реальная валидация при вводе
document.addEventListener("DOMContentLoaded", function () {
  // Для формы регистрации
  const registerForm = document.querySelector(
    'form[onsubmit="return validateRegisterForm()"]'
  );
  if (registerForm) {
    setupRealTimeValidation();
  }
});

function setupRealTimeValidation() {
  const nameInput = document.getElementById("name");
  const emailInput = document.getElementById("email");
  const passwordInput = document.getElementById("password");
  const confirmPasswordInput = document.getElementById("confirm_password");

  if (nameInput) {
    nameInput.addEventListener("blur", validateName);
  }
  if (emailInput) {
    emailInput.addEventListener("blur", validateEmail);
  }
  if (passwordInput) {
    passwordInput.addEventListener("blur", validatePassword);
  }
  if (confirmPasswordInput) {
    confirmPasswordInput.addEventListener("blur", validateConfirmPassword);
  }
}

function validateName() {
  const name = document.getElementById("name");
  if (name.value.trim().length < 2) {
    showError("name-error", "Имя должно содержать минимум 2 символа");
    name.classList.add("error");
    return false;
  } else {
    showError("name-error", "");
    name.classList.remove("error");
    return true;
  }
}

function validateEmail() {
  const email = document.getElementById("email");
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(email.value)) {
    showError("email-error", "Введите корректный email");
    email.classList.add("error");
    return false;
  } else {
    showError("email-error", "");
    email.classList.remove("error");
    return true;
  }
}

function validatePassword() {
  const password = document.getElementById("password");
  if (password.value.length < 6) {
    showError("password-error", "Пароль должен содержать минимум 6 символов");
    password.classList.add("error");
    return false;
  } else {
    showError("password-error", "");
    password.classList.remove("error");
    return true;
  }
}

function validateConfirmPassword() {
  const password = document.getElementById("password");
  const confirmPassword = document.getElementById("confirm_password");
  if (password.value !== confirmPassword.value) {
    showError("confirm-password-error", "Пароли не совпадают");
    confirmPassword.classList.add("error");
    return false;
  } else {
    showError("confirm-password-error", "");
    confirmPassword.classList.remove("error");
    return true;
  }
}
