<?php

class PedidoService
{
    private $pedidoModelo;
    private $clienteModelo;
    private $productoModelo;

    private $estadosValidos = [
        "PENDIENTE",
        "PREPARANDO",
        "ENTREGADO",
        "CANCELADO"
    ];

    public function __construct(
        $pedidoModelo,
        $clienteModelo,
        $productoModelo
    ) {
        $this->pedidoModelo =
            $pedidoModelo;

        $this->clienteModelo =
            $clienteModelo;

        $this->productoModelo =
            $productoModelo;
    }

    public function listar($estado = null)
    {
        if (
            $estado !== null &&
            !in_array(
                $estado,
                $this->estadosValidos
            )
        ) {

            return [
                "status" => 400,
                "body" => [
                    "success" => false,
                    "mensaje" =>
                        "Estado de pedido no válido"
                ]
            ];
        }

        return [
            "status" => 200,
            "body" => [
                "success" => true,
                "data" =>
                    $this->pedidoModelo->listar(
                        $estado
                    )
            ]
        ];
    }

    public function consultar($id)
    {
        $pedido =
            $this->pedidoModelo->buscarPorId($id);

        if (!$pedido) {

            return [
                "status" => 404,
                "body" => [
                    "success" => false,
                    "mensaje" =>
                        "Pedido no encontrado"
                ]
            ];
        }

        return [
            "status" => 200,
            "body" => [
                "success" => true,
                "data" => $pedido
            ]
        ];
    }

    public function crear($datos)
    {
        if (
            !isset($datos["cliente_id"]) ||
            !isset($datos["productos"])
        ) {

            return [
                "status" => 400,
                "body" => [
                    "success" => false,
                    "mensaje" =>
                        "Debe enviar cliente_id y productos"
                ]
            ];
        }

        $clienteId =
            (int) $datos["cliente_id"];

        $cliente =
            $this->clienteModelo->buscarPorId(
                $clienteId
            );

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

        if (
            !is_array($datos["productos"]) ||
            empty($datos["productos"])
        ) {

            return [
                "status" => 400,
                "body" => [
                    "success" => false,
                    "mensaje" =>
                        "El pedido debe contener productos"
                ]
            ];
        }

        $detalles = [];

        $total = 0;

        foreach (
            $datos["productos"]
            as $item
        ) {

            if (
                !isset($item["producto_id"]) ||
                !isset($item["cantidad"])
            ) {

                return [
                    "status" => 400,
                    "body" => [
                        "success" => false,
                        "mensaje" =>
                            "Cada producto debe contener producto_id y cantidad"
                    ]
                ];
            }

            $productoId =
                (int) $item["producto_id"];

            $cantidad =
                (int) $item["cantidad"];


            if ($cantidad <= 0) {

                return [
                    "status" => 400,
                    "body" => [
                        "success" => false,
                        "mensaje" =>
                            "La cantidad debe ser mayor que 0"
                    ]
                ];
            }


            $producto =
                $this->productoModelo
                    ->buscarPorId(
                        $productoId
                    );


            if (!$producto) {

                return [
                    "status" => 404,
                    "body" => [
                        "success" => false,
                        "mensaje" =>
                            "Producto no encontrado: " .
                            $productoId
                    ]
                ];
            }


            if (!(bool) $producto["activo"]) {

                return [
                    "status" => 409,
                    "body" => [
                        "success" => false,
                        "mensaje" =>
                            "El producto " .
                            $producto["nombre"] .
                            " no está disponible"
                    ]
                ];
            }


            if (
                $cantidad >
                $producto["stock"]
            ) {

                return [
                    "status" => 409,
                    "body" => [
                        "success" => false,
                        "mensaje" =>
                            "Stock insuficiente para " .
                            $producto["nombre"]
                    ]
                ];
            }


            $precio =
                (float) $producto["precio"];

            $subtotal =
                $precio * $cantidad;

            $subtotal =
                round($subtotal, 2);


            $detalles[] = [
                "producto_id" => $productoId,
                "cantidad" => $cantidad,
                "precio_unitario" => $precio,
                "subtotal" => $subtotal
            ];


            $total += $subtotal;
        }


        $total = round($total, 2);


        try {

            $pedidoId =
                $this->pedidoModelo->crear(
                    $clienteId,
                    $detalles,
                    $total
                );

        } catch (Exception $e) {

            return [
                "status" => 409,
                "body" => [
                    "success" => false,
                    "mensaje" =>
                        "No fue posible registrar el pedido: " .
                        $e->getMessage()
                ]
            ];
        }


        return [
            "status" => 201,
            "body" => [
                "success" => true,
                "mensaje" =>
                    "Pedido registrado correctamente",
                "pedido_id" =>
                    (int) $pedidoId,
                "total" => $total
            ]
        ];
    }

    public function actualizarEstado(
        $id,
        $datos
    ) {
        $pedido =
            $this->pedidoModelo->buscarPorId(
                $id
            );

        if (!$pedido) {

            return [
                "status" => 404,
                "body" => [
                    "success" => false,
                    "mensaje" =>
                        "Pedido no encontrado"
                ]
            ];
        }

        if (!isset($datos["estado"])) {

            return [
                "status" => 400,
                "body" => [
                    "success" => false,
                    "mensaje" =>
                        "Debe enviar el estado"
                ]
            ];
        }

        $estado =
            strtoupper(
                trim($datos["estado"])
            );

        if (
            !in_array(
                $estado,
                $this->estadosValidos
            )
        ) {

            return [
                "status" => 400,
                "body" => [
                    "success" => false,
                    "mensaje" =>
                        "Estado no válido"
                ]
            ];
        }

        $this->pedidoModelo
            ->actualizarEstado(
                $id,
                $estado
            );

        return [
            "status" => 200,
            "body" => [
                "success" => true,
                "mensaje" =>
                    "Estado del pedido actualizado correctamente"
            ]
        ];
    }

    public function cancelar($id)
    {
        $pedido =
            $this->pedidoModelo->buscarPorId(
                $id
            );

        if (!$pedido) {

            return [
                "status" => 404,
                "body" => [
                    "success" => false,
                    "mensaje" =>
                        "Pedido no encontrado"
                ]
            ];
        }

        $this->pedidoModelo->cancelar($id);

        return [
            "status" => 200,
            "body" => [
                "success" => true,
                "mensaje" =>
                    "Pedido cancelado correctamente"
            ]
        ];
    }
}