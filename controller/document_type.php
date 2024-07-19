<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
header('Content-Type: application/json');

require_once '../db_connect.php';
require_once '../models/document_type.php';

$documentType = new DocumentType($connection);

$requestMethod = $_SERVER['REQUEST_METHOD'];
$controller = $_GET['controller'] ?? '';

switch ($requestMethod) {
    case 'GET':
        if ($controller == 'document_types') {
            $response = $documentType->getAll();
        } elseif (isset($_GET['id'])) {
            $id = $_GET['id'];
            $response = $documentType->getById($id);
        } elseif (isset($_GET['filter'])) {
            $filter = $_GET['filter'];
            $response = $documentType->filter($filter);
        } else {
            $response = [
                'result' => "Error",
                'message' => 'Invalid Controller'
            ];
        }
        break;
        case 'POST':
            $input = json_decode(file_get_contents('php://input'), true);
            $response = $documentType->post($input);
            break;
    
        case 'PATCH':
            $id = $_GET['id'] ?? null;
            $input = json_decode(file_get_contents('php://input'), true);
            // SE añade una validación para verificar si el ID está presente antes de proceder a ejecutar la solicitud. 
            if ($id) {
                $response = $documentType->patch($id, $input);
            } else {
                $response = [
                    'result' => 'Error',
                    'message' => 'ID is required'
                ];
            }
            break;
    
        case 'DELETE':
            $id = $_GET['id'] ?? null;
            if ($id) {
                $response = $documentType->delete($id);
            } else {
                $response = [
                    'result' => 'Error',
                    'message' => 'ID is required'
                ];
            }
            break;
        

    default:
        $response = [
            'result' => 'Error',
            'message' => 'Invalid Request Method'
        ];

}

echo json_encode($response);

?>