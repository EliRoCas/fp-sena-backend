<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH');
header('Content-Type: application/json');

require_once '../db_connect.php';
require_once '../models/user_roles_assignment.php';

$userRoleAssignment = new UserRoleAssignment($connection);
;

$requestMethod = $_SERVER['REQUEST_METHOD'];

$response = [];

switch ($requestMethod) {

    case 'OPTIONS':
        http_response_code(200);

    // Se consulta un rol de usuario 
    case 'GET':
        if (isset($_GET['id_user'])) {
            $id_user = $_GET['id_user'];
            $response = $userRoleAssignment->getUserRoles($id_user);
        } else {
            $response = $userRoleAssignment->getAll();
        }
        break;
    //Se asigna un rol a un usuario
    case 'POST':
        $input = json_decode(file_get_contents('php://input'), true);
        $id_user = $input["fo_user"];
        $id_role = $input["id_user_role"];

        if ($id_user && $id_role) {
            $response = $userRoleAssignment->assignUserRole($id_user, $id_role);
        } else {
            $response = [
                'result' => 'Error',
                'message' => 'El id_user y el id_rol son requeridos.'
            ];
        }
        break;
    // Se elimina un rol asignado a un usuario 
    case 'DELETE':
        if (isset($_GET['id'])) {
            $id_user = $_GET['id'];
            $response = $userRoleAssignment->unassignUserRole($id_user);
        } else {
            $response = [
                'result' => 'Error',
                'message' => 'User ID y Role ID son requeridos'
            ];
        }
        break;
    default:
        http_response_code(405);

        $response = [
            'result' => 'Error',
            'message' => "Método inválido"
        ];
        break;
}
echo json_encode($response);

?>