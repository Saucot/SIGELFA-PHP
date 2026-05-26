<?php require_once __DIR__ . '/../../helpers/security.php'; ?>

<h1>Equipos SIGELFA</h1>

<?php if (empty($equipos)): ?>
    <p>No hay equipos registrados.</p>
<?php else: ?>
    <table border="1" cellpadding="10">
        <tr>
            <th>ID</th>
            <th>Equipo</th>
            <th>Representante</th>
            <th>Telefono</th>
            <th>Categoria</th>
            <th>Torneo</th>
        </tr>

        <?php foreach($equipos as $equipo): ?>
            <tr>
                <td><?= h($equipo['cveEquipo'] ?? '') ?></td>
                <td><?= h($equipo['nombEquipo'] ?? '') ?></td>
                <td><?= h($equipo['nombRepEq'] ?? '') ?></td>
                <td><?= h($equipo['numTelRepEq'] ?? '') ?></td>
                <td><?= h($equipo['nomCortoCat'] ?? '') ?></td>
                <td><?= h($equipo['perTorneo'] ?? '') ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>

