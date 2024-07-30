<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH');
header('Content-Type: application/json; charset=UTF-8');

require '../vendor/autoload.php';
require '../db_connect.php';
require '../models/login.php';

use \Firebase\JWT\JWT;
use \Dotenv\Dotenv;

// Se carga el archivo .env
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Se incluye la clave secreta y se hace un proceso de verificación 
$jwt_secret = $_ENV['JWT_SECRET'];

if (!$jwt_secret) {
    http_response_code(500);
    echo json_encode(['error' => 'JWT_SECRET no está definido']);
    exit();
}


$requestMethod = $_SERVER['REQUEST_METHOD'];

switch ($requestMethod) {
    case 'OPTIONS':
        http_response_code(200);
        break;

    case 'POST':
        $input = json_decode(file_get_contents('php://input'), true);

        // Se comprueba si los datos están presentes en la solicitud
        if (!isset($input['email']) || !isset($input['password'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Email y/o contraseña no proporcionados']);
            exit();
        }

        $email = $input['email'];
        $password = $input['password'];

        $login = new Login($connection);
        $user = $login->getUserByEmailAndPassword($email, $password);

        if ($user) {
            $issuedAt = time();
            $expirationTime = $issuedAt + 3600;
            $payload = [
                'iat' => $issuedAt,
                'exp' => $expirationTime,
                'email' => $email,
            ];

            try {
                $jwt = JWT::encode($payload, $jwt_secret, 'HS256');
                echo json_encode(['token' => $jwt]);
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['error' => 'Error al generar el token']);
            }
        } else {
            http_response_code(401);
            echo json_encode(['error' => 'Credenciales inválidas']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Método no permitido']);
        break;
}

?>