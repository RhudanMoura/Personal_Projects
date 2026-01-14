// Coordenadas fixas de origem
const origem = [51.5, -0.09];

// Inicializa o mapa
const map = L.map('mapid').setView(origem, 13);

// Adiciona os tiles do OpenStreetMap
L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
  attribution:
    '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
}).addTo(map);

// Adiciona o marcador fixo no ponto de origem
L.marker(origem).addTo(map).bindPopup('Eu estou aqui').openPopup();

// Função para buscar as coordenadas a partir do nome digitado
async function buscarCoordenadas(endereco) {
  const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(endereco)}`;
  const resposta = await fetch(url);
  const dados = await resposta.json();

  if (dados.length > 0) {
    const latitude = parseFloat(dados[0].lat);
    const longitude = parseFloat(dados[0].lon);
    return [latitude, longitude];
  } else {
    alert('Endereço não encontrado. Tente ser mais específico.');
    return null;
  }
}

// Evento do botão "Calcular rota"
document.addEventListener('DOMContentLoaded', function () {
  const botao = document.querySelector('.rotes button');

  if (botao) {
    botao.addEventListener('click', async () => {
      const destinoTexto = document.querySelector('.local').value.trim();

      if (!destinoTexto) {
        alert('Digite um endereço para calcular a rota.');
        return;
      }

      const destinoCoords = await buscarCoordenadas(destinoTexto);

      if (destinoCoords) {
        // Remove rota anterior se existir
        if (window.rotaAtual) {
          map.removeControl(window.rotaAtual);
        }

        // Cria nova rota
        window.rotaAtual = L.Routing.control({
          waypoints: [
            L.latLng(origem[0], origem[1]),
            L.latLng(destinoCoords[0], destinoCoords[1]),
          ],
          routeWhileDragging: false,
        }).addTo(map);
      }
    });
  }
});
