<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH, OPTIONS');
header('Content-Type: application/json');

require_once '../db_connect.php';
require_once '../models/transactions.php';

$transaction = new Transaction($connection);

$requestMethod = $_SERVER['REQUEST_METHOD'];

try {
    switch ($requestMethod) {

        case 'OPTIONS':
            http_response_code(200);
            exit;

        case 'GET':
            try {
                if (isset($_GET['id'])) {
                    $id = $_GET['id'];
                    $response = $transaction->getById($id);
                    if ($response) {
                        http_response_code(200);
                    } else {
                        http_response_code(404);
                        $response = ["message" => "Transaction not found"];
                    }
                } elseif (isset($_GET['filter'])) {
                    $filter = $_GET['filter'];
                    $response = $transaction->getByName($filter);
                    if ($response) {
                        http_response_code(200);
                    } else {
                        http_response_code(404);
                        $response = ["message" => "Transaction not filter"];
                    }
                } elseif (isset($_GET['type'])) {
                    $type = $_GET['type'];
                    $response = $transaction->getByType($type);
                    if ($response) {
                        http_response_code(200);
                    } else {
                        http_response_code(404);
                        $response = ["message" => "Transaction type not found"];
                    }
                } elseif (isset($_GET['date'])) {
                    $date = $_GET['date'];
                    $response = $transaction->getByDate($date);
                    if ($response) {
                        http_response_code(200);
                    } else {
                        http_response_code(404);
                        $response = ["message" => "Transaction date not found"];
                    }
                } else {
                    $response = $transaction->getAll();
                    if ($response) {
                        http_response_code(200);
                    } else {
                        http_response_code(404);
                        $response = ["message" => "No transactions found"];
                    }
                }
            } catch (Exception $e) {
                http_response_code(500);
                $response = ["message" => "An error occurred: " . $e->getMessage()];
            }
            break;
        case 'POST':
            $input = json_decode(file_get_contents('php://input'), true);
            $transaction->add($input);
            http_response_code(201);
            $response = ["message" => "Transaction created successfully"];
            break;
        case 'PUT':
            $id = $_GET['id'] ?? null;
            $input = json_decode(file_get_contents('php://input'), true);
            $transaction->update($id, $input);
            http_response_code(200);
            $response = ["message" => "Transaction updated successfully"];
            break;
        case 'DELETE':
            try {
                $id = $_GET['id'] ?? null;
                if ($id) {
                    $response = $transaction->delete($id);
                    http_response_code(204);
                } else {
                    http_response_code(400);
                    $response = [
                        'result' => 'Error',
                        'message' => 'id is required'
                    ];
                }
            } catch (Exception $e) {
                http_response_code(500);
                $response = ["result" => "Error", "message" => "An error occurred: " . $e->getMessage()];
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
} catch (Exception $e) {
    // $response = ["result" => "Error", "message" => "An error occurred: " . $e->getMessage()];
    // echo json_encode($response);
    http_response_code(500);
}
