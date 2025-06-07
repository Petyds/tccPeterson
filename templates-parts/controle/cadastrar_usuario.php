<?php
require 'conexao.php';

$erro = [];
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $confirmar_senha = $_POST['confirmar_senha'] ?? '';
    $tipo = $_POST['tipo'] ?? 'paciente'; // Paciente como padrão

    // Validações PHP
    if (empty($nome)) {
        $erro['nome'] = 'O nome é obrigatório.';
    }
    if (empty($email)) {
        $erro['email'] = 'O email é obrigatório.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro['email'] = 'Formato de email inválido.';
    } else {
        // Verificar se o email já existe
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE email = :email");
        $stmt->execute([':email' => $email]);
        if ($stmt->fetchColumn() > 0) {
            $erro['email'] = 'Este email já está cadastrado.';
        }
    }
    if (empty($senha)) {
        $erro['senha'] = 'A senha é obrigatória.';
    } elseif (strlen($senha) < 6) {
        $erro['senha'] = 'A senha deve ter pelo menos 6 caracteres.';
    }
    if ($senha !== $confirmar_senha) {
        $erro['confirmar_senha'] = 'As senhas não coincidem.';
    }
    if (!in_array($tipo, ['admin', 'medico', 'paciente', 'recepcionista'])) {
        $erro['tipo'] = 'Tipo de usuário inválido.';
    }

    if (empty($erro)) {
        try {
            $senha_hash = md5($senha); // Use password_hash em produção para mais segurança
            $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, tipo, criado_em) VALUES (:nome, :email, :senha, :tipo, NOW())");
            $stmt->execute([
                ':nome' => $nome,
                ':email' => $email,
                ':senha' => $senha_hash,
                ':tipo' => $tipo
            ]);
            $sucesso = '<div class="alert alert-success mt-3" role="alert">Usuário cadastrado com sucesso!</div>';
            // Limpar o formulário após o sucesso
            $_POST = [];
        } catch (PDOException $e) {
            $erro['banco'] = 'Erro ao cadastrar no banco de dados: ' . $e->getMessage();
        }
    }
}

?>


<?php include '../templates-parts/header.php'; // Inclui o cabeçalho do site ?>
   <style>
         
        .error-message {
            color: red;
            font-size: 0.8em;
            margin-top: 0.2rem;
        }
    </style>

    
            <div class="container mt-5"> 
                <h3>Cadastrar Usuário</h3>
                <hr>
                <?php echo $sucesso; ?>
                <form method="POST" action="cadastrar_usuario.php" onsubmit="return validarFormulario()">
                    <div class="form-group">
                        <label for="nome">Nome Completo</label>
                        <input type="text" class="form-control" id="nome" name="nome" value="<?php echo htmlspecialchars($_POST['nome'] ?? ''); ?>" required>
                        <div class="error-message" id="erroNome"><?php echo $erro['nome'] ?? ''; ?></div>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                        <div class="error-message" id="erroEmail"><?php echo $erro['email'] ?? ''; ?></div>
                    </div>
                    <div class="form-group">
                        <label for="senha">Senha</label>
                        <input type="password" class="form-control" id="senha" name="senha" required>
                        <div class="error-message" id="erroSenha"><?php echo $erro['senha'] ?? ''; ?></div>
                    </div>
                    <div class="form-group">
                        <label for="confirmar_senha">Confirmar Senha</label>
                        <input type="password" class="form-control" id="confirmar_senha" name="confirmar_senha" required>
                        <div class="error-message" id="erroConfirmarSenha"><?php echo $erro['confirmar_senha'] ?? ''; ?></div>
                    </div>
                    <div class="form-group">
                        <label for="tipo">Tipo de Usuário</label>
                        <select name="tipo" id="tipo" class="form-control">
                            <option value="paciente" <?php if (($_POST['tipo'] ?? '') === 'paciente') echo 'selected'; ?>>Paciente</option>
                            <option value="medico" <?php if (($_POST['tipo'] ?? '') === 'medico') echo 'selected'; ?>>Médico</option>
                            <option value="recepcionista" <?php if (($_POST['tipo'] ?? '') === 'recepcionista') echo 'selected'; ?>>Recepcionista</option>
                        </select>
                        <div class="error-message" id="erroTipo"><?php echo $erro['tipo'] ?? ''; ?></div>
                    </div>
                    <?php if (isset($erro['banco'])): ?>
                        <div class="alert alert-danger mt-3" role="alert"><?php echo $erro['banco']; ?></div>
                    <?php endif; ?>
                    <button type="submit" class="btn btn-primary btn-block">Cadastrar</button>
                    <p class="mt-3"><a href="login.php">Voltar para o Login</a></p>
                </form>
            </div>
<?php include '../templates-parts/footer.php'; // Inclui o cabeçalho do site ?>
    <script>
        function validarFormulario() {
            let valido = true;
            document.getElementById('erroNome').innerText = '';
            document.getElementById('erroEmail').innerText = '';
            document.getElementById('erroSenha').innerText = '';
            document.getElementById('erroConfirmarSenha').innerText = '';
            document.getElementById('erroTipo').innerText = '';

            const nome = document.getElementById('nome').value.trim();
            const email = document.getElementById('email').value.trim();
            const senha = document.getElementById('senha').value;
            const confirmarSenha = document.getElementById('confirmar_senha').value;
            const tipo = document.getElementById('tipo').value;

            if (!nome) {
                document.getElementById('erroNome').innerText = 'O nome é obrigatório.';
                valido = false;
            }
            if (!email) {
                document.getElementById('erroEmail').innerText = 'O email é obrigatório.';
                valido = false;
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                document.getElementById('erroEmail').innerText = 'Formato de email inválido.';
                valido = false;
            }
            if (!senha) {
                document.getElementById('erroSenha').innerText = 'A senha é obrigatória.';
                valido = false;
            } else if (senha.length < 6) {
                document.getElementById('erroSenha').innerText = 'A senha deve ter pelo menos 6 caracteres.';
                valido = false;
            }
            if (senha !== confirmarSenha) {
                document.getElementById('erroConfirmarSenha').innerText = 'As senhas não coincidem.';
                valido = false;
            }
            if (!['admin', 'medico', 'paciente', 'recepcionista'].includes(tipo)) {
                document.getElementById('erroTipo').innerText = 'Tipo de usuário inválido.';
                valido = false;
            }

            return valido;
        }
    </script>
