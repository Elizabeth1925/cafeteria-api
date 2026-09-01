<?php

class ProductoService
{
    private $modelo;

    public function __construct($modelo)
    {
        $this->modelo = $modelo;
    }

    public function listar($filtros = [])
    {
        return [
            "status" => 200,
            "body" => [
                "success" => true,
                "data" =>
                    $this->modelo->listar($filtros)
            ]
        ];
    }

    public function consultar($id)
    {
        $producto =
            $this->modelo->buscarPorId($id);

        if (!$producto) {

            return [
                "status" => 404,
                "body" => [
                    "success" => false,
                    "mensaje" =>
                        "Producto no encontrado"
                ]
            ];
        }

        return [
            "status" => 200,
            "body" => [
                "success" => true,
                "data" => $producto
            ]
        ];
    }

    public function crear($datos)
    {
        if (
            !isset($datos["nombre"]) ||
            !isset($datos["categoria"]) ||
            !isset($datos["precio"]) ||
            !isset($datos["stock"])
        ) {

            return [
                "status" => 400,
                "body" => [
                    "success" => false,
                    "mensaje" =>
                        "Debe enviar nombre, categoría, precio y stock"
                ]
            ];
        }

        if ($datos["precio"] <= 0) {

            return [
                "status" => 400,
                "body" => [
                    "success" => false,
                    "mensaje" =>
                        "El precio debe ser mayor que 0"
                ]
            ];
        }

        if ($datos["stock"] < 0) {

            return [
                "status" => 400,
                "body" => [
                    "success" => false,
                    "mensaje" =>
                        "El stock no puede ser negativo"
                ]
            ];
        }

        $datos["activo"] =
            isset($datos["activo"])
                ? (bool) $datos["activo"]
                : true;

        $id = $this->modelo->crear($datos);

        return [
            "status" => 201,
            "body" => [
                "success" => true,
                "mensaje" =>
                    "Producto registrado correctamente",
                "id" => (int) $id
            ]
        ];
    }

    public function actualizar($id, $datos)
    {
        $producto =
            $this->modelo->buscarPorId($id);

        if (!$producto) {

            return [
                "status" => 404,
                "body" => [
                    "success" => false,
                    "mensaje" =>
                        "Producto no encontrado"
                ]
            ];
        }

        $permitidos = [
            "nombre",
            "categoria",
            "precio",
            "stock",
            "activo"
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

        if (
            isset($datosValidos["precio"]) &&
            $datosValidos["precio"] <= 0
        ) {

            return [
                "status" => 400,
                "body" => [
                    "success" => false,
                    "mensaje" =>
                        "El precio debe ser mayor que 0"
                ]
            ];
        }

        if (
            isset($datosValidos["stock"]) &&
            $datosValidos["stock"] < 0
        ) {

            return [
                "status" => 400,
                "body" => [
                    "success" => false,
                    "mensaje" =>
                        "El stock no puede ser negativo"
                ]
            ];
        }

        if (isset($datosValidos["activo"])) {

            $datosValidos["activo"] =
                (bool) $datosValidos["activo"];
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
                    "Producto actualizado correctamente"
            ]
        ];
    }

    public function eliminar($id)
    {
        $producto =
            $this->modelo->buscarPorId($id);

        if (!$producto) {

            return [
                "status" => 404,
                "body" => [
                    "success" => false,
                    "mensaje" =>
                        "Producto no encontrado"
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
                            "El producto está asociado a un pedido"
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
                    "Producto eliminado correctamente"
            ]
        ];
    }
}