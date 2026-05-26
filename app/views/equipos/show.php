<?php require_once __DIR__ . '/../../helpers/security.php'; ?>

<?php
$estadoActivo = (int) ($equipo['activo'] ?? 1) === 1;
$categoriaTorneo = trim(($equipo['nomCortoCat'] ?? '') . ' / ' . ($equipo['perTorneo'] ?? '') . ' / ' . ($equipo['cveLiga'] ?? ''), ' /');
?>

<section class="page-header animate-fade-up">
    <div>
        <div class="page-eyebrow">Equipos</div>
        <h1 class="page-title"><?php echo h($equipo['nombEquipo'] ?? 'Equipo'); ?></h1>
        <p class="page-subtitle">Detalle administrativo del equipo seleccionado</p>
    </div>

    <div class="table-actions">
        <a class="btn btn-secondary btn-sm" href="/?controller=equipos&action=index">Volver</a>
        <a class="btn btn-primary btn-sm" href="/?controller=equipos&action=edit&id=<?php echo h($equipo['cveEquipo'] ?? ''); ?>">Editar</a>
    </div>
</section>

<section class="card form-card animate-fade-up stagger-1">
    <div class="card-header">
        <div>
            <h2 class="card-title">Informacion del equipo</h2>
            <p class="card-description">Datos leidos desde SQL Server</p>
        </div>
        <span class="badge <?php echo $estadoActivo ? 'badge-green' : 'badge-neutral'; ?>">
            <?php echo $estadoActivo ? 'Activo' : 'Inactivo'; ?>
        </span>
    </div>

    <div class="detail-grid">
        <div class="detail-item">
            <span class="detail-label">ID</span>
            <strong class="mono">#<?php echo h($equipo['cveEquipo'] ?? ''); ?></strong>
        </div>
        <div class="detail-item">
            <span class="detail-label">Categoria / Torneo / Liga</span>
            <strong><?php echo h($categoriaTorneo !== '' ? $categoriaTorneo : 'Sin categoria'); ?></strong>
        </div>
        <div class="detail-item">
            <span class="detail-label">Representante</span>
            <strong><?php echo h($equipo['nombRepEq'] ?? 'Sin asignar'); ?></strong>
        </div>
        <div class="detail-item">
            <span class="detail-label">Telefono</span>
            <strong class="mono"><?php echo h($equipo['numTelRepEq'] ?? ''); ?></strong>
        </div>
        <div class="detail-item">
            <span class="detail-label">Correo</span>
            <strong><?php echo h($equipo['eMailRepEq'] ?? 'Sin correo'); ?></strong>
        </div>
    </div>
</section>

