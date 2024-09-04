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

        if (!$stmt) {
            throw new Exception("Prepare:" . $this->connection->error);
        }

        // Se vincula el parámetro '$id' a la consulta
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $response = $stmt->get_result();
        $role = $response->fetch_assoc();

        if (!$role) {
            throw new Exception('Rol no encontrado');
        }

        return $role;
    }

    // Método para Filtrar 
    public function getByName($value)
    {
        $filterByNameSql = "SELECT * FROM user_roles WHERE role_name LIKE ? ORDER BY role_name";
        $stmt = $this->connection->prepare($filterByNameSql);

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

        $roles = [];
        while ($row = $response->fetch_assoc()) {
            $roles[] = $row;
        }

        return $roles;
    }

    // Método ADD 
    public function add($params)
    {
        if (!isset($params["role_name"]) || !isset($params["id_user_role"])) {
            throw new Exception("Todos los campos son requeridos");
        }

        $insertSql = "INSERT INTO user_roles (id_user_role, role_name) VALUES (?, ?)";
        $stmt = $this->connection->prepare($insertSql);

        if (!$stmt) {
            throw new Exception("Prepare" . $this->connection->error);
        }

        $stmt->bind_param("ss", $params["id_user_role"], $params["role_name"]);
        $result = $stmt->execute();

        if (!$result) {
            throw new Exception("Execute: " . $stmt->error);
        }

        // return [
        //     "result" => "OK",
        //     "message" => "El rol ha sido agregado"
        // ];
    }

    // MÉTODO para editar 
    public function update($id, $params)
    {
        if (!isset($params["role_name"])) {
            throw new Exception("Todos los campos son requeridos");
        }

        $updateSql = "UPDATE user_roles SET role_name = ? WHERE id_user_role = ?";
        $stmt = $this->connection->prepare($updateSql);

        if (!$stmt) {
            throw new Exception("Prepare:" . $this->connection->error);
        }

        $stmt->bind_param("ss", $params["role_name"], $id);
        $result = $stmt->execute();

        if (!$result) {
            throw new Exception("Execute: " . $stmt->error);
        }
        // return [
        //     "result" => "OK",
        //     "message" => "El rol ha sido actualizado con éxito"
        // ];
    }

    // MÉTODO DELETE 
    public function delete($id)
    {
        $deleteSql = "DELETE FROM user_roles WHERE id_user_role = ?";
        $stmt = $this->connection->prepare($deleteSql);

        if (!$stmt) {
            throw new Exception("Prepare:" . $this->connection->error);
        }

        // Se vincula el parámetro '$id' a la consulta, utilizando el método bind_param para asegurar el formato de la data (i)
        $stmt->bind_param("s", $id);
        $result = $stmt->execute();

        if (!$result) {
            throw new Exception("Execute:" . $stmt->error);
        }

        // return [
        //     "result" => "OK",
        //     "message" => "El rol ha sido eliminado"
        // ];
    }
}
