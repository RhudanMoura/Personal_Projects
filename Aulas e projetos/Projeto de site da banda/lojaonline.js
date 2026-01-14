document.addEventListener("DOMContentLoaded", function () {
  // --- Funções utilitárias ---
  function escapeHtml(str) {
    if (!str) return "";
    return String(str)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#39;");
  }

  // --- Adicionar ao carrinho via fetch para add_to_cart.php ---
  window.adicionarCarrinho = async function (event, productId) {
    event.preventDefault && event.preventDefault();

    try {
      const qtyInput = document.getElementById("qty-" + productId);
      const qty = Math.max(1, parseInt(qtyInput ? qtyInput.value : 1, 10));

      const formData = new FormData();
      formData.append("product_id", productId);
      formData.append("qty", qty);

      const res = await fetch("add_to_cart.php", {
        method: "POST",
        body: formData,
        credentials: "same-origin",
      });

      // tenta parsear JSON
      let data;
      try {
        data = await res.json();
      } catch (err) {
        throw new Error("Resposta inválida do servidor.");
      }

      if (!res.ok || !data.success) {
        const message =
          data && data.message ? data.message : "Erro ao adicionar o produto.";
        showMessageModal("Erro", message);
        return false;
      }

      // atualiza conteúdo do modal de confirmação
      const item = data.item;
      const confirmMessageEl = document.getElementById("confirm-message");
      if (confirmMessageEl) {
        confirmMessageEl.innerHTML = `
          <div class="d-flex align-items-center">
            <img src="${escapeHtml(item.image)}" alt="${escapeHtml(item.name)}"
                 style="width:64px;height:64px;object-fit:cover;border-radius:6px;margin-right:12px;">
            <div>
              <strong>${escapeHtml(item.name)}</strong><br>
              ${qty} unidade(s) adicionada(s)
            </div>
          </div>
        `;
      }

      // atualiza badge do carrinho (se existir)
      const badge = document.getElementById("cart-count");
      if (badge && typeof data.cartCount !== "undefined") {
        badge.textContent = data.cartCount;
      }

      // mostra o modal de confirmação (Bootstrap)
      const modalEl = document.getElementById("confirmModal");
      if (modalEl) bootstrap.Modal.getOrCreateInstance(modalEl).show();
    } catch (err) {
      console.error(err);
      showMessageModal(
        "Erro",
        "Não foi possível adicionar o produto. Tente novamente."
      );
    }
    return false;
  };

  // --- Modal de mensagem dinâmica (para erros ou alertas) ---
  function showMessageModal(title, message) {
    let el = document.getElementById("messageModal");
    if (!el) {
      el = document.createElement("div");
      el.innerHTML = `
      <div class="modal fade" id="messageModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">${escapeHtml(title)}</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <p>${escapeHtml(message)}</p>
            </div>
            <div class="modal-footer">
              <button class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
          </div>
        </div>
      </div>`;
      document.body.appendChild(el);
    } else {
      el.querySelector(".modal-title").textContent = title;
      el.querySelector(".modal-body p").textContent = message;
    }
    bootstrap.Modal.getOrCreateInstance(
      document.getElementById("messageModal")
    ).show();
  }

  // --- Cálculo de pedido (se tiver o formulário de cálculo existente) ---
  const calculateBtn = document.getElementById("calculate");
  if (calculateBtn) {
    calculateBtn.addEventListener("click", function () {
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
  }

  // --- Lightbox: fechar quando existir um botão .close-btn ---
  const closeBtn = document.querySelector(".close-btn");
  if (closeBtn) {
    closeBtn.addEventListener("click", function () {
      const lb = document.getElementById("lightbox");
      if (lb) lb.style.display = "none";
    });
  }
}); // DOMContentLoaded
