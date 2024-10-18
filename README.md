CrEATE DATABASE enoresa
CREATE TABLE Clientes (
    ClienteID INT PRIMARY KEY,
    Nombre VARCHAR(100),
    Email VARCHAR(100),
    Telefono VARCHAR(20)
);

CREATE TABLE Pedidos (
    PedidoID INT PRIMARY KEY,
    ClienteID INT,
    FechaPedido DATETIME,
    Estado VARCHAR(50),
    FOREIGN KEY (ClienteID) REFERENCES Clientes(ClienteID)
);

CREATE TABLE Productos (
    ProductoID INT PRIMARY KEY,
    Nombre VARCHAR(100),
    Precio DECIMAL(10,2)
);

CREATE TABLE DetallePedidos (
    DetalleID INT PRIMARY KEY,
    PedidoID INT,
    ProductoID INT,
    Cantidad INT,
    FOREIGN KEY (PedidoID) REFERENCES Pedidos(PedidoID),
    FOREIGN KEY (ProductoID) REFERENCES Productos(ProductoID)
);

CREATE PROCEDURE AgregarCliente (IN p_Nombre VARCHAR(100), IN p_Email VARCHAR(100), IN p_Telefono VARCHAR(20))
BEGIN
    INSERT INTO Clientes (Nombre, Email, Telefono) VALUES (p_Nombre, p_Email, p_Telefono);
END;

CREATE PROCEDURE CrearPedido (IN p_ClienteID INT, OUT p_PedidoID INT)
BEGIN
    INSERT INTO Pedidos (ClienteID, FechaPedido, Estado) VALUES (p_ClienteID, NOW(), 'Pendiente');
    SET p_PedidoID = LAST_INSERT_ID();
END;

CREATE PROCEDURE AgregarProductoAPedido (IN p_PedidoID INT, IN p_ProductoID INT, IN p_Cantidad INT)
BEGIN
    INSERT INTO DetallePedidos (PedidoID, ProductoID, Cantidad) VALUES (p_PedidoID, p_ProductoID, p_Cantidad);
END;

CREATE PROCEDURE ActualizarEstadoPedido (IN p_PedidoID INT, IN p_Estado VARCHAR(50))
BEGIN
    UPDATE Pedidos SET Estado = p_Estado WHERE PedidoID = p_PedidoID;
END;

CREATE PROCEDURE ObtenerDetallesPedido (IN p_PedidoID INT)
BEGIN
    SELECT p.PedidoID, c.Nombre AS Cliente, d.Cantidad, pr.Nombre AS Producto
    FROM DetallePedidos d
    JOIN Pedidos p ON d.PedidoID = p.PedidoID
    JOIN Clientes c ON p.ClienteID = c.ClienteID
    JOIN Productos pr ON d.ProductoID = pr.ProductoID
    WHERE p.PedidoID = p_PedidoID;
END;

CREATE TRIGGER trg_AfterInsertDetalle
AFTER INSERT ON DetallePedidos
FOR EACH ROW
BEGIN
    UPDATE Pedidos SET Estado = 'En Proceso' WHERE PedidoID = NEW.PedidoID;
END;

CREATE TRIGGER trg_BeforeInsertPedido
BEFORE INSERT ON Pedidos
FOR EACH ROW
BEGIN
    DECLARE num_pedidos INT;
    SELECT COUNT(*) INTO num_pedidos FROM Pedidos WHERE ClienteID = NEW.ClienteID AND Estado = 'Pendiente';
    IF num_pedidos > 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El cliente ya tiene un pedido pendiente.';
    END IF;
END;

CREATE TRIGGER trg_AfterUpdateEstado
AFTER UPDATE ON Pedidos
FOR EACH ROW
BEGIN
    INSERT INTO HistorialEstados (PedidoID, Estado, FechaCambio) VALUES (NEW.PedidoID, NEW.Estado, NOW());
END;

CREATE TRIGGER trg_AfterInsertDetalle
AFTER INSERT ON DetallePedidos
FOR EACH ROW
BEGIN
    DECLARE precio_total DECIMAL(10,2);
    SELECT SUM(d.Cantidad * p.Precio) INTO precio_total
    FROM DetallePedidos d
    JOIN Productos p ON d.ProductoID = p.ProductoID
    WHERE d.PedidoID = NEW.PedidoID;
    UPDATE Pedidos SET PrecioTotal = precio_total WHERE PedidoID = NEW.PedidoID;
END;

INSERT INTO Clientes (ClienteID, Nombre, Email, Telefono) VALUES 
(1, 'Elrubius', 'elrubius@example.com', '123456789'),
(2, 'Vegetta777', 'vegetta777@example.com', '987654321'),
(3, 'AuronPlay', 'auronplay@example.com', '555123456'),
(4, 'Dross', 'dross@example.com', '321654987'),
(5, 'Willyrex', 'willyrex@example.com', '654321789');

INSERT INTO Productos (ProductoID, Nombre, Precio) VALUES 
(1, 'Merch Elrubius', 19.99),
(2, 'Merch Vegetta777', 22.50),
(3, 'Merch AuronPlay', 15.00),
(4, 'Merch Dross', 25.00),
(5, 'Merch Willyrex', 20.00);

INSERT INTO Pedidos (PedidoID, ClienteID, FechaPedido, Estado) VALUES 
(1, 1, NOW(), 'Pendiente'),
(2, 2, NOW(), 'Pendiente'),
(3, 3, NOW(), 'Pendiente');

INSERT INTO DetallePedidos (DetalleID, PedidoID, ProductoID, Cantidad) VALUES 
(1, 1, 1, 2),
(2, 2, 2, 1),
(3, 3, 3, 3);
