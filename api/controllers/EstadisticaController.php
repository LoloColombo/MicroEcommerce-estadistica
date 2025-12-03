<?php
class EstadisticaController
{
    private $conexion;
    public function __construct($db)
    {
        $this->conexion = $db;
    }

    public function getPromedioVentas()
    {
        try {
            $sql = "SELECT AVG(total_diario) FROM (SELECT SUM(total) AS total_diario FROM pedidos GROUP BY DATE(fecha)) AS ventas_diarias;";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            $promedio = $stmt->fetchColumn();
            
            http_response_code(200);
            echo json_encode(["promedio_ventas_diarias" => $promedio]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["mensaje" => "Error: " . $e->getMessage()]);
        }
    }

    public function getDesvioEstandar()
    {
        try {
            $sql = "SELECT STDDEV(total) FROM pedidos";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            $desvio = $stmt->fetchColumn();

            http_response_code(200);
            echo json_encode(["desvio_estandar" => $desvio]); 
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["mensaje" => "Error: " . $e->getMessage()]);
        }
    }

    public function getCorrelacionPrecio()
    {
        try {
            $sql = "SELECT
                        COUNT(*) AS N,
                        SUM(p.precio * dp.cantidad) AS sum_xy,
                        SUM(p.precio) AS sum_x,
                        SUM(dp.cantidad) AS sum_y,
                        SUM(p.precio * p.precio) AS sum_x_sq,
                        SUM(dp.cantidad * dp.cantidad) AS sum_y_sq
                    FROM detalle_pedido AS dp
                    JOIN productos AS p ON dp.id_producto = p.id";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            $stats = $stmt->fetch(PDO::FETCH_ASSOC);

            $N = $stats['N'];
            $sum_x = $stats['sum_x'];
            $sum_y = $stats['sum_y'];
            $sum_xy = $stats['sum_xy'];
            $sum_x_sq = $stats['sum_x_sq'];
            $sum_y_sq = $stats['sum_y_sq'];

            $numerator = ($N * $sum_xy) - ($sum_x * $sum_y);
            $denominator_x = ($N * $sum_x_sq) - ($sum_x * $sum_x);
            $denominator_y = ($N * $sum_y_sq) - ($sum_y * $sum_y);
            
            $denominator = sqrt($denominator_x * $denominator_y);

            if ($denominator == 0) {
                $correlacion = 0;
            } else {
                $correlacion = $numerator / $denominator;
            }

            http_response_code(200);
            echo json_encode(["correlacion_precio_cantidad" => $correlacion]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["mensaje" => "Error: " . $e->getMessage()]);
        }
    }
}
?>
