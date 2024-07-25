<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH');
header('Content-Type: application/json');

require_once '../db_connect.php';
require_once '../models/users.php';

$user = new Users($connection);

$requestMethod = $_SERVER['REQUEST_METHOD'];

switch ($requestMethod) {
    
    case 'OPTIONS':
        http_response_code(200);

    case 'GET':
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $response = $user->getById($id);
        } elseif (isset($_GET['filter'])) {
            $filter = $_GET['filter'];
            $response = $user->filterByName($filter);
        } else {
            $response = $user->getAll();
        }
        break;
    case 'POST':
        $input = json_decode(file_get_contents('php://input'), true);
        $response = $user->add($input);
        break;

    case 'PUT':
        $id = $_GET['id'] ?? null;
        $input = json_decode(file_get_contents('php://input'), true);
        // SE añade una validación para verificar si el ID está presente antes de proceder a ejecutar la solicitud. 
        if ($id) {
            $response = $user->update($id, $input);
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
            $response = $user->delete($id);
        } else {
            $response = [
                'result' => 'Error',
                'message' => 'ID is required'
            ];
        }
        break;


    default:
    http_response_code(405);
        $response = [
            'result' => 'Error',
            'message' => 'Invalid Request Method'
        ];

}

echo json_encode($response);

?>