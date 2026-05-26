<?php require_once __DIR__ . '/../../helpers/security.php'; ?>

<?php
$esEdicion = ($modo ?? 'crear') === 'editar';
$titulo = $esEdicion ? 'Editar arbitro' : 'Nuevo arbitro';
$subtitulo = $esEdicion ? 'Actualiza la ficha del arbitro' : 'Registra un integrante del cuerpo arbitral';
$action = $esEdicion
    ? '/?controller=arbitros&action=update&id=' . urlencode((string) ($arbitro['numArb'] ?? ''))
    : '/?controller=arbitros&action=store';
?>

<section class="page-header animate-fade-up">
    <div>
        <div class="page-eyebrow">Arbitros</div>
        <h1 class="page-title"><?php echo h($titulo); ?></h1>
        <p class="page-subtitle"><?php echo h($subtitulo); ?></p>
    </div>

    <a class="btn btn-secondary btn-sm" href="/?controller=arbitros&action=index">Cancelar</a>
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
        <input type="hidden" name="id" value="<?php echo h($arbitro['numArb'] ?? ''); ?>">
    <?php endif; ?>

    <div class="card-header">
        <div>
            <h2 class="card-title">Ficha arbitral</h2>
            <p class="card-description">
                <?php if ($esEdicion): ?>
                    Numero de arbitro: <span class="mono"><?php echo h($arbitro['numArb'] ?? ''); ?></span>
                <?php else: ?>
                    El numero de arbitro se generara automaticamente: <span class="mono"><?php echo h($siguienteNumArb ?? 'A---'); ?></span>
                <?php endif; ?>
            </p>
        </div>
        <span class="badge badge-green">CRUD Arbitros</span>
    </div>

    <div class="form-grid">
        <label class="field">
            <span class="label">Nombre *</span>
            <input class="input" type="text" name="nomArb" maxlength="40" required value="<?php echo h($arbitro['nomArb'] ?? ''); ?>">
        </label>

        <label class="field">
            <span class="label">Apellido paterno</span>
            <input class="input" type="text" name="apPatArb" maxlength="40" value="<?php echo h($arbitro['apPatArb'] ?? ''); ?>">
        </label>

        <label class="field">
            <span class="label">Apellido materno</span>
            <input class="input" type="text" name="apMatArb" maxlength="40" value="<?php echo h($arbitro['apMatArb'] ?? ''); ?>">
        </label>

        <label class="field">
            <span class="label">Telefono</span>
            <input class="input" type="text" name="telArb" maxlength="15" value="<?php echo h($arbitro['telArb'] ?? ''); ?>">
        </label>

        <label class="field">
            <span class="label">Correo</span>
            <input class="input" type="email" name="eMailArb" maxlength="80" value="<?php echo h($arbitro['eMailArb'] ?? ''); ?>">
        </label>

        <label class="field">
            <span class="label">Estado</span>
            <select class="select" name="activo">
                <option value="1" <?php echo (int) ($arbitro['activo'] ?? 1) === 1 ? 'selected' : ''; ?>>Activo</option>
                <option value="0" <?php echo (int) ($arbitro['activo'] ?? 1) === 0 ? 'selected' : ''; ?>>Inactivo</option>
            </select>
            <span class="field-help">El estado se cambia aqui, sin borrar fisicamente el registro.</span>
        </label>
    </div>

    <div class="form-actions">
        <a class="btn btn-secondary" href="/?controller=arbitros&action=index">Cancelar</a>
        <button class="btn btn-primary" type="submit">
            <?php echo $esEdicion ? 'Guardar cambios' : 'Guardar arbitro'; ?>
        </button>
    </div>
</form>

