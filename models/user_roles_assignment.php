<?php
class UserRoleAssignment
{
    public $connection;

    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    // Método para consultar los roles asignados 
    public function getAll()
    {
        $getAllSql = "SELECT * FROM user_roles_assignments ORDER BY fo_user";
        $response = mysqli_query($this->connection, $getAllSql);

        if (!$response) {
            throw new Exception("Prepare: " . $this->connection->error);
        }

        $user_roles_assignment = [];

        while ($row = mysqli_fetch_assoc($response)) {
            $user_roles_assignment[] = $row;
        }

        return $user_roles_assignment;
    }

    // Método para consultar los roles de un usuario
    public function getUserRoles($id)
    {
        $getUserRoleSql = "SELECT * FROM user_roles_assignments WHERE fo_user = ?";
        $stmt = $this->connection->prepare($getUserRoleSql);
        if (!$stmt) {
            throw new Exception("Prepare: " . $this->connection->error);
        }

        $stmt->bind_param("s", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $userRoles = [];

        while ($row = $res->fetch_assoc()) {
            $userRoles[] = $row;
        }

        return $userRoles;
    }

    // Método para asignar un rol a un usuario
    public function assignUserRole($fo_user, $fo_user_role)
    {
        $assignSql = "INSERT INTO user_roles_assignments (fo_user, fo_user_role) VALUES (?, ?)";
        $stmt = $this->connection->prepare($assignSql);

        if (!$stmt) {
            throw new Exception("Prepare: " . $this->connection->error);
        }

        $stmt->bind_param("ss", $fo_user, $fo_user_role);
        $result = $stmt->execute();

        if (!$result) {
            throw new Exception("Execute: " . $stmt->error);
        }

        // return [
        //     "result" => "OK",
        //     "message" => "Rol asignado correctamente al usuario"
        // ];
    }

    // Método para eliminar un rol asignado a un usuario
    public function unassignUserRole($fo_user, $fo_user_role = null)
    {
        if ($fo_user_role === null) {
            $deleteSql = "DELETE FROM user_roles_assignments WHERE fo_user = ?";
            $stmt = $this->connection->prepare($deleteSql);

            if (!$stmt) {
                throw new Exception("Prepare: " . $this->connection->error);
            }

            $stmt->bind_param("s", $fo_user);
        } else {
            $deleteSql = "DELETE FROM user_roles_assignments WHERE fo_user = ? AND fo_user_role = ?";
            $stmt = $this->connection->prepare($deleteSql);

            if (!$stmt) {
                throw new Exception("Prepare: " . $this->connection->error);
            }

            $stmt->bind_param("ss", $fo_user, $fo_user_role);
        }

        $result = $stmt->execute();

        if (!$result) {
            throw new Exception("Execute: " . $stmt->error);
        }

        // return [
        //     "result" => "OK",
        //     "message" => "Rol desasignado correctamente del usuario"
        // ];
    }
}
