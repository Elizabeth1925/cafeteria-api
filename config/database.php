<?php

class Database
{
  private string $host = "localhost";
  private string $db_name = "cafeteria_api";
  private string $username = "root";
  private string $password = "";

  public function conectar(): PDO
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

  public function connect(): PDO
  {
    return $this->conectar();
  }

  public function probarConexion()
  {
    $this->conectar();
    return "Conexión exitosa a la base de datos cafeteria_api";
  }
}
