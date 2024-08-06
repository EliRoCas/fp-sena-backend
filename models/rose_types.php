<?php
class RoseType
{
    public $connection;

    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    //MÉTODO GET para consultar todos los tipos de rosas
    public function getAll()
    {
        $getAll = "SELECT * FROM rose_types ORDER BY rose_type_name";
        $response = mysqli_query($this->connection, $getAll);
        $r_types = [];

        while ($row = mysqli_fetch_assoc($response)) {
            $r_types[] = $row;
        }

        return $r_types;
    }

    // MÉTODO GET para consultar tipos de rosas por ID
    public function getById($id)
    {
        $getById = "SELECT * FROM rose_types WHERE id_rose_type = ? ";
        $stmt = $this->connection->prepare($getById);

        if ($stmt === false) {
            return [
                "result" => "Error",
                "message" => "Error al preparar la consula: " . $this->connection->error
            ];
        }

        $stmt->bind_param("i", $id);
        $stmt->execute();
        $response = $stmt->get_result();
        $r_types = $response->fetch_assoc();

        if (!$r_types) {
            return [
                "result" => "Error",
                "message" => "Tipo de rosa no encontrado"
            ];
        }

        return $r_types;
    }

    // Método para Filtrar 
    public function getByName($value)
    {
        $filter = "SELECT * FROM rose_types WHERE rose_type_name LIKE ?";
        $stmt = $this->connection->prepare($filter);

        if ($stmt === false) {
            return [
                'result' => 'Error',
                'message' => 'Error al preparar la consulta: ' . $this->connection->error
            ];
        }

        $value = "%$value%";
        $stmt->bind_param("s", $value);
        $stmt->execute();
        $response = $stmt->get_result();

        if ($response === false) {
            return [
                'result' => 'Error',
                'message' => 'Error al ejecutar la consulta: ' . $stmt->error
            ];
        }

        $r_types = [];
        while ($row = $response->fetch_assoc()) {
            $r_types[] = $row;
        }

        return $r_types;
    }

    // Método ADD 
    public function add($params)
    {
        if (!isset($params["rose_type_name"])) {
            return [
                "result" => "Error",
                "message" => "Todos los campos son requeridos"
            ];
        }

        $post = "INSERT INTO rose_types (rose_type_name) VALUES (?)";
        $stmt = $this->connection->prepare($post);

        if ($stmt === false) {
            return [
                "result" => "Error",
                "message" => "Error al preparar la consulta: " . $this->connection->error
            ];
        }

        $stmt->bind_param("s", $params["rose_type_name"]);
        $result = $stmt->execute();

        if ($result === false) {
            return [
                "result" => "Error",
                "message" => "Error al ejecutar la consulta: " . $stmt->error
            ];
        }

        return [
            "result" => "OK",
            "message" => "Se ha agregado el tipo de rosa"
        ];
    }

    // MÉTODO para editar 
    public function update($id, $params)
    {
        if (!isset($params["rose_type_name"])) {
            return [
                "result" => "Error",
                "message" => "Todos los campos son requeridos"
            ];
        }

        $patch = "UPDATE rose_types SET rose_type_name = ? WHERE id_rose_type = ?";
        $stmt = $this->connection->prepare($patch);

        if ($stmt === false) {
            return [
                "result" => "Error",
                "message" => "Error al preparar la consulta: " . $this->connection->error
            ];
        }

        $stmt->bind_param("si", $params["rose_type_name"], $id);
        $result = $stmt->execute();

        if ($result === false) {
            return [
                "result" => "Error",
                "message" => "Error al ejecutar la consulta: " . $stmt->error
            ];
        }
        return [
            "result" => "OK",
            "message" => "Se ha actualiza el tipo de rosa con éxito"
        ];
    }

    //MÉTODO DELETE 
    public function delete($id)
    {
        $delete = "DELETE FROM rose_types WHERE id_rose_type = ?";
        $stmt = $this->connection->prepare($delete);

        if ($stmt === false) {
            return [
                "result" => "Error",
                "message" => "Error al preparar la consulta: " . $this->connection->error
            ];
        }

        // Se vincula el parámetro '$id' a la consulta, utilizando el método bind_param para asegurar el formato de la data (i)
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();

        if ($result === false) {
            return [
                "result" => "Error",
                "message" => "Error al ejecutar la consulta: " . $stmt->error
            ];
        }

        return [
            "result" => "OK",
            "message" => "Se ha eliminado el tipo rosa"
        ];
    }

}
