<?php
require_once '../proteger.php';
require_once '../conexao.php';

if ($_SESSION['tipo'] !== 'paciente') {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mensagem'])) {
    $mensagem = trim($_POST['mensagem']);
    if (empty($mensagem)) {
        header("Location: paciente.php?erro=mensagem_vazia");
        exit;
    }

    $usuario_id = $_SESSION['usuario_id'];
    $sql_paciente = "SELECT id FROM pacientes WHERE usuario_id = ?";
    $stmt = $conn->prepare($sql_paciente);
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $stmt->bind_result($paciente_id);
    $stmt->fetch();
    $stmt->close();

    $sql_insert = "INSERT INTO comentarios_pacientes (paciente_id, mensagem) VALUES (?, ?)";
    $stmt = $conn->prepare($sql_insert);
    $stmt->bind_param("is", $paciente_id, $mensagem);
    if ($stmt->execute()) {
        header("Location: paciente.php?sucesso=comentario_enviado");
    } else {
        header("Location: paciente.php?erro=erro_bd");
    }
    exit;
}
?>
