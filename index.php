<?php

header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit;
}

require_once __DIR__ . "/config/database.php";
require_once __DIR__ . "/models/Producto.php";
require_once __DIR__ . "/services/ProductoService.php";
require_once __DIR__ . "/controllers/ProductoController.php";

$database = new Database();
$conexion = $database->conectar();

$modelo = new Producto($conexion);
$service = new ProductoService($modelo);
$controller = new ProductoController($service);

$controller->procesar();