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

    // Método para consultar todas las categorías con sus subcategorías
    public function getCategories()
    {
        // Se ejecuta la consulta SQL con el método left join para asegurar que se obtengan los resultados de las 
        // categorías con y sin subcategorías. 
        $getCategories = "SELECT cat.*, sub.id_subcategory, sub.subcategory_name AS subcategory_name
            FROM 
                categories cat
            LEFT JOIN 
                subcategories sub ON cat.id_category = sub.fo_category
            ORDER BY 
                cat.category_name, sub.subcategory_name
        ";

        // Se envía y ejecuta la consulta por medio de la función "mysqli_query" y se determina la conexión
        // Además, se inicializa un array vacío que contendrá los datos de la iteración del bucle while. 
        $res = mysqli_query($this->connection, $getCategories);
        $categories = [];

        // Se obtiene una fila de datos "mysqli...ASSOC)" con el resultado de la iteración, en donde row contendrá 
        // los nombre de columna como claves del array. 
        while ($row = mysqli_fetch_array($res, MYSQLI_ASSOC)) {
            //Se identifica la categoría actual por su id, para organizar el array 
            $categoryId = $row['id_category'];
            // Se verifica el si el objeto con el "id_category" identifica se encuentra o no en el array para continuar 
            // con el proceso. 
            if (!isset($categories[$categoryId])) {
                //Si no se encuentra, se inicializa el proceso y se crea una nueva entrada en el array con los datos dados
                $categories[$categoryId] = [
                    'id_category' => $row['id_category'],
                    'category_name' => $row['category_name'],
                    'subcategories' => []
                ];
            }
            // Se hace una validación similiar a la anterior, para determinar los datos que se contendrán en las filas de las subcategorias
            if (!is_null($row['id_subcategory'])) {
                $categories[$categoryId]['subcategories'][] = [
                    'id_subcategory' => $row['id_subcategory'],
                    'subcategory_name' => $row['subcategory_name']
                ];
            }
        }

        return array_values($categories);
    }


    // MÉTODO GET para consultar una categoría por ID con sus subcategorías
    public function getCategoryById($id)
    {
        $getCategory = "SELECT cat * sub.id_subcategory, sub.subcategory_name AS subcategory_name
           FROM categories cat
           LEFT JOIN subcategories sub ON cat.id_category = sub.fo_category
           WHERE cat.id_category = ?
           ORDER BY sub.subcategory_name;
       ";

        $stmt = $this->connection->prepare($getCategory);
        if ($stmt === false) {
            return [
                "result" => "Error",
                "message" => "Error al preparar la consulta: " . $this->connection->error
            ];
        }

        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();

        $category = null;
        while ($row = $res->fetch_assoc()) {
            if ($category === null) {
                $category = [
                    'id_category' => $row['id_category'],
                    'category_name' => $row['category_name'],
                    'subcategories' => []
                ];
            }
            if (!empty($row['id_subcategory'])) {
                $category['subcategories'][] = [
                    'id_subcategory' => $row['id_subcategory'],
                    'subcategory_name' => $row['subcategory_name']
                ];
            }
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
        // Preparamos la consulta SQL
        $filter = "SELECT 
                    cat.id_category, 
                    cat.category_name AS category_name, 
                    sub.id_subcategory, 
                    sub.subcategory_name 
                FROM 
                    categories cat
                INNER JOIN 
                    subcategories sub ON cat.id_category = sub.fo_category
                WHERE 
                    cat.id_category LIKE ? 
                    OR cat.category_name LIKE ? 
                    OR sub.subcategory_name LIKE ?";

        // Preparamos la consulta
        $stmt = $this->connection->prepare($filter);

        if ($stmt === false) {
            return [
                "result" => "Error",
                "message" => "Error al preparar la consulta: " . $this->connection->error
            ];
        }

        // Usamos parámetros preparados para evitar inyección SQL
        // Se incluye (%) para indicar que cualquier dato puede estar antes o después del valor
        $searchValue = "%{$value}%";
        $stmt->bind_param("sss", $searchValue, $searchValue, $searchValue);

        // Ejecutamos la consulta
        $stmt->execute();
        $res = $stmt->get_result();

        // Recogemos los resultados
        $results = [];
        while ($row = $res->fetch_assoc()) {
            $results[] = $row;
        }

        return $results;
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