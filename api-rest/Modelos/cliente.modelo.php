<?php
require_once "conexion.php";

class ModeloClientes {

    /* ---------- SELECT ---------- */
    public static function getClientes(string $tabla): array
    {
        $pdo  = Conexion::conectar();
        $stmt = $pdo->query("SELECT * FROM `$tabla`");
        $clientes = $stmt->fetchAll(PDO::FETCH_OBJ);   // o FETCH_ASSOC
        $stmt->closeCursor();
        return $clientes;
    }

    /* ---------- INSERT ---------- */
    public static function createCliente(string $tabla, array $datos)
    {
        try {
            $pdo = Conexion::conectar();

            $sql = "INSERT INTO `$tabla`
                       (nombre, apellido, email, id_cliente, llave_secreta, created_at, updated_at)
                    VALUES
                       (:nombre, :apellido, :email, :id_cliente, :llave_secreta, :created_at, :updated_at)";

            $stmt = $pdo->prepare($sql);

            $stmt->bindParam(':nombre',        $datos['nombre']);
            $stmt->bindParam(':apellido',      $datos['apellido']);
            $stmt->bindParam(':email',         $datos['email']);
            $stmt->bindParam(':id_cliente',    $datos['id_cliente']);
            $stmt->bindParam(':llave_secreta', $datos['llave_secreta']);
            $stmt->bindParam(':created_at',    $datos['created_at']);
            $stmt->bindParam(':updated_at',    $datos['updated_at']);

            return $stmt->execute() ? $pdo->lastInsertId() : false;

        } catch (PDOException $e) {
            error_log('createCliente: '.$e->getMessage());
            return false;
        }
    }
}
?>