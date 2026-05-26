<?php require_once __DIR__ . '/../../helpers/security.php'; ?>

<?php
$nombreCompleto = trim(($arbitro['nomArb'] ?? '') . ' ' . ($arbitro['apPatArb'] ?? '') . ' ' . ($arbitro['apMatArb'] ?? ''));
$estadoActivo = (int) ($arbitro['activo'] ?? 1) === 1;
?>

<section class="page-header animate-fade-up">
    <div>
        <div class="page-eyebrow">Arbitros</div>
        <h1 class="page-title"><?php echo h($nombreCompleto !== '' ? $nombreCompleto : 'Arbitro'); ?></h1>
        <p class="page-subtitle">Detalle administrativo del integrante arbitral</p>
    </div>

    <div class="table-actions">
        <a class="btn btn-secondary btn-sm" href="/?controller=arbitros&action=index">Volver</a>
        <a class="btn btn-primary btn-sm" href="/?controller=arbitros&action=edit&id=<?php echo h($arbitro['numArb'] ?? ''); ?>">Editar</a>
    </div>
</section>

<section class="card form-card animate-fade-up stagger-1">
    <div class="card-header">
        <div>
            <h2 class="card-title">Ficha arbitral</h2>
            <p class="card-description">Datos leidos desde SQL Server</p>
        </div>
        <span class="badge <?php echo $estadoActivo ? 'badge-green' : 'badge-neutral'; ?>">
            <?php echo $estadoActivo ? 'Activo' : 'Inactivo'; ?>
        </span>
    </div>

    <div class="detail-grid">
        <div class="detail-item">
            <span class="detail-label">Numero de arbitro</span>
            <strong class="mono"><?php echo h($arbitro['numArb'] ?? ''); ?></strong>
        </div>
        <div class="detail-item">
            <span class="detail-label">Nombre completo</span>
            <strong><?php echo h($nombreCompleto !== '' ? $nombreCompleto : '-'); ?></strong>
        </div>
        <div class="detail-item">
            <span class="detail-label">Telefono</span>
            <strong class="mono"><?php echo h($arbitro['telArb'] ?? '-'); ?></strong>
        </div>
        <div class="detail-item">
            <span class="detail-label">Correo</span>
            <strong><?php echo h($arbitro['eMailArb'] ?? '-'); ?></strong>
        </div>
    </div>
</section>

