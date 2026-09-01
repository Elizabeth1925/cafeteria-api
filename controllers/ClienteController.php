<?php

class ClienteController
{
    private $service;

    public function __construct($service)
    {
        $this->service = $service;
    }

    public function manejar(
        $metodo,
        $id,
        $datos
    ) {
        switch ($metodo) {

            case "GET":

                if ($id !== null) {

                    return $this->service
                        ->consultar($id);
                }

                return $this->service
                    ->listar();


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
                                "Debe enviar el id del cliente"
                        ]
                    ];
                }

                return $this->service
                    ->actualizar(
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
                                "Debe enviar el id del cliente"
                        ]
                    ];
                }

                return $this->service
                    ->eliminar($id);


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