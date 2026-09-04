-- Script compatible con MariaDB/MySQL.

CREATE DATABASE IF NOT EXISTS cafeteria_api;
USE cafeteria_api;

DROP TABLE IF EXISTS detalle_pedido, pedidos, productos, clientes;

CREATE TABLE clientes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  cedula VARCHAR(10) NOT NULL UNIQUE,
  nombre VARCHAR(100) NOT NULL,
  correo VARCHAR(100) NOT NULL
);

CREATE TABLE productos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  categoria VARCHAR(50) NOT NULL,
  precio DECIMAL(10,2) NOT NULL,
  stock INT NOT NULL,
  activo BOOLEAN NOT NULL DEFAULT TRUE
);

CREATE TABLE pedidos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  cliente_id INT NOT NULL,
  fecha DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  estado VARCHAR(20) NOT NULL DEFAULT 'PENDIENTE',
  total DECIMAL(10,2) NOT NULL DEFAULT 0,
  FOREIGN KEY (cliente_id) REFERENCES clientes(id)
);

CREATE TABLE detalle_pedido (
  id INT AUTO_INCREMENT PRIMARY KEY,
  pedido_id INT NOT NULL,
  producto_id INT NOT NULL,
  cantidad INT NOT NULL,
  precio_unitario DECIMAL(10,2) NOT NULL,
  subtotal DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (pedido_id) REFERENCES pedidos(id),
  FOREIGN KEY (producto_id) REFERENCES productos(id)
);

INSERT INTO clientes (cedula, nombre, correo) VALUES
('1801234567', 'Ana Perez', 'ana@gmail.com'),
('1802345678', 'Juan Lopez', 'juan@gmail.com'),
('1803456789', 'Maria Torres', 'maria@gmail.com'),
('1804567890', 'Carlos Diaz', 'carlos@gmail.com'),
('1805678901', 'Sofia Ruiz', 'sofia@gmail.com');

-- Volcado de datos para la tabla `productos`
--

INSERT INTO productos (nombre, categoria, precio, stock, activo) VALUES
('Cafe Americano', 'Bebidas', 1.50, 20, TRUE),
('Capuchino', 'Bebidas', 2.50, 15, TRUE),
('Chocolate', 'Bebidas', 2.00, 10, TRUE),
('Te', 'Bebidas', 1.25, 20, TRUE),
('Sandwich', 'Comida', 3.50, 10, TRUE),
('Hamburguesa', 'Comida', 5.00, 8, TRUE),
('Empanada', 'Comida', 1.75, 15, TRUE),
('Pastel', 'Postres', 2.50, 5, TRUE),
('Galleta', 'Postres', 1.00, 20, TRUE),
('Jugo Natural', 'Bebidas', 2.25, 12, TRUE);
