<?php

class Producto
{
    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    public function listar($filtros = [])
    {
        $sql = "SELECT id, nombre, categoria, precio, stock, activo
                FROM productos
                WHERE 1 = 1";

        $parametros = [];

        if (
            isset($filtros["categoria"]) &&
            trim($filtros["categoria"]) !== ""
        ) {

            $sql .= " AND categoria = :categoria";

            $parametros[":categoria"] =
                trim($filtros["categoria"]);
        }

        if (
            isset($filtros["nombre"]) &&
            trim($filtros["nombre"]) !== ""
        ) {

            $sql .= " AND nombre LIKE :nombre";

            $parametros[":nombre"] =
                "%" . trim($filtros["nombre"]) . "%";
        }

        $sql .= " ORDER BY id";

        $stmt = $this->conexion->prepare($sql);

        $stmt->execute($parametros);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarPorId($id)
    {
        $sql = "SELECT id, nombre, categoria, precio, stock, activo
                FROM productos
                WHERE id = :id";

        $stmt = $this->conexion->prepare($sql);

        $stmt->execute([
            ":id" => $id
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function crear($datos)
    {
        $sql = "INSERT INTO productos
                (nombre, categoria, precio, stock, activo)
                VALUES
                (:nombre, :categoria, :precio, :stock, :activo)";

        $stmt = $this->conexion->prepare($sql);

        $stmt->execute([
            ":nombre" => $datos["nombre"],
            ":categoria" => $datos["categoria"],
            ":precio" => $datos["precio"],
            ":stock" => $datos["stock"],
            ":activo" => $datos["activo"]
        ]);

        return $this->conexion->lastInsertId();
    }

    public function actualizar($id, $datos)
    {
        $campos = [];

        $parametros = [
            ":id" => $id
        ];

        if (isset($datos["nombre"])) {

            $campos[] = "nombre = :nombre";

            $parametros[":nombre"] = $datos["nombre"];
        }

        if (isset($datos["categoria"])) {

            $campos[] = "categoria = :categoria";

            $parametros[":categoria"] = $datos["categoria"];
        }

        if (isset($datos["precio"])) {

            $campos[] = "precio = :precio";

            $parametros[":precio"] = $datos["precio"];
        }

        if (isset($datos["stock"])) {

            $campos[] = "stock = :stock";

            $parametros[":stock"] = $datos["stock"];
        }

        if (isset($datos["activo"])) {

            $campos[] = "activo = :activo";

            $parametros[":activo"] = $datos["activo"];
        }

        if (empty($campos)) {
            return false;
        }

        $sql = "UPDATE productos
                SET " . implode(", ", $campos) . "
                WHERE id = :id";

        $stmt = $this->conexion->prepare($sql);

        return $stmt->execute($parametros);
    }

    public function eliminar($id)
    {
        $sql = "DELETE FROM productos
                WHERE id = :id";

        $stmt = $this->conexion->prepare($sql);

        return $stmt->execute([
            ":id" => $id
        ]);
    }
}