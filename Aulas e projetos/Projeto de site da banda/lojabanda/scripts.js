document.addEventListener("DOMContentLoaded", function () {
  let catalog = {};

  fetch("catalog.json")
    .then((response) => response.json())
    .then((data) => {
      catalog = data;
      carregarCategorias(data.categories);
      carregarProdutos(data.products);
      preencherSelectProdutos(data.products);
    });

  function carregarCategorias(categorias) {
    const select = document.getElementById("category-filter");
    categorias.forEach((cat) => {
      const option = document.createElement("option");
      option.value = cat.id;
      option.textContent = cat.name;
      select.appendChild(option);
    });
  }

  function carregarProdutos(produtos, categoria = "") {
    const lista = document.getElementById("product-list");
    lista.innerHTML = "";

    const filtrados = categoria
      ? produtos.filter((p) => p.category === categoria)
      : produtos;

    filtrados.forEach((prod) => {
      const card = document.createElement("div");
      card.classList.add("product-card");
      card.innerHTML = `
        <img src="${prod.image}" alt="${prod.name}">
        <h3>${prod.name}</h3>
        <p>Preço: €${prod.price}</p>
        <button onclick="abrirLightbox('${prod.name}', '${prod.image}', '${prod.description}', '${prod.price}')">Ver Detalhes</button>
      `;
      lista.appendChild(card);
    });
  }

  document
    .getElementById("category-filter")
    .addEventListener("change", function () {
      carregarProdutos(catalog.products, this.value);
    });

  function preencherSelectProdutos(produtos) {
    const select = document.getElementById("product");
    produtos.forEach((prod) => {
      const option = document.createElement("option");
      option.value = prod.price;
      option.textContent = prod.name;
      select.appendChild(option);
    });
  }

  document.getElementById("calculate").addEventListener("click", function () {
    const preco = parseFloat(document.getElementById("product").value);
    const quantidade = parseInt(document.getElementById("quantity").value);
    const totalDiv = document.getElementById("total-value");

    if (isNaN(preco) || isNaN(quantidade) || quantidade <= 0) {
      totalDiv.textContent =
        "Por favor selecione um produto e insira uma quantidade válida.";
    } else {
      const total = (preco * quantidade).toFixed(2);
      totalDiv.textContent = `Total: €${total}`;
    }
  });

  window.abrirLightbox = function (titulo, imagem, descricao, preco) {
    document.getElementById("lightbox-title").textContent = titulo;
    document.getElementById("lightbox-image").src = imagem;
    document.getElementById("lightbox-description").textContent = descricao;
    document.getElementById("lightbox-price").textContent = `Preço: €${preco}`;
    document.getElementById("lightbox").style.display = "flex";
  };

  document.querySelector(".close-btn").addEventListener("click", function () {
    document.getElementById("lightbox").style.display = "none";
  });
});
