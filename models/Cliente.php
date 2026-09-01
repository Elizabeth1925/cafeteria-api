<?php

class Cliente
{
    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    public function listar()
    {
        $sql = "SELECT id, cedula, nombre, correo
                FROM clientes
                ORDER BY id";

        $stmt = $this->conexion->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarPorId($id)
    {
        $sql = "SELECT id, cedula, nombre, correo
                FROM clientes
                WHERE id = :id";

        $stmt = $this->conexion->prepare($sql);

        $stmt->execute([
            ":id" => $id
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function buscarPorCedula($cedula)
    {
        $sql = "SELECT id, cedula, nombre, correo
                FROM clientes
                WHERE cedula = :cedula";

        $stmt = $this->conexion->prepare($sql);

        $stmt->execute([
            ":cedula" => $cedula
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function crear($datos)
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

    public function actualizar($id, $datos)
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

    public function eliminar($id)
    {
        $sql = "DELETE FROM clientes
                WHERE id = :id";

        $stmt = $this->conexion->prepare($sql);

        return $stmt->execute([
            ":id" => $id
        ]);
    }
}