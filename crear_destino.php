<?php
session_start();
require 'conexion.php';


if (!isset($_SESSION['usuario_id']) || ($_SESSION['usuario_rol'] ?? '') !== 'admin') {
    header("Location: login.php");
    exit;
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $pais = trim($_POST['pais'] ?? '');
    $ciudad = trim($_POST['ciudad'] ?? '');
    $precio = $_POST['precio'] ?? 0;
    $descripcion = trim($_POST['descripcion'] ?? '');
    $ruta_imagen = null;

    if ($nombre === '' || $pais === '' || $ciudad === '' || $precio === '') {
        $error = "Completá todos los campos obligatorios.";
    } elseif (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $archivo = $_FILES['imagen'];

     
        if ($archivo) {
            $error = "La imagen no puede superar los 2MB.";
        } else {
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $tipo = $finfo->file($archivo['tmp_name']);
            $permitidos = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];

            if (!isset($permitidos[$tipo])) {
                $error = "Solo se permiten JPG, PNG o WEBP.";
            } else {
                $ext = $permitidos[$tipo];
                $nombre_archivo = bin2hex(random_bytes(16)) . '.' . $ext;
                $destino = __DIR__ . '/img/' . $nombre_archivo;

                if (move_uploaded_file($archivo['tmp_name'], $destino)) {
                    $ruta_imagen = 'img/' . $nombre_archivo;
                } else {
                    $error = "Error al subir la imagen.";
                }
            }
        }
    }

    if ($error === "") {
        $stmt = $conexion->prepare("INSERT INTO destinos (nombre, pais, ciudad, precio, descripcion, imagen) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nombre, $pais, $ciudad, $precio, $descripcion, $ruta_imagen]);
        header("Location: admin_destinos.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Crear destino - Destinia</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<nav>
    <a href="panel.php">Volver al Panel</a>
    <a href="admin_destinos.php">Ir a Destinos</a>
    
</nav>

<h2>Crear nuevo destino</h2>

<?php if ($error): ?>
    <p style="color:red"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
    <label>Nombre del viaje:</label>
    <input type="text" name="nombre" required>

    <label>País:</label>
    <input type="text" name="pais" required>

    <label>Ciudad:</label>
    <input type="text" name="ciudad" required>

    <label>Precio:</label>
    <input type="number" name="precio" step="0.01" required>

    <label>Descripción:</label>
    <textarea name="descripcion"></textarea>

    <label>Foto del destino:</label>
    <input type="file" name="imagen" accept="image/jpeg,image/png,image/webp">

    <button type="submit">Guardar destino</button>
</form>

<a href="admin_destinos.php">Volver</a>
</body>
</html>
