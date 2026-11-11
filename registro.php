<?php
require_once __DIR__ . '/conexion.php';

$errores = [];
$mensaje = '';
$nombre = '';
$apellido = '';
$dni = '';
$email = '';

function escapar(string $valor): string {
    return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $dni = trim($_POST['dni'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $contrasena = $_POST['contrasena'] ?? '';

    if ($nombre === '') $errores[] = 'El nombre es obligatorio.';
    if ($apellido === '') $errores[] = 'El apellido es obligatorio.';
    if ($dni === '' || !preg_match('/^[0-9]{7,8}$/', $dni)) $errores[] = 'El DNI debe tener 7 u 8 dígitos.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errores[] = 'Ingresá un correo válido.';
    if ($contrasena === '') $errores[] = 'La contraseña es obligatoria.';

    if (empty($errores)) {
        try {
            $hash = password_hash($contrasena, PASSWORD_DEFAULT);
            $stmt = $conexion->prepare("INSERT INTO usuarios (nombre, apellido, dni, email, password_hash) VALUES (:nombre, :apellido, :dni, :email, :password_hash)");
            $stmt->execute([
                'nombre' => $nombre,
                'apellido' => $apellido,
                'dni' => $dni,
                'email' => $email,
                'password_hash' => $hash
            ]);
            $mensaje = 'Cuenta creada. Ahora podés iniciar sesión.';
            $nombre = ''; $apellido = ''; $dni = ''; $email = '';
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $errores[] = 'Ese DNI o correo ya está registrado.';
            } else {
                error_log($e->getMessage());
                $errores[] = 'Ocurrió un error al guardar los datos.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro - Destinia</title>
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
    <h2>Crear cuenta</h2>
    <?php if ($mensaje !== ''): ?><p class="exito"><?= escapar($mensaje) ?></p><?php endif; ?>
    <?php if (!empty($errores)): ?><ul class="error"><?php foreach ($errores as $error): ?><li><?= escapar($error) ?></li><?php endforeach; ?></ul><?php endif; ?>
    <form action="registro.php" method="POST">
        <label>Nombre<input type="text" name="nombre" value="<?= escapar($nombre) ?>" maxlength="80" required></label>
        <label>Apellido<input type="text" name="apellido" value="<?= escapar($apellido) ?>" maxlength="80" required></label>
        <label>DNI<input type="text" name="dni" value="<?= escapar($dni) ?>" inputmode="numeric" pattern="[0-9]{7,8}" maxlength="8" required></label>
        <label>Correo<input type="email" name="email" value="<?= escapar($email) ?>" maxlength="254" required></label>
        <label>Contraseña<input type="password" name="contrasena" required></label>
        <button type="submit">Registrarme</button>
    </form>
</div>
</body>
</html>
