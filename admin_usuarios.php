<?php
require_once __DIR__ . '/conexion.php';
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'admin') {
    header('Location: panel.php');
    exit;
}

$buscar = $_GET['buscar'] ?? '';

if ($buscar !== '') {
    $stmt = $conexion->prepare("SELECT id, nombre, apellido, dni, email, rol FROM usuarios WHERE fecha_archivado IS NULL AND (nombre LIKE :buscar OR apellido LIKE :buscar OR email LIKE :buscar OR dni LIKE :buscar) ORDER BY id DESC");
    $stmt->execute(['buscar' => '%' . $buscar . '%']);
} else {
    $stmt = $conexion->query("SELECT id, nombre, apellido, dni, email, rol FROM usuarios WHERE fecha_archivado IS NULL ORDER BY id DESC");
}

$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
     <link rel="stylesheet" href="style.css">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administración - Usuarios</title>
</head>
<body>

<h1>Panel de Administración - Usuarios</h1>

<a href="panel.php">Volver al Panel</a>
<a href="usuarios_archivados.php">Ver archivados</a>
<a href="crear_usuario.php">crear usuario</a>


<br><br>

<form method="GET">
    <input type="text" name="buscar" placeholder="Buscar por nombre, email o DNI..." value="<?php echo htmlspecialchars($buscar); ?>">
    <button type="submit">Buscar</button>
    <?php if ($buscar !== ''): ?>
        <a href="admin_usuarios.php">Limpiar</a>
    <?php endif; ?>
</form>
<br>

<table >
    <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Apellido</th>
        <th>DNI</th>
        <th>Email</th>
        <th>Rol</th>
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
        <td>
            <a href="editar_usuario.php?id=<?php echo $u['id']; ?>">Editar</a>
            |
            <a href="eliminar_usuario.php?id=<?php echo $u['id']; ?>" onclick="return confirm('¿Seguro que querés eliminar a <?php echo htmlspecialchars($u['nombre']); ?>?')">Eliminar</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<?php if (count($usuarios) === 0): ?>
    <p>No hay usuarios para mostrar.</p>
<?php endif; ?>

</body>
</html>

