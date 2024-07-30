<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
header("Access-Control-Allow-Headers: access");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH');
header('Content-Type: application/json; charset=UTF-8');


require 'vendor/autoload.php'; 

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

include_once '../db_connect.php'; // Incluir el archivo de conexión a la base de datos
include_once '../models/login.php'; // Incluir el archivo que define la clase User

// Clave secreta para firmar y verificar el token (debería ser segura y almacenada de forma segura)
$secret_key = "YOUR_SECRET_KEY";

// Crear una instancia de la clase User
$connection = $GLOBALS['connection']; 
$login = new Login($connection);

// Decodificación del JWT (para un endpoint de validación)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"));

    if (!empty($data->email) && !empty($data->password)) {
        $user_data = $login->getUserByEmailAndPassword($data->email, $data->password);

        if ($user_data) {
            // Definir la carga útil del token JWT
            $payload = [
                'iat' => time(), // Tiempo de emisión
                'exp' => time() + (60 * 60), // Tiempo de expiración (1 hora)
                'sub' => $user_data['id'] // Información del usuario
            ];

            // Generar el token JWT
            $jwt = JWT::encode($payload, $secret_key);

            echo json_encode(['token' => $jwt]);
        } else {
            http_response_code(401);
            echo json_encode(['message' => 'Invalid credentials.']);
        }
    } else {
        http_response_code(400);
        echo json_encode(['message' => 'Missing email or password.']);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Decodificación del JWT
    $jwt = isset($_GET['token']) ? $_GET['token'] : null;

    if ($jwt) {
        try {
            // Decodificar el token JWT
            $decoded = JWT::decode($jwt, new Key($secret_key, 'HS256'));

            // Imprimir el contenido decodificado del JWT
            print_r($decoded);
        } catch (Exception $e) {
            // Manejo de errores en caso de token inválido
            http_response_code(401);
            echo json_encode(['message' => 'Invalid token.', 'error' => $e->getMessage()]);
        }
    } else {
        http_response_code(400);
        echo json_encode(['message' => 'Token not provided.']);
    }
}
?>
