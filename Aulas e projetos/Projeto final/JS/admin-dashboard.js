/* js/admin-dashboard.js
   Explicação: Eu controlo a lógica do painel, incluindo modais, exclusões seguras e filtros.
*/

document.addEventListener("DOMContentLoaded", function () {
  // =========================================================
  // 1. INICIALIZAÇÃO DE MODAIS
  // Eu verifico se os elementos existem no HTML antes de tentar ligá-los.
  // =========================================================

  // Modal de Segurança (O mais importante para apagar coisas)
  let securityModal = null;
  const securityModalEl = document.getElementById("securityModal");

  if (securityModalEl) {
    // Eu encontrei o modal de segurança na página, então inicializo o Bootstrap nele.
    securityModal = new bootstrap.Modal(securityModalEl);
  }

  // Modal de Histórico (Pode não existir em todas as páginas, então sou cauteloso)
  const historyModalEl = document.getElementById("historyModal");
  let historyModal = historyModalEl
    ? new bootstrap.Modal(historyModalEl)
    : null;

  // Modal de Detalhes do Evento
  const eventDetailModalEl = document.getElementById("eventDetailModal");
  let eventDetailModal = eventDetailModalEl
    ? new bootstrap.Modal(eventDetailModalEl)
    : null;

  // =========================================================
  // 2. FUNÇÃO DE ABRIR O MODAL DE EXCLUSÃO (Global)
  // Eu defino isto no 'window' para que os botões HTML (onclick) consigam me chamar.
  // =========================================================
  window.openSecureDelete = function (type, id) {
    // Primeiro, eu verifico se o modal foi carregado corretamente.
    if (!securityModal) {
      console.error("Erro: O modal de segurança não foi encontrado no HTML.");
      return;
    }

    // Eu busco os campos escondidos dentro do modal.
    const typeInput = document.getElementById("deleteType");
    const idInput = document.getElementById("deleteId");
    const passInput = document.getElementById("adminPass");
    const errorDiv = document.getElementById("securityError");

    // Eu preencho os campos escondidos com o Tipo (user/event) e o ID que recebi.
    if (typeInput) typeInput.value = type;
    if (idInput) idInput.value = id;

    // Eu limpo o campo de senha e mensagens de erro antigas para deixar tudo pronto.
    if (passInput) passInput.value = "";
    if (errorDiv) errorDiv.textContent = "";

    // Finalmente, eu mostro o modal na tela.
    securityModal.show();
  };

  // =========================================================
  // 3. AÇÃO DO BOTÃO "CONFIRMAR EXCLUSÃO"
  // Aqui é onde eu realmente apago o item após verificar a senha.
  // =========================================================
  const confirmBtn = document.getElementById("confirmDeleteBtn");

  if (confirmBtn) {
    confirmBtn.addEventListener("click", function () {
      // Eu recolho todos os dados necessários para o envio.
      const type = document.getElementById("deleteType").value;
      const id = document.getElementById("deleteId").value;
      const emailEl = document.getElementById("adminEmail");
      const passEl = document.getElementById("adminPass");
      const errorDiv = document.getElementById("securityError");

      // Segurança: Eu verifico se os campos de email e senha existem na página.
      if (!emailEl || !passEl) {
        console.error(
          "Erro: Campos de formulário (email ou senha) não encontrados."
        );
        return;
      }

      const email = emailEl.value;
      const pass = passEl.value;

      // Se o utilizador não digitou a senha, eu aviso e paro aqui.
      if (!pass) {
        if (errorDiv)
          errorDiv.textContent =
            "Por favor, digite a sua senha de administrador.";
        return;
      }

      // Feedback Visual: Eu mudo o botão para "A processar..." e bloqueio cliques repetidos.
      const originalText = confirmBtn.innerHTML;
      confirmBtn.innerHTML =
        '<span class="spinner-border spinner-border-sm"></span> A processar...';
      confirmBtn.disabled = true;

      // Eu envio o pedido para o servidor (PHP) via AJAX.
      fetch("secure_delete.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          admin_email: email,
          admin_pass: pass,
          type: type,
          id: id,
        }),
      })
        .then((res) => res.json())
        .then((data) => {
          // O servidor respondeu. Vamos ver se correu bem.
          if (data.success) {
            // Sucesso! Eu fecho o modal.
            if (securityModal) securityModal.hide();

            // Eu mostro um alerta bonito de sucesso e recarrego a página.
            Swal.fire({
              title: "Sucesso!",
              text: "O item foi apagado corretamente.",
              icon: "success",
              timer: 1500,
              showConfirmButton: false,
            }).then(() => {
              location.reload(); // Atualizo a página para o item sumir da lista.
            });
          } else {
            // Erro (ex: senha errada). Eu mostro a mensagem vermelha no modal.
            if (errorDiv)
              errorDiv.textContent = data.message || "Erro ao apagar.";
          }
        })
        .catch((err) => {
          // Erro de rede ou servidor caiu.
          console.error(err);
          if (errorDiv)
            errorDiv.textContent = "Erro de conexão com o servidor.";
        })
        .finally(() => {
          // No final de tudo (sucesso ou erro), eu restauro o botão ao estado original.
          confirmBtn.innerHTML = originalText;
          confirmBtn.disabled = false;
        });
    });
  }

  // =========================================================
  // 4. FILTROS DE PESQUISA (EVENTOS)
  // Eu filtro a lista em tempo real enquanto você digita.
  // =========================================================
  const searchInput = document.getElementById("searchEvents");
  if (searchInput) {
    searchInput.addEventListener("keyup", function () {
      const value = this.value.toLowerCase();
      // Eu seleciono tanto as linhas da tabela (Desktop) quanto os cards (Mobile).
      const rows = document.querySelectorAll(
        "#tableEvents tbody tr, .event-row"
      );

      rows.forEach((row) => {
        const text = row.innerText.toLowerCase();
        // Se o texto bater certo, eu mostro. Se não, eu escondo (display: none).
        row.style.display = text.indexOf(value) > -1 ? "" : "none";
      });
    });
  }

  // =========================================================
  // 5. FILTROS DE PESQUISA (UTILIZADORES)
  // =========================================================
  const searchUser = document.getElementById("searchUsers");
  if (searchUser) {
    searchUser.addEventListener("keyup", function () {
      const value = this.value.toLowerCase();
      const rows = document.querySelectorAll("#tableUsers tbody tr, .user-row");

      rows.forEach((row) => {
        const text = row.innerText.toLowerCase();
        row.style.display = text.indexOf(value) > -1 ? "" : "none";
      });
    });
  }
});
