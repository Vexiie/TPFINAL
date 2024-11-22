<?php 

session_start();
$error = "";

// Incluir la conexión a la base de datos
require("./src/requirements/db.php");

if (!isset($_SESSION["user_data"]) || $_SESSION["user_data"]["group"] != "admin") {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["name"]) && isset($_POST["quantity"]) && isset($_FILES["image"])) {
        $name = $_POST["name"];
        $quantity = $_POST["quantity"];
        $image = $_FILES["image"]["tmp_name"];

        if ($_FILES["image"]["error"] !== UPLOAD_ERR_OK) {
            $error = "Error al cargar la imagen: " . $_FILES["image"]["error"];
        } else {
            $imageData = file_get_contents($image);
            $base64Image = base64_encode($imageData);
            
            $sql = "INSERT INTO `productos`(`name`, `quantity`, `img`) VALUES (?, ?, ?)";
            $stmt = $conexion->prepare($sql);

            if ($stmt === false) {
                die("Error en la preparación de la consulta: " . $conexion->error);
            }

            $stmt->bind_param("sis", $name, $quantity, $base64Image);
            $resultado = $stmt->execute();

            if ($resultado) {
                $error = "Producto registrado correctamente.";
            } else {
                $error = "Error al registrar el producto: " . $stmt->error;
            }
        }
    } else {
        $error = "Por favor completa los campos";
    }
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
    <?php include "./src/components/side-bar.php"; ?>
    <div class="container">
        <div class="content">
            <div class="content-2">
                <div class="recent-payments">
                    <div class="title">
                        <h2>Registrar producto</h2>
                    </div>
                    <form method="POST" enctype="multipart/form-data">
                        <div class="element">
                            <span>Nombre</span>
                            <br>
                            <input type="text" name="name" required>
                        </div>
                        <div class="element">
                            <span>Cantidad actual</span>
                            <br>
                            <input type="number" name="quantity" required>
                        </div>
                        <div class="element">
                            <span>Imagen</span>
                            <br>
                            <input type="file" name="image" required>
                        </div>                        
                        <span><?php echo $error; ?></span>
                        <br>
                        <input type="submit" value="Registrar Producto" class="btn">
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
