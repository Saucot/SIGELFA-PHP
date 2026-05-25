<?php
require_once __DIR__ . '/../app/models/Database.php';

$database = new Database();
$db = $database->getConnection();

/* Consulta de equipos */
$query = "SELECT
            cveEquipo,
            nombEquipo,
            nombRepEq,
            numTelRepEq
          FROM Equipo
          ORDER BY nombEquipo";

$stmt = $db->prepare($query);
$stmt->execute();

$equipos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIGELFA</title>

    <!-- CSS -->
    <link rel="stylesheet" href="/SIGELFA-PHP/public/assets/css/style.css">

    <!-- Fuente moderna -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

    <div class="container">

        <!-- Header -->
        <div class="header">
            <div>
                <h1>⚽ SIGELFA</h1>
                <p>Sistema de Gestión de Liga de Fútbol Amateur</p>
            </div>

            <button class="btn">
                + Nuevo Equipo
            </button>
        </div>

        <!-- Card -->
        <div class="card">

            <div class="card-header">
                <h2>Equipos registrados</h2>

                <span>
                    <?php echo count($equipos); ?> equipos
                </span>
            </div>

            <!-- Tabla -->
            <table>

                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Equipo</th>
                        <th>Representante</th>
                        <th>Teléfono</th>
                    </tr>
                </thead>

                <tbody>

                <?php foreach($equipos as $equipo): ?>

                    <tr>
                        <td><?php echo $equipo['cveEquipo']; ?></td>
                        <td><?php echo $equipo['nombEquipo']; ?></td>
                        <td><?php echo $equipo['nombRepEq']; ?></td>
                        <td><?php echo $equipo['numTelRepEq']; ?></td>
                    </tr>

                <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    </div>

</body>
</html>