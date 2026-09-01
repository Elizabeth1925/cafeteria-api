<?php

class Database
{
    private $host = "localhost";
    private $db_name = "cafeteria_api";
    private $username = "root";
    private $password = "";

    public function conectar()
    {
        try {

            $conexion = new PDO(
                "mysql:host={$this->host};dbname={$this->db_name};charset=utf8mb4",
                $this->username,
                $this->password
            );

            $conexion->setAttribute(
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION
            );

            $conexion->setAttribute(
                PDO::ATTR_DEFAULT_FETCH_MODE,
                PDO::FETCH_ASSOC
            );

            return $conexion;

        } catch (PDOException $e) {

            throw $e;
        }
    }

    public function probarConexion()
    {
        $this->conectar();

        return "Conexión exitosa a la base de datos cafeteria_api";
    }
}