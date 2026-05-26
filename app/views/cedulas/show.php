<?php require_once __DIR__ . '/../../helpers/security.php'; ?>

<?php
$arbitroCedula = trim(($cedula['nomArb'] ?? '') . ' ' . ($cedula['apPatArb'] ?? '') . ' ' . ($cedula['apMatArb'] ?? ''));
$resultadoCedula = (string) ($cedula['golesEquipoA'] ?? '0') . ' - ' . (string) ($cedula['golesEquipoB'] ?? '0');
?>

<section class="card form-card animate-fade-up stagger-3">
    <div class="card-header">
        <div>
            <h2 class="card-title">Cedula arbitral</h2>
            <p class="card-description">Resultado y eventos capturados por arbitraje</p>
        </div>
        <span class="badge badge-green"><?php echo h($cedula['estadoCedula'] ?? 'CERRADA'); ?></span>
    </div>

    <div class="detail-grid">
        <div class="detail-item">
            <span class="detail-label">Cedula</span>
            <strong class="mono">#<?php echo h($cedula['idCedula'] ?? ''); ?></strong>
        </div>
        <div class="detail-item">
            <span class="detail-label">Resultado</span>
            <strong class="mono"><?php echo h($resultadoCedula); ?></strong>
        </div>
        <div class="detail-item">
            <span class="detail-label">Arbitro</span>
            <strong><?php echo h($arbitroCedula !== '' ? $arbitroCedula : '-'); ?></strong>
            <p class="card-description"><?php echo h($cedula['numArb'] ?? ''); ?></p>
        </div>
        <div class="detail-item">
            <span class="detail-label">Fecha de captura</span>
            <strong class="mono"><?php echo h($cedula['fechaCaptura'] ?? '-'); ?></strong>
        </div>
    </div>

    <?php if (!empty($cedula['observacionesGenerales'])): ?>
        <div class="detail-item full-span">
            <span class="detail-label">Observaciones generales</span>
            <p class="card-description"><?php echo h($cedula['observacionesGenerales']); ?></p>
        </div>
    <?php endif; ?>
</section>

<section class="table-wrap animate-fade-up stagger-4">
    <div class="table-header">
        <div>
            <h2 class="table-title">Eventos registrados</h2>
            <p class="card-description">Goles, tarjetas y observaciones de la cedula</p>
        </div>

        <a class="btn btn-primary btn-sm" href="/?controller=partidos&action=agregarEvento&idCedula=<?php echo h($cedula['idCedula'] ?? ''); ?>">+ Evento</a>
    </div>

    <?php if (empty($eventos)): ?>
        <div class="empty-state">
            <div class="empty-icon">EV</div>
            <div class="empty-title">Sin eventos capturados</div>
            <p class="empty-desc">Agrega goles, tarjetas u observaciones para completar la cedula.</p>
        </div>
    <?php else: ?>
        <div class="table-scroll">
            <table class="data-table events-table">
                <thead>
                    <tr>
                        <th>Evento</th>
                        <th>Jugador</th>
                        <th>Equipo</th>
                        <th>Minuto</th>
                        <th>Observacion</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($eventos as $evento): ?>
                        <?php
                        $jugador = trim(($evento['nomJug'] ?? '') . ' ' . ($evento['apPatJug'] ?? '') . ' ' . ($evento['apMatJug'] ?? ''));
                        $abreviatura = (string) ($evento['abreviatura'] ?? '');
                        $badgeClass = match ($abreviatura) {
                            'GOL', 'AUTOGOL' => 'badge-green',
                            'AMARILLA' => 'badge-amber',
                            'ROJA' => 'badge-red',
                            default => 'badge-blue',
                        };
                        ?>
                        <tr>
                            <td>
                                <span class="badge <?php echo h($badgeClass); ?>"><?php echo h($evento['nombreEvento'] ?? 'Evento'); ?></span>
                                <span class="team-id">#<?php echo h($evento['idEvento'] ?? ''); ?></span>
                            </td>
                            <td>
                                <span class="contact-name"><?php echo h($jugador !== '' ? $jugador : '-'); ?></span>
                                <span class="team-id mono"><?php echo h($evento['numJug'] ?? ''); ?></span>
                            </td>
                            <td><?php echo h($evento['nombEquipo'] ?? '-'); ?></td>
                            <td><span class="mono"><?php echo h(($evento['minuto'] ?? '') !== '' ? $evento['minuto'] : '-'); ?></span></td>
                            <td><span class="contact-meta"><?php echo h($evento['observacion'] ?? '-'); ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

