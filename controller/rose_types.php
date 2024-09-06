<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH');
header('Content-Type: application/json');

require_once '../db_connect.php';
require_once '../models/rose_types.php';

$roseType = new roseType($connection);

$requestMethod = $_SERVER['REQUEST_METHOD'];
$controller = $_GET['controller'] ?? '';

try {
    switch ($requestMethod) {
        case 'OPTIONS':
            http_response_code(200);
            break;

        case 'GET':
            try {
                if (isset($_GET['id'])) {
                    $id = $_GET['id'];
                    $response = $roseType->getById($id);
                    if ($response) {
                        http_response_code(200);
                    } else {
                        http_response_code(404);
                        $response = ["message" => "Rose type not found"];
                    }
                } elseif (isset($_GET['filter'])) {
                    $filter = $_GET['filter'];
                    $response = $roseType->getByName($filter);
                    if ($response) {
                        http_response_code(200);
                    } else {
                        http_response_code(404);
                        $response = ["message" => "Rose type not filter"];
                    }
                } else {
                    $response = $roseType->getAll();
                    if ($response) {
                        http_response_code(200);
                    } else {
                        http_response_code(404);
                        $response = ["message" => "No rose types found"];
                    }
                }
            } catch (Exception $e) {
                http_response_code(500);
                $response = ["message" => "An error occurred: " . $e->getMessage()];
            }
            break;
        case 'POST':
            $input = json_decode(file_get_contents('php://input'), true);
            $roseType->add($input);
            http_response_code(201);
            $response = ["message" => "Rose type created successfully"];
            break;

        case 'PUT':
            $id = $_GET['id'] ?? null;
            $input = json_decode(file_get_contents('php://input'), true);
            $roseType->update($id, $input);
            http_response_code(200);
            $response = ["message" => "Rose type updated"];
            break;

        case 'DELETE':
            try {
                $id = $_GET['id'] ?? null;
                if ($id) {
                    $response = $roseType->delete($id);
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
                $response = ["message" => "An error occurred: " . $e->getMessage()];
            }
            break;

        default:
            http_response_code(500);
            $response = [
                'result' => 'Error',
                'message' => 'Invalid Request Method'
            ];
    }
    echo json_encode($response);
} catch (Exception $e) {
    echo $e->getMessage();
    http_response_code(500);
}
