<?php
require_once '../db_connect.php';

class Login
{
    private $connection;

    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    public function getUserByEmailAndPassword($email, $password)
    {
        // Usar consulta preparada para evitar inyecciones SQL
        $query = "SELECT id_user, email, password FROM users WHERE email = ? LIMIT 1";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // Verifica el hash de la contraseña
            if (password_verify($password, $user['password'])) {
                return $user;
            } else {
                error_log("Contraseña incorrecta para el usuario: $email");
            }
        } else {
            error_log("Usuario no encontrado: $email");
        }

        return null;
    }
}
?>