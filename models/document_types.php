<?php
class DocumentType
{
    public $connection;

    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    //MÉTODO GET para consultar todos los tipos de documentos 
    public function getAll()
    {
        $getAll = "SELECT * FROM document_types ORDER BY document_type_name";
        $res = mysqli_query($this->connection, $getAll);
        $d_types = [];

        while ($row = mysqli_fetch_assoc($res)) {
            $d_types[] = $row;
        }

        return $d_types;
    }

    // MÉTODO GET para consultar tipos de documentos por ID
    public function getById($id)
    {
        $getById = "SELECT * FROM document_types WHERE id_document_type = ? ";
        $stmt = $this->connection->prepare($getById);

        if (!$stmt) {
            // return [
            //     "result" => "Error",
            //     "message" => "Error al preparar la consula: " . $this->connection->error
            // ];
            throw new Exception("Prepare:" . $this->connection->error);
        }

        $stmt->bind_param("s", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $d_types = $res->fetch_assoc();

        if (!$d_types) {
            // return [
            //     "result" => "Error",
            //     "message" => "Tipo de documento no encontrado"
            // ];
            throw new Exception("Tipo de Documento no encontrado");
        }

        return $d_types;
    }

    // Método para Filtrar 
    public function getByName($value)
    {
        $filter = "SELECT * FROM document_types WHERE document_type_name LIKE ?";
        $stmt = $this->connection->prepare($filter);

        if (!$stmt) {
            // return [
            //     'result' => 'Error',
            //     'message' => 'Error al preparar la consulta: ' . $this->connection->error
            // ];
            throw new Exception("Prepare:" . $this->connection->error);
        }

        $value = "%$value%";
        $stmt->bind_param("s", $value);
        $stmt->execute();
        $res = $stmt->get_result();

        if (!$res) {
            // return [
            //     'result' => 'Error',
            //     'message' => 'Error al ejecutar la consulta: ' . $stmt->error
            // ];
            throw new Exception("Execute: " . $stmt->error);
        }

        $d_types = [];
        while ($row = $res->fetch_assoc()) {
            $d_types[] = $row;
        }

        return $d_types;
    }

    // Método ADD 
    public function add($params)
    {
        if (!isset($params["document_type_name"]) || !isset($params["id_document_type"])) {
            // return [
            //     "result" => "Error",
            //     "message" => "Todos los campos son requeridos"
            // ];
            throw new Exception("Todos los campos son requeridos");
        }

        $post = "INSERT INTO document_types (id_document_type, document_type_name) VALUES (?, ?)";
        $stmt = $this->connection->prepare($post);

        if (!$stmt) {
            // return [
            //     "result" => "Error",
            //     "message" => "Error al preparar la consulta: " . $this->connection->error
            // ];
            throw new Exception("Prepare: " . $this->connection->error);
        }

        $stmt->bind_param("ss", $params["id_document_type"], $params["document_type_name"]);
        $result = $stmt->execute();

        if (!$result) {
            // return [
            //     "result" => "Error",
            //     "message" => "Error al ejecutar la consulta: " . $stmt->error
            // ];
            throw new Exception("Excecute: " . $stmt->error);
        }

        // Se omite el mensaje para mejorar la seguridad de la app
        // return [
        //     "result" => "OK",
        //     "message" => "Se ha agregado el tipo de documento"
        // ];
    }

    // MÉTODO para editar 
    public function update($id, $params)
    {
        if (!isset($params["document_type_name"]) || !isset($id)) {
            // return [
            //     "result" => "Error",
            //     "message" => "Todos los campos son requeridos"
            // ];
            throw new Exception("Todos los campos son requeridos");
        }

        $patch = "UPDATE document_types SET document_type_name = ? WHERE id_document_type = ?";
        $stmt = $this->connection->prepare($patch);

        if (!$stmt) {
            // return [
            //     "result" => "Error",
            //     "message" => "Error al preparar la consulta: " . $this->connection->error
            // ];
            throw new Exception("Prepare: " . $this->connection->error);
        }

        $stmt->bind_param("ss", $id, $params["document_type_name"],);
        $result = $stmt->execute();

        if (!$result) {
            // return [
            //     "result" => "Error",
            //     "message" => "Error al ejecutar la consulta: " . $stmt->error
            // ];
            throw new Exception("Excecute: " . $stmt->error);
        }
        // return [
        //     "result" => "OK",
        //     "message" => "Se ha actualiza el tipo de documentodo con éxito"
        // ];
    }

    //MÉTODO DELETE 
    public function delete($id)
    {
        $deleteSql = "DELETE FROM document_types WHERE id_document_type = ?";
        $stmt = $this->connection->prepare($deleteSql);

        if (!$stmt) {
            // return [
            //     "result" => "Error",
            //     "message" => "Error al preparar la consulta: " . $this->connection->error
            // ];
            throw new Exception("Prepare: " . $this->connection->error);
        }

        // Se vincula el parámetro '$id' a la consulta, utilizando el método bind_param para asegurar el formato de la data (i)
        $stmt->bind_param("s", $id);
        $result = $stmt->execute();

        if (!$result) {
            // return [
            //     "result" => "Error",
            //     "message" => "Error al ejecutar la consulta: " . $stmt->error
            // ]
            throw new Exception("Excecute: " . $stmt->error);
        }

        if ($stmt->affected_rows === 0) {
            return false; 
        }

        // return [
        //     "result" => "OK",
        //     "message" => "Se ha eliminado el tipo de documento"
        // ];
    }
}
