<?php
require_once '../proteger.php';
proteger(['paciente']);
require_once '../conexao.php';

$usuario_id = $_SESSION['user_id'];
$usuario_nome = $_SESSION['nome'];
$erro_solicitacao = '';
$sucesso_solicitacao = '';

// Buscar especialidades para solicitação de consulta
$sql_especialidades = "SELECT DISTINCT especialidade FROM medicos ORDER BY especialidade ASC";
$stmt_especialidades = $pdo->prepare($sql_especialidades);
$stmt_especialidades->execute();
$especialidades = $stmt_especialidades->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['solicitar_consulta'])) {
        $especialidade_solicitada = $_POST['especialidade'] ?? null;
        $data_solicitacao = date('Y-m-d H:i:s');
        $status_solicitacao = 'pendente';

        if (empty($especialidade_solicitada)) {
            $erro_solicitacao = '<div class="alert alert-danger mt-3" role="alert">Por favor, selecione a especialidade desejada.</div>';
        } else {
            try {
                $stmt_solicitar_consulta = $pdo->prepare("INSERT INTO solicitacoes_consultas (paciente_id, especialidade_solicitada, data_solicitacao, status) VALUES (:paciente_id, :especialidade_solicitada, :data_solicitacao, :status)");
                $stmt_solicitar_consulta->execute([
                    ':paciente_id' => $usuario_id,
                    ':especialidade_solicitada' => $especialidade_solicitada,
                    ':data_solicitacao' => $data_solicitacao,
                    ':status' => $status_solicitacao
                ]);
                $sucesso_solicitacao = '<div class="alert alert-success mt-3" role="alert">Solicitação de consulta enviada com sucesso!</div>';
                $_POST = []; // Limpar o formulário
            } catch (PDOException $e) {
                $erro_solicitacao = '<div class="alert alert-danger mt-3" role="alert">Erro ao solicitar consulta: ' . $e->getMessage() . '</div>';
            }
        }
    } elseif (isset($_POST['solicitar_exame'])) {
        $tipo_exame_solicitado = $_POST['tipo_exame'] ?? null;
        $arquivo_guia = $_FILES['arquivo_guia'] ?? null;
        $data_solicitacao = date('Y-m-d H:i:s');
        $status_solicitacao = 'pendente';
        $nome_arquivo_guia = '';

        if (empty($tipo_exame_solicitado) || empty($arquivo_guia['name'])) {
            $erro_solicitacao = '<div class="alert alert-danger mt-3" role="alert">Por favor, preencha o tipo de exame e envie a guia médica.</div>';
        } elseif ($arquivo_guia['type'] !== 'application/pdf') {
            $erro_solicitacao = '<div class="alert alert-danger mt-3" role="alert">Por favor, envie a guia médica em formato PDF.</div>';
        } elseif ($arquivo_guia['error'] !== UPLOAD_ERR_OK) {
            $erro_solicitacao = '<div class="alert alert-danger mt-3" role="alert">Erro ao enviar o arquivo da guia médica.</div>';
        } else {
            $pasta_destino = '../uploads/'; // Criar esta pasta com permissões de escrita
            $nome_arquivo_guia = uniqid() . '_' . $arquivo_guia['name'];
            $caminho_completo = $pasta_destino . $nome_arquivo_guia;

            if (move_uploaded_file($arquivo_guia['tmp_name'], $caminho_completo)) {
                try {
                    $stmt_solicitar_exame = $pdo->prepare("INSERT INTO solicitacoes_exames (paciente_id, tipo_exame_solicitado, arquivo_guia, data_solicitacao, status) VALUES (:paciente_id, :tipo_exame_solicitado, :arquivo_guia, :data_solicitacao, :status)");
                    $stmt_solicitar_exame->execute([
                        ':paciente_id' => $usuario_id,
                        ':tipo_exame_solicitado' => $tipo_exame_solicitado,
                        ':arquivo_guia' => $nome_arquivo_guia,
                        ':data_solicitacao' => $data_solicitacao,
                        ':status' => $status_solicitacao
                    ]);
                    $sucesso_solicitacao = '<div class="alert alert-success mt-3" role="alert">Solicitação de exame enviada com sucesso! Guia médica anexada.</div>';
                    $_POST = []; // Limpar o formulário
                } catch (PDOException $e) {
                    $erro_solicitacao = '<div class="alert alert-danger mt-3" role="alert">Erro ao solicitar exame: ' . $e->getMessage() . '</div>';
                    // Remover o arquivo enviado em caso de erro no banco
                    if (file_exists($caminho_completo)) {
                        unlink($caminho_completo);
                    }
                }
            } else {
                $erro_solicitacao = '<div class="alert alert-danger mt-3" role="alert">Erro ao mover o arquivo da guia médica para o servidor.</div>';
            }
        }
    }
}

// Consultas Agendadas
$sql_consultas = "SELECT c.data_hora, c.status, m.especialidade, u.nome AS medico
                    FROM consultas c
                    JOIN pacientes p ON c.paciente_id = p.id
                    JOIN medicos m ON c.medico_id = m.id
                    JOIN usuarios u ON m.usuario_id = u.id
                    WHERE p.usuario_id = :usuario_id
                    ORDER BY c.data_hora DESC";
$stmt = $pdo->prepare($sql_consultas);
$stmt->execute(['usuario_id' => $usuario_id]);
$consultas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Exames Realizados
$sql_exames = "SELECT e.tipo_exame, e.resultado, e.data_exame
                    FROM exames e
                    JOIN pacientes p ON e.paciente_id = p.id
                    WHERE p.usuario_id = :usuario_id
                    ORDER BY e.data_exame DESC";
