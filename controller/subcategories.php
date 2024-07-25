<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH');
header('Content-Type: application/json');

require_once '../db_connect.php';
require_once '../models/subcategories.php';

$subcategory = new Subcategory($connection);

$requestMethod = $_SERVER['REQUEST_METHOD'];

switch ($requestMethod) {
    case 'GET':
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            http_response_code(200);
            $response = $subcategory->getById($id);
        } elseif (isset($_GET['filter'])) {
            $filter = $_GET['filter'];
            http_response_code(200);
            $response = $subcategory->getByName($filter);
        } else {
            http_response_code(200);
            $response = $subcategory->getAll();
        }
        break;
    case 'POST':
        $input = json_decode(file_get_contents('php://input'), true);
        http_response_code(201);
        $response = $subcategory->add($input['subcategory_name'], $input['fo_category']);
        break;
    case 'PATCH':
        $id = $_GET['id'] ?? null;
        $input = json_decode(file_get_contents('php://input'), true);
        if ($id) {
            http_response_code(200);
            $response = $subcategory->update($id, $input['subcategory_name'], $input['fo_category']);
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
            $response = $subcategory->delete($id);
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

?>