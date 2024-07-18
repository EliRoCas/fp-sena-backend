<?php
class Transaction
{
    public $connection;

    public function __construct($connection)
    {
        $this->connection = $connection;
    }


    // MÉTODO GET para consultar todas las transacciones
    public function getTransactions()
    {
        $getTransactions = "SELECT trans.*, cat.category_name AS category_name
            FROM transactions trans
            INNER JOIN categories cat ON trans.fo_category = cat.id_category
            ORDER BY trans.transaction_date";

        $res = mysqli_query($this->connection, $getTransactions);
        $transactions = [];

        while ($row = mysqli_fetch_array($res, MYSQLI_ASSOC)) {
            $transactions[] = $row;
        }
        return $transactions;
    }

    // Método GET para consultar una transacción por ID
    public function getTransactionById($id)
    {
        $getTransaction = "SELECT trans.*, cat.category_name AS category_name
            FROM transactions trans
            INNER JOIN categories cat ON trans.fo_category = cat.id_category
            WHERE  trans.id_transaction = ?";

        $stmt = $this->connection->prepare($getTransaction);

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
    public function deleteTransaction($id)
    {
        $deleteTransaction = "DELETE FROM transactions WHERE id_transaction = ?";
        $stmt = $this->connection->prepare($deleteTransaction);

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
    public function addTransaction($params)
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
        $addTransaction = "INSERT INTO transactions (transaction_name, transaction_rose_export, transaction_rose_type, transaction_customer, transaction_date, transaction_amount, transaction_description, transaction_type, fo_category) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->connection->prepare($addTransaction);

        if ($stmt === false) {
            return [
                "result" => "Error",
                "message" => "Error al preparar la consulta: " . $this->connection->error
            ];
        }

        // Bind de parámetros, manejando nulos con valores predeterminados
        $stmt->bind_param(
            "sssssdsss",
            $params["transaction_name"],
            $params["transaction_rose_export"] ?? null, // Permitir null
            $params["transaction_rose_type"] ?? null, // Permitir null
            $params["transaction_customer"] ?? null, // Permitir null
            $params["transaction_date"],
            $params["transaction_amount"],
            $params["transaction_description"] ?? null, // Permitir null
            $params["transaction_type"],
            $params["fo_category"]
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
    public function editTransaction($id, $params)
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

        $editTransaction = "UPDATE transactions SET transaction_name = ?, 
            transaction_rose_export = ?, 
            transaction_rose_type = ?, 
            transaction_customer = ?, 
            transaction_date = ?, 
            transaction_amount = ?, 
            transaction_description = ?, 
            transaction_type = ?, 
            fo_category = ? 
        WHERE id_transaction = ?";
        $stmt = $this->connection->prepare($editTransaction);

        if ($stmt === false) {
            return [
                "result" => "Error",
                "message" => "Error al preparar la consulta: " . $this->connection->error
            ];
        }

        $stmt->bind_param("ssssssssi", $params["transaction_name"], $params["transaction_rose_export"], $params["transaction_rose_type"], $params["transaction_customer"], $params["transaction_date"], $params["transaction_amount"], $params["transaction_description"], $params["transaction_type"], $params["fo_category"], $id);
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
    public function filterTransaction($value)
    {
        $filterTransaction = "SELECT trans.*, cat.category_name AS category_name
            FROM transactions trans
            INNER JOIN categories cat ON trans.fo_category = cat.id_category
            WHERE trans.transaction_name LIKE ?";

        // Preparamos la consulta
        $stmt = $this->connection->prepare($filterTransaction);

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
        $res = $stmt->get_result();

        $result = [];
        while ($row = $res->fetch_assoc()) {
            $result[] = $row;
        }

        return $result;
    }
}
?>