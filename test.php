<?php
// Código de conexión y consulta para obtener el producto
require("./src/requirements/db.php");
$sql = "SELECT name, quantity, img FROM productos WHERE id = ?"; // Supón que tienes el ID del producto
$stmt = $conexion->prepare($sql);
$id = 1; // Reemplaza con el ID del producto que deseas obtener
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

// Obtener el producto
if ($producto = $result->fetch_assoc()) {
    $nombre = $producto['name'];
    $cantidad = $producto['quantity'];
    $imagenBase64 = $producto['img']; // Esto es la imagen en Base64
} else {
    echo "Producto no encontrado";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle del Producto</title>
</head>
<body>
    <h1><?php echo htmlspecialchars($nombre); ?></h1>
    <p>Cantidad: <?php echo htmlspecialchars($cantidad); ?></p>

    <!-- Mostrar la imagen decodificándola -->
    <div>
        <h2>Imagen del Producto:</h2>
        <?php if ($imagenBase64): ?>
            <img src="data:image/jpeg;base64,<?php echo $imagenBase64; ?>" alt="Imagen del Producto">
        <?php else: ?>
            <p>No hay imagen disponible</p>
        <?php endif; ?>
    </div>
</body>
</html>