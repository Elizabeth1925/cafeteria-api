<?php

class Database
{
  private string $host = "localhost";
  private string $db = "cafeteria_api";
  private string $usuario = "root";
  private string $password = "";

  public function conectar(): PDO
  {
    try {
      $conexion = new PDO(
        "mysql:host={$this->host};dbname={$this->db};charset=utf8mb4",
        $this->usuario,
        $this->password
      );
      $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $conexion->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
      return $conexion;
    } catch (PDOException $e) {
      http_response_code(500);
      echo json_encode(["success" => false, "mensaje" => "Error de conexión"]);
      exit;
    }
  }

  public function connect(): PDO
  {
    return $this->conectar();
  }
}



?>