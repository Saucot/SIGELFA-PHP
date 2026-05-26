<?php
require_once __DIR__ . '/../app/models/Database.php';
require_once __DIR__ . '/../app/helpers/security.php';

$equipos = [];
$error = null;

try {
    $database = new Database();
    $db = $database->getConnection();

    $query = "SELECT
                cveEquipo,
                nombEquipo,
                nombRepEq,
                numTelRepEq
              FROM Equipo
              ORDER BY nombEquipo";

    $stmt = $db->prepare($query);
    $stmt->execute();
    $equipos = $stmt->fetchAll();
} catch (PDOException $exception) {
    error_log('SIGELFA equipos query error: ' . $exception->getMessage());
    $error = 'No se pudieron cargar los equipos. Verifica la configuracion local.';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIGELFA</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

    <div class="container">

        <div class="header">
            <div>
                <h1>SIGELFA</h1>
                <p>Sistema de Gestion de Liga de Futbol Amateur</p>
            </div>

            <button class="btn" type="button">
                + Nuevo Equipo
            </button>
        </div>

        <div class="card">

            <div class="card-header">
                <h2>Equipos registrados</h2>

                <span>
                    <?php echo h(count($equipos)); ?> equipos
                </span>
            </div>

            <?php if ($error !== null): ?>
                <p><?php echo h($error); ?></p>
            <?php elseif (count($equipos) === 0): ?>
                <p>No hay equipos registrados.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Equipo</th>
                            <th>Representante</th>
                            <th>Telefono</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php foreach($equipos as $equipo): ?>
                        <tr>
                            <td><?php echo h($equipo['cveEquipo'] ?? ''); ?></td>
                            <td><?php echo h($equipo['nombEquipo'] ?? ''); ?></td>
                            <td><?php echo h($equipo['nombRepEq'] ?? ''); ?></td>
                            <td><?php echo h($equipo['numTelRepEq'] ?? ''); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

        </div>

    </div>

</body>
</html>

