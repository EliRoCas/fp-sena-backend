<?php
class User
{
    public $connection;
    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    // MÉTODO GET para consultar todos los usuarios con sus roles y tipos de documento
    public function getUsers()
    {
        $getUsers = "SELECT 
                users.id_user,
                users.user_name,
                users.user_lastname,
                document_types.document_type_name,
                users.document_number,
                users.email,
                user_roles.role_name
        FROM  users
        INNER JOIN document_types ON users.fo_document_type = document_types.id_document_type
        LEFT JOIN user_roles_assignments ON users.id_user = user_roles_assignments.fo_user
        LEFT JOIN user_roles ON user_roles_assignments.fo_user_role = user_roles.id_user_role
         ORDER BY users.user_name; ";

        $res = mysqli_query($this->connection, $getUsers);
        $users = [];

        while ($row = mysqli_fetch_assoc($res)) {
            $users[] = $row;
        }

        return $users;
    }

    // Método GET para consultar un usuario por ID
    public function getUserById($id)
    {
        $getUser = "SELECT 
                users.id_user,
                users.user_name,
                users.user_lastname,
                document_types.document_type_name,
                users.document_number,
                users.email,
                user_roles.role_name
        FROM users
        INNER JOIN document_types ON users.fo_document_type = document_types.id_document_type
        LEFT JOIN  user_roles_assignments ON users.id_user = user_roles_assignments.fo_user
        LEFT JOIN  user_roles ON user_roles_assignments.fo_user_role = user_roles.id_user_role
         WHERE users.id_user = ?; ";

        $stmt = $this->connection->prepare($getUser);
        if ($stmt === false) {
            return [
                "result" => "Error",
                "message" => "Error al preparar la consulta: " . $this->connection->error
            ];
        }

        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows === 0) {
            return null; // Si no se encuentra el usuario
        }

        $user = $res->fetch_assoc();
        return $user;
    }

    // MÉTODO DELETE 
    public function deleteUser($id)
    {
        $deleteUser = "DELETE FROM users WHERE  id_user = ?";
        $stmt = $this->connection->prepare($deleteUser);

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
            "message" => "El usuario ha sido elimianado",
        ];
    }


    // MÉTODO ADD
    public function addUser($params)
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
        $addUser = "INSERT INTO users (
        user_name, 
        user_lastname, 
        fo_document_type, 
        document_number, 
        email, 
        password) 
        VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->connection->prepare($addUser);

        if ($stmt === false) {
            return [
                "result" => "Error",
                "message" => "Error al preparar la consulta" . $this->connection->error,
            ];
        }

        $stmt->bind_param(
            "ssiiss",
            $params["user_name"],
            $params["user_lastname"],
            $params["fo_document_type"],
            $params["document_number"],
            $params["email"],
            $params["password"]
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
            "message" => "El usuario a sido agregado con éxito",
        ];
    }

    // MÉTODO EDIT (update)
    public function editUser($id, $params)
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

        $editUser = "UPDATE users SET user_name = ?, user_lastname = ?, fo_document_type = ?, document_number = ?,
         email = ?, password = ? WHERE id_user = ?";
        $stmt = $this->connection->prepare($editUser);

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
            $params["password"],
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

    // MÉTODO FILTRAR
    public function filterUser($value)
    {
        $filterUser = "SELECT * FROM users WHERE user_name LIKE '%$value%";
        $res = mysqli_query($this->connection, $filterUser);
        $result = [];

        while ($row = mysqli_fetch_array($res)) {
            $result[] = $row;
        }
        return $result;

    }
}
?>