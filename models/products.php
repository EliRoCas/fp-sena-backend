<?php
class Product
{
    public $connection;

    public function __construct($connection)
    {
        $this->connection = $connection;
    }


    // Método GET para consultar todos los productos con sus categorías 
    public function getAll()
    {
        $getAllSql = "SELECT * FROM products ORDER BY id_product";
        $response = mysqli_query($this->connection, $getAllSql);
        $products = [];

        while ($row = mysqli_fetch_assoc($response)) {
            $products[] = $row;
        }
        return $products;
    }

    // Método GET para consultar un producto por ID con sus categorías 
    public function getById($id)
    {
        $getByIdSql = "SELECT * FROM products WHERE id_product = ?";

        $stmt = $this->connection->prepare($getByIdSql);
        if (!$stmt) {
            throw new Exception("Prepare:" . $this->connection->error);
        }

        $stmt->bind_param("s", $id);
        $stmt->execute();
        $response = $stmt->get_result();

        $product = $response->fetch_assoc();

        if (!$product) {
            throw new Exception('Producto no encontrado');
        }

        return $product;
    }

    // MÉTODO FILTRAR para consultar productos por nombre, tipo o categoría
    public function getByName($value)
    {
        $filterSql = "SELECT * FROM products WHERE product_name LIKE ? ORDER BY product_name";

        $stmt = $this->connection->prepare($filterSql);
        if (!$stmt) {
            throw new Exception("Prepare:" . $this->connection->error);
        }

        // Se prepara el valor para los filtros de búsqueda
        $likeValue = "%$value%";
        $stmt->bind_param("s", $likeValue);
        $stmt->execute();
        $response = $stmt->get_result();

        $products = [];
        while ($row = $response->fetch_assoc()) {
            $products[] = $row;
        }

        return $products;
    }

    // Método ADD para agregar un nuevo producto
    public function add($params)
    {
        if (
            !isset($params["product_name"]) ||
            !isset($params["quantity"]) ||
            !isset($params["fo_category"]) ||
            !isset($params["id_product"])
        ) {
            throw new Exception("Los campos obligatorios son requeridos");
        }

        $insertSql = "INSERT INTO products (
            id_product,
            product_name, 
            fo_subcategory, 
            product_img, 
            product_description, 
            quantity, 
            fo_category
            ) 
        VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->connection->prepare($insertSql);

        if (!$stmt) {
            throw new Exception("Prepare: " . $this->connection->error);
        }

        // Preparar valores para bind_param
        $id_product = $params["id_product"];
        $product_name = $params["product_name"];
        $fo_subcategory = $params["fo_subcategory"];
        $product_img = $params["product_img"];
        $product_description = $params["product_description"] ?? null;
        $quantity = $params["quantity"];
        $fo_category = $params["fo_category"];

        $stmt->bind_param(
            "sssssds",
            $id_product,
            $product_name,
            $fo_subcategory,
            $product_img,
            $product_description,
            $quantity,
            $fo_category
        );
        $result = $stmt->execute();

        if (!$result) {
            throw new Exception("Execute: " . $stmt->error);
        }

        // return [
        //     "result" => "OK",
        //     "message" => "Producto agregado correctamente"
        // ];
    }

    // Método EDIT para actualizar un producto
    public function update($id, $params)
    {
        if (
            !isset($params["product_name"]) ||
            !isset($params["quantity"]) ||
            !isset($params["fo_category"])
        ) {
            return [
                "result" => "Error",
                "message" => "Los campos obligatorios son requeridos"
            ];
        }

        $updateSql = "UPDATE products SET product_name = ?, 
            fo_subcategory = ?, 
            product_img = ?, 
            product_description = ?,
            quantity = ?,
            fo_category = ? 
        WHERE id_product = ?";
        $stmt = $this->connection->prepare($updateSql);

        if (!$stmt) {
            throw new Exception("Prepare: " . $this->connection->error);
        }

        // Preparar valores para bind_param
        $product_name = $params["product_name"];
        $fo_subcategory = $params["fo_subcategory"];
        $product_img = $params["product_img"];
        $product_description = $params["product_description"] ?? null;
        $quantity = $params["quantity"];
        $fo_category = $params["fo_category"];

        $stmt->bind_param(
            "ssssdss",
            $product_name,
            $fo_subcategory,
            $product_img,
            $product_description,
            $quantity,
            $fo_category,
            $id
        );
        $result = $stmt->execute();

        if (!$result) {
            throw new Exception("Execute: " . $stmt->error);
        }

        // return [
        //     "result" => "OK",
        //     "message" => "Producto actualizado correctamente"
        // ];
    }

    // Método DELETE para eliminar un producto
    public function delete($id)
    {
        $deleteSql = "DELETE FROM products WHERE id_product = ?";
        $stmt = $this->connection->prepare($deleteSql);

        if (!$stmt) {
            throw new Exception("Prepare: " . $this->connection->error);
        }

        $stmt->bind_param("s", $id);
        $result = $stmt->execute();

        if (!$result) {
            throw new Exception("Execute: " . $stmt->error);
        }

        // return [
        //     "result" => "OK",
        //     "message" => "Producto eliminado correctamente"
        // ];
    }
}
