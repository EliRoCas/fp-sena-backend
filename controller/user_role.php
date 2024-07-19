<?php
require '../db_connect.php';
require '../models/user_role.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
header('Content-Type: application/json');

$user_role = new User_role($connection);

$requestMethod = $_SERVER['REQUEST_METHOD'];
$endpoint = $_GET['endpoint'] ?? '';

switch ($requestMethod) {
    case 'GET':
        if ($endpoint === 'user_roles') {
            $response = $user_role->getAll();
        } elseif (isset($_GET['id'])) {
            $id = $_GET['id'];
            $response = $user_role->getById($id);
        } elseif (isset($_GET['filter'])) {
            $filter = $_GET['filter'];
            $response = $user_role->filter($filter);
        } else {
            $response = [
                'result' => 'Error',
                'message' => 'Invalid endpoint'
            ];
        }
        break;

    case 'POST':
        $input = json_decode(file_get_contents('php://input'), true);
        $response = $user_role->post($input);
        break;

    case 'PUT':
        $id = $_GET['id'] ?? null;
        $input = json_decode(file_get_contents('php://input'), true);
        if ($id) {
            $response = $user_role->patch($id, $input);
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
            $response = $user_role->delete($id);
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
            'message' => 'Method not allowed'
        ];
        break;
}

echo json_encode($response);
?>





<!-- 

SE opta por utilizar un controlador más apegado a los principios de arquitectura RESTfull 
 
//Se realiza la configuración para admitir solicitudes desde cualquier origen (diferentes dominios)
header('Access-Control-Allow-Origin: *');
// Se especifican los encabezados permitidos en las solicitudes HTTP. 
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');

// Se establece el header "Content-Type" a "application/json" indicando que la respuesta es de tipo json
header('Content-Type: application/json');

// Se inserta o requiere al archivo "connection.php" y el archivo "user_role.php" una sola vez de forma "obligatoria" 
require_once ("../db_connect.php");
require_once ("../models/user_role.php");

//Se establece la superglobal $_GET, para asignar el valor que se pasa como parámetro URL a la variable $control
$control = $_GET['control'];

// Se crea una nueva instancia de la clase "User_Role" del modelo, que lleva "connection" como argumento del constructor
$user_role = new User_Role($connection);

// Se ejecuta la estructura de control "switch" para validar los posibles casos de la variable "$control"
switch ($control) {
    // primero comprueba si $control es igual getAll (case) y, si lo es, llama al método getAll del objeto del modelo, 
    // asignando su resultado a la variable $us_role 
    case 'getAll':
        $us_role = $user_role->getAll();
        break;
    case 'getById':
        $id = $_GET['id'];
        $us_role = $user_role->getById($id);
        break;
    case 'post':
        // $json = '[{"nombre": "prueba"}]'; Linea de prueba
        $json = file_get_contents('php://input');
        $params = json_decode($json, true);
        $us_role = $user_role->post($params);
        break;
    case 'delete':
        $id = $_GET['id'];
        $us_role = $user_role->delete($id);
        break;
    case 'patch':
        $json = '{"id": 1, "nombre": "prueba"}';
        // $json = file_get_contents('php://input');
        $params = json_decode($json, true);

        // Se realiza un método de verificación del parámetro "id" en el $_GET
        if (!isset($_GET['id'])) {
            echo json_encode([
                'result' => 'Error',
                'message' => 'El ID es requerido'
            ]);
            exit;
        }

        $id = $_GET['id'];
        $us_role = $user_role->patch($id, $params);
        break;
    case 'filter':
        $data = $_GET['data'];
        $value = $user_role->filter($data);


}

// Pasada la verificación, se codifica la variable "$us_role" a formato json y se la asigna a "$data_role"
$data_role = json_encode($us_role);
// Se imprime la respuesta json al cliente  
echo $data_role; -->