<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH');
header('Content-Type: application/json');

require_once '../db_connect.php';
require_once '../models/categories.php';

$category = new Category($connection);

$requestMethod = $_SERVER['REQUEST_METHOD'];

try {
    switch ($requestMethod) {
        case 'OPTIONS':
            http_response_code(200);
            break;

        case 'GET':
            try {
                if (isset($_GET['id'])) {
                    $id = $_GET['id'];
                    $response = $category->getById($id);
                    if ($response) {
                        http_response_code(200);
                    } else {
                        $response = ["message" => "Category not found"];
                        http_response_code(404);
                    }
                } elseif (isset($_GET['filter'])) {
                    $filter = $_GET['filter'];
                    $response = $category->getByName($filter);
                    if ($response) {
                        http_response_code(200);
                    } else {
                        http_response_code(404);
                        $response = ["message" => "Category not filter"];
                    }
                } else {
                    $response = $category->getAll();
                    if ($response) {
                        http_response_code(200);
                    } else {
                        http_response_code(404);
                        $response = ["message" => "No categories found"];
                    }
                }
            } catch (Exception $e) {
                http_response_code(500);
                $response = ["message" => "An error occurred: " . $e->getMessage()];
            }
            break;
        case 'POST':
            $input = json_decode(file_get_contents('php://input'), true);
            $category->add($input);
            http_response_code(201);
            $response = ["message" => "Category created successfully"];
            break;
        case 'PUT':
            $id = $_GET['id'] ?? null;
            $input = json_decode(file_get_contents('php://input'), true);
            $category->update($id, $input);
            http_response_code(200);
            $response = ["message" => "Category updated successfully"];
            break;
        case 'DELETE':
            try {
                $id = $_GET['id'] ?? null;
                if ($id) {
                    $response = $category->delete($id);
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
            http_response_code(405);
            $response = [
                'result' => 'Error',
                'message' => 'Method not allowed'
            ];
            break;
    }

    echo json_encode($response);
} catch (Exception $e) {
    http_response_code(500);
    // $response = ["message" => "An error occurred: " . $e->getMessage()];
    // echo json_encode($response);
}
