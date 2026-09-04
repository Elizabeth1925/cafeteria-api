<?php

class Cliente
{
    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    public function listar(): array
    {
        $sql = "SELECT id, cedula, nombre, correo
                FROM clientes
                ORDER BY id";

        $stmt = $this->conexion->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarPorId(int $id): ?array
    {
        $sql = "SELECT id, cedula, nombre, correo
                FROM clientes
                WHERE id = :id";

        $stmt = $this->conexion->prepare($sql);

        $stmt->execute([
            ":id" => $id
        ]);

        $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

        return $cliente ?: null;
    }

    public function buscarPorCedula(string $cedula): ?array
    {
        $sql = "SELECT id, cedula, nombre, correo
                FROM clientes
                WHERE cedula = :cedula";

        $stmt = $this->conexion->prepare($sql);

        $stmt->execute([
            ":cedula" => $cedula
        ]);

        $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

        return $cliente ?: null;
    }

    public function crear(array $datos): string|false
    {
        $sql = "INSERT INTO clientes
                (cedula, nombre, correo)
                VALUES
                (:cedula, :nombre, :correo)";

        $stmt = $this->conexion->prepare($sql);

        $stmt->execute([
            ":cedula" => $datos["cedula"],
            ":nombre" => $datos["nombre"],
            ":correo" => $datos["correo"]
        ]);

        return $this->conexion->lastInsertId();
    }

    public function actualizar(int $id, array $datos): bool
    {
        $campos = [];

        $parametros = [
            ":id" => $id
        ];

        if (isset($datos["cedula"])) {

            $campos[] = "cedula = :cedula";

            $parametros[":cedula"] = $datos["cedula"];
        }

        if (isset($datos["nombre"])) {

            $campos[] = "nombre = :nombre";

            $parametros[":nombre"] = $datos["nombre"];
        }

        if (isset($datos["correo"])) {

            $campos[] = "correo = :correo";

            $parametros[":correo"] = $datos["correo"];
        }

        if (empty($campos)) {
            return false;
        }

        $sql = "UPDATE clientes
                SET " . implode(", ", $campos) . "
                WHERE id = :id";

        $stmt = $this->conexion->prepare($sql);

        return $stmt->execute($parametros);
    }

    public function eliminar(int $id): bool
    {
        $sql = "DELETE FROM clientes
                WHERE id = :id";

        $stmt = $this->conexion->prepare($sql);

        return $stmt->execute([
            ":id" => $id
        ]);
    }
}
