<?php

class ProductoService
{
    private $producto;

    public function __construct($producto)
    {
        $this->producto = $producto;
    }

    // LISTAR
    public function listar()
    {
        return $this->producto->listar();
    }

    // BUSCAR
    public function buscar($id)
    {
        if (!$id || $id <= 0) {
            return [
                "success" => false,
                "status" => 400,
                "mensaje" => "ID inválido"
            ];
        }

        $producto = $this->producto->buscar($id);

        if (!$producto) {
            return [
                "success" => false,
                "status" => 404,
                "mensaje" => "Producto no encontrado"
            ];
        }

        return [
            "success" => true,
            "status" => 200,
            "data" => $producto
        ];
    }

    // CREAR
    public function crear($data)
    {
        if (
            !isset($data["nombre"]) ||
            !isset($data["categoria"]) ||
            !isset($data["precio"]) ||
            !isset($data["stock"])
        ) {
            return [
                "success" => false,
                "status" => 400,
                "mensaje" => "Faltan datos obligatorios"
            ];
        }

        $nombre = trim($data["nombre"]);
        $categoria = trim($data["categoria"]);
        $precio = $data["precio"];
        $stock = $data["stock"];
        $activo = $data["activo"] ?? true;

        if ($nombre === "" || $categoria === "") {
            return [
                "success" => false,
                "status" => 400,
                "mensaje" => "El nombre y la categoría son obligatorios"
            ];
        }

        if ($precio <= 0) {
            return [
                "success" => false,
                "status" => 400,
                "mensaje" => "El precio debe ser mayor que 0"
            ];
        }

        if ($stock < 0) {
            return [
                "success" => false,
                "status" => 400,
                "mensaje" => "El stock no puede ser negativo"
            ];
        }

        $id = $this->producto->crear(
            $nombre,
            $categoria,
            $precio,
            $stock,
            $activo
        );

        if ($id) {
            return [
                "success" => true,
                "status" => 201,
                "mensaje" => "Producto registrado correctamente",
                "producto_id" => $id
            ];
        }

        return [
            "success" => false,
            "status" => 500,
            "mensaje" => "No se pudo registrar el producto"
        ];
    }

    // ACTUALIZAR
    public function actualizar($id, $data)
    {
        $producto = $this->producto->buscar($id);

        if (!$producto) {
            return [
                "success" => false,
                "status" => 404,
                "mensaje" => "Producto no encontrado"
            ];
        }

        $nombre = $data["nombre"] ?? $producto["nombre"];
        $categoria = $data["categoria"] ?? $producto["categoria"];
        $precio = $data["precio"] ?? $producto["precio"];
        $stock = $data["stock"] ?? $producto["stock"];
        $activo = $data["activo"] ?? $producto["activo"];

        if ($precio <= 0) {
            return [
                "success" => false,
                "status" => 400,
                "mensaje" => "El precio debe ser mayor que 0"
            ];
        }

        if ($stock < 0) {
            return [
                "success" => false,
                "status" => 400,
                "mensaje" => "El stock no puede ser negativo"
            ];
        }

        $resultado = $this->producto->actualizar(
            $id,
            $nombre,
            $categoria,
            $precio,
            $stock,
            $activo
        );

        if ($resultado) {
            return [
                "success" => true,
                "status" => 200,
                "mensaje" => "Producto actualizado correctamente"
            ];
        }

        return [
            "success" => false,
            "status" => 500,
            "mensaje" => "No se pudo actualizar el producto"
        ];
    }

    // ELIMINAR
    public function eliminar($id)
    {
        $producto = $this->producto->buscar($id);

        if (!$producto) {
            return [
                "success" => false,
                "status" => 404,
                "mensaje" => "Producto no encontrado"
            ];
        }

        if ($this->producto->eliminar($id)) {
            return [
                "success" => true,
                "status" => 200,
                "mensaje" => "Producto eliminado correctamente"
            ];
        }

        return [
            "success" => false,
            "status" => 500,
            "mensaje" => "No se pudo eliminar el producto"
        ];
    }
}