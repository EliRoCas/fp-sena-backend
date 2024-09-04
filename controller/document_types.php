<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH');
header('Content-Type: application/json');

require_once '../db_connect.php';
require_once '../models/document_types.php';

$documentType = new DocumentType($connection);

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
                    $response = $documentType->getById($id);
                    if ($response) {
                        http_response_code(200);
                    } else {
                        http_response_code(404);
                        $response = ["message" => "Document type not found"];
                    }
                } elseif (isset($_GET['filter'])) {
                    $filter = $_GET['filter'];
                    $response = $documentType->getByName($filter);
                    if ($response) {
                        http_response_code(200);
                    } else {
                        http_response_code(404);
                        $response = ["message" => "Document type not filter"];
                    }
                } else {
                    $response = $documentType->getAll();
                    if ($response) {
                        http_response_code(200);
                    } else {
                        http_response_code(404);
                        $response = ["message" => "No document types found"];
                    }
                }
            } catch (Exception $e) {
                http_response_code(500);
                $response = ["message" => "An error occurred: " . $e->getMessage()];
            }
            break;
        case 'POST':
            try {
                $input = json_decode(file_get_contents('php://input'), true);
                $documentType->add($input);
                http_response_code(201);
                $response = ["message" => "Document type created successfully"];
            } catch (Exception $e) {
                http_response_code(500);
                $response = ["message" => "An error occurred: " . $e->getMessage()];
            }
            break;

        case 'PUT':
            try {
                $id = $_GET['id'] ?? null;
                $input = json_decode(file_get_contents('php://input'), true);
                // SE añade una validación para verificar si el ID está presente antes de proceder a ejecutar la solicitud. 
                if ($id) {
                    $response = $documentType->update($id, $input);
                    http_response_code(200);
                    $response = ["message" => "Document type updated"];
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

        case 'DELETE':
            try {
                $id = $_GET['id'] ?? null;
                if ($id) {
                    $response = $documentType->delete($id);
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
            };
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
