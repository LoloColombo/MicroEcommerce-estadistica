<?php
class ClienteController
{
    private $conexion;
public function __construct($db) 
    {
        
        $this->conexion = $db; 
    }

    public function getClientes()
    {
        try {
            $sql = "SELECT * FROM clientes ORDER BY nombre ASC";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();

            $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($clientes)) {
                http_response_code(200);
                echo json_encode([]);
                return;
            }
            
            http_response_code(200);
            echo json_encode($clientes);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                "error" => "Error al obtener los clientes",
                "mensaje" => "No se pudieron obtener los clientes desde la base de datos",
                "detalle" => $e->getMessage()
            ]);
        }
    }

    public function createCliente()
    {
        try {
            $datos = json_decode(file_get_contents("php://input"), true);

            if (!isset($datos['nombre']) || !isset($datos['email']) || !isset($datos['direccion'])) {
                http_response_code(400);
                echo json_encode(["mensaje" => "Faltan datos requeridos (nombre, email, direccion)"]);
                return;
            }

            $sql = "INSERT INTO clientes (nombre, email, direccion) VALUES (?, ?, ?)";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute(
                [
                    $datos['nombre'],
                    $datos['email'],
                    $datos['direccion']
                ]
            );
            $id_cliente_nuevo = $this->conexion->lastInsertId();
            http_response_code(201);
            echo json_encode(['id-cliente-creado' => $id_cliente_nuevo]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["mensaje" => "Error al crear un cliente" .  $e->getMessage()]);
        }
    }
}
?>
