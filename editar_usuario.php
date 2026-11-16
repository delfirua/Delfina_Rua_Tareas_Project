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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $dni = trim($_POST['dni'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $rol = $_POST['rol'] ?? 'usuario';

    $stmt = $conexion->prepare("UPDATE usuarios SET nombre = :nombre, apellido = :apellido, dni = :dni, email = :email, rol = :rol WHERE id = :id");
    $stmt->execute([
        'nombre' => $nombre,
        'apellido' => $apellido,
        'dni' => $dni,
        'email' => $email,
        'rol' => $rol,
        'id' => $id
    ]);

    header('Location: admin_usuarios.php');
    exit;
}


$stmt = $conexion->prepare("SELECT id, nombre, apellido, dni, email, rol FROM usuarios WHERE id = :id");
$stmt->execute(['id' => $id]);
$usuario = $stmt->fetch();

if (!$usuario) {
    header('Location: admin_usuarios.php');
    exit;
}

function escapar(string $valor): string {
    return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar usuario - Destinia</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <h1>Editar usuario</h1>
</header>
<nav>
    <a href="panel.php">Mi panel</a>
    <a href="admin_usuarios.php">Panel de Administración</a>
    <a href="cerrar_sesion.php">Cerrar sesión</a>
</nav>

<div class="caja">
    <form method="POST">
        <p>Nombre:</p>
        <input type="text" name="nombre" value="<?= escapar($usuario['nombre']) ?>" required><br><br>

        <p>Apellido:</p>
        <input type="text" name="apellido" value="<?= escapar($usuario['apellido']) ?>" required><br><br>

        <p>DNI:</p>
        <input type="text" name="dni" value="<?= escapar($usuario['dni']) ?>" required><br><br>

        <p>Email:</p>
        <input type="email" name="email" value="<?= escapar($usuario['email']) ?>" required><br><br>

        <p>Rol:</p>
        <select name="rol">
            <option value="usuario" <?= $usuario['rol'] === 'usuario' ? 'selected' : '' ?>>usuario</option>
            <option value="admin" <?= $usuario['rol'] === 'admin' ? 'selected' : '' ?>>admin</option>
        </select><br><br>

        <button type="submit">Guardar cambios</button>
        <a href="admin_usuarios.php">Cancelar</a>
    </form>
</div>
</body>
</html>
