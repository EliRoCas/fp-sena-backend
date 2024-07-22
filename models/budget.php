<?php
class Budget
{
    public $connection;

    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    // Método GET para consultar todos los presupuestos
    public function getAll()
    {
        $getAllSql = "SELECT bud.*, cat.category_name AS categories 
        FROM budgets bud
        INNER JOIN categories cat ON bud.fo_category = cat.id_category
        ORDER BY budget_date";
        $response = mysqli_query($this->connection, $getAllSql);
        $budgets = [];

        while ($row = mysqli_fetch_assoc($response)) {
            $budgets[] = $row;
        }
        return $budgets;
    }

    // MÉTODO GET para consultar un presupuesto por ID con su categoría
    public function getById($id)
    {
        $getByIdSql = "SELECT bud.*, cat.category_name AS category
            FROM budgets bud
            INNER JOIN categories cat ON bud.fo_category = cat.id_category
            WHERE bud.id_budget = ?
            ORDER BY bud.budget_date";

        $stmt = $this->connection->prepare($getByIdSql);
        if ($stmt === false) {
            return [
                "result" => "Error",
                "message" => "Error al preparar la consulta: " . $this->connection->error
            ];
        }

        $stmt->bind_param("i", $id);
        $stmt->execute();
        $response = $stmt->get_result();

        $budget = $response->fetch_assoc();

        if (!$budget) {
            return [
                "result" => "Error",
                "message" => "Presupuesto no encontrado"
            ];
        }

        return $budget;
    }

    // Método DELETE para eliminar un presupuesto
    public function delete($id)
    {
        $deleteSql = "DELETE FROM budgets WHERE id_budget = ?";
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
            "message" => "Presupuesto eliminado correctamente"
        ];
    }

    // Método ADD para agregar un nuevo presupuesto
    public function add($params)
    {
        if (
            !isset($params["budget_date"]) || !isset($params["amount"]) ||
            !isset($params["fo_category"])
        ) {
            return [
                "result" => "Error",
                "message" => "Los campos obligatorios son requeridos"
            ];
        }

        $insertSql = "INSERT INTO budgets (budget_date, amount, fo_category, description_budget) VALUES (?, ?, ?, ?)";
        $stmt = $this->connection->prepare($insertSql);

        if ($stmt === false) {
            return [
                "result" => "Error",
                "message" => "Error al preparar la consulta: " . $this->connection->error
            ];
        }

        $budget_date = $params["budget_date"];
        $amount = $params["amount"];
        $fo_category = $params["fo_category"];
        $descrption_budget = $params["description_budget"] ?? null; // Permitir null

        $stmt->bind_param(
            "sdss",
            $budget_date,
            $amount,
            $fo_category,
            $descrption_budget
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
            "message" => "Presupuesto agregado correctamente"
        ];
    }

    // Método EDIT para actualizar un presupuesto
    public function update($id, $params)
    {
        if (
            !isset($params["budget_date"]) || !isset($params["amount"]) ||
            !isset($params["fo_category"])
        ) {
            return [
                "result" => "Error",
                "message" => "Los campos obligatorios son requeridos"
            ];
        }

        $updateSql = "UPDATE budgets SET budget_date = ?, amount = ?, fo_category = ?, description_budget = ? WHERE id_budget = ?";
        $stmt = $this->connection->prepare($updateSql);

        if ($stmt === false) {
            return [
                "result" => "Error",
                "message" => "Error al preparar la consulta: " . $this->connection->error
            ];
        }

        $budget_date = $params["budget_date"];
        $amount = $params["amount"];
        $fo_category = $params["fo_category"];
        $descrption_budget = $params["description_budget"] ?? null; // Permitir null

        $stmt->bind_param(
            "sdssi",
            $budget_date,
            $amount,
            $fo_category,
            $descrption_budget,
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
            "message" => "Presupuesto actualizado correctamente"
        ];
    }

    // MÉTODO FILTRAR para consultar presupuestos por fecha, cantidad o categoría
    public function getByDate($value)
    {
        $filterSql = "SELECT bud.*, cat.category_name AS category
            FROM budgets bud
            INNER JOIN categories cat ON bud.fo_category = cat.id_category
            WHERE bud.budget_date LIKE ? 
               OR bud.amount LIKE ? 
               OR cat.category_name LIKE ?
            ORDER BY bud.budget_date";

        $stmt = $this->connection->prepare($filterSql);
        if ($stmt === false) {
            return [
                "result" => "Error",
                "message" => "Error al preparar la consulta: " . $this->connection->error
            ];
        }

        // Se prepara el valor para los filtros de búsqueda
        $likeValue = "%$value%";
        $stmt->bind_param("sss", $likeValue, $likeValue, $likeValue);
        $stmt->execute();
        $response = $stmt->get_result();

        $budgets = [];
        while ($row = $response->fetch_assoc()) {
            $budgets[] = $row;
        }

        return $budgets;
    }

}
?>