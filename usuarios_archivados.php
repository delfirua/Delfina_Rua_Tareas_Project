<?php
require_once __DIR__ . '/conexion.php';
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'admin') {
    header('Location: panel.php');
    exit;
}

$buscar = trim($_GET['buscar'] ?? '');

if ($buscar !== '') {
    $stmt = $conexion->prepare("SELECT id, nombre, apellido, dni, email, rol, fecha_archivado FROM usuarios WHERE fecha_archivado IS NOT NULL AND (nombre LIKE :buscar OR apellido LIKE :buscar OR dni LIKE :buscar OR email LIKE :buscar) ORDER BY fecha_archivado DESC");
    $stmt->execute(['buscar' => '%' . $buscar . '%']);
} else {
    $stmt = $conexion->query("SELECT id, nombre, apellido, dni, email, rol, fecha_archivado FROM usuarios WHERE fecha_archivado IS NOT NULL ORDER BY fecha_archivado DESC");
}
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
     <link rel="stylesheet" href="style.css">
<head>
    <meta charset="UTF-8">
    <title>Usuarios Archivados</title>
    
</head>
<body>

<h1>Usuarios Archivados</h1>

<a href="admin_usuarios.php">Volver a Usuarios</a>
<a href="panel.php">Volver al Panel</a>
<br><br>

<form method="GET">
    <input type="text" name="buscar" placeholder="Buscar por nombre, DNI o email..." value="<?php echo htmlspecialchars($buscar); ?>">
    <button type="submit">Buscar</button>
    <?php if ($buscar !== ''): ?>
        <a href="usuarios_archivados.php">Limpiar</a>
    <?php endif; ?>
</form>

<p>Total archivados: <?php echo count($usuarios); ?></p>

<table>
    <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Apellido</th>
        <th>DNI</th>
        <th>Email</th>
        <th>Rol</th>
        <th>Fecha Archivado</th>
        <th>Acciones</th>
    </tr>
    <?php foreach ($usuarios as $u): ?>
    <tr>
        <td><?php echo $u['id']; ?></td>
        <td><?php echo htmlspecialchars($u['nombre']); ?></td>
        <td><?php echo htmlspecialchars($u['apellido']); ?></td>
        <td><?php echo htmlspecialchars($u['dni']); ?></td>
        <td><?php echo htmlspecialchars($u['email']); ?></td>
        <td><?php echo htmlspecialchars($u['rol']); ?></td>
        <td><?php echo $u['fecha_archivado']; ?></td>
        <td>
            <a href="restaurar_usuario.php?id=<?php echo $u['id']; ?>" onclick="return confirm('¿Seguro que querés restaurar a <?php echo htmlspecialchars($u['nombre']); ?>?')">Restaurar</a>
            |
            <a href="eliminar_definitivo.php?id=<?php echo $u['id']; ?>" onclick="return confirm('¿Seguro que querés ELIMINAR PARA SIEMPRE a <?php echo htmlspecialchars($u['nombre']); ?>? Esto no se puede deshacer.')">Eliminar definitivo</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<?php if (count($usuarios) === 0): ?>
    <p>No hay usuarios archivados.</p>
<?php endif; ?>

</body>
</html>
