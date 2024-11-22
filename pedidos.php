<?php 
session_start();

if ($_SESSION["user_data"] == null) {
    header("Location: index.php");
}

require('./src/requirements/db.php');



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
                        <h2>Lista de pedidos</h2>
                    </div>
                    <table>
                        <tr>
                            <th>ID Pedido</th>
                            <th>Cliente</th>
                            <th>Fecha</th>
                            
                        </tr>
                        <?php
                            $sql = "SELECT o.*, u.username FROM orders o JOIN users u ON o.owner_id = u.id";
                            $resultado = $conexion->query($sql);

                            while ($row = $resultado->fetch_assoc()) {
                                echo "<tr>
                                        <td>" . $row['ID'] . "</td>
                                        <td>" . $row['username'] . "</td>
                                        <td>" . $row['date'] . "</td>
                                        <td><div><a href=\"/pedidosBorrar.php?id=".$row['ID']."\" class=\"btn\">Borrar</a> <a href=\"/pedido.php?id=".$row['ID']."\" class=\"btn\">Ver</a></div></td>
                                    </tr>";
                            }
                        ?>
                        <!-- <tr>
                            <td>1</td>
                            <td>St. James College</td>
                            <td>25/04/2020</td>
                            <td><div><a href="#" class="btn">Borrar</a> <a href="#" class="btn">Ver</a></div></td>
                        </tr> -->
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>