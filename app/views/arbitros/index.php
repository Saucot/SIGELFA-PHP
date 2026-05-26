<?php require_once __DIR__ . '/../../helpers/security.php'; ?>

<?php
$totalArbitros = count($arbitros ?? []);
$arbitrosActivos = 0;
$arbitrosConCorreo = 0;

foreach (($arbitros ?? []) as $arbitro) {
    if ((int) ($arbitro['activo'] ?? 1) === 1) {
        $arbitrosActivos++;
    }

    if (!empty($arbitro['eMailArb'])) {
        $arbitrosConCorreo++;
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
        <div class="page-eyebrow">Gestion</div>
        <h1 class="page-title">Arbitros</h1>
        <p class="page-subtitle">Administracion del cuerpo arbitral de la liga</p>
    </div>

    <a class="btn btn-primary btn-sm" href="/?controller=arbitros&action=create">+ Nuevo arbitro</a>
</section>

<?php if (!empty($flash)): ?>
    <div class="alert <?php echo h($flashClass); ?> animate-fade-up">
        <span><?php echo h($flash['message'] ?? ''); ?></span>
    </div>
<?php endif; ?>

<section class="stat-grid">
    <article class="stat-card animate-fade-up stagger-1">
        <div class="stat-label">Total arbitros</div>
        <div class="stat-value"><?php echo h($totalArbitros); ?></div>
        <div class="stat-meta">Registros cargados</div>
    </article>

    <article class="stat-card animate-fade-up stagger-2">
        <div class="stat-label">Activos</div>
        <div class="stat-value"><?php echo h($arbitrosActivos); ?></div>
        <div class="stat-meta">Disponibles para asignacion</div>
    </article>

    <article class="stat-card animate-fade-up stagger-3">
        <div class="stat-label">Con correo</div>
        <div class="stat-value"><?php echo h($arbitrosConCorreo); ?></div>
        <div class="stat-meta">Contacto digital</div>
    </article>

    <article class="stat-card animate-fade-up stagger-4">
        <div class="stat-label">Inactivos</div>
        <div class="stat-value"><?php echo h(max(0, $totalArbitros - $arbitrosActivos)); ?></div>
        <div class="stat-meta">Sin borrado fisico</div>
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
            <h2 class="table-title">Directorio arbitral</h2>
            <p class="card-description">Listado conectado a SQL Server</p>
        </div>

        <div class="table-actions">
            <input class="search-input" type="search" placeholder="Buscar arbitro..." aria-label="Buscar arbitro">
            <a class="btn btn-secondary btn-sm" href="/?controller=arbitros&action=index">Actualizar</a>
        </div>
    </div>

    <?php if (empty($arbitros)): ?>
        <div class="empty-state">
            <div class="empty-icon">AR</div>
            <div class="empty-title">No hay arbitros registrados</div>
            <p class="empty-desc">Crea el primer arbitro para iniciar el directorio.</p>
            <a class="btn btn-primary btn-sm" href="/?controller=arbitros&action=create">Nuevo arbitro</a>
        </div>
    <?php else: ?>
        <div class="table-scroll">
            <table class="data-table referees-table">
                <thead>
                    <tr>
                        <th>Arbitro</th>
                        <th>Contacto</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($arbitros as $arbitro): ?>
                        <?php
                        $nombreCompleto = trim(($arbitro['nomArb'] ?? '') . ' ' . ($arbitro['apPatArb'] ?? '') . ' ' . ($arbitro['apMatArb'] ?? ''));
                        $iniciales = strtoupper(substr((string) ($arbitro['nomArb'] ?? 'A'), 0, 1) . substr((string) ($arbitro['apPatArb'] ?? 'R'), 0, 1));
                        $estadoActivo = (int) ($arbitro['activo'] ?? 1) === 1;
                        $telefono = trim((string) ($arbitro['telArb'] ?? ''));
                        $correo = trim((string) ($arbitro['eMailArb'] ?? ''));
                        $numArb = (string) ($arbitro['numArb'] ?? '');
                        ?>
                        <tr>
                            <td>
                                <div class="team-cell">
                                    <span class="avatar"><?php echo h($iniciales ?: 'AR'); ?></span>
                                    <span>
                                        <span class="team-name"><?php echo h($nombreCompleto); ?></span>
                                        <span class="team-id"><?php echo h($numArb); ?></span>
                                    </span>
                                </div>
                            </td>
                            <td>
                                <div class="contact-cell">
                                    <span class="contact-name mono"><?php echo h($telefono !== '' ? $telefono : '-'); ?></span>
                                    <span class="contact-meta contact-email"><?php echo h($correo !== '' ? $correo : '-'); ?></span>
                                </div>
                            </td>
                            <td>
                                <span class="badge <?php echo $estadoActivo ? 'badge-green' : 'badge-neutral'; ?>">
                                    <?php echo $estadoActivo ? 'Activo' : 'Inactivo'; ?>
                                </span>
                            </td>
                            <td>
                                <div class="table-row-actions">
                                    <a class="btn btn-secondary btn-sm" href="/?controller=arbitros&action=show&id=<?php echo h($numArb); ?>">Ver</a>
                                    <a class="btn btn-ghost btn-sm" href="/?controller=arbitros&action=edit&id=<?php echo h($numArb); ?>">Editar</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="pagination">
            <span class="page-btn active">1</span>
            <span class="card-description">Mostrando <?php echo h($totalArbitros); ?> arbitros</span>
        </div>
    <?php endif; ?>
</section>

