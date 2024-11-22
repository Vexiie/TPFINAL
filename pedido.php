<?php 

session_start();

// Verificar si el usuario está logueado
if ($_SESSION["user_data"] == null) {
    header("Location: index.php");
    exit();
}

require("./src/requirements/db.php");

// Obtener el ID del usuario desde la sesión
$user_id = $_SESSION["user_data"]["ID"];

// Obtener el pedido desde la base de datos para el usuario logueado
$sql = "SELECT id, products, `date` FROM orders WHERE owner_id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $user_id); // Vinculamos el ID del usuario
$stmt->execute();
$result = $stmt->get_result();

// Verificamos si encontramos el pedido del usuario
if ($result->num_rows > 0) {
    $pedido = $result->fetch_assoc();
    // Decodificamos los productos del pedido (en formato JSON)
    $productos = json_decode($pedido['products'], true);
} else {
    $pedido = null;
}

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
                        <h2>Pedido <?php echo ($pedido ? "#".$pedido['id'] : "No hay pedidos"); ?></h2>
                    </div>
                    
                    <?php if ($pedido): ?>
                    <table>
                        <tr>
                            <th>Producto</th>
                            <th>Descripción</th>
                            <th>Fecha</th>
                        </tr>
                        <?php foreach ($productos as $producto): ?>
                            <tr>
                                <td><?php 
                                $sql = "SELECT name FROM productos WHERE ID = '".$producto[0]."'";
                                $resultado = $conexion->query($sql);
                                $row = $resultado->fetch_assoc();
                                echo $row['name']; 
                                ?></td>
                                <td><?php echo $producto[1];?></td>
                                <td><?php echo $pedido['date'];?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                    <a href="pedidos.php" class="btn">Aceptar</a>
                    <?php else: ?>
                        <p>No hay pedidos registrados para este usuario.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
