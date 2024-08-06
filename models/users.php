<?php
class Users
{
    public $connection;
    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    // MÉTODO GET para consultar todos los usuarios con sus roles y tipos de documento
    public function getAll()
    {
        $getAllSql = "SELECT * FROM users ORDER BY user_name";
        $response = mysqli_query($this->connection, $getAllSql);

        $users = [];

        while ($row = mysqli_fetch_assoc($response)) {
            $users[] = $row;
        }

        return $users;
    }

    // Método GET para consultar un usuario por ID
    public function getById($id)
    {
        $getByIdSql = "SELECT * FROM users  WHERE id_user = ?";
        $stmt = $this->connection->prepare($getByIdSql);
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

        $user = $response->fetch_assoc();

        if (!$user) {
            return [
                "result" => "Error",
                "message" => "Usuario no encontrado"
            ];
        }

        return $user;
    }

    // MÉTODO FILTRAR
    public function filterByName($value)
    {
        $filterSql = "SELECT * FROM users WHERE user_name LIKE ? GROUP BY id_user";
        $stmt = $this->connection->prepare($filterSql);
        if ($stmt === false) {
            return [
                "result" => "Error",
                "message" => "Error al preparar la consulta: " . $this->connection->error
            ];
        }
        // Se vincula el parámetro '$value' a la consulta
        $likeValue = "%$value%";
        $stmt->bind_param("s", $likeValue);
        $stmt->execute();
        $response = $stmt->get_result();

        $users = [];
        while ($row = $response->fetch_assoc()) {
            $users[] = $row;
        }

        return $users;
    }

    // MÉTODO ADD
    public function add($params)
    {
        if (
            !isset($params["user_name"]) || !isset($params["user_lastname"]) || !isset($params["fo_document_type"]) ||
            !isset($params["document_number"]) || !isset($params["email"]) || !isset($params["password"])
        ) {
            return [
                "result" => "Error",
                "message" => "Todos los campos son requeridos" . $this->connection->error,
            ];
        }

        $hashedPassword = password_hash($params["password"], PASSWORD_BCRYPT);

        $insertSql = "INSERT INTO users (
            user_name, 
            user_lastname, 
            fo_document_type, 
            document_number, 
            email, 
            password
        ) 
        VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->connection->prepare($insertSql);
        if ($stmt === false) {
            return [
                "result" => "Error",
                "message" => "Error al preparar la consulta" . $this->connection->error,
            ];
        }

        $stmt->bind_param (
            "ssiiss",
            $params["user_name"],
            $params["user_lastname"],
            $params["fo_document_type"],
            $params["document_number"],
            $params["email"],
            $hashedPassword
        );
        $result = $stmt->execute();

        if ($result === false) {
            return [
                "result" => "Error",
                "message" => "Error al ejecutar la consulta" . $stmt->error,
            ];
        }
        return [
            "result" => "Ok",
            "message" => "El usuario ha sido agregado con éxito",
        ];
    }

    // MÉTODO EDIT 
    public function update($id, $params)
    {
        if (
            !isset($params["user_name"]) || !isset($params["user_lastname"]) || !isset($params["fo_document_type"])
            || !isset($params["document_number"]) || !isset($params["email"]) || !isset($params["password"])
        ) {
            return [
                "result" => "Error",
                "message" => "Todos los campos son requeridos"
            ];
        }

        $hashedPassword = password_hash($params["password"], PASSWORD_BCRYPT);

        $updatSql = "UPDATE users SET user_name = ?, user_lastname = ?, fo_document_type = ?, document_number = ?,
         email = ?, password = ? WHERE id_user = ?";
        $stmt = $this->connection->prepare($updatSql);
        if ($stmt === false) {
            return [
                "result" => "Error",
                "message" => "Error al preparar la consulta: " . $this->connection->error
            ];
        }

        $stmt->bind_param(
            "ssiissi",
            $params["user_name"],
            $params["user_lastname"],
            $params["fo_document_type"],
            $params["document_number"],
            $params["email"],
            $hashedPassword,
            $id
        );
        $result = $stmt->execute();

        if ($result === false) {
            return [
                "result" => "Error",
                "message" => "Error al ejecutar la consulta: " . $stmt->error
            ];
        }
        return [
            "result" => "OK",
            "message" => "El usuario ha sido actualizado con éxito"
        ];
    }

    // MÉTODO DELETE 
    public function delete($id)
    {
        $deleteSql = "DELETE FROM users WHERE  id_user = ?";
        $stmt = $this->connection->prepare($deleteSql);
        if ($stmt === false) {
            return [
                "result" => "Error",
                "message" => "Error al preparar la consulta" . $this->connection->error,
            ];
        }
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();

        if ($result === false) {
            return [
                "result" => "Error",
                "message" => "Error al ejecutar la consulta" . $this->connection->error,
            ];
        }

        return [
            "result" => "Ok",
            "message" => "El usuario ha sido eliminado",
        ];
    }

}
