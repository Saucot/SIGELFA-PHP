<?php require_once __DIR__ . '/../../helpers/security.php'; ?>

<?php
$esEdicion = ($modo ?? 'crear') === 'editar';
$titulo = $esEdicion ? 'Editar jugador' : 'Nuevo jugador';
$subtitulo = $esEdicion ? 'Actualiza la ficha del jugador' : 'Registra un jugador y asignalo a un equipo';
$action = $esEdicion
    ? '/?controller=jugadores&action=update&id=' . urlencode((string) ($jugador['numJug'] ?? ''))
    : '/?controller=jugadores&action=store';
?>

<section class="page-header animate-fade-up">
    <div>
        <div class="page-eyebrow">Jugadores</div>
        <h1 class="page-title"><?php echo h($titulo); ?></h1>
        <p class="page-subtitle"><?php echo h($subtitulo); ?></p>
    </div>

    <a class="btn btn-secondary btn-sm" href="/?controller=jugadores&action=index">Cancelar</a>
</section>

<?php if (!empty($errores)): ?>
    <div class="alert alert-danger animate-fade-up">
        <div>
            <?php foreach ($errores as $mensaje): ?>
                <p><?php echo h($mensaje); ?></p>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<form class="card form-card animate-fade-up stagger-1" method="post" action="<?php echo h($action); ?>">
    <?php if ($esEdicion): ?>
        <input type="hidden" name="id" value="<?php echo h($jugador['numJug'] ?? ''); ?>">
    <?php endif; ?>

    <div class="card-header">
        <div>
            <h2 class="card-title">Ficha del jugador</h2>
            <p class="card-description">
                <?php if ($esEdicion): ?>
                    Numero de jugador: <span class="mono"><?php echo h($jugador['numJug'] ?? ''); ?></span>
                <?php else: ?>
                    El numero de jugador se generara automaticamente: <span class="mono"><?php echo h($siguienteNumJug ?? 'J---'); ?></span>
                <?php endif; ?>
            </p>
        </div>
        <span class="badge badge-green">CRUD Jugadores</span>
    </div>

    <div class="form-grid">
        <label class="field">
            <span class="label">Nombre *</span>
            <input class="input" type="text" name="nomJug" maxlength="40" required value="<?php echo h($jugador['nomJug'] ?? ''); ?>">
        </label>

        <label class="field">
            <span class="label">Apellido paterno</span>
            <input class="input" type="text" name="apPatJug" maxlength="40" value="<?php echo h($jugador['apPatJug'] ?? ''); ?>">
        </label>

        <label class="field">
            <span class="label">Apellido materno</span>
            <input class="input" type="text" name="apMatJug" maxlength="40" value="<?php echo h($jugador['apMatJug'] ?? ''); ?>">
        </label>

        <label class="field">
            <span class="label">Fecha de nacimiento</span>
            <input class="input" type="date" name="fechaNacJug" value="<?php echo h($jugador['fechaNacJug'] ?? ''); ?>">
        </label>

        <label class="field">
            <span class="label">Edad</span>
            <input class="input" type="number" name="edadJug" min="0" value="<?php echo h($jugador['edadJug'] ?? ''); ?>">
        </label>

        <label class="field">
            <span class="label">Numero de camiseta</span>
            <input class="input" type="number" name="numeroCamiseta" min="1" max="999" value="<?php echo h($jugador['numeroCamiseta'] ?? ''); ?>">
        </label>

        <label class="field">
            <span class="label">Telefono</span>
            <input class="input" type="text" name="telJug" maxlength="15" value="<?php echo h($jugador['telJug'] ?? ''); ?>">
        </label>

        <label class="field">
            <span class="label">Correo</span>
            <input class="input" type="email" name="eMailJug" maxlength="80" value="<?php echo h($jugador['eMailJug'] ?? ''); ?>">
        </label>

        <label class="field">
            <span class="label">Equipo *</span>
            <select class="select" name="cveEquipo" required>
                <option value="">Selecciona un equipo</option>
                <?php foreach (($equipos ?? []) as $equipo): ?>
                    <?php $equipoTexto = ($equipo['nombEquipo'] ?? '') . ' - ' . ($equipo['nomCortoCat'] ?? '') . ' / ' . ($equipo['perTorneo'] ?? ''); ?>
                    <option value="<?php echo h($equipo['cveEquipo'] ?? ''); ?>" <?php echo (int) ($jugador['cveEquipo'] ?? 0) === (int) ($equipo['cveEquipo'] ?? 0) ? 'selected' : ''; ?>>
                        <?php echo h($equipoTexto); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <label class="field">
            <span class="label">Posicion *</span>
            <select class="select" name="idPosicion" required>
                <option value="">Selecciona una posicion</option>
                <?php foreach (($posiciones ?? []) as $posicion): ?>
                    <?php $posicionTexto = ($posicion['abreviatura'] ?? '') . ' - ' . ($posicion['nombrePosicion'] ?? ''); ?>
                    <option value="<?php echo h($posicion['idPosicion'] ?? ''); ?>" <?php echo (int) ($jugador['idPosicion'] ?? 0) === (int) ($posicion['idPosicion'] ?? 0) ? 'selected' : ''; ?>>
                        <?php echo h($posicionTexto); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <label class="field">
            <span class="label">Estado</span>
            <select class="select" name="activo">
                <option value="1" <?php echo (int) ($jugador['activo'] ?? 1) === 1 ? 'selected' : ''; ?>>Activo</option>
                <option value="0" <?php echo (int) ($jugador['activo'] ?? 1) === 0 ? 'selected' : ''; ?>>Inactivo</option>
            </select>
            <span class="field-help">El estado se cambia aqui, sin borrar fisicamente el registro.</span>
        </label>
    </div>

    <?php if (empty($equipos) || empty($posiciones)): ?>
        <div class="alert alert-info">
            <span>Necesitas equipos y posiciones disponibles antes de registrar jugadores.</span>
        </div>
    <?php endif; ?>

    <div class="form-actions">
        <a class="btn btn-secondary" href="/?controller=jugadores&action=index">Cancelar</a>
        <button class="btn btn-primary" type="submit" <?php echo empty($equipos) || empty($posiciones) ? 'disabled' : ''; ?>>
            <?php echo $esEdicion ? 'Guardar cambios' : 'Guardar jugador'; ?>
        </button>
    </div>
</form>

