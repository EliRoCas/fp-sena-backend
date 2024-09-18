<?php
require 'vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$host = $_ENV['DB_HOST'];
$username = $_ENV['DB_USERNAME'];
$password = $_ENV['DB_PASSWORD'];
$dbname = $_ENV['DB_NAME'];

$connection = mysqli_connect($host, $username, $password) or die("No se encontró el servidor");
mysqli_select_db($connection, $dbname) or die("No se encontró la Base de Datos");
mysqli_set_charset($connection, "utf8");
// echo "La conexión ha sido exitosa. ";
