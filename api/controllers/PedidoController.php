<?php
class PedidoController
{
    private $conexion;
    public function __construct($db)
    {
        $this->conexion = $db;
    }

    public function getPedidos()
    {
        try {
            $sql = "SELECT pedidos.id, pedidos.fecha, pedidos.total, pedidos.estado, 
                           clientes.nombre AS nombre_cliente, 
                           clientes.email, clientes.direccion 
                    FROM pedidos 
                    JOIN clientes ON pedidos.id_cliente = clientes.id";

            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();

            $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            http_response_code(200);
            echo json_encode($pedidos);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["mensaje" => "Error: " . $e->getMessage()]);
        }
    }

    public function getDetallePedido($id)
    {
        try {
            $sql = "SELECT productos.nombre, productos.precio, productos.categoria, 
                           detalle_pedido.cantidad, detalle_pedido.subtotal 
                    FROM detalle_pedido 
                    JOIN productos ON detalle_pedido.id_producto = productos.id 
                    WHERE detalle_pedido.id_pedido = ?";

            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([$id]);
            $detalles = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            http_response_code(200);
            echo json_encode($detalles);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["mensaje" => "Error: " . $e->getMessage()]);
        }
    }

    public function createPedido()
    {
        try {
            $datos = json_decode(file_get_contents("php://input"), true);
            $this->conexion->beginTransaction();

            $totalPedido = 0;
            $itemsCalculados = [];
            $sqlProductos = "SELECT precio FROM productos WHERE id = ?";
            $stmtProducto = $this->conexion->prepare($sqlProductos);

            foreach ($datos['items'] as $item) {
                $stmtProducto->execute([$item['id_producto']]);
                $producto = $stmtProducto->fetch();
                
                $subtotal = $producto['precio'] * $item['cantidad'];
                $totalPedido += $subtotal;
                
                $itemsCalculados[] = [
                    'id_producto' => $item['id_producto'],
                    'cantidad' => $item['cantidad'],
                    'subtotal' => $subtotal
                ];
            }

            $sqlPedido = "INSERT INTO pedidos (id_cliente, fecha, total, estado) VALUES (?, NOW(), ?, ?)";
            $stmtPedido = $this->conexion->prepare($sqlPedido);
            $stmtPedido->execute([$datos['id_cliente'], $totalPedido, $datos['estado']]);
            $id_pedido = $this->conexion->lastInsertId();

            $sqlDetalle = "INSERT INTO detalle_pedido (id_pedido, id_producto, cantidad, subtotal) VALUES (?, ?, ?, ?)";
            $stmtDetalle = $this->conexion->prepare($sqlDetalle);

            foreach ($itemsCalculados as $item) {
                $stmtDetalle->execute([$id_pedido, $item['id_producto'], $item['cantidad'], $item['subtotal']]);
            }

            $this->conexion->commit();
            http_response_code(201);
            echo json_encode(['id-pedido-creado' => $id_pedido]);

        } catch (Exception $e) {
            $this->conexion->rollBack();
            http_response_code(500);
            echo json_encode(["mensaje" => "Error: " . $e->getMessage()]);
        }
    }
}
?>
