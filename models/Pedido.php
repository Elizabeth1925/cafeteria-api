<?php

class Pedido
{
    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    public function listar(?string $estado = null): array
    {
        $sql = "SELECT
                    p.id,
                    p.cliente_id,
                    c.nombre AS cliente,
                    p.fecha,
                    p.estado,
                    p.total
                FROM pedidos p
                INNER JOIN clientes c
                    ON c.id = p.cliente_id";

        $parametros = [];

        if ($estado !== null) {

            $sql .= " WHERE p.estado = :estado";

            $parametros[":estado"] = $estado;
        }

        $sql .= " ORDER BY p.id DESC";

        $stmt = $this->conexion->prepare($sql);

        $stmt->execute($parametros);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarPorId(int $id): ?array
    {
        $sql = "SELECT
                    p.id,
                    p.cliente_id,
                    c.nombre AS cliente,
                    p.fecha,
                    p.estado,
                    p.total
                FROM pedidos p
                INNER JOIN clientes c
                    ON c.id = p.cliente_id
                WHERE p.id = :id";

        $stmt = $this->conexion->prepare($sql);

        $stmt->execute([
            ":id" => $id
        ]);

        $pedido = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$pedido) {
            return null;
        }

        $sqlDetalle = "SELECT
                            d.id,
                            d.producto_id,
                            pr.nombre AS producto,
                            d.cantidad,
                            d.precio_unitario,
                            d.subtotal
                       FROM detalle_pedido d
                       INNER JOIN productos pr
                           ON pr.id = d.producto_id
                       WHERE d.pedido_id = :pedido_id";

        $stmtDetalle =
            $this->conexion->prepare($sqlDetalle);

        $stmtDetalle->execute([
            ":pedido_id" => $id
        ]);

        $pedido["productos"] =
            $stmtDetalle->fetchAll(PDO::FETCH_ASSOC);

        return $pedido;
    }

    public function crear(int $clienteId, array $detalles, float $total): string|false
    {
        try {

            $this->conexion->beginTransaction();

            $sqlPedido = "INSERT INTO pedidos
                          (cliente_id, estado, total)
                          VALUES
                          (:cliente_id, 'PENDIENTE', :total)";

            $stmtPedido =
                $this->conexion->prepare($sqlPedido);

            $stmtPedido->execute([
                ":cliente_id" => $clienteId,
                ":total" => $total
            ]);

            $pedidoId =
                $this->conexion->lastInsertId();


            $sqlDetalle = "INSERT INTO detalle_pedido
                           (
                               pedido_id,
                               producto_id,
                               cantidad,
                               precio_unitario,
                               subtotal
                           )
                           VALUES
                           (
                               :pedido_id,
                               :producto_id,
                               :cantidad,
                               :precio_unitario,
                               :subtotal
                           )";

            $stmtDetalle =
                $this->conexion->prepare($sqlDetalle);


            $sqlStock = "UPDATE productos
                         SET stock = stock - :cantidad
                         WHERE id = :producto_id
                         AND stock >= :cantidad";

            $stmtStock =
                $this->conexion->prepare($sqlStock);


            foreach ($detalles as $detalle) {

                $stmtDetalle->execute([
                    ":pedido_id" => $pedidoId,
                    ":producto_id" =>
                    $detalle["producto_id"],
                    ":cantidad" =>
                    $detalle["cantidad"],
                    ":precio_unitario" =>
                    $detalle["precio_unitario"],
                    ":subtotal" =>
                    $detalle["subtotal"]
                ]);


                $stmtStock->execute([
                    ":cantidad" =>
                    $detalle["cantidad"],
                    ":producto_id" =>
                    $detalle["producto_id"]
                ]);

                if ($stmtStock->rowCount() === 0) {

                    throw new Exception(
                        "Stock insuficiente durante el registro del pedido"
                    );
                }
            }


            $this->conexion->commit();

            return $pedidoId;
        } catch (Exception $e) {

            if ($this->conexion->inTransaction()) {

                $this->conexion->rollBack();
            }

            throw $e;
        }
    }

    public function actualizarEstado(int $id, string $estado): bool
    {
        $sql = "UPDATE pedidos
                SET estado = :estado
                WHERE id = :id";

        $stmt = $this->conexion->prepare($sql);

        return $stmt->execute([
            ":estado" => $estado,
            ":id" => $id
        ]);
    }

    public function cancelar(int $id): bool
    {
        $sql = "UPDATE pedidos
                SET estado = 'CANCELADO'
                WHERE id = :id";

        $stmt = $this->conexion->prepare($sql);

        return $stmt->execute([
            ":id" => $id
        ]);
    }
}
