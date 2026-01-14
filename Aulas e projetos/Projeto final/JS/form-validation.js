// js/form-validation.js

document.addEventListener("DOMContentLoaded", function () {
  const contactForm = document.getElementById("contactForm");

  if (contactForm) {
    contactForm.addEventListener("submit", function (event) {
      event.preventDefault(); // Impede o recarregamento da página

      const submitBtn = contactForm.querySelector('button[type="submit"]');
      const originalBtnText = submitBtn.innerHTML;

      // 1. CAPTURA DOS DADOS
      const firstName = document.getElementById("firstName").value.trim();
      const lastName = document.getElementById("lastName").value.trim();
      const email = document.getElementById("email").value.trim();
      const phone = document.getElementById("phone").value.trim();
      const subject = document.getElementById("subject").value;
      const message = document.getElementById("message").value.trim();

      // 2. VALIDAÇÃO ESPECÍFICA DE TELEMÓVEL
      // Se o campo não estiver vazio, TEM que ter 9 dígitos
      if (phone.length > 0 && phone.length !== 9) {
        Swal.fire({
          title: "Telemóvel Inválido",
          text: "Por favor, insira um número com 9 dígitos (Ex: 912345678).",
          icon: "warning",
          confirmButtonColor: "#ffc107",
        });
        return; // Para tudo e não envia
      }

      // Preparar dados para envio
      const formData = new FormData();
      formData.append("firstName", firstName);
      formData.append("lastName", lastName);
      formData.append("email", email);
      formData.append("phone", phone);
      formData.append("subject", subject);
      formData.append("message", message);

      // Feedback visual
      submitBtn.disabled = true;
      submitBtn.innerHTML =
        '<span class="spinner-border spinner-border-sm"></span> Enviando...';

      // 3. ENVIO PARA O PHP
      // IMPORTANTE: O caminho assume que você está na raiz (index.php) chamando a pasta php/
      fetch("php/process_contact.php", {
        method: "POST",
        body: formData,
      })
        .then((response) => {
          // Se o PHP retornar erro técnico (ex: 404 ou 500), lançamos erro manual
          if (!response.ok) {
            throw new Error("Erro na conexão com o servidor.");
          }
          return response.json();
        })
        .then((data) => {
          if (data.success) {
            Swal.fire({
              title: "Enviado!",
              text: "Recebemos a sua mensagem. Entraremos em contacto em breve.",
              icon: "success",
              confirmButtonColor: "#0d6efd",
            });
            contactForm.reset();
          } else {
            Swal.fire({
              title: "Atenção",
              text: data.message,
              icon: "warning",
              confirmButtonColor: "#ffc107",
            });
          }
        })
        .catch((error) => {
          console.error("Erro:", error);
          Swal.fire({
            title: "Erro no Envio",
            text: 'Não foi possível enviar a mensagem. Verifique se o arquivo "php/process_contact.php" existe.',
            icon: "error",
            confirmButtonColor: "#dc3545",
          });
        })
        .finally(() => {
          submitBtn.disabled = false;
          submitBtn.innerHTML = originalBtnText;
        });
    });
  }
});
