<?php
class UserRoleAssignment
{
    public $connection;

    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    // Método para asignar un rol a un usuario
    public function assignUserRole($id_user, $id_role)
    {
        $assignSql = "INSERT INTO user_roles_assignments (fo_user, fo_user_role) VALUES (?, ?)";
        $stmt = $this->connection->prepare($assignSql);

        if ($stmt === false) {
            return [
                "result" => "Error",
                "message" => "Error al preparar la consulta: " . $this->connection->error
            ];
        }

        $stmt->bind_param("ii", $id_user, $id_role);
        $result = $stmt->execute();

        if ($result === false) {
            return [
                "result" => "Error",
                "message" => "Error al ejecutar la consulta: " . $stmt->error
            ];
        }

        return [
            "result" => "OK",
            "message" => "Rol asignado correctamente al usuario"
        ];
    }

    // Método para consultar los roles de un usuario
    public function getUserRoles($id_user)
    {
        $getUserRoleSql = "SELECT ur.* FROM user_roles ur
                    INNER JOIN user_roles_assignments ura ON ur.id_user_role = ura.fo_user_role
                    WHERE ura.fo_user = ?";
        $stmt = $this->connection->prepare($getUserRoleSql);

        if ($stmt === false) {
            return [
                "result" => "Error",
                "message" => "Error al preparar la consulta: " . $this->connection->error
            ];
        }

        $stmt->bind_param("i", $id_user);
        $stmt->execute();
        $res = $stmt->get_result();
        $userRoles = [];

        while ($row = $res->fetch_assoc()) {
            $userRoles[] = $row;
        }

        return $userRoles;
    }
    // Método para consultar los roles de un usuario
    public function getAll()
    {
        $getAllSql = "SELECT ur.*, ura.fo_user 
                      FROM user_roles ur
                      INNER JOIN user_roles_assignments ura ON ur.id_user_role = ura.fo_user_role
                      ORDER BY ura.fo_user";

        $response = mysqli_query($this->connection, $getAllSql);

        if (!$response) {
            return [
                "result" => "Error",
                "message" => "Error en la consulta: " . mysqli_error($this->connection)
            ];
        }

        $user_roles_assignment = [];

        while ($row = mysqli_fetch_assoc($response)) {
            $user_roles_assignment[] = $row;
        }

        return $user_roles_assignment;
    }

    // Método para eliminar un rol asignado a un usuario
    public function unassignUserRole($id_user, $roleId = null)
    {
        if ($roleId === null) {
            $deleteSql = "DELETE FROM user_roles_assignments WHERE fo_user = ?";
            $stmt = $this->connection->prepare($deleteSql);

            if ($stmt === false) {
                return [
                    "result" => "Error",
                    "message" => "Error al preparar la consulta: " . $this->connection->error
                ];
            }

            $stmt->bind_param("i", $id_user);
        } else {
            $deleteSql = "DELETE FROM user_roles_assignments WHERE fo_user = ? AND fo_user_role = ?";
            $stmt = $this->connection->prepare($deleteSql);

            if ($stmt === false) {
                return [
                    "result" => "Error",
                    "message" => "Error al preparar la consulta: " . $this->connection->error
                ];
            }

            $stmt->bind_param("ii", $id_user, $roleId);
        }

        $result = $stmt->execute();

        if ($result === false) {
            return [
                "result" => "Error",
                "message" => "Error al ejecutar la consulta: " . $stmt->error
            ];
        }

        return [
            "result" => "OK",
            "message" => "Rol desasignado correctamente del usuario"
        ];
    }
}
?>