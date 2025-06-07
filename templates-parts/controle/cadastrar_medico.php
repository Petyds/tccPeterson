<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Médico</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
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
</head>
<body>
<div class="container mt-5">
    <h2>Cadastro de Médico</h2>
    <form method="POST" onsubmit="return validarFormulario()">
        
    <div class="form-group">
        <label>ID do Usuário</label>
        <input type="number" name="usuario_id" class="form-control" required>
    </div>
    <div class="form-group">
        <label>CRM</label>
        <input type="text" name="crm" class="form-control" required>
    </div>
    <div class="form-group">
        <label>Especialidade</label>
        <input type="text" name="especialidade" class="form-control" required>
    </div>
    
        <button type="submit" class="btn btn-primary">Cadastrar</button>
    </form>
</div>
</body>
</html>
