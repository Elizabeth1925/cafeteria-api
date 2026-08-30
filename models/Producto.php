<?php

class Producto
{
    private $conn;
    private $table = "productos";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // LISTAR PRODUCTOS
    public function listar()
    {
        $query = "SELECT * FROM {$this->table} ORDER BY id ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // BUSCAR PRODUCTO POR ID
    public function buscar($id)
    {
        $query = "SELECT * FROM {$this->table} WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // CREAR PRODUCTO
    public function crear($nombre, $categoria, $precio, $stock, $activo)
    {
        $query = "INSERT INTO {$this->table}
                  (nombre, categoria, precio, stock, activo)
                  VALUES
                  (:nombre, :categoria, :precio, :stock, :activo)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":nombre", $nombre);
        $stmt->bindParam(":categoria", $categoria);
        $stmt->bindParam(":precio", $precio);
        $stmt->bindParam(":stock", $stock);
        $stmt->bindParam(":activo", $activo);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }

        return false;
    }

    // ACTUALIZAR PRODUCTO
    public function actualizar(
        $id,
        $nombre,
        $categoria,
        $precio,
        $stock,
        $activo
    ) {
        $query = "UPDATE {$this->table}
                  SET nombre = :nombre,
                      categoria = :categoria,
                      precio = :precio,
                      stock = :stock,
                      activo = :activo
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":nombre", $nombre);
        $stmt->bindParam(":categoria", $categoria);
        $stmt->bindParam(":precio", $precio);
        $stmt->bindParam(":stock", $stock);
        $stmt->bindParam(":activo", $activo);

        return $stmt->execute();
    }

    // ELIMINAR PRODUCTO
    public function eliminar($id)
    {
        $query = "DELETE FROM {$this->table} WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);

        return $stmt->execute();
    }
    
}


?> 