<?php
require_once __DIR__ . '/../app/models/Database.php';

$database = new Database();
$db = $database->getConnection();

if ($db) {
    echo "¡Conexión a SQL Server exitosa!";
}
?>