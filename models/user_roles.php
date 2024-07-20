<?php
class UserRole
{
    public $connection;

    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    //MÉTODO GET para consultar todos los roles 
    public function getAll()
    {
        $getAllSql = "SELECT * FROM user_roles ORDER BY role_name";
        $response = mysqli_query($this->connection, $getAllSql);
        $roles = [];

        while ($row = mysqli_fetch_assoc($response)) {
            $roles[] = $row;
        }

        return $roles;
    }

    // Método GET para consultar un rol por ID
    public function getById($id)
    {
        $getByIdSql = "SELECT * FROM user_roles WHERE id_user_role = ?";
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
        $role = $response->fetch_assoc();

        if (!$role) {
            return [
                "result" => "Error",
                "message" => "Rol no encontrado"
            ];
        }

        return $role;
    }


    // MÉTODO DELETE 
    public function delete($id)
    {
        $deleteSql = "DELETE FROM user_roles WHERE id_user_role = ?";
        $stmt = $this->connection->prepare($deleteSql);

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
            "message" => "El rol ha sido eliminado"
        ];
    }

    // Método ADD 
    public function add($params)
    {
        if (!isset($params["role_name"])) {
            return [
                "result" => "Error",
                "message" => "Todos los campos son requeridos"
            ];
        }

        $insertSql = "INSERT INTO user_roles (role_name) VALUES (?)";
        $stmt = $this->connection->prepare($insertSql);

        if ($stmt === false) {
            return [
                "result" => "Error",
                "message" => "Error al preparar la consulta: " . $this->connection->error
            ];
        }

        $stmt->bind_param("s", $params["role_name"]);
        $result = $stmt->execute();

        if ($result === false) {
            return [
                "result" => "Error",
                "message" => "Error al ejecutar la consulta: " . $stmt->error
            ];
        }

        return [
            "result" => "OK",
            "message" => "El rol ha sido agregado"
        ];
    }

    // MÉTODO para editar 
    public function update($id, $params)
    {
        if (!isset($params["role_name"])) {
            return [
                "result" => "Error",
                "message" => "Todos los campos son requeridos"
            ];
        }

        $updateSql = "UPDATE user_roles SET role_name = ? WHERE id_user_role = ?";
        $stmt = $this->connection->prepare($updateSql);

        if ($stmt === false) {
            return [
                "result" => "Error",
                "message" => "Error al preparar la consulta: " . $this->connection->error
            ];
        }

        $stmt->bind_param("si", $params["role_name"], $id);
        $result = $stmt->execute();

        if ($result === false) {
            return [
                "result" => "Error",
                "message" => "Error al ejecutar la consulta: " . $stmt->error
            ];
        }
        return [
            "result" => "OK",
            "message" => "El rol ha sido actualizado con éxito"
        ];
    }

    // Método para Filtrar 
    public function getByName($value)
    {
        $filterByNameSql = "SELECT * FROM user_roles WHERE role_name LIKE ?";
        $stmt = $this->connection->prepare($filterByNameSql);

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

        $roles = [];
        while ($row = $response->fetch_assoc()) {
            $roles[] = $row;
        }

        return $roles;
    }

}
?>