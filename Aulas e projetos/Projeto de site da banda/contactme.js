function validarFormulario() {
  var telefone = document.getElementById("telefone").value;
  var mensagem = document.getElementById("mensagem").value;
  var dataNascimento = document.getElementById("dataNascimento").value;

  if (dataNascimento) {
    var nascimento = new Date(dataNascimento);
    var dataAtual = new Date();
    var msAno = 1000 * 60 * 60 * 24 * 365.25;
    var idadeFinal = (dataAtual - nascimento) / msAno;
    console.log(idadeFinal.toFixed(1));
    if (idadeFinal < 18) {
      alert("Você deve ter mais de 18 anos para preencher esse formulário.");
    }
  }

  if ((telefone.length < 9) | (telefone.length > 10)) {
    alert("Por favor, insira um número válido (9 dígitos).");
  }
}
