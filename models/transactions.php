<?php
class Transaction
{
    public $connection;

    public function __construct($connection)
    {
        $this->connection = $connection;
    }


    // MÉTODO GET para consultar todas las transacciones
    public function getAll()
    {
        $getAllSql = "SELECT trans.*, cat.category_name AS category_name
            FROM transactions trans
            INNER JOIN categories cat ON trans.fo_category = cat.id_category
            ORDER BY trans.transaction_date";

        $response = mysqli_query($this->connection, $getAllSql);
        $transactions = [];

        while ($row = mysqli_fetch_array($response, MYSQLI_ASSOC)) {
            $transactions[] = $row;
        }
        return $transactions;
    }

    // Método GET para consultar una transacción por ID
    public function getById($id)
    {
        $getByIdSql = "SELECT trans.*, cat.category_name AS category_name
            FROM transactions trans
            INNER JOIN categories cat ON trans.fo_category = cat.id_category
            WHERE  trans.id_transaction = ?";

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
        $res = $stmt->get_result();
        $transaction = $res->fetch_assoc();

        if (!$transaction) {
            return [
                "result" => "Error",
                "message" => "Transacción no encontrada"
            ];
        }

        return $transaction;
    }

    // MÉTODO DELETE 
    public function delete($id)
    {
        $deleteSql = "DELETE FROM transactions WHERE id_transaction = ?";
        $stmt = $this->connection->prepare($deleteSql);

        if ($stmt === false) {
            return [
                "result" => "Error",
                "message" => "Error al preparar la consulta: " . $this->connection->error
            ];
        }

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
            "message" => "La transacción ha sido eliminada"
        ];
    }

    // Método ADD 
    public function add($params)
    {
        // Validación de campos obligatorios
        if (
            !isset($params["transaction_name"]) || !isset($params["transaction_date"]) ||
            !isset($params["transaction_amount"]) || !isset($params["transaction_type"]) ||
            !isset($params["fo_category"])
        ) {
            return [
                "result" => "Error",
                "message" => "Los campos obligatorios son requeridos"
            ];
        }

        // Preparar la consulta SQL
        $insertSql = "INSERT INTO transactions (transaction_name, transaction_rose_export, transaction_rose_type, transaction_customer, transaction_date, transaction_amount, transaction_description, transaction_type, fo_category) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->connection->prepare($insertSql);

        if ($stmt === false) {
            return [
                "result" => "Error",
                "message" => "Error al preparar la consulta: " . $this->connection->error
            ];
        }

        // Preparar valores para bind_param
        $transaction_name = $params["transaction_name"];
        $transaction_rose_export = $params["transaction_rose_export"] ?? null;
        $transaction_rose_type = $params["transaction_rose_type"] ?? null;
        $transaction_customer = $params["transaction_customer"] ?? null;
        $transaction_date = $params["transaction_date"];
        $transaction_amount = $params["transaction_amount"];
        $transaction_description = $params["transaction_description"] ?? null;
        $transaction_type = $params["transaction_type"];
        $fo_category = $params["fo_category"];

        // Bind de parámetros
        $stmt->bind_param(
            "sssssdsis",
            $transaction_name,
            $transaction_rose_export,
            $transaction_rose_type,
            $transaction_customer,
            $transaction_date,
            $transaction_amount,
            $transaction_description,
            $transaction_type,
            $fo_category
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
            "message" => "La transacción ha sido agregada"
        ];
    }

    // MÉTODO para editar 
    public function update($id, $params)
    {
        if (
            !isset($params["transaction_name"]) || !isset($params["transaction_date"]) || !isset($params["transaction_amount"]) ||
            !isset($params["transaction_type"]) || !isset($params["fo_category"])
        ) {
            return [
                "result" => "Error",
                "message" => "Todos los campos son requeridos"
            ];
        }

        $updateSql = "UPDATE transactions SET transaction_name = ?, 
            transaction_rose_export = ?, 
            transaction_rose_type = ?, 
            transaction_customer = ?, 
            transaction_date = ?, 
            transaction_amount = ?, 
            transaction_description = ?, 
            transaction_type = ?, 
            fo_category = ? 
        WHERE id_transaction = ?";
        $stmt = $this->connection->prepare($updateSql);

        if ($stmt === false) {
            return [
                "result" => "Error",
                "message" => "Error al preparar la consulta: " . $this->connection->error
            ];
        }

        // Preparar valores para bind_param
        $transaction_name = $params["transaction_name"];
        $transaction_rose_export = $params["transaction_rose_export"] ?? null;
        $transaction_rose_type = $params["transaction_rose_type"] ?? null;
        $transaction_customer = $params["transaction_customer"] ?? null;
        $transaction_date = $params["transaction_date"];
        $transaction_amount = $params["transaction_amount"];
        $transaction_description = $params["transaction_description"] ?? null;
        $transaction_type = $params["transaction_type"];
        $fo_category = $params["fo_category"];

        // Bind de parámetros
        $stmt->bind_param(
            "sssssdssii",
            $transaction_name,
            $transaction_rose_export,
            $transaction_rose_type,
            $transaction_customer,
            $transaction_date,
            $transaction_amount,
            $transaction_description,
            $transaction_type,
            $fo_category,
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
            "message" => "La transacción ha sido actualizada con éxito"
        ];
    }

    // MÉTODO FILTRAR
    public function getByName($value)
    {
        $filterSql = "SELECT trans.*, cat.category_name AS category_name
            FROM transactions trans
            INNER JOIN categories cat ON trans.fo_category = cat.id_category
            WHERE trans.transaction_name LIKE ?";

        // Preparamos la consulta
        $stmt = $this->connection->prepare($filterSql);

        if ($stmt === false) {
            return [
                "result" => "Error",
                "message" => "Error al preparar la consulta: " . $this->connection->error
            ];
        }

        // Usamos parámetros preparados para evitar inyección SQL
        $searchValue = "%{$value}%";
        $stmt->bind_param("s", $searchValue);
        $stmt->execute();
        $response = $stmt->get_result();

        $result = [];
        while ($row = $response->fetch_assoc()) {
            $result[] = $row;
        }

        return $result;
    }
}
?>