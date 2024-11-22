<?php
    session_start();
    $error = "";

    if (!isset($_SESSION["user_data"]) || $_SESSION["user_data"]["group"] == "admin") {
        header("Location: index.php");
        exit();
    }

    require("./src/requirements/db.php");

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $pedido = [];

        foreach ($_POST['productos'] as $producto) {
            $id = $producto['id'];
            $cantidad = $producto['quantity'];

            if ($cantidad > 0) {
                $pedido[] = [$id, $cantidad];
            }
        }

        $pedido_json = json_encode($pedido);
        $user_id = $_SESSION["user_data"]["ID"];
        $current_date = date("d/m/Y");

        if ($user_id === null || sizeof($pedido) == 0 || empty($current_date)) {
            $error = "Error: algunos valores necesarios son nulos o vacíos.";
        } else {
            $sql = "INSERT INTO orders (owner_id, products, `date`) VALUES (?, ?, ?)";
            $stmt = $conexion->prepare($sql);
    
            if ($stmt === false) {
                echo "Error en la preparación de la consulta: " . $conexion->error;
            }
    
            $stmt->bind_param("iss", $user_id, $pedido_json, $current_date);
    
            if ($stmt->execute()) {
                $error = "Pedido registrado con éxito.";
            } else {
                $error = "Error al ejecutar la consulta: " . $stmt->error;
            }
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
    <title>Registrar Pedido</title>
</head>
<body>
    <?php include "./src/components/side-bar.php";?>
    <div class="container">
        <div class="content">
            <div class="content-2">
                <div class="recent-payments">
                    <div class="title">
                        <h2>Registrar pedido</h2>
                    </div>
                    <form action="" method="POST">
                        <div class="inventory">
                            <?php if (isset($mensaje)): ?>
                                <p><?php echo $mensaje; ?></p>
                            <?php endif; ?>
                            <?php while ($producto = $result->fetch_assoc()): ?>
                                <div class="product">
                                    <?php if ($producto['img']): ?>
                                        <img src="data:image/jpeg;base64,<?php echo $producto['img']; ?>" alt="Imagen del Producto">
                                    <?php else: ?>
                                        <p>No hay imagen disponible</p>
                                    <?php endif; ?>
                                    <h3><?php echo $producto['name']; ?></h3>
                                    <p>Stock: <?php echo $producto['quantity']; ?></p>
                                    <div class="buttons">
                                        <input type="hidden" name="productos[<?php echo $producto['id']; ?>][id]" value="<?php echo $producto['id']; ?>"/>
                                        <input type="number" name="productos[<?php echo $producto['id']; ?>][quantity]" id="quantity" value="0" min="0"/>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                            
                        </div>
                        
                        <input type="submit" value="Pedir" class="btn"/>
                    </form>
                    <br>
                    <span><?php echo $error; ?></span>
                </div>
            </div>
        </div>
    </div>
</body>
</html>