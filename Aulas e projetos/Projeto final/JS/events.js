// js/events.js - CORRIGIDO PARA ATUALIZAR TODOS OS CARRINHOS

document.addEventListener("DOMContentLoaded", function () {
  // Verifica se o API_CONFIG foi carregado
  if (typeof API_CONFIG === "undefined") {
    console.error("Erro: api-config.js não foi carregado!");
    return;
  }

  const PHP_PATH = API_CONFIG.baseURL;

  // 1. ATUALIZAR CONTADOR DO CARRINHO (CORRIGIDO)
  function updateCartCount() {
    fetch(`${PHP_PATH}/get_cart_count.php`)
      .then((response) => response.json())
      .then((data) => {
        const count = data.count || 0;

        // ATUALIZAÇÃO: Procura por classe em vez de ID único
        // Assim atualiza tanto o mobile quanto o desktop
        const counters = document.querySelectorAll(".cart-count");

        counters.forEach((counter) => {
          counter.textContent = count;

          // Opcional: Esconde a bolinha se for zero para ficar mais limpo
          if (count === 0) {
            counter.classList.add("d-none");
          } else {
            counter.classList.remove("d-none");
          }
        });
      })
      .catch((error) => console.error("Erro ao buscar contagem:", error));
  }

  // Chama ao carregar
  updateCartCount();

  // 2. VER DETALHES DO EVENTO (Modal)
  const detailButtons = document.querySelectorAll(".view-details");

  detailButtons.forEach((button) => {
    button.addEventListener("click", function () {
      const eventData = JSON.parse(this.getAttribute("data-event"));
      document.getElementById("eventModalTitle").textContent = eventData.title;

      const modalBody = document.getElementById("eventModalBody");

      // Se não tiver imagem, usa uma padrão
      const imageUrl = eventData.image_url
        ? eventData.image_url
        : "https://images.unsplash.com/photo-1501281668745-f7f57925c3b4?ixlib=rb-4.0.3&auto=format&fit=crop&w=1350&q=80";

      const eventDate = new Date(eventData.event_date).toLocaleString("pt-PT");
      const endDate = eventData.end_date
        ? new Date(eventData.end_date).toLocaleString("pt-PT")
        : null;

      modalBody.innerHTML = `
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <img src="${imageUrl}" class="img-fluid rounded w-100" alt="${
        eventData.title
      }" style="object-fit: cover; max-height: 300px;">
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold">Descrição Completa:</h6>
                        <p>${eventData.description}</p>
                        <hr>
                        <p><i class="bi bi-calendar-event me-2"></i><strong>Data:</strong> ${eventDate}</p>
                        ${
                          endDate
                            ? `<p><i class="bi bi-clock me-2"></i><strong>Fim:</strong> ${endDate}</p>`
                            : ""
                        }
                        <p><i class="bi bi-geo-alt me-2"></i><strong>Local:</strong> ${
                          eventData.location
                        }</p>
                        <p><i class="bi bi-currency-euro me-2"></i><strong>Preço:</strong> €${parseFloat(
                          eventData.price
                        ).toFixed(2)}</p>
                        <p><i class="bi bi-ticket-perforated me-2"></i><strong>Disponíveis:</strong> ${
                          eventData.available_tickets
                        }</p>
                    </div>
                </div>
            `;
    });
  });

  // 3. ADICIONAR AO CARRINHO
  const addForms = document.querySelectorAll(".add-to-cart-form");

  addForms.forEach((form) => {
    form.addEventListener("submit", function (e) {
      e.preventDefault();

      const submitBtn = this.querySelector('button[type="submit"]');
      const originalText = submitBtn.innerHTML;

      // Dados visuais para o modal (buscando a classe price-tag que adicionamos)
      const card = this.closest(".card");
      const eventTitle = card.querySelector(".card-title").textContent;

      // Correção de segurança: Tenta pegar o preço, se não achar, usa um padrão
      const priceElement = card.querySelector(".price-tag");
      const eventPriceTag = priceElement
        ? priceElement.textContent
        : "Preço não disponível";

      const formData = new FormData(this);
      formData.append("event_id", this.getAttribute("data-event-id"));

      submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> ...';
      submitBtn.disabled = true;

      fetch(`${PHP_PATH}/add_to_cart.php`, {
        method: "POST",
        body: formData,
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            document.getElementById("confirmationEventTitle").textContent =
              eventTitle;
            const quantity = formData.get("quantity");

            document.getElementById("confirmationDetails").innerHTML = `
                <strong>Quantidade:</strong> ${quantity} bilhete(s)<br>
                <strong>Preço Unitário:</strong> ${eventPriceTag}
            `;

            const modalEl = document.getElementById("cartConfirmationModal");
            const modal = new bootstrap.Modal(modalEl);
            modal.show();

            // Aqui a mágica acontece: atualiza TODOS os contadores
            updateCartCount();
          } else {
            alert("Atenção: " + data.message);
          }
        })
        .catch((error) => {
          console.error("Erro:", error);
          alert("Ocorreu um erro. Verifique se você está logado.");
        })
        .finally(() => {
          submitBtn.innerHTML = originalText;
          submitBtn.disabled = false;
        });
    });
  });
});
