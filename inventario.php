<?php 

session_start();

if (!isset($_SESSION["user_data"]) || $_SESSION["user_data"]["group"] != "admin") {
    header("Location: index.php");
    exit();
}

require("./src/requirements/db.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    

    if (isset($_POST['id']) && isset($_POST['quantity'])) {
        $id = $_POST['id'];
        $cantidad = $_POST['quantity'];  // Usamos la variable $cantidad

        // Actualizar la cantidad del producto
        $sql = "UPDATE productos SET quantity = ? WHERE id = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("ii", $cantidad, $id);
        $stmt->execute();
    }


    if (isset($_POST['delete_id'])) {
        $delete_id = $_POST['delete_id'];

        // Eliminar el producto
        $sql = "DELETE FROM productos WHERE id = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("i", $delete_id);
        $stmt->execute();
    }
}

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
    <title>Admin Panel</title>
</head>
<body>
    <?php include "./src/components/side-bar.php";?>
    <div class="container">
        <div class="content">
            <div class="content-2">
                <div class="recent-payments">
                    <div class="title">
                        <h2>Inventario</h2>
                    </div>
                    
                        <div class="inventory">
                            <?php if (isset($mensaje)): ?>
                                <p><?php echo $mensaje; ?></p>
                            <?php endif; ?>

                            <?php while ($producto = $result->fetch_assoc()): ?>
                                <div class="product">
                                    <?php if ($producto['img']): ?>
                                        <img src="data:image/jpeg;base64,<?php echo $producto['img']; ?>" alt="Imagen del Producto" width=100px>
                                    <?php else: ?>
                                        <p>No hay imagen disponible</p>
                                    <?php endif; ?>
                                    <h3><?php echo $producto['name']; ?></h3>
                                    <div class="buttons">
                                        <form action="" method="POST" style="display:inline;">
                                        <!-- Formulario de modificación de cantidad -->
                                        <input type="hidden" name="id" value="<?php echo $producto['id']; ?>"/>
                                        <input type="number" name="quantity" id="quantity" value="<?php echo $producto['quantity']; ?>" min="0"/>
                                        <input type="submit" value="Modificar" class="btn"/>
                                        </form>
                                        <form action="" method="POST" style="display:inline;">
                                        <!-- Formulario de eliminación de producto -->
                                        <input type="hidden" name="delete_id" value="<?php echo $producto['id']; ?>"/>
                                        <input type="submit" value="Eliminar" class="btn" onclick="return confirm('¿Estás seguro de que quieres eliminar este producto?');"/>
                                        </form>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    
                </div>
            </div>
        </div>
    </div>
</body>
</html>
