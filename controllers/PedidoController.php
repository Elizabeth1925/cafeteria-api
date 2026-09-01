<?php

class PedidoController
{
    private PedidoService $service;

    public function __construct(PedidoService $service)
    {
        $this->service = $service;
    }

    public function manejar(
        string $metodo,
        ?int $id,
        ?array $datos,
        array $query = []
    ) {
        switch ($metodo) {

            case "GET":

                if ($id !== null) {

                    return $this->service
                        ->consultar($id);
                }

                $estado =
                    isset($query["estado"])
                    ? strtoupper(
                        trim($query["estado"])
                    )
                    : null;

                return $this->service
                    ->listar($estado);


            case "POST":

                return $this->service
                    ->crear($datos);


            case "PUT":

                if ($id === null) {

                    return [
                        "status" => 400,
                        "body" => [
                            "success" => false,
                            "mensaje" =>
                            "Debe enviar el id del pedido"
                        ]
                    ];
                }

                return $this->service
                    ->actualizarEstado(
                        $id,
                        $datos
                    );


            case "DELETE":

                if ($id === null) {

                    return [
                        "status" => 400,
                        "body" => [
                            "success" => false,
                            "mensaje" =>
                            "Debe enviar el id del pedido"
                        ]
                    ];
                }

                return $this->service
                    ->cancelar($id);


            default:

                return [
                    "status" => 405,
                    "body" => [
                        "success" => false,
                        "mensaje" =>
                        "Método HTTP no permitido"
                    ]
                ];
        }
    }
}
