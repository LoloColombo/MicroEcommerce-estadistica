<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    require_once '../config/conexion.php';
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "error" => "Error al cargar configuración",
        "mensaje" => "No se pudo cargar la configuración de la base de datos",
        "detalle" => $e->getMessage()
    ]);
    exit();
}

try {
    require_once 'controllers/EstadisticaController.php';
    require_once 'controllers/ClienteController.php';
    require_once 'controllers/ProductoController.php';
    require_once 'controllers/PedidoController.php';
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "error" => "Error al cargar controladores",
        "mensaje" => "No se pudieron cargar los controladores",
        "detalle" => $e->getMessage()
    ]);
    exit();
}

$metodo = $_SERVER['REQUEST_METHOD'];


$url = '';

if (isset($_GET['url']) && !empty($_GET['url'])) {
    $url = trim($_GET['url'], '/');
}

if (empty($url) && isset($_SERVER['REQUEST_URI'])) {
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    
    // Remover diferentes variantes del path base
    $uri = str_replace('/Ecommerce-Estadisticas-ramaManu/api', '', $uri);
    $uri = str_replace('/Ecommerce-Estadisticas/api', '', $uri);
    $uri = str_replace('/api', '', $uri);
    $uri = str_replace('/index.php', '', $uri);
    
    // Limpiar la ruta
    $url = trim($uri, '/');
}

// También intentar desde PATH_INFO si está disponible
if (empty($url) && isset($_SERVER['PATH_INFO'])) {
    $url = trim($_SERVER['PATH_INFO'], '/');
}

$partes = explode('/', $url);
$recurso = $partes[0] ?? '';

// Debug (comentar en producción)
// error_log("Método: $metodo | URL recibida: $url | Recurso: $recurso | REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'N/A'));

//llamadas
switch ($metodo) {
    case 'GET':
        switch ($recurso) {
            case 'clientes':
                $controller = new ClienteController($conexion);
                $controller->getClientes();
                break;
            case 'estadisticas':
                $controller = new EstadisticaController($conexion);
                $endpoint_especifico = $partes[1] ?? null;
                switch ($endpoint_especifico) {
                    case 'promedio-ventas':
                        $controller->getPromedioVentas();
                        break;

                    case 'desvio-estandar':
                        $controller->getDesvioEstandar();
                        break;

                    case 'correlacion-precio':
                        $controller->getCorrelacionPrecio();
                        break;

                    default:
                        http_response_code(400); 
                        echo json_encode(["mensaje" => "Debe especificar un cálculo estadístico"]);
                        break;
                }
                break;
            case 'pedidos':
                $controller = new PedidoController($conexion);
                $id = isset($partes[1]) ? $partes[1] : null;

                if ($id) {
                    $controller->getDetallePedido($id);
                } else {
                    $controller->getPedidos();
                }
                break;
            case 'productos':
                $controller = new ProductoController($conexion);
                $controller->getProductos();
                break;
            case '':
            case 'test':
                // Endpoint de prueba para verificar que la API funciona
                http_response_code(200);
                echo json_encode([
                    "status" => "ok",
                    "mensaje" => "API funcionando correctamente",
                    "recurso" => $recurso,
                    "url_recibida" => $url,
                    "metodo" => $metodo,
                    "request_uri" => $_SERVER['REQUEST_URI'] ?? 'N/A',
                    "path_info" => $_SERVER['PATH_INFO'] ?? 'N/A',
                    "get_url" => $_GET['url'] ?? 'N/A'
                ]);
                break;
            default:
                http_response_code(404);
                echo json_encode([
                    "error" => "Recurso no encontrado",
                    "mensaje" => "El recurso '$recurso' no existe",
                    "recursos_disponibles" => ["clientes", "productos", "pedidos", "estadisticas"]
                ]);
                break;
        }
        break;
    case 'POST':
        switch ($recurso) {
            case 'clientes':
                $controller = new ClienteController($conexion);
                $controller->createCliente();
                break;
            case 'pedidos':
                $controller = new PedidoController($conexion);
                $controller->createPedido();
                break;
            default:
                http_response_code(405);
                echo json_encode(["mensaje" => "Metodo no permitido en $recurso"]);
                break;
        }
        break;
    default:
        http_response_code(405);
        echo json_encode(["mensaje" => "Metodo no permitido"]);
        break;
}
?>