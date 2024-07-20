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
        //Se establece la consulta utilizando inner join y left join para asegurar todos los campos requeridos. 
        // Además, se utiliza el 'GROUP_CONCAT' para combinar todos los roles asociados a un usuario en una sola col. 
        $getUsers = "SELECT u.*, doc.document_type_name, GROUP_CONCAT(uRol.role_name SEPARATOR ', ') AS roles
            FROM users u
            INNER JOIN document_types doc ON u.fo_document_type = doc.id_document_type
            LEFT JOIN user_roles_assignments uRolAss ON u.id_user = uRolAss.fo_user
            LEFT JOIN user_roles uRol ON uRolAss.fo_user_role = uRol.id_user_role
            GROUP BY u.id_user
            ORDER BY u.user_name";

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
        $getUser = "SELECT u.*, doc.document_type_name, GROUP_CONCAT(uRol.role_name SEPARATOR ', ') AS roles
            FROM users u
            INNER JOIN document_types doc ON u.fo_document_type = doc.id_document_type
            LEFT JOIN user_roles_assignments uRolAss ON u.id_user = uRolAss.fo_user
            LEFT JOIN user_roles uRol ON uRolAss.fo_user_role = uRol.id_user_role
            WHERE u.id_user = ?
            GROUP BY u.id_user";

        $stmt = $this->connection->prepare($getUser);
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

        $user = $res->fetch_assoc();

        if (!$user) {
            return [
                "result" => "Error",
                "message" => "Usuario no encontrado"
            ];
        }

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
        $filterUser = "SELECT u.*, doc.document_type_name, GROUP_CONCAT(uRol.role_name SEPARATOR ', ') AS roles
            FROM users u
            INNER JOIN document_types doc ON u.fo_document_type = doc.id_document_type
            LEFT JOIN user_roles_assignments uRolAss ON u.id_user = uRolAss.fo_user
            LEFT JOIN user_roles uRol ON uRolAss.fo_user_role = uRol.id_user_role
            WHERE u.user_name LIKE ?
            GROUP BY u.id_user";

        $stmt = $this->connection->prepare($filterUser);
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
        $res = $stmt->get_result();

        $users = [];
        while ($row = $res->fetch_assoc()) {
            $users[] = $row;
        }

        return $users;

    }
}
?>