$stmt2 = $pdo->prepare($sql_exames);
$stmt2->execute(['usuario_id' => $usuario_id]);
$exames = $stmt2->fetchAll(PDO::FETCH_ASSOC);

// Comentários
$sql_coment = "SELECT cp.mensagem, cp.criado_em FROM comentarios_pacientes cp
                    JOIN pacientes p ON cp.paciente_id = p.id
                    WHERE p.usuario_id = :usuario_id
                    ORDER BY cp.criado_em DESC";

$stmt3 = $pdo->prepare($sql_coment);
$stmt3->execute(['usuario_id' => $usuario_id]);
$comentarios = $stmt3->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Paciente</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body class="container mt-5">

    <h1 class="mb-4">Olá, <?= htmlspecialchars($usuario_nome) ?>! Seja bem-vindo(a) ao seu painel.</h1>

    <?php echo $erro_solicitacao; ?>
    <?php echo $sucesso_solicitacao; ?>

    <h3 class="mt-4">Minhas Consultas</h3>
    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Data e Hora</th>
                    <th>Médico</th>
                    <th>Especialidade</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($consultas)): ?>
                    <tr><td colspan="4">Nenhuma consulta agendada.</td></tr>
                <?php else: ?>
                    <?php foreach ($consultas as $c): ?>
                        <tr>
                            <td><?= date('d/m/Y H:i', strtotime($c['data_hora'])) ?></td>
                            <td><?= htmlspecialchars($c['medico']) ?></td>
                            <td><?= htmlspecialchars($c['especialidade']) ?></td>
                            <td><?= ucfirst(htmlspecialchars($c['status'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <h3 class="mt-5">Meus Exames</h3>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Tipo</th>
                    <th>Resultado</th>
                    <th>Data</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($exames)): ?>
                    <tr><td colspan="3">Nenhum exame realizado.</td></tr>
                <?php else: ?>
                    <?php foreach ($exames as $e): ?>
                        <tr>
                            <td><?= htmlspecialchars($e['tipo_exame']) ?></td>
                            <td><?= nl2br(htmlspecialchars($e['resultado'])) ?></td>
                            <td><?= date('d/m/Y', strtotime($e['data_exame'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="accordion mt-5" id="accordionPaciente">
        <div class="card">
            <div class="card-header" id="headingConsulta">
                <h2 class="mb-0">
                    <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapseConsulta" aria-expanded="false" aria-controls="collapseConsulta">
                        Solicitar Consulta
                    </button>
                </h2>
            </div>

            <div id="collapseConsulta" class="collapse" aria-labelledby="headingConsulta" data-parent="#accordionPaciente">
                <div class="card-body">
                    <form method="POST" action="" class="mb-3">
                        <div class="form-group">
                            <label for="especialidade">Especialidade Desejada</label>
                            <select class="form-control" id="especialidade" name="especialidade" required>
                                <option value="">Selecione a Especialidade</option>
                                <?php foreach ($especialidades as $especialidade): ?>
                                    <option value="<?= htmlspecialchars($especialidade['especialidade']) ?>"><?= htmlspecialchars($especialidade['especialidade']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary" name="solicitar_consulta">Solicitar Consulta</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header" id="headingExame">
                <h2 class="mb-0">
                    <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapseExame" aria-expanded="false" aria-controls="collapseExame">
                        Solicitar Exame
                    </button>
                </h2>
            </div>
            <div id="collapseExame" class="collapse" aria-labelledby="headingExame" data-parent="#accordionPaciente">
                <div class="card-body">
                    <form method="POST" action="" enctype="multipart/form-data" class="mb-3">
                        <div class="form-group">
                            <label for="tipo_exame">Tipo de Exame</label>
                            <input type="text" class="form-control" id="tipo_exame" name="tipo_exame" placeholder="Digite o tipo de exame" required>
                        </div>
                        <div class="form-group">
                            <label for="arquivo_guia">Guia Médica (PDF)</label>
                            <input type="file" class="form-control-file" id="arquivo_guia" name="arquivo_guia" accept="application/pdf" required>
                            <small class="form-text text-muted">Envie a guia médica em formato PDF.</small>
                        </div>
                        <button type="submit" class="btn btn-info" name="solicitar_exame">Solicitar Exame</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header" id="headingComentario">
                <h2 class="mb-0">
                    <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapseComentario" aria-expanded="false" aria-controls="collapseComentario">
                        Deixar Comentário
                    </button>
                </h2>
            </div>
            <div id="collapseComentario" class="collapse" aria-labelledby="headingComentario" data-parent="#accordionPaciente">
                <div class="card-body">
                    <form action="comentario_paciente.php" method="post" class="mb-3">
                        <div class="form-group">
                            <textarea name="mensagem" class="form-control" rows="4" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Enviar Comentário</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <h3 class="mt-5">Meus Comentários</h3>
    <?php if (empty($comentarios)): ?>
        <p>Nenhum comentário registrado.</p>
    <?php else: ?>
        <ul class="list-group">
            <?php foreach ($comentarios as $c): ?>
                <li class="list-group-item">
                    <strong><?= date('d/m/Y H:i', strtotime($c['criado_em'])) ?>:</strong><br>
                    <?= nl2br(htmlspecialchars($c['mensagem'])) ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>