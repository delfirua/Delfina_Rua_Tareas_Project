<?php
session_start();
require_once __DIR__ . '/conexion.php';

$error = '';
$email = '';

function escapar(string $valor): string {
    return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
}

if (isset($_SESSION['usuario_id'])) {
    header('Location: panel.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $contrasena = $_POST['contrasena'] ?? '';

    if ($email === '' || $contrasena === '') {
        $error = 'Completá correo y contraseña.';
    } else {
        $stmt = $conexion->prepare("SELECT id, nombre, apellido, email, password_hash, rol FROM usuarios WHERE email = :email AND fecha_archivado IS NULL LIMIT 1");
        $stmt->execute(['email' => $email]);
        $usuario = $stmt->fetch();

        if ($usuario && password_verify($contrasena, $usuario['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nombre'] = $usuario['nombre'];
            $_SESSION['usuario_apellido'] = $usuario['apellido'];
            $_SESSION['usuario_email'] = $usuario['email'];
            $_SESSION['usuario_rol'] = $usuario['rol'] ?? 'usuario';
            header('Location: panel.php');
            exit;
        } else {
            $error = 'Correo o contraseña incorrectos.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar sesión - Destinia</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <h1>Destinia</h1>
    <p>Tu próximo destino empieza acá</p>
</header>
<nav>
    <a href="index.php">Inicio</a>
    <a href="registro.php">Registrarse</a>
    <a href="login.php">Iniciar sesión</a>
</nav>
<div class="caja">
    <h2>Iniciar sesión</h2>
    <?php if ($error !== ''): ?><p class="error"><?= escapar($error) ?></p><?php endif; ?>
    <form action="login.php" method="POST">
        <label>Correo<input type="email" name="email" value="<?= escapar($email) ?>" maxlength="254" required></label>
        <label>Contraseña<input type="password" name="contrasena" required></label>
        <button type="submit">Entrar</button>
    </form>
    <p>¿No tenés cuenta? <a href="registro.php">Registrate acá</a></p>
</div>
</body>
</html>
