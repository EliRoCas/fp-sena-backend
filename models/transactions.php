<?php
class Transaction
{
    public $connection;

    public function __construct($connection)
    {
        $this->connection = $connection;
    }


    // MÉTODO GET para consultar todos los usuarios 
    public function getTransaction()
    {
        $getTransaction = "SELECT * FROM transactions ORDER BY transaction_date";
        $res = mysqli_query($this->connection, $getTransaction);
        $transactions = [];

        while ($row = mysqli_fetch_array($res)) {
            $transactions[] = $row;
        }
        return $transactions;
    }

    // MÉTODO DELETE 
    public function delete($id)
    {
        $delete = "DELETE FROM transactions WHERE id_transaction = ?";
        $stmt = $this->connection->prepare($delete);

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
        if (
            !isset($params["transaction_name"]) || !isset($params["transaction_date"]) || !isset($params["transaction_amount"]) ||
            !isset($params["transaction_type"]) || !isset($params["fo_category"])
        ) {
            return [
                "result" => "Error",
                "message" => "Todos los campos son requeridos"
            ];
        }

        $add = "INSERT INTO transactions (transaction_name, transaction_rose_export, transaction_rose_type, transaction_customer, transaction_date, transaction_amount, transaction_description, transaction_type, fo_category) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->connection->prepare($add);

        if ($stmt === false) {
            return [
                "result" => "Error",
                "message" => "Error al preparar la consulta: " . $this->connection->error
            ];
        }

        $stmt->bind_param("ssssssssi", $params["transaction_name"], $params["transaction_rose_export"], $params["transaction_rose_type"], $params["transaction_customer"], $params["transaction_date"], $params["transaction_amount"], $params["transaction_description"], $params["transaction_type"], $params["fo_category"]);
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
    public function edit($id, $params)
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

        $edit = "UPDATE transactions SET transaction_name = ?, transaction_rose_export = ?, transaction_rose_type = ?, transaction_customer = ?, transaction_date = ?, transaction_amount = ?, transaction_description = ?, transaction_type = ?, fo_category = ? WHERE id_transaction = ?";
        $stmt = $this->connection->prepare($edit);

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
        $filterTransaction = "SELECT * FROM document_types WHERE document_type_name LIKE '%$value%";
        $res = mysqli_query($this->connection, $filterTransaction);
        $result = [];

        while ($row = mysqli_fetch_array($res)) {
            $result[] = $row;
        }
        return $result;

    }
}
?>