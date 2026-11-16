<?php
require_once __DIR__ . '/conexion.php';
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'admin') {
    header('Location: panel.php');
    exit;
}

$error = '';
$nombre = '';
$apellido = '';
$dni = '';
$email = '';
$rol = 'usuario';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $dni = trim($_POST['dni'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $contrasena = $_POST['contrasena'] ?? '';
    $rol = $_POST['rol'] ?? 'usuario';

    if ($nombre === '' || $apellido === '' || $dni === '' || $email === '' || $contrasena === '') {
        $error = 'Completá todos los campos.';
    } elseif ($rol !== 'usuario' && $rol !== 'admin') {
        $error = 'Rol inválido.';
    } else {
        $stmt = $conexion->prepare("SELECT id FROM usuarios WHERE email = :email OR dni = :dni LIMIT 1");
        $stmt->execute(['email' => $email, 'dni' => $dni]);
        if ($stmt->fetch()) {
            $error = 'Ya existe un usuario con ese email o DNI.';
        } else {
            $password_hash = password_hash($contrasena, PASSWORD_DEFAULT);
            $stmt = $conexion->prepare("INSERT INTO usuarios (nombre, apellido, dni, email, password_hash, rol) VALUES (:nombre, :apellido, :dni, :email, :password_hash, :rol)");
            $stmt->execute([
                'nombre' => $nombre,
                'apellido' => $apellido,
                'dni' => $dni,
                'email' => $email,
                'password_hash' => $password_hash,
                'rol' => $rol
            ]);

            header('Location: admin_usuarios.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
    <link rel="stylesheet" href="style.css">
<head>
    <meta charset="UTF-8">
    <title>Crear Usuario</title>
</head>
<body>

<h1>Crear Usuario</h1>

<a href="admin_usuarios.php">Volver a Usuarios</a>
<br><br>

<?php if ($error !== ''): ?>
    <p><?php echo htmlspecialchars($error); ?></p>
<?php endif; ?>

<form method="POST">
    <label>Nombre<br>
        <input type="text" name="nombre" value="<?php echo htmlspecialchars($nombre); ?>" required>
    </label>
    <br><br>

    <label>Apellido<br>
        <input type="text" name="apellido" value="<?php echo htmlspecialchars($apellido); ?>" required>
    </label>
    <br><br>

    <label>DNI<br>
        <input type="text" name="dni" value="<?php echo htmlspecialchars($dni); ?>" required>
    </label>
    <br><br>

    <label>Email<br>
        <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
    </label>
    <br><br>

    <label>Contraseña<br>
        <input type="password" name="contrasena" required>
    </label>
    <br><br>

    <label>Rol<br>
        <select name="rol">
            <option value="usuario" <?php if ($rol === 'usuario') echo 'selected'; ?>>Usuario</option>
            <option value="admin" <?php if ($rol === 'admin') echo 'selected'; ?>>Admin</option>
        </select>
    </label>
    <br><br>

    <button type="submit">Guardar</button>
</form>

</body>
</html>
