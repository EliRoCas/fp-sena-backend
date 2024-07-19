<?php

//Se realiza la configuración para admitir solicitudes desde cualquier origen (diferentes dominios)
header('Access-Control-Allow-Origin: *');
// Se especifican los encabezados permitidos en las solicitudes HTTP. 
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
// Se establece el header "Content-Type" a "application/json" indicando que la respuesta es de tipo json
header('Content-Type: application/json');

// Se inserta o requiere al archivo "connection.php" y el archivo "userRole.php" una sola vez de forma "obligatoria" 
require_once '../db_connect.php';
require_once '../models/user_role.php';

// Se crea una nueva instancia de la clase "User_Role" del modelo, que lleva "connection" como argumento del constructor
$userRole = new UserRole($connection);

// Se utiliza, en lugar de la superglobal $_GET, que almacena los datos pasados como parámetro URL a la variable $control,
// la superglobal $_SERVER que permite reconocer el tipo de la solicitud realizada por medio del método HTTP de la solicitud
$requestMethod = $_SERVER['REQUEST_METHOD'];
// Se obtiene el parámetro "controller" de la URL o un string vacío si no está, con el fin de determinar qué acción realizar
$controller = $_GET['controller'] ?? '';

// SE ejecuta la variable de control "switch" para validar las diferentes acciones (endpoints) que el controlador puede 
// recibir a partir de cada solicitud HTTP. 
switch ($requestMethod) {
    // Se le da manejo al método "GET" por medio de la estructura de control "if...elseif", para validar qué respuesta 
    // generar según cada controller. 
    case 'GET':
        if ($controller === 'user_roles') {
            $response = $userRole->getAll();
        } elseif (isset($_GET['id'])) {
            $id = $_GET['id'];
            $response = $userRole->getById($id);
        } elseif (isset($_GET['filter'])) {
            $filter = $_GET['filter'];
            $response = $userRole->filter($filter);
        } else {
            $response = [
                'result' => 'Error',
                'message' => 'Invalid controller'
            ];
        }
        break;

    case 'POST':
        $input = json_decode(file_get_contents('php://input'), true);
        $response = $userRole->post($input);
        break;

    case 'PATCH':
        $id = $_GET['id'] ?? null;
        $input = json_decode(file_get_contents('php://input'), true);
        // SE añade una validación para verificar si el ID está presente antes de proceder a ejecutar la solicitud. 
        if ($id) {
            $response = $userRole->patch($id, $input);
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
            $response = $userRole->delete($id);
        } else {
            $response = [
                'result' => 'Error',
                'message' => 'ID is required'
            ];
        }
        break;

    // Se genera un "default" para genera un código de error HTTP, en caso de no ser permitido o conocido el método HTTP
    default:
        http_response_code(405);
        $response = [
            'result' => 'Error',
            'message' => 'Method not allowed'
        ];
        break;
}

// Se envía la respuesta al cliente, luego de convertirla a formato JSON 
echo json_encode($response);
?>