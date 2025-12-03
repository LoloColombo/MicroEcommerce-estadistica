<?php
class ProductoController
{
    private $conexion;
public function __construct($db) 
    {
        
        $this->conexion = $db; 
    }

    public function getProductos()
    {
        try {
            $sql = "SELECT * FROM productos ORDER BY nombre ASC";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            
            $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($productos)) {
                http_response_code(200);
                echo json_encode([]);
                return;
            }
            
            http_response_code(200);
            echo json_encode($productos);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                "error" => "Error al obtener los productos",
                "mensaje" => "No se pudieron obtener los productos desde la base de datos",
                "detalle" => $e->getMessage()
            ]);
        }
    }
}
?>
