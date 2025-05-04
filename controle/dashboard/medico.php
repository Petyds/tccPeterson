<?php
require_once '../proteger.php';
proteger(['medico']); // Garante que apenas médicos acessam

require_once '../conexao.php';

$medico_id = $_SESSION['user_id']; // Usando 'user_id' como padrão da sessão
$medico_nome = $_SESSION['nome']; // Recupera o nome do médico da sessão
$erro_medico = '';

try {
    $sql = "SELECT c.id, u.nome AS paciente, c.data_hora, c.status
            FROM consultas c
            JOIN pacientes pa ON c.paciente_id = pa.id
            JOIN usuarios u ON pa.usuario_id = u.id
            JOIN medicos m ON c.medico_id = m.id
            WHERE m.usuario_id = :medico_id
            ORDER BY c.data_hora DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['medico_id' => $medico_id]);
    $consultas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $erro_medico = '<div class="alert alert-danger mt-3" role="alert">Erro ao buscar consultas: ' . $e->getMessage() . '</div>';
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel do Médico</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body class="container mt-5">
    <h2>Olá, Dr(a). <?= htmlspecialchars($medico_nome) ?>! Bem-vindo(a) ao seu painel.</h2>

    <h3 class="mt-4">Minhas Consultas</h3>

    <?php echo $erro_medico; // Exibe mensagens de erro ?>

    <?php if (empty($consultas)): ?>
        <div class="alert alert-info mt-3" role="alert">
            Nenhuma consulta agendada para você no momento.
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Paciente</th>
                        <th>Data e Hora</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($consultas as $consulta): ?>
                        <tr>
                            <td><?= htmlspecialchars($consulta['paciente']) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($consulta['data_hora'])) ?></td>
                            <td><?= ucfirst($consulta['status']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>