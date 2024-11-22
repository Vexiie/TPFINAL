<?php
    $conexion = new mysqli("localhost", "root", "", "crusi_juegos");

    if ($conexion->connect_error) {
        die("Error en la conexión: " . $conexion->connect_error);

        
    }    
?>