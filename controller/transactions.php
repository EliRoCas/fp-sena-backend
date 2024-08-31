<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH, OPTIONS');
header('Content-Type: application/json');

require_once '../db_connect.php';
require_once '../models/transactions.php';

$transaction = new Transaction($connection);

$requestMethod = $_SERVER['REQUEST_METHOD'];

switch ($requestMethod) {

    case 'OPTIONS':
        http_response_code(200);
        exit;

    case 'GET':
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $response = $transaction->getById($id);
            if ($response) {
                http_response_code(200);
            } else {
                http_response_code(404);
                $response = ["result" => "Error", "message" => "Transacción no encontrada"];
            }
        } elseif (isset($_GET['filter'])) {
            $filter = $_GET['filter'];
            $response = $transaction->getByName($filter);
            if ($response) {
                http_response_code(200);
            } else {
                http_response_code(404);
                $response = ["result" => "Error", "message" => "Transacción no encontrada"];
            }
        } elseif (isset($_GET['type'])) {
            $type = $_GET['type'];
            $response = $transaction->getByType($type);
            if ($response) {
                http_response_code(200);
            } else {
                http_response_code(404);
                $response = ["result" => "Error", "message" => "Transacción no encontrada"];
            }
        } elseif (isset($_GET['date'])) {
            $date = $_GET['date'];
            $response = $transaction->getByDate($date);
            if ($response) {
                http_response_code(200);
            } else {
                http_response_code(404);
                $response = ["result" => "Error", "message" => "Transacción no encontrada"];
            }
        } else {
            $response = $transaction->getAll();
            if ($response) {
                http_response_code(200);
            } else {
                http_response_code(404);
                $response = ["result" => "Error", "message" => "No se encontraron transacciones"];
            }
        }
        break;
    case 'POST':
        $input = json_decode(file_get_contents('php://input'), true);
        http_response_code(201);
        $response = $transaction->add($input);
        break;
    case 'PUT':
        $id = $_GET['id'] ?? null;
        $input = json_decode(file_get_contents('php://input'), true);
        if ($id) {
            http_response_code(200);
            $response = $transaction->update($id, $input);
        } else {
            http_response_code(400);
            $response = [
                'result' => 'Error',
                'message' => 'id is required'
            ];
        }
        break;
    case 'DELETE':
        $id = $_GET['id'] ?? null;
        if ($id) {
            http_response_code(204);
            $response = $transaction->delete($id);
        } else {
            http_response_code(400);
            $response = [
                'result' => 'Error',
                'message' => 'id is required'
            ];
        }
        break;

    default:
        http_response_code(405);
        $response = [
            'result' => 'Error',
            'message' => 'Method not allowed'
        ];
}

echo json_encode($response);