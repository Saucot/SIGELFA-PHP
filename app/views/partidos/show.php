<?php require_once __DIR__ . '/../../helpers/security.php'; ?>

<?php
$idPartido = (int) ($partido['idPartido'] ?? 0);
$marcador = ($partido['golesEquipoA'] ?? null) !== null && ($partido['golesEquipoB'] ?? null) !== null
    ? (string) $partido['golesEquipoA'] . ' - ' . (string) $partido['golesEquipoB']
    : 'vs';
$estado = (string) ($partido['estadoPartido'] ?? 'PROGRAMADO');
$estadoClass = $estado === 'JUGADO' ? 'badge-green' : ($estado === 'CANCELADO' ? 'badge-red' : 'badge-blue');
$arbitro = trim(($partido['nomArb'] ?? '') . ' ' . ($partido['apPatArb'] ?? '') . ' ' . ($partido['apMatArb'] ?? ''));
$sede = trim('Cancha ' . (string) ($partido['numCancha'] ?? '') . ' / ' . (string) ($partido['nombUd'] ?? ''), ' /');
$flashClass = 'alert-info';

if (!empty($flash['type']) && $flash['type'] === 'success') {
    $flashClass = 'alert-success';
}

if (!empty($flash['type']) && $flash['type'] === 'error') {
    $flashClass = 'alert-danger';
}
?>

<section class="page-header animate-fade-up">
    <div>
        <div class="page-eyebrow">Partidos</div>
        <h1 class="page-title"><?php echo h(($partido['equipoA'] ?? 'Equipo A') . ' vs ' . ($partido['equipoB'] ?? 'Equipo B')); ?></h1>
        <p class="page-subtitle">Detalle del partido y seguimiento de cedula arbitral</p>
    </div>

    <div class="table-actions">
        <a class="btn btn-secondary btn-sm" href="/?controller=partidos&action=index">Volver</a>
        <?php if (empty($cedula)): ?>
            <a class="btn btn-primary btn-sm" href="/?controller=partidos&action=capturarCedula&id=<?php echo h($idPartido); ?>">Capturar cedula</a>
        <?php else: ?>
            <a class="btn btn-primary btn-sm" href="/?controller=partidos&action=agregarEvento&idCedula=<?php echo h($cedula['idCedula'] ?? ''); ?>">Agregar evento</a>
        <?php endif; ?>
    </div>
</section>

<?php if (!empty($flash)): ?>
    <div class="alert <?php echo h($flashClass); ?> animate-fade-up">
        <span><?php echo h($flash['message'] ?? ''); ?></span>
    </div>
<?php endif; ?>

<section class="match-hero card animate-fade-up stagger-1">
    <div class="match-team">
        <span class="avatar"><?php echo h(strtoupper(substr((string) ($partido['equipoA'] ?? 'A'), 0, 2))); ?></span>
        <strong><?php echo h($partido['equipoA'] ?? 'Equipo A'); ?></strong>
    </div>

    <div class="match-center">
        <span class="match-score-large mono"><?php echo h($marcador); ?></span>
        <span class="badge <?php echo h($estadoClass); ?>"><?php echo h($estado); ?></span>
    </div>

    <div class="match-team right">
        <span class="avatar"><?php echo h(strtoupper(substr((string) ($partido['equipoB'] ?? 'B'), 0, 2))); ?></span>
        <strong><?php echo h($partido['equipoB'] ?? 'Equipo B'); ?></strong>
    </div>
</section>

<section class="card form-card animate-fade-up stagger-2">
    <div class="card-header">
        <div>
            <h2 class="card-title">Informacion del partido</h2>
            <p class="card-description">Datos programados desde jornadas y calendario</p>
        </div>
        <span class="tag">Partido #<?php echo h($idPartido); ?></span>
    </div>

    <div class="detail-grid">
        <div class="detail-item">
            <span class="detail-label">Jornada</span>
            <strong><?php echo h('Jornada ' . ($partido['numJornada'] ?? '-')); ?></strong>
            <p class="card-description"><?php echo h(trim(($partido['nomCortoCat'] ?? '') . ' / ' . ($partido['perTorneo'] ?? '') . ' / ' . ($partido['cveLiga'] ?? ''), ' /')); ?></p>
        </div>
        <div class="detail-item">
            <span class="detail-label">Fecha / Hora</span>
            <strong class="mono"><?php echo h(trim((string) ($partido['fechaPart'] ?? '') . ' ' . (string) ($partido['horaPart'] ?? '')) ?: '-'); ?></strong>
        </div>
        <div class="detail-item">
            <span class="detail-label">Sede</span>
            <strong><?php echo h($sede !== 'Cancha' ? $sede : '-'); ?></strong>
        </div>
        <div class="detail-item">
            <span class="detail-label">Arbitro</span>
            <strong><?php echo h($arbitro !== '' ? $arbitro : '-'); ?></strong>
        </div>
    </div>
</section>

<?php if (empty($cedula)): ?>
    <section class="empty-state animate-fade-up stagger-3">
        <div class="empty-icon">CD</div>
        <div class="empty-title">Cedula pendiente</div>
        <p class="empty-desc">Captura marcador, arbitro y observaciones para cerrar el resultado del partido.</p>
        <a class="btn btn-primary btn-sm" href="/?controller=partidos&action=capturarCedula&id=<?php echo h($idPartido); ?>">Capturar cedula</a>
    </section>
<?php else: ?>
    <?php require __DIR__ . '/../cedulas/show.php'; ?>
<?php endif; ?>

