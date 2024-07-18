<?php
class DocumentType
{
    public $connection;

    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    //MÉTODO GET para consultar todos los tipos de documentos 
    public function getDocumentType()
    {
        $getDocumentType = "SELECT * FROM document_types ORDER BY document_type_name";
        $res = mysqli_query($this->connection, $getDocumentType);
        $document_type_name = [];

        while ($row = mysqli_fetch_array($res)) {
            $document_type_name[] = $row;
        }

        return $document_type_name;
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

        $add = "INSERT INTO document_types (document_type_name) VALUES (?)";
        $stmt = $this->connection->prepare($add);

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
    public function edit($id, $params)
    {
        if (!isset($params["document_type_name"])) {
            return [
                "result" => "Error",
                "message" => "Todos los campos son requeridos"
            ];
        }

        $edit = "UPDATE document_types SET document_type_name = ? WHERE id_document_type = ?";
        $stmt = $this->connection->prepare($edit);

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
    public function filter($value)
    {
        $filter = "SELECT * FROM document_types WHERE document_type_name LIKE '%$value%";
        $res = mysqli_query($this->connection, $filter);
        $result = [];

        while ($row = mysqli_fetch_array($res)) {
            $result[] = $row;
        }
        return $result;

    }

}

?>