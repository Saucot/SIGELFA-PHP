<?php require_once __DIR__ . '/../../helpers/security.php'; ?>

<?php
$totalPartidos = count($partidos ?? []);
$jugados = 0;
$conCedula = 0;

foreach (($partidos ?? []) as $partido) {
    if (($partido['estadoPartido'] ?? '') === 'JUGADO') {
        $jugados++;
    }

    if (!empty($partido['idCedula'])) {
        $conCedula++;
    }
}

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
        <div class="page-eyebrow">Competencia</div>
        <h1 class="page-title">Partidos</h1>
        <p class="page-subtitle">Seguimiento de partidos programados, jugados y cedulas arbitrales</p>
    </div>

    <a class="btn btn-secondary btn-sm" href="/?controller=partidos&action=index">Actualizar</a>
</section>

<?php if (!empty($flash)): ?>
    <div class="alert <?php echo h($flashClass); ?> animate-fade-up">
        <span><?php echo h($flash['message'] ?? ''); ?></span>
    </div>
<?php endif; ?>

<section class="stat-grid">
    <article class="stat-card animate-fade-up stagger-1">
        <div class="stat-label">Total partidos</div>
        <div class="stat-value"><?php echo h($totalPartidos); ?></div>
        <div class="stat-meta">Registros del calendario</div>
    </article>

    <article class="stat-card animate-fade-up stagger-2">
        <div class="stat-label">Jugados</div>
        <div class="stat-value"><?php echo h($jugados); ?></div>
        <div class="stat-meta">Con marcador registrado</div>
    </article>

    <article class="stat-card animate-fade-up stagger-3">
        <div class="stat-label">Cedulas</div>
        <div class="stat-value"><?php echo h($conCedula); ?></div>
        <div class="stat-meta">Capturadas en el sistema</div>
    </article>

    <article class="stat-card animate-fade-up stagger-4">
        <div class="stat-label">Pendientes</div>
        <div class="stat-value"><?php echo h(max(0, $totalPartidos - $conCedula)); ?></div>
        <div class="stat-meta">Sin cedula arbitral</div>
    </article>
</section>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger animate-fade-up">
        <span><?php echo h($error); ?></span>
    </div>
<?php endif; ?>

<section class="table-wrap animate-fade-up stagger-2">
    <div class="table-header">
        <div>
            <h2 class="table-title">Calendario de partidos</h2>
            <p class="card-description">Listado conectado a SQL Server</p>
        </div>

        <div class="table-actions">
            <input class="search-input" type="search" placeholder="Buscar partido..." aria-label="Buscar partido">
        </div>
    </div>

    <?php if (empty($partidos)): ?>
        <div class="empty-state">
            <div class="empty-icon">PT</div>
            <div class="empty-title">No hay partidos registrados</div>
            <p class="empty-desc">Cuando existan jornadas programadas apareceran aqui.</p>
        </div>
    <?php else: ?>
        <div class="table-scroll">
            <table class="data-table matches-table">
                <thead>
                    <tr>
                        <th>Partido</th>
                        <th>Jornada</th>
                        <th>Fecha/Hora</th>
                        <th>Arbitro</th>
                        <th>Estado</th>
                        <th>Cedula</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($partidos as $partido): ?>
                        <?php
                        $idPartido = (int) ($partido['idPartido'] ?? 0);
                        $marcador = ($partido['golesEquipoA'] ?? null) !== null && ($partido['golesEquipoB'] ?? null) !== null
                            ? (string) $partido['golesEquipoA'] . ' - ' . (string) $partido['golesEquipoB']
                            : 'vs';
                        $estado = (string) ($partido['estadoPartido'] ?? 'PROGRAMADO');
                        $estadoClass = $estado === 'JUGADO' ? 'badge-green' : ($estado === 'CANCELADO' ? 'badge-red' : 'badge-blue');
                        $tieneCedula = !empty($partido['idCedula']);
                        $arbitro = trim(($partido['nomArb'] ?? '') . ' ' . ($partido['apPatArb'] ?? '') . ' ' . ($partido['apMatArb'] ?? ''));
                        $fechaHora = trim((string) ($partido['fechaPart'] ?? '') . ' ' . (string) ($partido['horaPart'] ?? ''));
                        ?>
                        <tr>
                            <td>
                                <div class="match-cell">
                                    <span class="team-name"><?php echo h($partido['equipoA'] ?? 'Equipo A'); ?></span>
                                    <span class="match-score mono"><?php echo h($marcador); ?></span>
                                    <span class="team-name"><?php echo h($partido['equipoB'] ?? 'Equipo B'); ?></span>
                                    <span class="team-id">#<?php echo h($idPartido); ?></span>
                                </div>
                            </td>
                            <td>
                                <span class="tag"><?php echo h('J' . ($partido['numJornada'] ?? '-')); ?></span>
                                <span class="contact-meta"><?php echo h(trim(($partido['nomCortoCat'] ?? '') . ' / ' . ($partido['perTorneo'] ?? ''), ' /')); ?></span>
                            </td>
                            <td>
                                <span class="contact-name mono"><?php echo h($fechaHora !== '' ? $fechaHora : '-'); ?></span>
                            </td>
                            <td>
                                <span class="contact-name"><?php echo h($arbitro !== '' ? $arbitro : '-'); ?></span>
                            </td>
                            <td>
                                <span class="badge <?php echo h($estadoClass); ?>"><?php echo h($estado); ?></span>
                            </td>
                            <td>
                                <span class="badge <?php echo $tieneCedula ? 'badge-green' : 'badge-amber'; ?>">
                                    <?php echo $tieneCedula ? 'Capturada' : 'Pendiente'; ?>
                                </span>
                            </td>
                            <td>
                                <div class="table-row-actions">
                                    <a class="btn btn-secondary btn-sm" href="/?controller=partidos&action=show&id=<?php echo h($idPartido); ?>">Ver</a>
                                    <?php if ($tieneCedula): ?>
                                        <a class="btn btn-ghost btn-sm" href="/?controller=partidos&action=show&id=<?php echo h($idPartido); ?>">Cedula</a>
                                    <?php else: ?>
                                        <a class="btn btn-primary btn-sm" href="/?controller=partidos&action=capturarCedula&id=<?php echo h($idPartido); ?>">Capturar</a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="pagination">
            <span class="page-btn active">1</span>
            <span class="card-description">Mostrando <?php echo h($totalPartidos); ?> partidos</span>
        </div>
    <?php endif; ?>
</section>

