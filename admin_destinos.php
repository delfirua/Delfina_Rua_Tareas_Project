<?php
session_start();
require_once __DIR__ . '/conexion.php';

if (!isset($_SESSION['usuario_id']) || ($_SESSION['usuario_rol'] ?? '') !== 'admin') {
    header('Location: login.php');
    exit;
}

function escapar(string $valor): string {
    return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
}

$stmt = $conexion->query("SELECT * FROM destinos WHERE fecha_archivado IS NULL ORDER BY fecha_creacion DESC");
$destinos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administrar Destinos - Destinia</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <h1>Destinia</h1>
    <p>Administrar destinos</p>
</header>

<nav>
    <a href="panel.php">← Volver al Panel</a>
    <a href="admin_usuarios.php">Usuarios</a>
    <a href="crear_destino.php">Crear Nuevo Destino</a>
    <a href="cerrar_sesion.php">Cerrar sesión</a>
  
</nav>

<div class="caja">
    <h2>Destinos cargados (<?= count($destinos) ?>)</h2>

    <?php if (empty($destinos)): ?>
        <p>Todavía no cargaste ningún destino.</p>
        <p><a href="crear_destino.php">Crear el primer destino</a></p>
    <?php else: ?>
        <table >
            <tr>
                <th>Foto</th>
                <th>Nombre</th>
                <th>País / Ciudad</th>
                <th>Precio</th>
                
            </tr>
            <?php foreach ($destinos as $d): ?>
            <tr>
                <td>
                    <?php if (!empty($d['imagen'])): ?>
                        <img src="<?= escapar($d['imagen']) ?>" alt="" style="width:100px; height:70px; object-fit:cover;">
                    <?php else: ?>
                        Sin foto
                    <?php endif; ?>
                </td>
                <td><strong><?= escapar($d['nombre']) ?></strong><br><small><?= escapar($d['descripcion'] ?? '') ?></small></td>
                <td><?= escapar($d['pais']) ?> - <?= escapar($d['ciudad']) ?></td>
                <td>$<?= number_format($d['precio'], 2, ',', '.') ?></td>
               
            </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</div>
</body>
</html>

