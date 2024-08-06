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
    public function getAll()
    {
        // Se ejecuta la consulta para obtener todas las categorías existentes 
        $getAllSql = "SELECT * FROM categories ORDER BY category_name";
        // Se envía y ejecuta la consulta por medio de la función "mysqli_query" y se determina la conexión
        $response = mysqli_query($this->connection, $getAllSql);
        // Se inicializa un array vacío que contendrá los datos de la iteración del bucle while (las categorías). 
        $categories = [];

        // Se obtiene una fila de datos "mysqli...ASSOC)" con el resultado de la iteración, en donde row contendrá 
        // los nombre de columna como claves del array. 
        while ($row = mysqli_fetch_assoc($response)) {
            //Se agrega cada elemento de la categoría en el array 
            $categories[] = [
                'id_category' => $row['id_category'],
                'category_name' => $row['category_name']
            ];
        }
        // Se retorna el array de las categorías 
        return array_values($categories);
    }

    // MÉTODO GET para consultar una categoría por ID con sus subcategorías
    public function getById($id)
    {
        $getByIdSql = "SELECT * FROM categories WHERE id_category = ?";
        // Se ejecuta la consulta preparada para validar la conexión
        $stmt = $this->connection->prepare($getByIdSql);
        if ($stmt === false) {
            return [
                "result" => "Error",
                "message" => "Error al preparar la consulta: " . $this->connection->error
            ];
        }

        $stmt->bind_param("i", $id);
        $stmt->execute();
        $response = $stmt->get_result();

        $category = null;
        while ($row = $response->fetch_assoc()) {
            if ($category === null) {
                $category = [
                    'id_category' => $row['id_category'],
                    'category_name' => $row['category_name'],
                ];
            } else {
                return [
                    "result" => "Error",
                    "message" => "No se encontró la categoría con el id proporcionado."
                ];
            }
        }
        return $category;
    }

    // Método para filtrar por un valor/nombre en particular
    public function getByName($value)
    {
        // Preparamos la consulta SQL
        $filterSql = "SELECT * FROM categories WHERE category_name LIKE ?";
        // Preparamos la consulta
        $stmt = $this->connection->prepare($filterSql);

        if ($stmt === false) {
            return [
                "result" => "Error",
                "message" => "Error al preparar la consulta: " . $this->connection->error
            ];
        }

        // Usamos parámetros preparados para evitar inyección SQL
        // Se incluye (%) para indicar que cualquier dato puede estar antes o después del valor
        $searchValue = "%{$value}%";
        $stmt->bind_param("s", $searchValue);

        // Ejecutamos la consulta
        $stmt->execute();
        $response = $stmt->get_result();

        // Recogemos los resultados
        $results = [];
        while ($row = $response->fetch_assoc()) {
            $results[] = $row;
        }

        return $results;
    }

    // Método para agregar una nueva categoría
    public function add($params)
    {
        // Se define la consulta SQL para insertar una nueva categoría
        $insertSql = "INSERT INTO categories (category_name) VALUES (?)";
        $stmt = $this->connection->prepare($insertSql);

        if ($stmt === false) {
            return [
                "result" => "Error",
                "message" => "Error al preparar la consulta: " . $this->connection->error
            ];
        }

        $stmt->bind_param("s", $params['category_name']);
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

    // Método para validar si una categoría existe 
    public function exists($id)
    {
        $existsSql = "SELECT COUNT(*) as count FROM categories WHERE id_category = ?";
        $stmt = $this->connection->prepare($existsSql);

        if ($stmt === false) {
            return false;
        }

        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        return $row['count'] > 0;
    }

    // Método para editar una categoría existente
    public function update($id, $params)
    {
        // Verifica si la categoría existe
        if (!$this->exists($id)) {
            return [
                "result" => "Error",
                "message" => "La categoría no existe"
            ];
        }

        $updateSql = "UPDATE categories SET category_name = ? WHERE id_category = ?";
        $stmt = $this->connection->prepare($updateSql);

        if ($stmt === false) {
            return [
                "result" => "Error",
                "message" => "Error al preparar la consulta: " . $this->connection->error
            ];
        }

        $stmt->bind_param("si", $params['category_name'], $id);
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
    public function delete($id)
    {
        $deleteSql = "DELETE FROM categories WHERE id_category = ?";
        $stmt = $this->connection->prepare($deleteSql);

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

}
