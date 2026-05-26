<?php require_once __DIR__ . '/../../helpers/security.php'; ?>

<?php
$idPartido = (int) ($partido['idPartido'] ?? $cedula['idPartido'] ?? 0);
$action = '/?controller=partidos&action=guardarCedula&id=' . urlencode((string) $idPartido);
?>

<section class="page-header animate-fade-up">
    <div>
        <div class="page-eyebrow">Cedulas</div>
        <h1 class="page-title">Capturar cedula</h1>
        <p class="page-subtitle"><?php echo h(($partido['equipoA'] ?? 'Equipo A') . ' vs ' . ($partido['equipoB'] ?? 'Equipo B')); ?></p>
    </div>

    <a class="btn btn-secondary btn-sm" href="/?controller=partidos&action=show&id=<?php echo h($idPartido); ?>">Cancelar</a>
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
    <input type="hidden" name="id" value="<?php echo h($idPartido); ?>">

    <div class="card-header">
        <div>
            <h2 class="card-title">Resultado del partido</h2>
            <p class="card-description">La cedula se guardara mediante el procedimiento almacenado oficial</p>
        </div>
        <span class="badge badge-blue">sp_registrar_cedula_arbitral</span>
    </div>

    <div class="match-mini">
        <span><?php echo h($partido['equipoA'] ?? 'Equipo A'); ?></span>
        <strong class="mono">vs</strong>
        <span><?php echo h($partido['equipoB'] ?? 'Equipo B'); ?></span>
    </div>

    <div class="form-grid">
        <label class="field">
            <span class="label">Arbitro *</span>
            <select class="select" name="numArb" required>
                <option value="">Selecciona un arbitro</option>
                <?php foreach (($arbitros ?? []) as $arbitro): ?>
                    <?php
                    $nombreArbitro = trim(($arbitro['nomArb'] ?? '') . ' ' . ($arbitro['apPatArb'] ?? '') . ' ' . ($arbitro['apMatArb'] ?? ''));
                    $numArb = (string) ($arbitro['numArb'] ?? '');
                    ?>
                    <option value="<?php echo h($numArb); ?>" <?php echo (string) ($cedula['numArb'] ?? '') === $numArb ? 'selected' : ''; ?>>
                        <?php echo h($numArb . ' - ' . $nombreArbitro); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <label class="field">
            <span class="label">Goles <?php echo h($partido['equipoA'] ?? 'Equipo A'); ?> *</span>
            <input class="input" type="number" name="golesEquipoA" min="0" required value="<?php echo h($cedula['golesEquipoA'] ?? 0); ?>">
        </label>

        <label class="field">
            <span class="label">Goles <?php echo h($partido['equipoB'] ?? 'Equipo B'); ?> *</span>
            <input class="input" type="number" name="golesEquipoB" min="0" required value="<?php echo h($cedula['golesEquipoB'] ?? 0); ?>">
        </label>

        <label class="field field-wide">
            <span class="label">Observaciones generales</span>
            <textarea class="input textarea" name="observacionesGenerales" maxlength="800" rows="5"><?php echo h($cedula['observacionesGenerales'] ?? ''); ?></textarea>
            <span class="field-help">No incluyas datos sensibles; este texto queda asociado al partido.</span>
        </label>
    </div>

    <?php if (empty($arbitros)): ?>
        <div class="alert alert-info">
            <span>Necesitas al menos un arbitro activo antes de capturar la cedula.</span>
        </div>
    <?php endif; ?>

    <div class="form-actions">
        <a class="btn btn-secondary" href="/?controller=partidos&action=show&id=<?php echo h($idPartido); ?>">Cancelar</a>
        <button class="btn btn-primary" type="submit" <?php echo empty($arbitros) ? 'disabled' : ''; ?>>Guardar cedula</button>
    </div>
</form>

