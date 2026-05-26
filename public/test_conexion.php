<?php
require_once __DIR__ . '/../app/config/env.php';
require_once __DIR__ . '/../app/models/Database.php';
require_once __DIR__ . '/../app/helpers/security.php';

if (!env_bool('APP_DEBUG', false)) {
    http_response_code(403);
    echo 'Prueba de conexion no disponible.';
    exit;
}

$baseActual = null;
$error = null;

try {
    $database = new Database();
    $db = $database->getConnection();

    $stmt = $db->query('SELECT DB_NAME() AS base_actual');
    $row = $stmt->fetch();
    $baseActual = $row['base_actual'] ?? null;
} catch (PDOException $exception) {
    error_log('SIGELFA test connection error: ' . $exception->getMessage());
    $error = 'No se pudo conectar con la base de datos. Verifica la configuracion local.';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prueba de conexion - SIGELFA</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h1>Prueba de conexion</h1>
            </div>

            <?php if ($error !== null): ?>
                <p><?php echo h($error); ?></p>
            <?php else: ?>
                <p>Conexion a SQL Server exitosa.</p>
                <p>Base actual: <strong><?php echo h($baseActual); ?></strong></p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

