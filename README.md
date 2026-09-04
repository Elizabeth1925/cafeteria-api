# ☕ API REST - Gestión de Cafetería

## 📌 Descripción

Este proyecto consiste en el desarrollo de una API REST para la gestión de una cafetería utilizando PHP y MySQL.

La API permite administrar clientes, productos y pedidos mediante solicitudes HTTP y respuestas en formato JSON.

El proyecto utiliza una arquitectura multicapa, separando las responsabilidades entre configuración, modelos, servicios y controladores.

---

## 👥 Integrantes

- Elizabeth De la Cruz
- Verónica Jaque
- Juan Toalombo

---

## 🛠️ Tecnologías utilizadas

- PHP
- MySQL
- Apache
- XAMPP
- Postman
- Git
- GitHub
- REST API
- JSON

---

## 📋 Requisitos

Para ejecutar el proyecto se necesita:

- XAMPP
- PHP
- MySQL
- Apache
- Postman
- Git

---

## 📁 Estructura del proyecto

```text
cafeteria-api/
│
├── config/
│   └── database.php
│
├── controllers/
│   ├── ClienteController.php
│   ├── ProductoController.php
│   └── PedidoController.php
│
├── models/
│   ├── Cliente.php
│   ├── Producto.php
│   └── Pedido.php
│
├── services/
│   ├── ClienteService.php
│   ├── ProductoService.php
│   └── PedidoService.php
│
├── database/
│   └── cafeteria_api.sql
│
└── index.php
