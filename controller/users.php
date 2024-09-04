<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH');
header('Content-Type: application/json');

require_once '../db_connect.php';
require_once '../models/users.php';

$user = new Users($connection);

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
                    $response = $user->getById($id);
                    if ($response) {
                        http_response_code(200);
                    } else {
                        http_response_code(404);
                        $response = ["message" => "User not found"];
                    }
                } elseif (isset($_GET['filter'])) {
                    $filter = $_GET['filter'];
                    $response = $user->filterByName($filter);
                    if ($response) {
                        http_response_code(200);
                    } else {
                        http_response_code(404);
                        $response = ["message" => "User not filter"];
                    }
                } else {
                    $response = $user->getAll();
                    if ($response) {
                        http_response_code(200);
                    } else {
                        http_response_code(404);
                        $response = ["message" => "No users found"];
                    }
                }
            } catch (Exception $e) {
                http_response_code(500);
                $response = ["message" => "An error occurred: " . $e->getMessage()];
            }
            break;
        case 'POST':
            $input = json_decode(file_get_contents('php://input'), true);
            $user->add($input);
            http_response_code(201);
            $response = ["message" => "User created successfully"];
            break;
        case 'PUT':
            $id = $_GET['id'] ?? null;
            $input = json_decode(file_get_contents('php://input'), true);
            $user->update($id, $input);
            http_response_code(200);
            $response = ["message" => "User updated successfully"];
            break;
        case 'DELETE':
            try {
                $id = $_GET['id'] ?? null;
                if ($id) {
                    $response = $user->delete($id);
                    http_response_code(204);
                } else {
                    http_response_code(400);
                    $response = [
                        'result' => 'Error',
                        'message' => 'ID is required'
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
                'message' => 'Invalid Request Method'
            ];
    }

    echo json_encode($response);
} catch (Exception $e) {
    http_response_code(500);
    // $response = ["message" => "An error occurred: " . $e->getMessage()];
    // echo json_encode($response);
}
