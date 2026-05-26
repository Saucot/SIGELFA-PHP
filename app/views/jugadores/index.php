<?php require_once __DIR__ . '/../../helpers/security.php'; ?>

<?php
$totalJugadores = count($jugadores ?? []);
$jugadoresActivos = 0;
$jugadoresConCorreo = 0;

foreach (($jugadores ?? []) as $jugador) {
    if ((int) ($jugador['activo'] ?? 1) === 1) {
        $jugadoresActivos++;
    }

    if (!empty($jugador['eMailJug'])) {
        $jugadoresConCorreo++;
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
        <h1 class="page-title">Jugadores</h1>
        <p class="page-subtitle">Administracion de jugadores registrados por equipo y posicion</p>
    </div>

    <a class="btn btn-primary btn-sm" href="/?controller=jugadores&action=create">+ Nuevo jugador</a>
</section>

<?php if (!empty($flash)): ?>
    <div class="alert <?php echo h($flashClass); ?> animate-fade-up">
        <span><?php echo h($flash['message'] ?? ''); ?></span>
    </div>
<?php endif; ?>

<section class="stat-grid">
    <article class="stat-card animate-fade-up stagger-1">
        <div class="stat-label">Total jugadores</div>
        <div class="stat-value"><?php echo h($totalJugadores); ?></div>
        <div class="stat-meta">Registros cargados</div>
    </article>

    <article class="stat-card animate-fade-up stagger-2">
        <div class="stat-label">Activos</div>
        <div class="stat-value"><?php echo h($jugadoresActivos); ?></div>
        <div class="stat-meta">Disponibles en plantilla</div>
    </article>

    <article class="stat-card animate-fade-up stagger-3">
        <div class="stat-label">Con correo</div>
        <div class="stat-value"><?php echo h($jugadoresConCorreo); ?></div>
        <div class="stat-meta">Contacto digital</div>
    </article>

    <article class="stat-card animate-fade-up stagger-4">
        <div class="stat-label">Inactivos</div>
        <div class="stat-value"><?php echo h(max(0, $totalJugadores - $jugadoresActivos)); ?></div>
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
            <h2 class="table-title">Plantilla de jugadores</h2>
            <p class="card-description">Listado conectado a SQL Server</p>
        </div>

        <div class="table-actions">
            <input class="search-input" type="search" placeholder="Buscar jugador..." aria-label="Buscar jugador">
            <a class="btn btn-secondary btn-sm" href="/?controller=jugadores&action=index">Actualizar</a>
        </div>
    </div>

    <?php if (empty($jugadores)): ?>
        <div class="empty-state">
            <div class="empty-icon">JG</div>
            <div class="empty-title">No hay jugadores registrados</div>
            <p class="empty-desc">Crea el primer jugador para iniciar la plantilla.</p>
            <a class="btn btn-primary btn-sm" href="/?controller=jugadores&action=create">Nuevo jugador</a>
        </div>
    <?php else: ?>
        <div class="table-scroll">
            <table class="data-table players-table">
                <thead>
                    <tr>
                        <th>Jugador</th>
                        <th>Equipo</th>
                        <th>Posicion</th>
                        <th>Contacto</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($jugadores as $jugador): ?>
                        <?php
                        $nombreCompleto = trim(($jugador['nomJug'] ?? '') . ' ' . ($jugador['apPatJug'] ?? '') . ' ' . ($jugador['apMatJug'] ?? ''));
                        $iniciales = strtoupper(substr((string) ($jugador['nomJug'] ?? 'J'), 0, 1) . substr((string) ($jugador['apPatJug'] ?? 'G'), 0, 1));
                        $estadoActivo = (int) ($jugador['activo'] ?? 1) === 1;
                        $categoriaTorneo = trim(($jugador['nomCortoCat'] ?? '') . ' / ' . ($jugador['perTorneo'] ?? ''), ' /');
                        $telefono = trim((string) ($jugador['telJug'] ?? ''));
                        $correo = trim((string) ($jugador['eMailJug'] ?? ''));
                        $numJug = (string) ($jugador['numJug'] ?? '');
                        ?>
                        <tr>
                            <td>
                                <div class="team-cell">
                                    <span class="avatar"><?php echo h($iniciales ?: 'JG'); ?></span>
                                    <span>
                                        <span class="team-name"><?php echo h($nombreCompleto); ?></span>
                                        <span class="team-id"><?php echo h($numJug); ?><?php echo !empty($jugador['numeroCamiseta']) ? ' / #' . h($jugador['numeroCamiseta']) : ''; ?></span>
                                    </span>
                                </div>
                            </td>
                            <td>
                                <div class="contact-cell">
                                    <span class="contact-name"><?php echo h($jugador['nombEquipo'] ?? 'Sin equipo'); ?></span>
                                    <span class="contact-meta"><?php echo h($categoriaTorneo !== '' ? $categoriaTorneo : '-'); ?></span>
                                </div>
                            </td>
                            <td>
                                <span class="tag"><?php echo h(($jugador['abreviatura'] ?? '') . ' - ' . ($jugador['nombrePosicion'] ?? '')); ?></span>
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
                                    <a class="btn btn-secondary btn-sm" href="/?controller=jugadores&action=show&id=<?php echo h($numJug); ?>">Ver</a>
                                    <a class="btn btn-ghost btn-sm" href="/?controller=jugadores&action=edit&id=<?php echo h($numJug); ?>">Editar</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="pagination">
            <span class="page-btn active">1</span>
            <span class="card-description">Mostrando <?php echo h($totalJugadores); ?> jugadores</span>
        </div>
    <?php endif; ?>
</section>

