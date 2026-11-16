<?php
session_start();
require_once __DIR__ . '/conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$nombre = $_SESSION['usuario_nombre'] ?? '';
$apellido = $_SESSION['usuario_apellido'] ?? '';
$email = $_SESSION['usuario_email'] ?? '';
$rol = $_SESSION['usuario_rol'] ?? 'usuario';

function escapar(string $valor): string {
    return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi panel - Destinia</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <h1>Destinia</h1>
    <p>Tu próximo destino empieza acá</p>
</header>
<nav>
    <a href="index.php">Inicio</a>
    <a href="panel.php">Mi panel</a>
    <a href="cerrar_sesion.php">Cerrar sesión</a>
</nav>

<div class="caja">
    <h2>Hola, <?= escapar($nombre) ?> <?= escapar($apellido) ?></h2>
    <p>Entraste con: <strong><?= escapar($email) ?></strong></p>
    <p>Desde acá vas a ver tus viajes y reservas cuando los agreguemos.</p>

    <?php if ($rol === 'admin'): ?>
        <hr>
        <p><strong>Sos administradora</strong></p>
        <p><a href="admin_usuarios.php">Panel de Administración</a></p>
        <p><a href="admin_destinos.php">Administrar Destinos</a></p>
        <p><a href="crear_destino.php"> Crear Nuevo Destino</a></p>
    <?php endif; ?>

    <p><a href="cerrar_sesion.php">Cerrar sesión</a></p>
</div>
</body>
</html>

