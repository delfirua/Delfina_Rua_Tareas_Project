<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Destinia</title>
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
    <h2>Bienvenido a Destinia</h2>
    <p>Creá tu cuenta para guardar tus viajes, ver tus datos y viajar con nosotras.</p>

    <?php if (isset($_SESSION['id'])): ?>
        <p>Ya estás logueada como <b><?php echo htmlspecialchars($_SESSION['nombre'], ENT_QUOTES, 'UTF-8'); ?></b></p>
        <a class="btn" href="panel.php">Ir a mi panel</a>
        <a class="btn" href="cerrar_sesion.php">Cerrar sesión</a>
    <?php else: ?>
        <a class="btn" href="registro.php">Registrarme</a>
        <a class="btn" href="login.php">Iniciar sesión</a>
    <?php endif; ?>

    <img class="foto-index" src="img/banner.jpg" alt="Viajes Destinia">
</div>

</body>
</html>



