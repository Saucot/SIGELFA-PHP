<?php require_once __DIR__ . '/../../helpers/security.php'; ?>

<?php
$totalEquipos = count($equipos ?? []);
$equiposActivos = 0;
$equiposConRepresentante = 0;

foreach (($equipos ?? []) as $equipo) {
    $activo = $equipo['activo'] ?? 1;

    if ((int) $activo === 1) {
        $equiposActivos++;
    }

    if (!empty($equipo['nombRepEq'])) {
        $equiposConRepresentante++;
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
        <h1 class="page-title">Equipos</h1>
        <p class="page-subtitle">Gestion de equipos registrados en la liga</p>
    </div>

    <a class="btn btn-primary btn-sm" href="/?controller=equipos&action=create">
        + Nuevo equipo
    </a>
</section>

<?php if (!empty($flash)): ?>
    <div class="alert <?php echo h($flashClass); ?> animate-fade-up">
        <span><?php echo h($flash['message'] ?? ''); ?></span>
    </div>
<?php endif; ?>

<section class="stat-grid">
    <article class="stat-card animate-fade-up stagger-1">
        <div class="stat-label">Total equipos</div>
        <div class="stat-value"><?php echo h($totalEquipos); ?></div>
        <div class="stat-meta">Registros cargados</div>
    </article>

    <article class="stat-card animate-fade-up stagger-2">
        <div class="stat-label">Activos</div>
        <div class="stat-value"><?php echo h($equiposActivos); ?></div>
        <div class="stat-meta">Disponibles para competencia</div>
    </article>

    <article class="stat-card animate-fade-up stagger-3">
        <div class="stat-label">Representantes</div>
        <div class="stat-value"><?php echo h($equiposConRepresentante); ?></div>
        <div class="stat-meta">Con contacto asignado</div>
    </article>

    <article class="stat-card animate-fade-up stagger-4">
        <div class="stat-label">Inactivos</div>
        <div class="stat-value"><?php echo h(max(0, $totalEquipos - $equiposActivos)); ?></div>
        <div class="stat-meta">Desactivados sin borrar</div>
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
            <h2 class="table-title">Directorio de equipos</h2>
            <p class="card-description">Listado conectado a SQL Server</p>
        </div>

        <div class="table-actions">
            <input class="search-input" type="search" placeholder="Buscar equipo..." aria-label="Buscar equipo">
            <a class="btn btn-secondary btn-sm" href="/?controller=equipos&action=index">Actualizar</a>
        </div>
    </div>

    <?php if (empty($equipos)): ?>
        <div class="empty-state">
            <div class="empty-icon">EQ</div>
            <div class="empty-title">No hay equipos registrados</div>
            <p class="empty-desc">Crea el primer equipo para iniciar el directorio de la liga.</p>
            <a class="btn btn-primary btn-sm" href="/?controller=equipos&action=create">Nuevo equipo</a>
        </div>
    <?php else: ?>
        <div class="table-scroll">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Equipo</th>
                        <th>Contacto</th>
                        <th>Categoria/Torneo</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($equipos as $equipo): ?>
                        <?php
                        $nombreEquipo = $equipo['nombEquipo'] ?? '';
                        $iniciales = strtoupper(substr((string) $nombreEquipo, 0, 2));
                        $estadoActivo = (int) ($equipo['activo'] ?? 1) === 1;
                        $categoriaTorneo = trim(($equipo['nomCortoCat'] ?? '') . ' / ' . ($equipo['perTorneo'] ?? '') . ' / ' . ($equipo['cveLiga'] ?? ''), ' /');
                        $id = (int) ($equipo['cveEquipo'] ?? 0);
                        $representante = trim((string) ($equipo['nombRepEq'] ?? ''));
                        $telefono = trim((string) ($equipo['numTelRepEq'] ?? ''));
                        $correo = trim((string) ($equipo['eMailRepEq'] ?? ''));
                        ?>
                        <tr>
                            <td>
                                <div class="team-cell">
                                    <span class="avatar"><?php echo h($iniciales ?: 'EQ'); ?></span>
                                    <span>
                                        <span class="team-name"><?php echo h($nombreEquipo); ?></span>
                                        <span class="team-id">#<?php echo h($id); ?></span>
                                    </span>
                                </div>
                            </td>
                            <td>
                                <div class="contact-cell">
                                    <span class="contact-name"><?php echo h($representante !== '' ? $representante : 'Sin representante'); ?></span>
                                    <span class="contact-meta">
                                        <span><?php echo h($telefono !== '' ? $telefono : '-'); ?></span>
                                        <span class="contact-separator">/</span>
                                        <span class="contact-email"><?php echo h($correo !== '' ? $correo : '-'); ?></span>
                                    </span>
                                </div>
                            </td>
                            <td>
                                <span class="tag"><?php echo h($categoriaTorneo !== '' ? $categoriaTorneo : 'Sin categoria'); ?></span>
                            </td>
                            <td>
                                <span class="badge <?php echo $estadoActivo ? 'badge-green' : 'badge-neutral'; ?>">
                                    <?php echo $estadoActivo ? 'Activo' : 'Inactivo'; ?>
                                </span>
                            </td>
                            <td>
                                <div class="table-row-actions">
                                    <a class="btn btn-secondary btn-sm" href="/?controller=equipos&action=show&id=<?php echo h($id); ?>">Ver</a>
                                    <a class="btn btn-ghost btn-sm" href="/?controller=equipos&action=edit&id=<?php echo h($id); ?>">Editar</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="pagination">
            <span class="page-btn active">1</span>
            <span class="card-description">Mostrando <?php echo h($totalEquipos); ?> equipos</span>
        </div>
    <?php endif; ?>
</section>
