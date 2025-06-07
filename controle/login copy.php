<?php
session_start();
require 'conexao.php';
include 'header.php'; // Inclui o cabeçalho do site
//include '../css/bootstrap.php'; // Inclui o CSS do Bootstrap

$erro_login = ''; // Variável para armazenar mensagens de erro

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = trim($_POST['senha'] ?? '');
    $tipo_form = trim($_POST['tipo'] ?? ''); // Captura o tipo de usuário do formulário

    if (empty($email) || empty($senha)) {
        $erro_login = '<div class="alert alert-danger mt-3" role="alert">Preencha todos os campos.</div>';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch();

            if ($user) {
                $senha_md5 = md5($senha);

                if ($senha_md5 === $user['senha']) {
                    // Verifique se o tipo de usuário do banco de dados corresponde ao tipo selecionado no formulário
                    if ($user['tipo'] === $tipo_form) {
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['tipo'] = $user['tipo'];
                        $_SESSION['nome'] = $user['nome']; // **Adicione esta linha para armazenar o nome**

                        // Redireciona com base no tipo (agora usando o tipo do banco de dados)
                        switch ($user['tipo']) {
                            case 'admin':
                                header("Location: dashboard/admin.php");
                                break;
                            case 'medico':
                                header("Location: dashboard/medico.php");
                                break;
                            case 'paciente':
                                header("Location: dashboard/paciente.php");
                                break;
                            case 'recepcionista':
                                header("Location: dashboard/recepcionista.php");
                                break;
                            default:
                                $erro_login = '<div class="alert alert-danger mt-3" role="alert">Tipo de usuário desconhecido.</div>';
                                break;
                        }
                        exit;
                    } else {
                        $erro_login = '<div class="alert alert-danger mt-3" role="alert">Tipo de usuário selecionado não corresponde ao cadastrado.</div>';
                    }
                } else {
                    $erro_login = '<div class="alert alert-danger mt-3" role="alert">Senha inválida.</div>';
                }
            } else {
                $erro_login = '<div class="alert alert-danger mt-3" role="alert">Usuário não encontrado.</div>';
            }
        } catch (PDOException $e) {
            $erro_login = '<div class="alert alert-danger mt-3" role="alert">Erro no login: ' . $e->getMessage() . '</div>';
        }
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Sistema de Hospital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .password-toggle {
            cursor: pointer;
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h3>Login</h3>
                <hr>
                <form method="POST" action="login.php">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Informe seu email" required>
                    </div>
                    <div class="form-group">
                        <label for="senha">Senha</label>
                        <div style="position: relative;">
                            <input type="password" class="form-control" id="senha" name="senha" placeholder="Informe sua senha" required>
                            <span class="password-toggle" id="togglePassword">
                                <i class="fa fa-eye" aria-hidden="true"></i>
                            </span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="tipo">Tipo de Usuário</label>
                        <select name="tipo" id="tipo" class="form-control">
                            <option value="admin">Admin</option>
                            <option value="medico">Médico</option>
                            <option value="paciente">Paciente</option>
                            <option value="recepcionista">Recepcionista</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Entrar</button>
                </form>

                <?php echo $erro_login; // Exibe a mensagem de erro abaixo do formulário ?>

                <p class="mt-3">Não tem uma conta? <a href="cadastrar_usuario.php">Inscrever-se</a></p>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script>
        const passwordInput = document.getElementById('senha');
        const togglePassword = document.getElementById('togglePassword');

        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.querySelector('i').classList.toggle('fa-eye');
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });
    </script>
</body>
</html>