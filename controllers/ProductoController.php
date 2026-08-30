<?php

class ProductoController
{
    private $service;

    public function __construct($service)
    {
        $this->service = $service;
    }

    public function procesar()
    {
        $method = $_SERVER["REQUEST_METHOD"];

        $id = $_GET["id"] ?? null;

        $data = json_decode(
            file_get_contents("php://input"),
            true
        );

        switch ($method) {

            case "GET":

                if ($id !== null) {
                    $response = $this->buscar($id);
                } else {
                    $response = $this->listar();
                }

                break;

            case "POST":

                $response = $this->crear($data ?? []);

                break;

            case "PUT":

                if ($id === null) {
                    $response = [
                        "success" => false,
                        "status" => 400,
                        "mensaje" => "Debe proporcionar el ID del producto"
                    ];
                } else {
                    $response = $this->actualizar(
                        $id,
                        $data ?? []
                    );
                }

                break;

            case "DELETE":

                if ($id === null) {
                    $response = [
                        "success" => false,
                        "status" => 400,
                        "mensaje" => "Debe proporcionar el ID del producto"
                    ];
                } else {
                    $response = $this->eliminar($id);
                }

                break;

            default:

                $response = [
                    "success" => false,
                    "status" => 405,
                    "mensaje" => "Método HTTP no permitido"
                ];
        }

        $status = $response["status"] ?? 200;

        http_response_code($status);

        echo json_encode(
            $response,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
        );
    }

    public function listar()
    {
        $productos = $this->service->listar();

        return [
            "success" => true,
            "status" => 200,
            "data" => $productos
        ];
    }

    public function buscar($id)
    {
        return $this->service->buscar($id);
    }

    public function crear($data)
    {
        return $this->service->crear($data);
    }

    public function actualizar($id, $data)
    {
        return $this->service->actualizar($id, $data);
    }

    public function eliminar($id)
    {
        return $this->service->eliminar($id);
    }
}