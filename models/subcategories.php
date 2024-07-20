<?php
class Subsubcategory
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
        $getSubsubcategory = "SELECT * FROM subcategories ORDER BY subsubcategory_name";

        //Se prepara la consulta con una conexión a la DB 
        //la variable $stmt es una instancia de la clase "mysqli_stmt" para sentencias SQL preparadas
        $stmt = $this->connection->prepare($getSubsubcategory);

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
        $subcategories = [];

        // Se utiliza un bucle 'while' que recorre cada fila (row) del resultado y la agrega al arreglo '$subcategories',
        // utilizando el método 'fetch_assoc'
        while ($row = $res->fetch_assoc()) {
            $subcategories[] = $row;
        }

        return $subcategories;
    }

    // Método GET para consultar una categoría por ID
    public function getSubcategoryById($id)
    {
        $getSubcategory = "SELECT * FROM users WHERE id_user = ?";
        $stmt = $this->connection->prepare($getSubcategory);

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
        $subcategory = $res->fetch_assoc();

        if (!$subcategory) {
            return [
                "result" => "Error",
                "message" => "Usuario no encontrado"
            ];
        }

        return $subcategory;
    }


    // Método para agregar una nueva categoría
    public function addSubsubcategory($subsubcategory_name, $fo_subcategory)
    {
        // Se define la consulta SQL para insertar una nueva categoría
        $addSubsubcategory = "INSERT INTO subcategories (subsubcategory_name) VALUES (?)";
        $stmt = $this->connection->prepare($addSubsubcategory);

        if ($stmt === false) {
            return [
                "result" => "Error",
                "message" => "Error al preparar la consulta: " . $this->connection->error
            ];
        }

        $stmt->bind_param("si", $subsubcategory_name, $fo_subcategory);
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

    // Método para editar una categoría existente
    public function editSubsubcategory($id, $subsubcategory_name, $fo_subcategory)
    {
        $editSubsubcategory = "UPDATE subcategories SET subsubcategory_name = ?, fo_subcategory = ? WHERE id_subsubcategory = ?";
        $stmt = $this->connection->prepare($editSubsubcategory);

        if ($stmt === false) {
            return [
                "result" => "Error",
                "message" => "Error al preparar la consulta: " . $this->connection->error
            ];
        }

        $stmt->bind_param("sii", $subsubcategory_name, $fo_subcategory, $id);
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

    // Método para eliminar una categoría
    public function deleteSubsubcategory($id)
    {
        $deleteSubsubcategory = "DELETE FROM subcategories WHERE id_subsubcategory = ?";
        $stmt = $this->connection->prepare($deleteSubsubcategory);

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

    // Método para filtrar por un valor/nombre en particular
    public function filter($value)
    {
        // Se crea la consulta SQL para seleccionar todas las filas de la tabla donde el nombre contenga el
        // valor dado en '$value'. Se incluye (%) para indicar que cualquier dato puede estar antes o después del valor
        $filter = "SELECT * FROM subcategories WHERE subsubcategory_name LIKE '%$value%";
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