<?php
require '../proteger.php';
 proteger(['admin']);
require '../conexao.php';

$admin_nome = $_SESSION['nome'];
$erro_admin = '';
$sucesso_admin = '';

// Buscar todos os usuários
$sql_usuarios = "SELECT id, nome, email, tipo, criado_em FROM usuarios ORDER BY nome ASC";
$stmt_usuarios = $pdo->prepare($sql_usuarios);
try {
    $stmt_usuarios->execute();
    $usuarios = $stmt_usuarios->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $erro_admin = '<div class="alert alert-danger mt-3" role="alert">Erro ao buscar usuários: ' . $e->getMessage() . '</div>';
    $usuarios = [];
}

// Processamento de exclusão (desativação) de usuário
if (isset($_GET['desativar_id']) && is_numeric($_GET['desativar_id'])) {
    $id_desativar = filter_var($_GET['desativar_id'], FILTER_SANITIZE_NUMBER_INT);
    // Aqui você precisaria implementar a lógica para "desativar" o usuário
    // Exemplo: Atualizar uma coluna 'ativo' para 0, e registrar a ação.
    // Por simplicidade, vamos apenas exibir uma mensagem.
    $sucesso_admin = '<div class="alert alert-success mt-3" role="alert">Usuário com ID ' . $id_desativar . ' marcado para desativação (lógica não implementada).</div>';
}

// Processamento de edição (apenas para exibir link)
?>

<!DOCTYPE html>
<html lang="pt-br">
<?php 

 include '../../templates-parts/header.php'; // Inclui o cabeçalho do site';

?>
<div class="container mt-5">
    <h2>Painel do Administrador</h2>
    <p class="lead">Bem-vindo(a), <?= htmlspecialchars($admin_nome) ?>! Aqui você pode gerenciar os usuários do sistema.</p>

    <?php echo $erro_admin; ?>
    <?php echo $sucesso_admin; ?>

    <h3 class="mt-4">Lista de Usuários</h3>
    <?php if (empty($usuarios)): ?>
        <p>Nenhum usuário cadastrado no sistema.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Tipo</th>
                        <th>Criado em</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $usuario): ?>
                        <tr>
                            <td><?= htmlspecialchars($usuario['id']) ?></td>
                            <td><?= htmlspecialchars($usuario['nome']) ?></td>
                            <td><?= htmlspecialchars($usuario['email']) ?></td>
                            <td><?= ucfirst(htmlspecialchars($usuario['tipo'])) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($usuario['criado_em'])) ?></td>
                            <td>
                                <a href="editar_usuario.php?id=<?= $usuario['id'] ?>" class="btn btn-sm btn-primary">Editar</a>
                                <button class="btn btn-sm btn-danger" onclick="confirmDesativar(<?= $usuario['id'] ?>)">Desativar</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <h3 class="mt-4">Ações Administrativas</h3>
    <div class="row respiro">
        <div class="col-md-3">
            <a href="../cadastrar_usuario.php" class="btn btn-success btn-block">Cadastrar Usuário</a>
        </div>
        </div>
</div>
    <script>
        function confirmDesativar(id) {
            if (confirm("Tem certeza que deseja desativar este usuário?")) {
                window.location.href = "admin.php?desativar_id=" + id;
                // Lembre-se de implementar a lógica real de desativação no servidor
            }
        }
    </script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <?php 

 include '../../templates-parts/footer.php'; // Inclui o cabeçalho do site';

?>
