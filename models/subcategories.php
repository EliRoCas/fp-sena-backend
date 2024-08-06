<?php
class Subcategory
{
    // Atributo para almacenar la cadena de conexión
    public $connection;

    // Método constructor de la clase, que recibe la cadena de conexión y la asigna al atributo
    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    // Método para consultar todas las subcategorías
    public function getAll()
    {
        $getAllSql = "SELECT * FROM subcategories ORDER BY subcategory_name";
        //Se prepara la consulta con una conexión a la DB 
        //la variable $stmt es una instancia de la clase "mysqli_stmt" para sentencias SQL preparadas
        $stmt = $this->connection->prepare($getAllSql);
        if ($stmt === false) {
            return [
                "result" => "Error",
                "message" => "Error al preparar la consulta: " . $this->connection->error
            ];
        }

        // Se ejecuta la consulta preparada, se utiliza "GET" para obtener el resultado y se inicializa un array vacío para
        // almacenar las categoráis obtenidas
        $stmt->execute();
        $response = $stmt->get_result();
        $subcategories = [];

        // Se utiliza un bucle 'while' que recorre cada fila (row) del resultado y la agrega al arreglo '$subcategories',
        // utilizando el método 'fetch_assoc'
        while ($row = $response->fetch_assoc()) {
            $subcategories[] = $row;
        }

        return $subcategories;
    }

    // Método GET para consultar una subcategoría por ID
    public function getById($id)
    {
        $getById = "SELECT * FROM subcategories WHERE id_subcategory = ?";
        $stmt = $this->connection->prepare($getById);
        if ($stmt === false) {
            return [
                "result" => "Error",
                "message" => "Error al preparar la consulta: " . $this->connection->error
            ];
        }

        // Se vincula el parámetro '$id' a la consulta
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $response = $stmt->get_result();
        $subcategory = $response->fetch_assoc();

        if (!$subcategory) {
            return [
                "result" => "Error",
                "message" => "Subcategoría no encontrada"
            ];
        }

        return $subcategory;
    }

    // Método para filtrar por un valor/nombre en particular
    public function getByName($value)
    {
        // Se crea la consulta SQL para seleccionar todas las filas de la tabla donde el nombre contenga el
        // valor dado en '$value'. Se incluye (%) para indicar que cualquier dato puede estar antes o después del valor
        $filterSql = "SELECT * FROM subcategories WHERE subcategory_name LIKE ?";

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


    // Método para agregar una nueva subcategoría
    public function add($subcategory_name, $fo_category)
    {
        // Se define la consulta SQL para insertar una nueva subcategoría
        $insertSql = "INSERT INTO subcategories (subcategory_name, fo_category) VALUES (?, ?)";
        $stmt = $this->connection->prepare($insertSql);

        if ($stmt === false) {
            return [
                "result" => "Error",
                "message" => "Error al preparar la consulta: " . $this->connection->error
            ];
        }

        $stmt->bind_param("si", $subcategory_name, $fo_category);
        $result = $stmt->execute();

        if ($result === false) {
            return [
                "result" => "Error",
                "message" => "Error al ejecutar la consulta: " . $stmt->error
            ];
        }

        return [
            "result" => "OK",
            "message" => "Subcategoría agregada correctamente"
        ];
    }

    // Método para validar si una subcategoría existe 
    public function exists($id)
    {
        $existsSql = "SELECT COUNT(*) as count 
        FROM subcategories 
        WHERE id_subcategory = ?";
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

    // Método para editar una subcategoría existente
    public function update($id, $subcategory_name, $fo_category)
    {
        // Verifica si la subcategoría existe
        if (!$this->exists($id)) {
            return [
                "result" => "Error",
                "message" => "La subcategoría no existe"
            ];
        }

        $updateSql = "UPDATE subcategories SET subcategory_name = ?, fo_category = ? WHERE id_subcategory = ?";
        $stmt = $this->connection->prepare($updateSql);

        if ($stmt === false) {
            return [
                "result" => "Error",
                "message" => "Error al preparar la consulta: " . $this->connection->error
            ];
        }

        $stmt->bind_param("sii", $subcategory_name, $fo_category, $id);
        $result = $stmt->execute();

        if ($result === false) {
            return [
                "result" => "Error",
                "message" => "Error al ejecutar la consulta: " . $stmt->error
            ];
        }

        return [
            "result" => "OK",
            "message" => "Subcategoría actualizada correctamente"
        ];
    }

    // Método para eliminar una subcategoría
    public function delete($id)
    {
        $delete = "DELETE FROM subcategories WHERE id_subcategory = ?";
        $stmt = $this->connection->prepare($delete);

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
            "message" => "Subcategoría eliminada correctamente"
        ];
    }

}