
<?php
require_once __DIR__ . '/conexion.php';
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'admin') {
    header('Location: panel.php');
    exit;
}

$id = $_GET['id'] ?? '';
if ($id === '') {
    header('Location: usuarios_archivados.php');
    exit;
}

$stmt = $conexion->prepare("UPDATE usuarios SET fecha_archivado = NULL WHERE id = :id");
$stmt->execute(['id' => $id]);

header('Location: usuarios_archivados.php');
exit;
