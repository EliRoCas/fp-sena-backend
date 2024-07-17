<?php
class User_role
{
    public $connection;

    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    //MÉTODO para consultar 
    public function consult()
    {
        $consult = "SELECT * FROM user_role ORDER BY role_name";
        $stmt = $this->connection->prepare($consult);

        if ($stmt === false) {
            return [
                "result" => "Error",
                "message" => "Error al preparar la consulta: " . $this->connection->error
            ];
        }

        $stmt->execute();
        $res = $stmt->get_result();
        $user_roles = [];

        // Se utiliza un bucle 'while' que recorre cada fila (row) del resultado y la agrega al arreglo '$user_roles', utilizando el método 
        // 'fetch_assoc' 
        while ($row = $res->fetch_assoc()) {
            $user_roles[] = $row;
        }

        return $user_roles;
    }

    // MÉTODO DELETE 
    public function delete($id)
    {
        $delete = "DELETE FROM user_role WHERE id_user_role = ?";
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
    public function add($params)
    {
        if (!isset($params["role_name"])) {
            return [
                "result" => "Error",
                "message" => "Todos los campos son requeridos"
            ];
        }

        $add = "INSERT INTO user_role (role_name) VALUES (?)";
        $stmt = $this->connection->prepare($add);

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
    public function edit($id, $params)
    {
        if (!isset($params["role_name"])) {
            return [
                "result" => "Error",
                "message" => "Todos los campos son requeridos"
            ];
        }

        $edit = "UPDATE user_role SET role_name = ? WHERE id_user_role = ?";
        $stmt = $this->connection->prepare($edit);

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
            "message" => "El rol ha sido actualizado con éxito"
        ];
    }

    // Método para Filtrar 
    // Método para filtrar por un valor/nombre en particular
    public function filter($value)
    {
        $filter = "SELECT * FROM user_role WHERE role_name LIKE '%$value%";
        $res = mysqli_query($this->connection, $filter);
        $result = [];

        while ($row = mysqli_fetch_array($res)) {
            $result[] = $row;
        }
        return $result;

    }
}
?>