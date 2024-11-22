<?php 

if ($_SESSION["user_data"]["group"] == "admin") {
    echo '
    <div class="side-menu">
        <div class="brand-name">
            <h1>Crusi Juegos</h1>
        </div>
        <ul>
            <li><a href="#"><i class="bx bx-user"></i><span>' . $_SESSION["user_data"]["username"] . '</span> </a></li>
            <br>
            <li><a href="../pedidos.php"><i class="bx bx-store"></i> <span>Pedidos</span> </a></li>
            <li><a href="../inventario.php"><i class="bx bxs-backpack"></i><span>Inventario</span> </a></li>
            <li><a href="../product_register.php"><i class="bx bxs-backpack"></i><span>Registrar producto</span> </a></li>
            <li><a href="../logout.php"><i class="bx bx-log-out-circle"></i><span>Cerrar Sesión</span> </a></li>
        </ul>
    </div>';
} else {
    echo '
    <div class="side-menu">
        <div class="brand-name">
            <h1>Cruci Juegos</h1>
        </div>
        <ul>
            <li><a href="#"><i class="bx bx-user"></i><span>' . $_SESSION["user_data"]["username"] . '</span> </a></li>
            <br>
            <li><a href="../order.php"><i class="bx bx-store"></i> <span>Registrar pedido</span> </a></li>
            <li><a href="../pedidos.php"><i class="bx bx-store"></i> <span>Pedidos</span> </a></li>
            <li><a href="../logout.php"><i class="bx bx-log-out-circle"></i><span>Cerrar Sesión</span> </a></li>
        </ul>
    </div>';
}
?>
