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

switch ($requestMethod) {
    case 'GET':
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            http_response_code(200);
            $response = $documentType->getById($id);
        } elseif (isset($_GET['filter'])) {
            $filter = $_GET['filter'];
            http_response_code(200);
            $response = $documentType->getByName($filter);
        } else {
            http_response_code(200);
            $response = $documentType->getAll();
        }
        break;
    case 'POST':
        $input = json_decode(file_get_contents('php://input'), true);
        http_response_code(201);
        $response = $documentType->add($input);
        break;

    case 'PATCH':
        $id = $_GET['id'] ?? null;
        $input = json_decode(file_get_contents('php://input'), true);
        // SE añade una validación para verificar si el ID está presente antes de proceder a ejecutar la solicitud. 
        if ($id) {
            http_response_code(200);
            $response = $documentType->update($id, $input);
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
            $response = $documentType->delete($id);
        } else {
            http_response_code(400);
            $response = [
                'result' => 'Error',
                'message' => 'id is required'
            ];
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

?>