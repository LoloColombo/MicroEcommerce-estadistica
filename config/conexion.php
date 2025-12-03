<?php
/*
credenciales de la base de datos local o remota
$host = "";
$port = ;                      
$user = "";                     
$password = "";
$database = "";              
*/
try {
    $conexion = new PDO("mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4", $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);
    
    
} catch (PDOException $e) {
    http_response_code(500);
    header("Content-Type: application/json; charset=UTF-8");
    echo json_encode([
        "error" => "Error de conexión a la base de datos",
        "mensaje" => "No se pudo conectar a la base de datos. Verifique la configuración."
    ]);
    die();
}

