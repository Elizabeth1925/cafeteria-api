<?php

class ClienteService
{
    private Cliente $modelo;

    public function __construct(Cliente $modelo)
    {
        $this->modelo = $modelo;
    }

    public function listar(): array
    {
        return [
            "status" => 200,
            "body" => [
                "success" => true,
                "data" => $this->modelo->listar()
            ]
        ];
    }

    public function consultar(int $id): array
    {
        if ($id <= 0) {

            return [
                "status" => 400,
                "body" => [
                    "success" => false,
                    "mensaje" => "El id no es válido"
                ]
            ];
        }

        $cliente =
            $this->modelo->buscarPorId($id);

        if (!$cliente) {

            return [
                "status" => 404,
                "body" => [
                    "success" => false,
                    "mensaje" => "Cliente no encontrado"
                ]
            ];
        }

        return [
            "status" => 200,
            "body" => [
                "success" => true,
                "data" => $cliente
            ]
        ];
    }

    public function crear(array $datos): array
    {
        if (
            !isset($datos["cedula"]) ||
            !isset($datos["nombre"]) ||
            !isset($datos["correo"])
        ) {

            return [
                "status" => 400,
                "body" => [
                    "success" => false,
                    "mensaje" =>
                    "Debe enviar cédula, nombre y correo"
                ]
            ];
        }

        $cedula = trim($datos["cedula"]);
        $nombre = trim($datos["nombre"]);
        $correo = trim($datos["correo"]);

        if (
            $cedula === "" ||
            $nombre === "" ||
            $correo === ""
        ) {

            return [
                "status" => 400,
                "body" => [
                    "success" => false,
                    "mensaje" =>
                    "Los campos no pueden estar vacíos"
                ]
            ];
        }

        if (strlen($cedula) > 10) {

            return [
                "status" => 400,
                "body" => [
                    "success" => false,
                    "mensaje" =>
                    "La cédula no puede superar 10 caracteres"
                ]
            ];
        }

        if (
            $this->modelo->buscarPorCedula(
                $cedula
            )
        ) {

            return [
                "status" => 409,
                "body" => [
                    "success" => false,
                    "mensaje" =>
                    "La cédula ya se encuentra registrada"
                ]
            ];
        }

        $datos["cedula"] = $cedula;
        $datos["nombre"] = $nombre;
        $datos["correo"] = $correo;

        $id = $this->modelo->crear($datos);

        return [
            "status" => 201,
            "body" => [
                "success" => true,
                "mensaje" =>
                "Cliente registrado correctamente",
                "id" => (int) $id
            ]
        ];
    }

    public function actualizar(int $id, array $datos): array
    {
        if ($id <= 0) {

            return [
                "status" => 400,
                "body" => [
                    "success" => false,
                    "mensaje" => "El id no es válido"
                ]
            ];
        }

        $cliente =
            $this->modelo->buscarPorId($id);

        if (!$cliente) {

            return [
                "status" => 404,
                "body" => [
                    "success" => false,
                    "mensaje" => "Cliente no encontrado"
                ]
            ];
        }

        $permitidos = [
            "cedula",
            "nombre",
            "correo"
        ];

        $datosValidos = [];

        foreach ($permitidos as $campo) {

            if (array_key_exists($campo, $datos)) {

                $datosValidos[$campo] =
                    $datos[$campo];
            }
        }

        if (empty($datosValidos)) {

            return [
                "status" => 400,
                "body" => [
                    "success" => false,
                    "mensaje" =>
                    "Debe enviar datos para actualizar"
                ]
            ];
        }

        if (isset($datosValidos["cedula"])) {

            $datosValidos["cedula"] =
                trim($datosValidos["cedula"]);

            $existente =
                $this->modelo->buscarPorCedula(
                    $datosValidos["cedula"]
                );

            if (
                $existente &&
                $existente["id"] != $id
            ) {

                return [
                    "status" => 409,
                    "body" => [
                        "success" => false,
                        "mensaje" =>
                        "La cédula ya se encuentra registrada"
                    ]
                ];
            }
        }

        $this->modelo->actualizar(
            $id,
            $datosValidos
        );

        return [
            "status" => 200,
            "body" => [
                "success" => true,
                "mensaje" =>
                "Cliente actualizado correctamente"
            ]
        ];
    }

    public function eliminar(int $id): array
    {
        $cliente =
            $this->modelo->buscarPorId($id);

        if (!$cliente) {

            return [
                "status" => 404,
                "body" => [
                    "success" => false,
                    "mensaje" =>
                    "Cliente no encontrado"
                ]
            ];
        }

        try {

            $this->modelo->eliminar($id);
        } catch (PDOException $e) {

            if ($e->getCode() == "23000") {

                return [
                    "status" => 409,
                    "body" => [
                        "success" => false,
                        "mensaje" =>
                        "El cliente tiene pedidos registrados"
                    ]
                ];
            }

            throw $e;
        }

        return [
            "status" => 200,
            "body" => [
                "success" => true,
                "mensaje" =>
                "Cliente eliminado correctamente"
            ]
        ];
    }
}
