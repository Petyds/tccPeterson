<?php
require_once '../proteger.php';
require_once '../conexao.php';

// Verificar permissão de admin
if ($_SESSION['tipo'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Desabilitar usuário
if (isset($_GET['desativar']) && is_numeric($_GET['desativar'])) {
    $id = $_GET['desativar'];
    $admin_id = $_SESSION['id'];
    $sql = "UPDATE usuarios SET ativo = 0, desativado_por = ?, desativado_em = NOW() WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $admin_id, $id);
    $stmt->execute();
    $stmt->close();
    header("Location: listar_usuarios.php");
    exit;
}

// Buscar usuários ativos
$sql = "SELECT u.id, u.nome, u.email, u.tipo FROM usuarios u WHERE ativo = 1 ORDER BY tipo, nome";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">

<?php include '../../templates-parts/header.php'; // Inclui o cabeçalho do site ?>

<body>
    <div class="container mt-5">
        <h1 class="mb-4">Gerenciar Usuários</h1>
        <?php if (isset($_SESSION['mensagem'])): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($_SESSION['mensagem']) ?>
            </div>
            <?php unset($_SESSION['mensagem']); ?>
        <?php endif; ?>
        
        <a href="cadastrar_usuario.php" class="btn btn-success mb-3">Cadastrar Novo Usuário</a>
    <h2 class="mb-4">Usuários Ativos</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nome</th><th>Email</th><th>Tipo</th><th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['nome']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= ucfirst($row['tipo']) ?></td>
                <td>
                    <a href="editar_usuario.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">Editar</a>
                    <a href="?desativar=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Deseja desativar este usuário?')">Desativar</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    </div>
<?php include '../../templates-parts/footer.php'; // Inclui o rodapé do site ?>
</body>
</html>
