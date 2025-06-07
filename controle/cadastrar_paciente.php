<?php include '../templates-parts/header.php'; // Inclui o cabeçalho do site ?>
    <script>
    function validarFormulario() {
        var campos = document.querySelectorAll('input[required], select[required]');
        for (var i = 0; i < campos.length; i++) {
            if (!campos[i].value) {
                alert('Por favor, preencha o campo: ' + campos[i].name);
                return false;
            }
        }
        return true;
    }
    </script>
<div class="container mt-5 respiro">
    <h2>Cadastro de Paciente</h2>
    <form method="POST" onsubmit="return validarFormulario()">
        
    <div class="form-group">
        <label>ID do Usuário</label>
        <input type="number" name="usuario_id" class="form-control" required>
    </div>
    <div class="form-group">
        <label>Data de Nascimento</label>
        <input type="date" name="data_nascimento" class="form-control" required>
    </div>
    <div class="form-group">
        <label>CPF</label>
        <input type="text" name="cpf" class="form-control" required>
    </div>
    <div class="form-group">
        <label>Telefone</label>
        <input type="text" name="telefone" class="form-control">
    </div>
    <div class="form-group">
        <label>Endereço</label>
        <textarea name="endereco" class="form-control" required></textarea>
    </div>
    
        <button type="submit" class="btn btn-primary">Cadastrar</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
<script>
$(document).ready(function(){
    $('input[name="cpf"]').mask('000.000.000-00');
    $('input[name="telefone"]').mask('(00) 00000-0000');
});
</script>
<?php include '../templates-parts/footer.php'; // Inclui o rodapé do site ?>
