<?php require_once __DIR__ . '/../../helpers/security.php'; ?>

<?php
$nombreCompleto = trim(($jugador['nomJug'] ?? '') . ' ' . ($jugador['apPatJug'] ?? '') . ' ' . ($jugador['apMatJug'] ?? ''));
$estadoActivo = (int) ($jugador['activo'] ?? 1) === 1;
$categoriaTorneo = trim(($jugador['nomCortoCat'] ?? '') . ' / ' . ($jugador['perTorneo'] ?? ''), ' /');
?>

<section class="page-header animate-fade-up">
    <div>
        <div class="page-eyebrow">Jugadores</div>
        <h1 class="page-title"><?php echo h($nombreCompleto !== '' ? $nombreCompleto : 'Jugador'); ?></h1>
        <p class="page-subtitle">Detalle deportivo y administrativo del jugador</p>
    </div>

    <div class="table-actions">
        <a class="btn btn-secondary btn-sm" href="/?controller=jugadores&action=index">Volver</a>
        <a class="btn btn-primary btn-sm" href="/?controller=jugadores&action=edit&id=<?php echo h($jugador['numJug'] ?? ''); ?>">Editar</a>
    </div>
</section>

<section class="card form-card animate-fade-up stagger-1">
    <div class="card-header">
        <div>
            <h2 class="card-title">Ficha del jugador</h2>
            <p class="card-description">Datos leidos desde SQL Server</p>
        </div>
        <span class="badge <?php echo $estadoActivo ? 'badge-green' : 'badge-neutral'; ?>">
            <?php echo $estadoActivo ? 'Activo' : 'Inactivo'; ?>
        </span>
    </div>

    <div class="detail-grid">
        <div class="detail-item">
            <span class="detail-label">Numero de jugador</span>
            <strong class="mono"><?php echo h($jugador['numJug'] ?? ''); ?></strong>
        </div>
        <div class="detail-item">
            <span class="detail-label">Camiseta</span>
            <strong><?php echo h($jugador['numeroCamiseta'] ?? '-'); ?></strong>
        </div>
        <div class="detail-item">
            <span class="detail-label">Equipo</span>
            <strong><?php echo h($jugador['nombEquipo'] ?? 'Sin equipo'); ?></strong>
            <p class="card-description"><?php echo h($categoriaTorneo !== '' ? $categoriaTorneo : '-'); ?></p>
        </div>
        <div class="detail-item">
            <span class="detail-label">Posicion</span>
            <strong><?php echo h(($jugador['abreviatura'] ?? '') . ' - ' . ($jugador['nombrePosicion'] ?? '')); ?></strong>
        </div>
        <div class="detail-item">
            <span class="detail-label">Nacimiento / Edad</span>
            <strong><?php echo h($jugador['fechaNacJug'] ?? '-'); ?> / <?php echo h($jugador['edadJug'] ?? '-'); ?></strong>
        </div>
        <div class="detail-item">
            <span class="detail-label">Contacto</span>
            <strong class="mono"><?php echo h($jugador['telJug'] ?? '-'); ?></strong>
            <p class="card-description"><?php echo h($jugador['eMailJug'] ?? '-'); ?></p>
        </div>
    </div>
</section>

