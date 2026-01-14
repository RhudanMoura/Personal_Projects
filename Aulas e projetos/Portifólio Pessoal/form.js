function calcularOrcamento() {
  // Pegar os valores dos inputs e select
  const nome = document.getElementById('nome').value.trim();
  const email = document.getElementById('emailInput').value.trim();
  const tipoProjeto = document.getElementById('projectType').value;
  const tamanhoProjeto = parseInt(document.getElementById('tamanhoProjeto').value.trim());
  const descricaoProjeto = document.getElementById('descricaoProjeto').value.trim();

  // Validar campos obrigatórios
  if (!nome || !email || !tipoProjeto || isNaN(tamanhoProjeto) || tamanhoProjeto <= 0) {
    alert('Por favor, preencha todos os campos corretamente.');
    return;
  }

  // Valores fixos por tipo de projeto
  const precosPorPagina = {
    "Web design": 100,
    "Web Development": 200,
    "UX/UI": 150
  };

  // Calcular orçamento
  const precoPorPagina = precosPorPagina[tipoProjeto];
  if (!precoPorPagina) {
    alert('Tipo de projeto inválido.');
    return;
  }

  const orcamento = precoPorPagina * tamanhoProjeto;

  // Mostrar resultado na página (div #resultado)
  const resultadoDiv = document.getElementById('resultado');
  resultadoDiv.innerHTML = `
    <h4>Resultado da Simulação</h4>
    <p><strong>Nome:</strong> ${nome}</p>
    <p><strong>Email:</strong> ${email}</p>
    <p><strong>Tipo de Projeto:</strong> ${tipoProjeto}</p>
    <p><strong>Tamanho do Projeto:</strong> ${tamanhoProjeto} página(s)</p>
    <p><strong>Descrição:</strong> ${descricaoProjeto || 'Nenhuma descrição'}</p>
    <hr />
    <p><strong>Orçamento estimado:</strong> €${orcamento.toFixed(2)}</p>
  `;
}
