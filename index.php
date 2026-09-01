<?php

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit;
}

require_once __DIR__ . "/config/database.php";
require_once __DIR__ . "/models/Cliente.php";
require_once __DIR__ . "/models/Producto.php";
require_once __DIR__ . "/models/Pedido.php";
require_once __DIR__ . "/services/ClienteService.php";
require_once __DIR__ . "/services/ProductoService.php";
require_once __DIR__ . "/services/PedidoService.php";
require_once __DIR__ . "/controllers/ClienteController.php";
require_once __DIR__ . "/controllers/ProductoController.php";
require_once __DIR__ . "/controllers/PedidoController.php";

try {

    $database = new Database();
    $conexion = $database->conectar();

    $clienteModelo = new Cliente($conexion);
    $productoModelo = new Producto($conexion);
    $pedidoModelo = new Pedido($conexion);

    $clienteService = new ClienteService($clienteModelo);
    $productoService = new ProductoService($productoModelo);
    $pedidoService = new PedidoService($pedidoModelo, $clienteModelo, $productoModelo);

    $clienteController = new ClienteController($clienteService);
    $productoController = new ProductoController($productoService);
    $pedidoController = new PedidoController($pedidoService);

    $metodo = $_SERVER["REQUEST_METHOD"];
    $id = isset($_GET["id"]) ? (int) $_GET["id"] : null;
    $datos = [];

    if ($metodo === "POST" || $metodo === "PUT") {
        $contenido = file_get_contents("php://input");

        if ($contenido !== false && trim($contenido) !== "") {
            $datos = json_decode($contenido, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                http_response_code(400);
                echo json_encode(
                    ["success" => false, "mensaje" => "JSON inválido"],
                    JSON_UNESCAPED_UNICODE
                );
                exit;
            }
        }
    }

    $ruta = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
    $base = dirname($_SERVER["SCRIPT_NAME"]);

    if ($base !== "/" && $base !== "." && strpos($ruta, $base) === 0) {
        $ruta = substr($ruta, strlen($base));
    }

    $recurso = trim($ruta, "/");

    if ($recurso === "index.php") {
        $recurso = "";
    }

    $query = $_GET;
    unset($query["id"]);

    switch ($recurso) {
        case "clientes":
            $respuesta = $clienteController->manejar($metodo, $id, $datos);
            break;

        case "productos":
            $respuesta = $productoController->manejar($metodo, $id, $datos, $query);
            break;

        case "pedidos":
            $respuesta = $pedidoController->manejar($metodo, $id, $datos, $query);
            break;

        case "":
            $respuesta = [
                "status" => 200,
                "body" => [
                    "success" => true,
                    "mensaje" => "API de cafetería funcionando correctamente"
                ]
            ];
            break;

        default:
            $respuesta = [
                "status" => 404,
                "body" => [
                    "success" => false,
                    "mensaje" => "Recurso no encontrado"
                ]
            ];
            break;
    }

    http_response_code($respuesta["status"]);
    echo json_encode($respuesta["body"], JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(
        ["success" => false, "mensaje" => "Error interno de base de datos"],
        JSON_UNESCAPED_UNICODE
    );
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(
        ["success" => false, "mensaje" => "Error interno del servidor"],
        JSON_UNESCAPED_UNICODE
    );
}
