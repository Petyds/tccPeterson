<?php
require '../proteger.php';
proteger(['recepcionista']);
require '../conexao.php';

$recepcionista_nome = $_SESSION['nome'];

// --- Lógica para visualização da agenda ---
// Consultas Agendadas
$sql_consultas = "SELECT
    c.id AS consulta_id,
    p.nome AS paciente_nome,
    m.especialidade AS medico_especialidade,
    u_medico.nome AS medico_nome,
    c.data_hora,
    c.status
FROM consultas c
JOIN pacientes pa ON c.paciente_id = pa.id
JOIN usuarios p ON pa.usuario_id = p.id
JOIN medicos m ON c.medico_id = m.id
JOIN usuarios u_medico ON m.usuario_id = u_medico.id
ORDER BY c.data_hora ASC";
$stmt_consultas = $pdo->prepare($sql_consultas);
$stmt_consultas->execute();
$consultas = $stmt_consultas->fetchAll(PDO::FETCH_ASSOC);

// Exames Agendados
$sql_exames = "SELECT
    e.id AS exame_id,
    p.nome AS paciente_nome,
    e.tipo_exame,
    e.data_exame,
    e.horario,
    e.status
FROM exames e
JOIN pacientes pa ON e.paciente_id = pa.id
JOIN usuarios p ON pa.usuario_id = p.id
ORDER BY e.data_exame ASC, e.horario ASC";
$stmt_exames = $pdo->prepare($sql_exames);
$stmt_exames->execute();
$exames = $stmt_exames->fetchAll(PDO::FETCH_ASSOC);

$erro_recepcionista = ''; // Para exibir mensagens de erro ou sucesso
?>


<?php include '../../templates-parts/header.php'; // Inclui o cabeçalho do site ?>

<!-- <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel da Recepcionista</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head> -->

    <div class="container mt-5 respiro">
    <h2>Olá, <?= htmlspecialchars($recepcionista_nome) ?>!</h2>
    <p class="lead">Gerenciar agendamentos e informações dos pacientes.</p>

    <?php if ($erro_recepcionista): ?>
        <div class="alert alert-info mt-3" role="alert">
            <?= $erro_recepcionista ?>
        </div>
    <?php endif; ?>

    <h3 class="mt-4">Consultas Agendadas</h3>
    <?php if (empty($consultas)): ?>
        <p>Nenhuma consulta agendada.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Paciente</th>
                        <th>Médico</th>
                        <th>Especialidade</th>
                        <th>Data e Hora</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($consultas as $consulta): ?>
                        <tr>
                            <td><?= htmlspecialchars($consulta['consulta_id']) ?></td>
                            <td><?= htmlspecialchars($consulta['paciente_nome']) ?></td>
                            <td><?= htmlspecialchars($consulta['medico_nome']) ?></td>
                            <td><?= htmlspecialchars($consulta['medico_especialidade']) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($consulta['data_hora'])) ?></td>
                            <td><?= ucfirst($consulta['status']) ?></td>
                            <td>
                                <a href="editar_consulta.php?id=<?= $consulta['consulta_id'] ?>" class="btn btn-sm btn-primary">Editar</a>
                                <button class="btn btn-sm btn-danger" onclick="confirmDesabilitarConsulta(<?= $consulta['consulta_id'] ?>)">Desabilitar</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <h3 class="mt-4">Exames Agendados</h3>
    <?php if (empty($exames)): ?>
        <p>Nenhum exame agendado.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Paciente</th>
                        <th>Tipo de Exame</th>
                        <th>Data</th>
                        <th>Horário</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($exames as $exame): ?>
                        <tr>
                            <td><?= htmlspecialchars($exame['exame_id']) ?></td>
                            <td><?= htmlspecialchars($exame['paciente_nome']) ?></td>
                            <td><?= htmlspecialchars($exame['tipo_exame']) ?></td>
                            <td><?= date('d/m/Y', strtotime($exame['data_exame'])) ?></td>
                            <td><?= $exame['horario'] ? date('H:i', strtotime($exame['horario'])) : '-' ?></td>
                            <td><?= ucfirst($exame['status']) ?></td>
                            <td>
                                <a href="editar_exame.php?id=<?= $exame['exame_id'] ?>" class="btn btn-sm btn-primary">Editar</a>
                                <button class="btn btn-sm btn-danger" onclick="confirmDesabilitarExame(<?= $exame['exame_id'] ?>)">Desabilitar</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <h3 class="mt-4" style="padding: 10px;">Ações Rápidas</h3>
    <div class="row">
        <div class="col-md-3">
            <a href="agendar_consulta.php" class="btn btn-success btn-block">Agendar Consulta</a>
        </div>
        <div class="col-md-3">
            <a href="agendar_exame.php" class="btn btn-info btn-block">Agendar Exame</a>
        </div>
        <div class="col-md-3">
            <a href="../cadastrar_paciente.php" class="btn btn-secondary btn-block">Cadastrar Paciente</a>
        </div>
    </div>

    <script>
        function confirmDesabilitarConsulta(id) {
            if (confirm("Tem certeza que deseja desabilitar esta consulta?")) {
                window.location.href = "desabilitar_consulta.php?id=" + id;
                // Aqui você precisará implementar a lógica no desabilitar_consulta.php
                // para realmente desabilitar (atualizar o status) e registrar a ação.
            }
        }

        function confirmDesabilitarExame(id) {
            if (confirm("Tem certeza que deseja desabilitar este exame?")) {
                window.location.href = "desabilitar_exame.php?id=" + id;
                // Similarmente, implemente a lógica no desabilitar_exame.php
            }
        }
    </script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    </div>
    <?php include '../../templates-parts/footer.php'; // Inclui o rodapé do site ?>
