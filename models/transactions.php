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
        $getAllSql = "SELECT * FROM transactions ORDER BY transaction_date";
        $response = mysqli_query($this->connection, $getAllSql);

        $transactions = [];

        while ($row = mysqli_fetch_assoc($response)) {
            $transactions[] = $row;
        }
        return $transactions;
    }

    // Método GET para consultar una transacción por ID
    public function getById($id)
    {
        $getByIdSql = "SELECT * FROM transactions WHERE id_transaction = ?";
        $stmt = $this->connection->prepare($getByIdSql);

        if (!$stmt) {
            throw new Exception("Prepare:" . $this->connection->error);
        }

        // Se vincula el parámetro '$id' a la consulta
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $transaction = $res->fetch_assoc();

        if (!$transaction) {
            throw new Exception('Transacción no encontrada');
        }

        return $transaction;
    }

    // MÉTODO FILTRAR
    public function getByName($value)
    {
        $filterSql = "SELECT * FROM transactions WHERE transaction_name LIKE ? ORDER BY transaction_name";
        // Preparamos la consulta
        $stmt = $this->connection->prepare($filterSql);

        if (!$stmt) {
            throw new Exception("Prepare:" . $this->connection->error);
        }

        // Usamos parámetros preparados para evitar inyección SQL
        $searchValue = "%{$value}%";
        $stmt->bind_param("s", $searchValue);
        $stmt->execute();
        $response = $stmt->get_result();

        $transactions = [];
        while ($row = $response->fetch_assoc()) {
            $transactions[] = $row;
        }

        return $transactions;
    }

    // Filtrar por "transaction_type" 
    public function getByType($type)
    {
        $filterByTypeSql = "SELECT * FROM transactions WHERE transaction_type = ? ORDER BY transaction_type, transaction_name";
        $stmt = $this->connection->prepare($filterByTypeSql);

        if (!$stmt) {
            throw new Exception("Prepare:" . $this->connection->error);
        }

        $stmt->bind_param("s", $type);
        $stmt->execute();
        $response = $stmt->get_result();

        $transactions = [];
        while ($row = $response->fetch_assoc()) {
            $transactions[] = $row;
        }

        return $transactions;
    }

    // Filtrar por fechas 
    public function getByDate($date)
    {
        $filterByDateSql = "SELECT * FROM transactions WHERE transaction_date = ?";
        $stmt = $this->connection->prepare($filterByDateSql);

        if (!$stmt) {
            throw new Exception("Prepare:" . $this->connection->error);
        }

        $stmt->bind_param("s", $date);
        $stmt->execute();
        $response = $stmt->get_result();

        $transactions = [];
        while ($row = $response->fetch_assoc()) {
            $transactions[] = $row;
        }

        return $transactions;
    }

    // Método ADD 
    public function add($params)
    {
        // Validación de campos obligatorios
        if (
            !isset($params["transaction_name"]) || !isset($params["transaction_date"]) ||
            !isset($params["transaction_amount"]) || !isset($params["transaction_type"]) ||
            !isset($params["fo_category"]) || !isset($params["id_transaction"])
        ) {
            throw new Exception("Todos los campos son requeridos");
        }

        // Preparar la consulta SQL
        $insertSql = "INSERT INTO transactions (
        id_transaction,
        transaction_name,
         transaction_rose_export, 
         fo_rose_type, 
         transaction_customer, 
         transaction_date, 
         transaction_amount, 
         transaction_description, 
         transaction_type, 
         fo_category, 
         fo_subcategory) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->connection->prepare($insertSql);

        if (!$stmt) {
            throw new Exception("Prepare:" . $this->connection->error);
        }

        // Preparar valores para bind_param
        $id_transaction = $params["id_transaction"];
        $transaction_name = $params["transaction_name"];
        $transaction_rose_export = $params["transaction_rose_export"] ?? null;
        $fo_rose_type = $params["fo_rose_type"] ?? null;
        $transaction_customer = $params["transaction_customer"] ?? null;
        $transaction_date = $params["transaction_date"];
        $transaction_amount = $params["transaction_amount"];
        $transaction_description = $params["transaction_description"] ?? null;
        $transaction_type = $params["transaction_type"];
        $fo_category = $params["fo_category"];
        $fo_subcategory = $params["fo_subcategory"];

        // Bind de parámetros
        $stmt->bind_param(
            "ssssssdssss",
            $id_transaction,
            $transaction_name,
            $transaction_rose_export,
            $fo_rose_type,
            $transaction_customer,
            $transaction_date,
            $transaction_amount,
            $transaction_description,
            $transaction_type,
            $fo_category,
            $fo_subcategory
        );
        $result = $stmt->execute();

        if (!$result) {
            throw new Exception("Execute:" . $stmt->error);
        }

        // return [
        //     "result" => "OK",
        //     "message" => "La transacción ha sido agregada"
        // ];
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
            fo_rose_type = ?, 
            transaction_customer = ?, 
            transaction_date = ?, 
            transaction_amount = ?, 
            transaction_description = ?, 
            transaction_type = ?, 
            fo_category = ?, 
            fo_subcategory = ? 
        WHERE id_transaction = ?";
        $stmt = $this->connection->prepare($updateSql);

        if (!$stmt) {
            throw new Exception("Prepare:" . $this->connection->error);
        }

        // Preparar valores para bind_param
        $transaction_name = $params["transaction_name"];
        $transaction_rose_export = $params["transaction_rose_export"] ?? null;
        $fo_rose_type = $params["fo_rose_type"] ?? null;
        $transaction_customer = $params["transaction_customer"] ?? null;
        $transaction_date = $params["transaction_date"];
        $transaction_amount = $params["transaction_amount"];
        $transaction_description = $params["transaction_description"] ?? null;
        $transaction_type = $params["transaction_type"];
        $fo_category = $params["fo_category"];
        $fo_subcategory = $params["fo_subcategory"];

        // Bind de parámetros
        $stmt->bind_param(
            "sssssdsssss",
            $transaction_name,
            $transaction_rose_export,
            $fo_rose_type,
            $transaction_customer,
            $transaction_date,
            $transaction_amount,
            $transaction_description,
            $transaction_type,
            $fo_category,
            $fo_subcategory,
            $id
        );

        $result = $stmt->execute();

        if (!$result) {
            throw new Exception("Execute:" . $stmt->error);
        }
        // return [
        //     "result" => "OK",
        //     "message" => "La transacción ha sido actualizada con éxito"
        // ];
    }

    // MÉTODO DELETE 
    public function delete($id)
    {
        $deleteSql = "DELETE FROM transactions WHERE id_transaction = ?";
        $stmt = $this->connection->prepare($deleteSql);

        if (!$stmt) {
            throw new Exception("Prepare:" . $this->connection->error);
        }

        $stmt->bind_param("s", $id);
        $result = $stmt->execute();

        if (!$result) {
            throw new Exception("Execute:" . $stmt->error);
        }

        // return [
        //     "result" => "OK",
        //     "message" => "La transacción ha sido eliminada"
        // ];
    }
}
