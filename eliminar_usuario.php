<?php
require_once __DIR__ . '/conexion.php';
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'admin') {
    header('Location: panel.php');
    exit;
}

$id = $_GET['id'] ?? '';
if ($id === '') {
    header('Location: admin_usuarios.php');
    exit;
}

if ((int)$id === (int)$_SESSION['usuario_id']) {
    header('Location: admin_usuarios.php');
    exit;
}

$stmt = $conexion->prepare("UPDATE usuarios SET fecha_archivado = NOW() WHERE id = :id");
$stmt->execute(['id' => $id]);

header('Location: admin_usuarios.php');
exit;
