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
        $getAll = "SELECT * FROM user_roles ORDER BY role_name";
        $res = mysqli_query($this->connection, $getAll);
        $roles = [];

        while ($row = mysqli_fetch_assoc($res)) {
            $roles[] = $row;
        }

        return $roles;
    }

    // Método GET para consultar un rol por ID
    public function getById($id)
    {
        $getById = "SELECT * FROM user_roles WHERE id_user_role = ?";
        $stmt = $this->connection->prepare($getById);

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
        $role = $res->fetch_assoc();

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
        $delete = "DELETE FROM user_roles WHERE id_user_role = ?";
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
            "message" => "El rol ha sido eliminado"
        ];
    }

    // Método ADD 
    public function post($params)
    {
        if (!isset($params["role_name"])) {
            return [
                "result" => "Error",
                "message" => "Todos los campos son requeridos"
            ];
        }

        $post = "INSERT INTO user_roles (role_name) VALUES (?)";
        $stmt = $this->connection->prepare($post);

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
    public function patch($id, $params)
    {
        if (!isset($params["role_name"])) {
            return [
                "result" => "Error",
                "message" => "Todos los campos son requeridos"
            ];
        }

        $patch = "UPDATE user_roles SET role_name = ? WHERE id_user_role = ?";
        $stmt = $this->connection->prepare($patch);

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
    public function filter($value)
    {
        $filter = "SELECT * FROM user_roles WHERE role_name LIKE ?";
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

        $roles = [];
        while ($row = $res->fetch_assoc()) {
            $roles[] = $row;
        }

        return $roles;
    }

}
?>