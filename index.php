<?php

session_start();

if (isset($_SESSION["user_data"])) {
    header("Location: pedidos.php");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require("./src/requirements/db.php");

    $username = $_POST["username"];
    $password = $_POST["password"];

    if (!empty($username) && !empty($password)) {
        $sql = "SELECT * FROM users WHERE username = '$username'";
        $resultado = $conexion->query($sql);

        if ($resultado === false) {
            echo $conexion->error; 
            exit();
        }
    
        if ($resultado->num_rows > 0) {
            $row = $resultado->fetch_assoc();
            $hash = $row["password"];

            if (password_verify($password, $hash)) {
                unset($row["password"]);

                $_SESSION["user_data"] = $row;

                echo "Inicio de sesión exitoso";
                header("Location: pedidos.php");
                exit();
            } else {
                $error = "La contraseña es incorrecta.";
            }
        } else {
            $error = "El usuario ingresado no existe.";
        }
    
        $conexion->close();
    } else {
        $error = "Por favor, completa los campos.";
    }
} else {
    $error = "";
    $username = "";
    $password = "";
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/form.css">
    <title>Document</title>
</head>
<body>
    <form method="POST">
        <div class="title">
            <h1>INICIAR SESIÓN</h1>
        </div>
        <div class="content">
            <div class="element">
                <span>Usuario</span>
                <br>
                <input type="text" name="username" require>
            </div>
            <div class="element">
                <span>Contraseña</span>
                <br>
                <input type="password"  name="password" require>
            </div>
        </div>
        <span style="color: red;"><?php echo $error; ?></span>
        <input type="submit" value="Iniciar Sesión">
    </form>
    
</body>
</html>