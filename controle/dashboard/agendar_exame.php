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

// Buscar médicos para o select (quem solicitou o exame)
$sql_medicos = "SELECT m.id, u.nome, m.especialidade FROM medicos m JOIN usuarios u ON m.usuario_id = u.id ORDER BY u.nome ASC";
$stmt_medicos = $pdo->prepare($sql_medicos);
$stmt_medicos->execute();
$medicos = $stmt_medicos->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $paciente_id = $_POST['paciente_id'] ?? null;
    $tipo_exame = $_POST['tipo_exame'] ?? null;
    $data_exame = $_POST['data_exame'] ?? null;
    $horario = $_POST['horario'] ?? null;
    $local = $_POST['local'] ?? '';
    $solicitado_por = $_POST['solicitado_por'] ?? null;

    if (empty($paciente_id) || empty($tipo_exame) || empty($data_exame)) {
        $erro_agendamento = '<div class="alert alert-danger mt-3" role="alert">Por favor, selecione o paciente, o tipo de exame e a data do exame.</div>';
    } else {
        try {
            $stmt_agendar = $pdo->prepare("INSERT INTO exames (paciente_id, tipo_exame, data_exame, horario, local, solicitado_por) VALUES (:paciente_id, :tipo_exame, :data_exame, :horario, :local, :solicitado_por)");
            $stmt_agendar->execute([
                ':paciente_id' => $paciente_id,
                ':tipo_exame' => $tipo_exame,
                ':data_exame' => $data_exame,
                ':horario' => $horario,
                ':local' => $local,
                ':solicitado_por' => $solicitado_por ?: null // Permitir nulo se não selecionado
            ]);
            $sucesso_agendamento = '<div class="alert alert-success mt-3" role="alert">Exame agendado com sucesso!</div>';
            // Limpar os campos após o sucesso
            $_POST = [];
        } catch (PDOException $e) {
            $erro_agendamento = '<div class="alert alert-danger mt-3" role="alert">Erro ao agendar o exame: ' . $e->getMessage() . '</div>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendar Exame</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/css/bootstrap-timepicker.min.css">
</head>
<body class="container mt-5">
    <h2>Agendar Exame</h2>
    <p class="lead">Agende um novo exame para um paciente.</p>

    <a href="recepcionista.php" class="btn btn-secondary mb-3">Voltar</a>

    <?php echo $erro_agendamento; ?>
    <?php echo $sucesso_agendamento; ?>

    <form method="POST" action="agendar_exame.php">
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
            <label for="tipo_exame">Tipo de Exame</label>
            <input type="text" class="form-control" id="tipo_exame" name="tipo_exame" placeholder="Digite o tipo de exame" required value="<?= htmlspecialchars($_POST['tipo_exame'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label for="data_exame">Data do Exame</label>
            <input type="date" class="form-control" id="data_exame" name="data_exame" required value="<?= htmlspecialchars($_POST['data_exame'] ?? '') ?>">
            <small class="form-text text-muted">Selecione a data desejada para o exame.</small>
        </div>
        <div class="form-group">
            <label for="horario">Horário do Exame (Opcional)</label>
            <input type="time" class="form-control" id="horario" name="horario" value="<?= htmlspecialchars($_POST['horario'] ?? '') ?>">
            <small class="form-text text-muted">Selecione o horário desejado para o exame (se aplicável).</small>
        </div>
        <div class="form-group">
            <label for="local">Local do Exame (Opcional)</label>
            <input type="text" class="form-control" id="local" name="local" placeholder="Digite o local do exame" value="<?= htmlspecialchars($_POST['local'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label for="solicitado_por">Solicitado Por (Médico Opcional)</label>
            <select class="form-control" id="solicitado_por" name="solicitado_por">
                <option value="">Selecione o Médico (Opcional)</option>
                <?php foreach ($medicos as $medico): ?>
                    <option value="<?= $medico['id'] ?>" <?= (isset($_POST['solicitado_por']) && $_POST['solicitado_por'] == $medico['id']) ? 'selected' : '' ?>><?= htmlspecialchars($medico['nome']) ?> (<?= htmlspecialchars($medico['especialidade']) ?>)</option>
                <?php endforeach; ?>
            </select>
            <small class="form-text text-muted">Selecione o médico que solicitou o exame, se aplicável.</small>
        </div>
        <button type="submit" class="btn btn-primary">Agendar Exame</button>
    </form>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.pt-BR.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/js/bootstrap-timepicker.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#data_exame').datepicker({
                format: 'yyyy-mm-dd',
                language: 'pt-BR',
                autoclose: true
            });
            $('#horario').timepicker({
                minuteStep: 15,
                showMeridian: false
            });
        });
    </script>
</body>
</html>