<?php
class Budget
{
    public $connection;

    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    // Método GET para consultar todos los presupuestos
    public function getBudgets()
    {
        $getBudgets = "SELECT bud.*, cat.category_name AS categories 
        FROM budgets 
        INNER JOIN categories cat ON bud.fo_category = cat.id_category
        ORDER BY budget_date";
        $res = mysqli_query($this->connection, $getBudgets);
        $budgets = [];

        while ($row = mysqli_fetch_array($res)) {
            $budgets[] = $row;
        }
        return $budgets;
    }

    // MÉTODO GET para consultar un presupuesto por ID con su categoría
    public function getBudgetById($id)
    {
        $getBudget = "SELECT bud.*, cat.category_name AS category
            FROM budgets bud
            INNER JOIN categories cat ON bud.fo_category = cat.id_category
            WHERE bud.id_budget = ?
            ORDER BY bud.budget_date";

        $stmt = $this->connection->prepare($getBudget);
        if ($stmt === false) {
            return [
                "result" => "Error",
                "message" => "Error al preparar la consulta: " . $this->connection->error
            ];
        }

        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();

        $budget = $res->fetch_assoc();

        if (!$budget) {
            return [
                "result" => "Error",
                "message" => "Presupuesto no encontrado"
            ];
        }

        return $budget;
    }

    // Método DELETE para eliminar un presupuesto
    public function deleteBudget($id)
    {
        $deleteBudget = "DELETE FROM budgets WHERE id_budget = ?";
        $stmt = $this->connection->prepare($deleteBudget);

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
    public function addBudget($params)
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

        $addBudget = "INSERT INTO budgets (budget_date, amount, fo_category, description_budget) VALUES (?, ?, ?, ?)";
        $stmt = $this->connection->prepare($addBudget);

        if ($stmt === false) {
            return [
                "result" => "Error",
                "message" => "Error al preparar la consulta: " . $this->connection->error
            ];
        }

        $stmt->bind_param(
            "sdss",
            $params["budget_date"],
            $params["amount"],
            $params["fo_category"],
            $params["description_budget"] ?? null // Permitir null
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
    public function editBudget($id, $params)
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

        $editBudget = "UPDATE budgets SET budget_date = ?, amount = ?, fo_category = ?, description_budget = ? WHERE id_budget = ?";
        $stmt = $this->connection->prepare($editBudget);

        if ($stmt === false) {
            return [
                "result" => "Error",
                "message" => "Error al preparar la consulta: " . $this->connection->error
            ];
        }

        $stmt->bind_param(
            "sdssi",
            $params["budget_date"],
            $params["amount"],
            $params["fo_category"],
            $params["description_budget"] ?? null, // Permitir null
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
    public function filterBudgets($value)
    {
        $filterBudgets = "SELECT bud.*, cat.category_name AS category
            FROM budgets bud
            INNER JOIN categories cat ON bud.fo_category = cat.id_category
            WHERE bud.budget_date LIKE ? 
               OR bud.amount LIKE ? 
               OR cat.category_name LIKE ?
            ORDER BY bud.budget_date";

        $stmt = $this->connection->prepare($filterBudgets);
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
        $res = $stmt->get_result();

        $budgets = [];
        while ($row = $res->fetch_assoc()) {
            $budgets[] = $row;
        }

        return $budgets;
    }

}
?>