document.querySelector("form").addEventListener("submit", function (e) {
  let valid = true;

  // Nome de Utilizador
  let userInput = document.getElementById("username");
  if (userInput.value.trim().length < 3) {
    userInput.classList.add("is-invalid");
    valid = false;
  } else {
    userInput.classList.remove("is-invalid");
    userInput.classList.add("is-valid");
  }

  // Email
  let emailInput = document.getElementById("email");
  let emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailPattern.test(emailInput.value.trim())) {
    emailInput.classList.add("is-invalid");
    valid = false;
  } else {
    emailInput.classList.remove("is-invalid");
    emailInput.classList.add("is-valid");
  }

  // Senha
  let passInput = document.getElementById("password");
  if (passInput.value.length < 6) {
    passInput.classList.add("is-invalid");
    valid = false;
  } else {
    passInput.classList.remove("is-invalid");
    passInput.classList.add("is-valid");
  }

  // Confirmar Senha
  let confirmInput = document.getElementById("confirm_password");
  if (confirmInput.value !== passInput.value || confirmInput.value === "") {
    confirmInput.classList.add("is-invalid");
    valid = false;
  } else {
    confirmInput.classList.remove("is-invalid");
    confirmInput.classList.add("is-valid");
  }

  // Tipo de Utilizador
  let typeInput = document.getElementById("user_type");
  if (typeInput.value === "") {
    typeInput.classList.add("is-invalid");
    valid = false;
  } else {
    typeInput.classList.remove("is-invalid");
    typeInput.classList.add("is-valid");
  }

  // Foto de Perfil
  let profileInput = document.getElementById("profile_pic");
  if (profileInput.files.length === 0) {
    profileInput.classList.add("is-invalid");
    valid = false;
  } else {
    profileInput.classList.remove("is-invalid");
    profileInput.classList.add("is-valid");
  }

  // Se algum campo não for válido, não envia o formulário
  if (!valid) {
    e.preventDefault();
  }
});
