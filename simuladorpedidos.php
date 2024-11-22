<?php
session_start();

if ($_SESSION["user_data"] == null) {
    header("Location: index.php");
    exit();
}

require("./src/requirements/db.php");

$error = "";

// Procesar el pedido
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["productos"])) {
    $productos_seleccionados = $_POST["productos"];
    $usuario_id = $_SESSION["user_data"]["ID"];
    $fecha = date("Y-m-d H:i:s");

    // Validamos que haya productos seleccionados
    $pedido = [];
    foreach ($productos_seleccionados as $id => $cantidad) {
        if ($cantidad > 0) {
            $pedido[] = [$id, $cantidad];
        }
    }

    // Si hay productos seleccionados, guardamos el pedido y actualizamos el inventario
    if (count($pedido) > 0) {
        $pedido_json = json_encode($pedido); // Convertimos los productos a formato JSON

        // Iniciar una transacción para asegurar que ambos procesos (insertar pedido y actualizar inventario) sean atómicos
        $conexion->begin_transaction();

        try {
            // Insertar el pedido en la tabla `orders`
            $sql = "INSERT INTO orders (owner_id, products, `date`) VALUES (?, ?, ?)";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("iss", $usuario_id, $pedido_json, $fecha);
            $stmt->execute();

            // Actualizar el inventario restando las cantidades solicitadas
            foreach ($pedido as $producto) {
                $producto_id = $producto[0];
                $cantidad_pedida = $producto[1];

                // Obtener la cantidad actual del producto
                $sql = "SELECT quantity FROM productos WHERE id = ?";
                $stmt = $conexion->prepare($sql);
                $stmt->bind_param("i", $producto_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $producto_info = $result->fetch_assoc();

                if ($producto_info) {
                    $cantidad_actual = $producto_info['quantity'];

                    // Verificamos si hay suficiente stock
                    if ($cantidad_actual >= $cantidad_pedida) {
                        // Restamos la cantidad del producto
                        $nueva_cantidad = $cantidad_actual - $cantidad_pedida;
                        $sql = "UPDATE productos SET quantity = ? WHERE id = ?";
                        $stmt = $conexion->prepare($sql);
                        $stmt->bind_param("ii", $nueva_cantidad, $producto_id);
                        $stmt->execute();
                    } else {
                        throw new Exception("No hay suficiente stock del producto " . $producto_id);
                    }
                } else {
                    throw new Exception("Producto no encontrado: " . $producto_id);
                }
            }

            // Commit de la transacción
            $conexion->commit();
            $error = "Pedido realizado con éxito.";

        } catch (Exception $e) {
            // Si algo falla, deshacemos los cambios
            $conexion->rollback();
            $error = "Error al realizar el pedido: " . $e->getMessage();
        }
    } else {
        $error = "Por favor, selecciona al menos un producto.";
    }
}

// Obtener los productos disponibles
$sql = "SELECT id, name, quantity, img FROM productos";
$result = $conexion->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <title>Simulador de Pedidos</title>
</head>
<body>
    <?php include "./src/components/side-bar.php";?>
    <div class="container">
        <div class="content">
            <div class="content-2">
                <div class="recent-payments">
                    <div class="title">
                        <h2>Simulador de Pedidos</h2>
                    </div>
                    <form method="POST" action="">
                        <div class="inventory">
                            <?php if (isset($error)): ?>
                                <p><?php echo $error; ?></p>
                            <?php endif; ?>

                            <?php while ($producto = $result->fetch_assoc()): ?>
                                <div class="product">
                                    <h3><?php echo $producto['name']; ?></h3>
                                    <?php if ($producto['img']): ?>
                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($producto['img']); ?>" alt="Imagen del Producto">
                                    <?php else: ?>
                                        <p>No hay imagen disponible</p>
                                    <?php endif; ?>
                                    <p>Stock: <?php echo $producto['quantity']; ?></p>
                                    <label for="producto-<?php echo $producto['id']; ?>">Cantidad</label>
                                    <input type="number" id="producto-<?php echo $producto['id']; ?>" name="productos[<?php echo $producto['id']; ?>]" min="0" max="<?php echo $producto['quantity']; ?>" value="0" />
                                </div>
                            <?php endwhile; ?>
                        </div>

                        <input type="submit" value="Realizar Pedido" class="btn">
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
