// config/api-config.js

// Pego a URL completa da página onde estou agora
const fullURL = window.location.href;
let phpPath = "";

// Lógica para descobrir onde está a pasta '/php' dependendo de onde estou navegando
if (fullURL.includes("/admin/")) {
  // Se estou no painel admin, preciso subir um nível para achar a pasta php
  const baseURL = fullURL.substring(0, fullURL.lastIndexOf("/admin/"));
  phpPath = baseURL + "/php";
} else if (fullURL.includes("/php/")) {
  // Se já estou dentro da pasta php, pego o caminho até aqui
  phpPath = fullURL.substring(0, fullURL.lastIndexOf("/php/")) + "/php";
} else {
  // Se estou na raiz (index.php), pego a base e adiciono /php
  let rootPath = fullURL.substring(0, fullURL.lastIndexOf("/"));
  // Se tiver barra no final, removo para não duplicar
  if (rootPath.endsWith("/")) rootPath = rootPath.slice(0, -1);
  phpPath = rootPath + "/php";
}

// Limpo a URL caso tenha parâmetros (?id=1) ou âncoras (#) sobrando
phpPath = phpPath.split("?")[0].split("#")[0];

// Exporto a configuração para usar nos outros arquivos JS
window.API_CONFIG = {
  baseURL: phpPath, // Caminho correto da API

  // Lista de atalhos para os arquivos que mais uso
  endpoints: {
    login: "/login.php",
    register: "/register_logic.php",
    contact: "/process_contact.php",
    cart: "/cart.php",
  },
};
