<h1>Equipos SIGELFA</h1>

<table border="1" cellpadding="10">

    <tr>
        <th>ID</th>
        <th>Equipo</th>
        <th>Representante</th>
        <th>Teléfono</th>
        <th>Categoría</th>
        <th>Torneo</th>
    </tr>

    <?php foreach($equipos as $equipo): ?>

    <tr>
        <td><?= $equipo['cveEquipo'] ?></td>

        <td><?= $equipo['nombEquipo'] ?></td>

        <td><?= $equipo['nombRepEq'] ?></td>

        <td><?= $equipo['numTelRepEq'] ?></td>

        <td><?= $equipo['nomCortoCat'] ?></td>

        <td><?= $equipo['perTorneo'] ?></td>
    </tr>

    <?php endforeach; ?>

</table>