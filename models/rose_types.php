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
        $getAllSql = "SELECT * FROM rose_types ORDER BY rose_type_name";
        $response = mysqli_query($this->connection, $getAllSql);
        $r_types = [];

        while ($row = mysqli_fetch_assoc($response)) {
            $r_types[] = $row;
        }

        return $r_types;
    }

    // MÉTODO GET para consultar tipos de rosas por ID
    public function getById($id)
    {
        $getByIdSql = "SELECT * FROM rose_types WHERE id_rose_type = ? ";
        $stmt = $this->connection->prepare($getByIdSql);

        if (!$stmt) {
            throw new Exception("Prepare:" . $this->connection->error);
        }

        $stmt->bind_param("s", $id);
        $stmt->execute();
        $response = $stmt->get_result();
        $r_types = $response->fetch_assoc();

        if (!$r_types) {
            throw new Exception('Tipo de rosa no encontrado');
        }

        return $r_types;
    }

    // Método para Filtrar 
    public function getByName($value)
    {
        $filterSql = "SELECT * FROM rose_types WHERE rose_type_name LIKE ?";
        $stmt = $this->connection->prepare($filterSql);

        if (!$stmt) {
            throw new Exception("Prepare:" . $this->connection->error);
        }

        $value = "%$value%";
        $stmt->bind_param("s", $value);
        $stmt->execute();
        $response = $stmt->get_result();

        if (!$response) {
            throw new Exception("Execute: " . $stmt->error);
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
        if (!isset($params["rose_type_name"]) || !isset($params["id_rose_type"])) {
            throw new Exception("Todos los campos son requeridos");
        }

        $postSql = "INSERT INTO rose_types (id_rose_type, rose_type_name) VALUES (?, ?)";
        $stmt = $this->connection->prepare($postSql);

        if (!$stmt) {
            throw new Exception("Prepare" . $this->connection->error);
        }

        $stmt->bind_param("ss",  $params["id_rose_type"], $params["rose_type_name"]);
        $result = $stmt->execute();

        if (!$result) {
            throw new Exception("Execute" . $stmt->error);
        }

        // return [
        //     "result" => "OK",
        //     "message" => "Se ha agregado el tipo de rosa"
        // ];
    }

    // MÉTODO para editar 
    public function update($id, $params)
    {
        if (!isset($params["rose_type_name"])) {
            throw new Exception("Todos los campos son requeridos");
        }

        $patchSql = "UPDATE rose_types SET rose_type_name = ? WHERE id_rose_type = ?";
        $stmt = $this->connection->prepare($patchSql);

        if (!$stmt) {
            throw new Exception("Prepare" . $this->connection->error);
        }

        $stmt->bind_param("ss", $params["rose_type_name"], $id);
        $result = $stmt->execute();

        if (!$result) {
            throw new Exception("Execute" . $stmt->error);
        }
        // return [
        //     "result" => "OK",
        //     "message" => "Se ha actualiza el tipo de rosa con éxito"
        // ];
    }

    //MÉTODO DELETE 
    public function delete($id)
    {
        $deleteSql = "DELETE FROM rose_types WHERE id_rose_type = ?";
        $stmt = $this->connection->prepare($deleteSql);

        if (!$stmt) {
            throw new Exception("Prepare" . $this->connection->error);
        }

        // Se vincula el parámetro '$id' a la consulta, utilizando el método bind_param para asegurar el formato de la data (i)
        $stmt->bind_param("s", $id);
        $result = $stmt->execute();

        if (!$result) {
            throw new Exception("Execute" . $stmt->error);
        }

        // return [
        // "result" => "OK",
        // "message" => "Se ha eliminado el tipo rosa"
        // ];
    }
}
