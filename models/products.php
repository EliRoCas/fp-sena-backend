<?php
class Product
{
    public $connection;

    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    // Método GET para consultar todos los productos
    public function getProducts()
    {
        $getProducts = "SELECT * FROM products ORDER BY product_name";
        $res = mysqli_query($this->connection, $getProducts);
        $products = [];

        while ($row = mysqli_fetch_array($res)) {
            $products[] = $row;
        }
        return $products;
    }

    // Método GET para consultar un producto por ID
    public function getProductById($id)
    {
        $getProduct = "SELECT * FROM products WHERE id_product = ?";
        $stmt = $this->connection->prepare($getProduct);

        if ($stmt === false) {
            return [
                "result" => "Error",
                "message" => "Error al preparar la consulta: " . $this->connection->error
            ];
        }

        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $product = $res->fetch_assoc();

        if (!$product) {
            return [
                "result" => "Error",
                "message" => "Producto no encontrado"
            ];
        }

        return $product;
    }

    // Método DELETE para eliminar un producto
    public function deleteProduct($id)
    {
        $deleteProduct = "DELETE FROM products WHERE id_product = ?";
        $stmt = $this->connection->prepare($deleteProduct);

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
            "message" => "Producto eliminado correctamente"
        ];
    }

    // Método ADD para agregar un nuevo producto
    public function addProduct($params)
    {
        if (
            !isset($params["product_name"]) || !isset($params["product_type"]) ||
            !isset($params["quantity"]) || !isset($params["fo_category"])
        ) {
            return [
                "result" => "Error",
                "message" => "Los campos obligatorios son requeridos"
            ];
        }

        $addProduct = "INSERT INTO products (product_name, 
            product_type, 
            product_img, 
            product_description, 
            quantity, 
            fo_category) 
        VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->connection->prepare($addProduct);

        if ($stmt === false) {
            return [
                "result" => "Error",
                "message" => "Error al preparar la consulta: " . $this->connection->error
            ];
        }

        $stmt->bind_param(
            "ssssdi",
            $params["product_name"],
            $params["product_type"],
            $params["product_img"] ?? null, // Permitir null
            $params["product_description"] ?? null, // Permitir null
            $params["quantity"],
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
            "message" => "Producto agregado correctamente"
        ];
    }

    // Método EDIT para actualizar un producto
    public function editProduct($id, $params)
    {
        if (
            !isset($params["product_name"]) || !isset($params["product_type"]) ||
            !isset($params["quantity"]) || !isset($params["fo_category"])
        ) {
            return [
                "result" => "Error",
                "message" => "Los campos obligatorios son requeridos"
            ];
        }

        $editProduct = "UPDATE products SET product_name = ?, 
            product_type = ?, 
            product_img = ?, 
            product_description = ?,
            quantity = ?,
            fo_category = ? 
        WHERE id_product = ?";
        $stmt = $this->connection->prepare($editProduct);

        if ($stmt === false) {
            return [
                "result" => "Error",
                "message" => "Error al preparar la consulta: " . $this->connection->error
            ];
        }

        $stmt->bind_param(
            "ssssdii",
            $params["product_name"],
            $params["product_type"],
            $params["product_img"] ?? null, // Permitir null
            $params["product_description"] ?? null, // Permitir null
            $params["quantity"],
            $params["fo_category"],
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
            "message" => "Producto actualizado correctamente"
        ];
    }

    // MÉTODO FILTRAR
    public function filterProduct($value)
    {
        $filterProduct = "SELECT * FROM products WHERE product_name LIKE '%$value%";
        $res = mysqli_query($this->connection, $filterProduct);
        $result = [];

        while ($row = mysqli_fetch_array($res)) {
            $result[] = $row;
        }
        return $result;

    }
}

?>