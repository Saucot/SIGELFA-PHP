<?php require_once __DIR__ . '/../../helpers/security.php'; ?>

<?php
$esEdicion = ($modo ?? 'crear') === 'editar';
$titulo = $esEdicion ? 'Editar equipo' : 'Nuevo equipo';
$subtitulo = $esEdicion ? 'Actualiza la informacion administrativa del equipo' : 'Registra un equipo dentro de una categoria existente';
$action = $esEdicion
    ? '/?controller=equipos&action=update&id=' . urlencode((string) ($equipo['cveEquipo'] ?? ''))
    : '/?controller=equipos&action=store';
$categoriaSeleccionada = ($equipo['nomCortoCat'] ?? '') . '|' . ($equipo['perTorneo'] ?? '') . '|' . ($equipo['cveLiga'] ?? '');
?>

<section class="page-header animate-fade-up">
    <div>
        <div class="page-eyebrow">Equipos</div>
        <h1 class="page-title"><?php echo h($titulo); ?></h1>
        <p class="page-subtitle"><?php echo h($subtitulo); ?></p>
    </div>

    <a class="btn btn-secondary btn-sm" href="/?controller=equipos&action=index">Cancelar</a>
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
        <input type="hidden" name="id" value="<?php echo h($equipo['cveEquipo'] ?? ''); ?>">
    <?php endif; ?>

    <div class="card-header">
        <div>
            <h2 class="card-title">Datos del equipo</h2>
            <p class="card-description">Los campos marcados como requeridos deben coincidir con la estructura de SQL Server.</p>
        </div>
        <span class="badge badge-green">CRUD Equipos</span>
    </div>

    <div class="form-grid">
        <label class="field field-full">
            <span class="label">Nombre del equipo *</span>
            <input class="input" type="text" name="nombEquipo" maxlength="50" required value="<?php echo h($equipo['nombEquipo'] ?? ''); ?>">
        </label>

        <label class="field">
            <span class="label">Representante</span>
            <input class="input" type="text" name="nombRepEq" maxlength="60" value="<?php echo h($equipo['nombRepEq'] ?? ''); ?>">
        </label>

        <label class="field">
            <span class="label">Telefono</span>
            <input class="input" type="text" name="numTelRepEq" maxlength="15" value="<?php echo h($equipo['numTelRepEq'] ?? ''); ?>">
        </label>

        <label class="field">
            <span class="label">Correo</span>
            <input class="input" type="email" name="eMailRepEq" maxlength="80" value="<?php echo h($equipo['eMailRepEq'] ?? ''); ?>">
        </label>

        <label class="field">
            <span class="label">Categoria / Torneo / Liga *</span>
            <select class="select" name="categoriaClave" required>
                <option value="">Selecciona una categoria</option>
                <?php foreach (($categorias ?? []) as $categoria): ?>
                    <?php
                    $valorCategoria = ($categoria['nomCortoCat'] ?? '') . '|' . ($categoria['perTorneo'] ?? '') . '|' . ($categoria['cveLiga'] ?? '');
                    $textoCategoria = ($categoria['nomCortoCat'] ?? '') . ' - ' . ($categoria['perTorneo'] ?? '') . ' - ' . ($categoria['cveLiga'] ?? '');
                    ?>
                    <option value="<?php echo h($valorCategoria); ?>" <?php echo $valorCategoria === $categoriaSeleccionada ? 'selected' : ''; ?>>
                        <?php echo h($textoCategoria); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <span class="field-help">Se cargan categorias existentes desde SQL Server.</span>
        </label>

        <label class="field">
            <span class="label">Estado</span>
            <select class="select" name="activo">
                <option value="1" <?php echo (int) ($equipo['activo'] ?? 1) === 1 ? 'selected' : ''; ?>>Activo</option>
                <option value="0" <?php echo (int) ($equipo['activo'] ?? 1) === 0 ? 'selected' : ''; ?>>Inactivo</option>
            </select>
            <span class="field-help">En edicion puedes activar o inactivar el equipo sin borrarlo.</span>
        </label>
    </div>

    <?php if (empty($categorias)): ?>
        <div class="alert alert-info">
            <span>No hay categorias disponibles. Ejecuta los scripts de base de datos antes de registrar equipos.</span>
        </div>
    <?php endif; ?>

    <div class="form-actions">
        <a class="btn btn-secondary" href="/?controller=equipos&action=index">Cancelar</a>
        <button class="btn btn-primary" type="submit" <?php echo empty($categorias) ? 'disabled' : ''; ?>>
            <?php echo $esEdicion ? 'Guardar cambios' : 'Guardar equipo'; ?>
        </button>
    </div>
</form>
