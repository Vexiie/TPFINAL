<?php 
session_start();

if ($_SESSION["user_data"] == null) {
    header("Location: index.php");
}

require('./src/requirements/db.php');

$id = $_GET['id'] ?? null;

if (!$id) {
    echo "id not found";
    exit;
}

$sql = "DELETE FROM orders WHERE id = '$id'";
$resultado = $conexion->query($sql);

header("Location: pedidos.php");
?>