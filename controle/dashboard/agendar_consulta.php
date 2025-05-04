<?php
require '../proteger.php';
proteger(['recepcionista']);
require '../conexao.php';

$recepcionista_nome = $_SESSION['nome'];
$erro_agendamento = '';
$sucesso_agendamento = '';

// Buscar pacientes para o select
$sql_pacientes = "SELECT id, nome FROM usuarios WHERE tipo = 'paciente' ORDER BY nome ASC";
$stmt_pacientes = $pdo->prepare($sql_pacientes);
$stmt_pacientes->execute();
$pacientes = $stmt_pacientes->fetchAll(PDO::FETCH_ASSOC);

// Buscar médicos para o select
$sql_medicos = "SELECT m.id, u.nome, m.especialidade FROM medicos m JOIN usuarios u ON m.usuario_id = u.id ORDER BY u.nome ASC";
$stmt_medicos = $pdo->prepare($sql_medicos);
$stmt_medicos->execute();
$medicos = $stmt_medicos->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $paciente_id = $_POST['paciente_id'] ?? null;
    $medico_id = $_POST['medico_id'] ?? null;
    $data_hora = $_POST['data_hora'] ?? null;
    $descricao = $_POST['descricao'] ?? '';

    if (empty($paciente_id) || empty($medico_id) || empty($data_hora)) {
        $erro_agendamento = '<div class="alert alert-danger mt-3" role="alert">Por favor, selecione o paciente, o médico e a data/hora da consulta.</div>';
    } else {
        try {
            $stmt_agendar = $pdo->prepare("INSERT INTO consultas (paciente_id, medico_id, data_hora, descricao) VALUES (:paciente_id, :medico_id, :data_hora, :descricao)");
            $stmt_agendar->execute([
                ':paciente_id' => $paciente_id,
                ':medico_id' => $medico_id,
                ':data_hora' => $data_hora,
                ':descricao' => $descricao
            ]);
            $sucesso_agendamento = '<div class="alert alert-success mt-3" role="alert">Consulta agendada com sucesso!</div>';
            // Limpar os campos após o sucesso
            $_POST = [];
        } catch (PDOException $e) {
            $erro_agendamento = '<div class="alert alert-danger mt-3" role="alert">Erro ao agendar a consulta: ' . $e->getMessage() . '</div>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendar Consulta</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/css/bootstrap-timepicker.min.css">
</head>
<body class="container mt-5">
    <h2>Agendar Consulta</h2>
    <p class="lead">Agende uma nova consulta para um paciente.</p>

    <a href="recepcionista.php" class="btn btn-secondary mb-3">Voltar</a>

    <?php echo $erro_agendamento; ?>
    <?php echo $sucesso_agendamento; ?>

    <form method="POST" action="agendar_consulta.php">
        <div class="form-group">
            <label for="paciente_id">Paciente</label>
            <select class="form-control" id="paciente_id" name="paciente_id" required>
                <option value="">Selecione o Paciente</option>
                <?php foreach ($pacientes as $paciente): ?>
                    <option value="<?= $paciente['id'] ?>" <?= (isset($_POST['paciente_id']) && $_POST['paciente_id'] == $paciente['id']) ? 'selected' : '' ?>><?= htmlspecialchars($paciente['nome']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="medico_id">Médico</label>
            <select class="form-control" id="medico_id" name="medico_id" required>
                <option value="">Selecione o Médico</option>
                <?php foreach ($medicos as $medico): ?>
                    <option value="<?= $medico['id'] ?>" <?= (isset($_POST['medico_id']) && $_POST['medico_id'] == $medico['id']) ? 'selected' : '' ?>><?= htmlspecialchars($medico['nome']) ?> (<?= htmlspecialchars($medico['especialidade']) ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="data_hora">Data e Hora da Consulta</label>
            <input type="datetime-local" class="form-control" id="data_hora" name="data_hora" required value="<?= htmlspecialchars($_POST['data_hora'] ?? '') ?>">
            <small class="form-text text-muted">Selecione a data e a hora desejada para a consulta.</small>
        </div>
        <div class="form-group">
            <label for="descricao">Descrição (Opcional)</label>
            <textarea class="form-control" id="descricao" name="descricao" rows="3"><?= htmlspecialchars($_POST['descricao'] ?? '') ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Agendar Consulta</button>
    </form>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.pt-BR.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/js/bootstrap-timepicker.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#data_consulta').datepicker({
                format: 'dd/mm/yyyy',
                language: 'pt-BR',
                autoclose: true
            });
            $('#hora_consulta').timepicker({
                minuteStep: 15,
                showMeridian: false
            });
        });
    </script>
</body>
</html>