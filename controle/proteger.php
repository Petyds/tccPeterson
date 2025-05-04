<?php
session_start();
function proteger($tiposPermitidos = []) {
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['tipo'])) {
        header("Location: login.php");
        exit;
    }
    if (!in_array($_SESSION['tipo'], $tiposPermitidos)) {
        echo "Acesso negado.";
        exit;
    }
}
?>