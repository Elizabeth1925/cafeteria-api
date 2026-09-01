<?php

class ProductoController
{
    private ProductoService $service;

    public function __construct(ProductoService $service)
    {
        $this->service = $service;
    }

    public function manejar(string $metodo, ?int $id, ?array $datos, array $query = [])
    {
        switch ($metodo) {
            case "GET":
                if ($id !== null) {
                    return $this->service->consultar($id);
                }
                return $this->service->listar($query);

            case "POST":
                return $this->service->crear($datos);

            case "PUT":
                if ($id === null) {
                    return [
                        "status" => 400,
                        "body" => [
                            "success" => false,
                            "mensaje" => "Debe enviar el id del producto"
                        ]
                    ];
                }
                return $this->service->actualizar($id, $datos);

            case "DELETE":
                if ($id === null) {
                    return [
                        "status" => 400,
                        "body" => [
                            "success" => false,
                            "mensaje" => "Debe enviar el id del producto"
                        ]
                    ];
                }
                return $this->service->eliminar($id);

            default:
                return [
                    "status" => 405,
                    "body" => [
                        "success" => false,
                        "mensaje" => "Método HTTP no permitido"
                    ]
                ];
        }
    }
}
