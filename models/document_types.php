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

        if ($stmt === false) {
            return [
                "result" => "Error",
                "message" => "Error al preparar la consula: " . $this->connection->error
            ];
        }

        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $d_types = $res->fetch_assoc();

        if (!$d_types) {
            return [
                "result" => "Error",
                "message" => "Tipo de documento no encontrado"
            ];
        }

        return $d_types;
    }

    //MÉTODO DELETE 
    public function delete($id)
    {
        $delete = "DELETE FROM document_types WHERE id_document_type = ?";
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
            "message" => "Se ha eliminado el tipo de documento"
        ];
    }

    // Método ADD 
    public function add($params)
    {
        if (!isset($params["document_type_name"])) {
            return [
                "result" => "Error",
                "message" => "Todos los campos son requeridos"
            ];
        }

        $post = "INSERT INTO document_types (document_type_name) VALUES (?)";
        $stmt = $this->connection->prepare($post);

        if ($stmt === false) {
            return [
                "result" => "Error",
                "message" => "Error al preparar la consulta: " . $this->connection->error
            ];
        }

        $stmt->bind_param("s", $params["document_type_name"]);
        $result = $stmt->execute();

        if ($result === false) {
            return [
                "result" => "Error",
                "message" => "Error al ejecutar la consulta: " . $stmt->error
            ];
        }

        return [
            "result" => "OK",
            "message" => "Se ha agregado el tipo de documento"
        ];
    }

    // MÉTODO para editar 
    public function update($id, $params)
    {
        if (!isset($params["document_type_name"])) {
            return [
                "result" => "Error",
                "message" => "Todos los campos son requeridos"
            ];
        }

        $patch = "UPDATE document_types SET document_type_name = ? WHERE id_document_type = ?";
        $stmt = $this->connection->prepare($patch);

        if ($stmt === false) {
            return [
                "result" => "Error",
                "message" => "Error al preparar la consulta: " . $this->connection->error
            ];
        }

        $stmt->bind_param("si", $params["document_type_name"], $id);
        $result = $stmt->execute();

        if ($result === false) {
            return [
                "result" => "Error",
                "message" => "Error al ejecutar la consulta: " . $stmt->error
            ];
        }
        return [
            "result" => "OK",
            "message" => "Se ha actualiza el tipo de documentodo con éxito"
        ];
    }

    // Método para Filtrar 
    public function getByName($value)
    {
        $filter = "SELECT * FROM document_types WHERE document_type_name LIKE ?";
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
        $res = $stmt->get_result();

        if ($res === false) {
            return [
                'result' => 'Error',
                'message' => 'Error al ejecutar la consulta: ' . $stmt->error
            ];
        }

        $d_types = [];
        while ($row = $res->fetch_assoc()) {
            $d_types[] = $row;
        }

        return $d_types;
    }

}

?>