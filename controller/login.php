<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
header("Access-Control-Allow-Headers: access");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH');
header('Content-Type: application/json; charset=UTF-8');

require '../vendor/autoload.php';
require '../db_connect.php';
require '../models/login.php';

use \Firebase\JWT\JWT;

// Se commprueba si los datos están presentes en la solicitud
if (!isset($_POST['email']) || !isset($_POST['password'])) {
    http_response_code(400); 
    echo json_encode(['error' => 'Email y/o contraseña no proporcionados']);
    exit();
}

$email = $_POST['email'];
$password = $_POST['password'];

$jwt_secret = $_ENV['JWT_SECRET'];

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

    $jwt = JWT::encode($payload, $jwt_secret, 'HS256');
    echo json_encode(['token' => $jwt]);
} else {
    http_response_code(401);
    echo json_encode(['error' => 'Credenciales inválidas']);
}

?>