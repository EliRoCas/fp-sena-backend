<?php
class Category
{
    // Atributo para almacenar la cadena de conexión
    public $connection;

    // Método constructor de la clase, que recibe la cadena de conexión y la asigna al atributo
    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    // Método para consultar todas las categorías
    public function getAll()
    {
        $getCategory = "SELECT * FROM categories ORDER BY category_name";

        //Se prepara la consulta con una conexión a la DB 
        //la variable $stmt es una instancia de la clase "mysqli_stmt" para sentencias SQL preparadas
        $stmt = $this->connection->prepare($getCategory);

        // Verificar si la preparación fue exitosa
        if ($stmt === false) {
            return [
                "result" => "Error",
                "message" => "Error al preparar la consulta: " . $this->connection->error
            ];
        }

        // Se ejecuta la consulta preparada, se utiliza "GET" para obtener el resultado y se inicializa un array vacío para
        // almacenar las categoráis obtenidas
        $stmt->execute();
        $res = $stmt->get_result();
        $categories = [];

        // Se utiliza un bucle 'while' que recorre cada fila (row) del resultado y la agrega al arreglo '$categories',
        // utilizando el método 'fetch_assoc'
        while ($row = $res->fetch_assoc()) {
            $categories[] = $row;
        }

        return $categories;
    }

    // Método GET para consultar una categoría por ID
    public function getCategoryById($id)
    {
        $getCategory = "SELECT * FROM users WHERE id_user = ?";
        $stmt = $this->connection->prepare($getCategory);

        if ($stmt === false) {
            return [
                "result" => "Error",
                "message" => "Error al preparar la consulta: " . $this->connection->error
            ];
        }

        // Se vincula el parámetro '$id' a la consulta
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $category = $res->fetch_assoc();

        if (!$category) {
            return [
                "result" => "Error",
                "message" => "Usuario no encontrado"
            ];
        }

        return $category;
    }

    // Método para agregar una nueva categoría
    public function addCategory($category_name)
    {
        // Se define la consulta SQL para insertar una nueva categoría
        $addCategory = "INSERT INTO categories (category_name) VALUES (?)";
        $stmt = $this->connection->prepare($addCategory);

        if ($stmt === false) {
            return [
                "result" => "Error",
                "message" => "Error al preparar la consulta: " . $this->connection->error
            ];
        }

        $stmt->bind_param("s", $category_name);
        $result = $stmt->execute();

        if ($result === false) {
            return [
                "result" => "Error",
                "message" => "Error al ejecutar la consulta: " . $stmt->error
            ];
        }

        return [
            "result" => "OK",
            "message" => "Categoría agregada correctamente"
        ];
    }

    // Método para editar una categoría existente
    public function editCategory($id, $category_name)
    {
        $editCategory = "UPDATE categories SET category_name = ? WHERE id_category = ?";
        $stmt = $this->connection->prepare($editCategory);

        if ($stmt === false) {
            return [
                "result" => "Error",
                "message" => "Error al preparar la consulta: " . $this->connection->error
            ];
        }

        $stmt->bind_param("si", $category_name, $id);
        $result = $stmt->execute();

        if ($result === false) {
            return [
                "result" => "Error",
                "message" => "Error al ejecutar la consulta: " . $stmt->error
            ];
        }

        return [
            "result" => "OK",
            "message" => "Categoría actualizada correctamente"
        ];
    }

    // Método para eliminar una categoría
    public function deleteCategory($id)
    {
        $deleteCategory = "DELETE FROM categories WHERE id_category = ?";
        $stmt = $this->connection->prepare($deleteCategory);

        // Verificar si la preparación fue exitosa
        if ($stmt === false) {
            return [
                "result" => "Error",
                "message" => "Error al preparar la consulta: " . $this->connection->error
            ];
        }

        // Se vincula el parámetro '$id' a la consulta, utilizando el método bind_param para asegurar el formato de la data
        $stmt->bind_param("i", $id);
        // Se ejecuta la consulta preparada
        $result = $stmt->execute();

        // Se realiza la verificación de la ejecución
        if ($result === false) {
            return [
                "result" => "Error",
                "message" => "Error al ejecutar la consulta: " . $stmt->error
            ];
        }

        return [
            "result" => "OK",
            "message" => "Categoría eliminada correctamente"
        ];
    }

    // Método para filtrar por un valor/nombre en particular
    public function filter($value)
    {
        // Se crea la consulta SQL para seleccionar todas las filas de la tabla donde el nombre contenga el
        // valor dado en '$value'. Se incluye (%) para indicar que cualquier dato puede estar antes o después del valor
        $filter = "SELECT * FROM categories WHERE category_name LIKE '%$value%";
        // Se ejecuta la consulta por medio de la cadena de conexión
        $res = mysqli_query($this->connection, $filter);
        $result = [];

        while ($row = mysqli_fetch_array($res)) {
            $result[] = $row;
        }
        return $result;
    }
}
?>





// Se decide usar métodos con sentencias preparadas, por cuestiones de seguridad y buenas prácticas
// //Método para consultar todas las categorías ordenadas por nombre
// public function consult()
// {
// // Consulta SQL para seleccionar todas las filas de la tabla 'category' ordenadas por 'category_name'
// $consult = "SELECT * FROM category ORDER BY category_name";
// // Ejecuta la consulta usando mysqli_query() con la conexión almacenada en $this->connection
// $res = mysqli_query($this->connection, $consult);
// //Array para almacenar todas las filas obtenidas en la consulta
// $categories = [];

// // Recorre cada fila del resultado y la agrega al arreglo $categories
// while ($row = mysqli_fetch_array($res)) {
// $categories[] = $row;
// }

// return $categories;
// }

// // Método para eliminar una categoría basada en su ID
// public function deleteCategory($id)
// {
// // Consulta SQL para eliminar la categoría con el ID especificado
// $del = "DELETE FROM category WHERE id_category = $id";
// // Ejecuta la consulta de eliminación usando mysqli_query() con la conexión almacenada en $this->connection
// mysqli_query($this->connection, $del);

// // Array para almacenar el resultado y el mensaje de la operación de eliminación
// $result = [];
// $result['result'] = "Ok";
// $result['message'] = "La categoría ha sido eliminada";

// // Se devuelve el arreglo con el resultado y mensaje de la operación
// return $result;
// }

// // Método para insertar una categoría
// public function add($params)
// {
// $add = "INSERT INTO category(category_name) VALUES ('$params -> category_name')";
// mysqli_query($this->connection, $add);
// $result = [];
// $result["result"] = "OK";
// $result["message"] = "La categoría ha sido agregada";
// return $result;
// }

// // Método para editar una categoría
// public function editCategory($id, $params)
// {
// $editCategory = "UPDATE category SET category_name = '$params->category_name' WHERE id_category =id";
// mysqli_query($this->connection, $editCategory);
// $result = [];
// $result["result"] = "OK";
// $result["message"] = "La categoría ha sido editada con éxito";
// return $result;
// }