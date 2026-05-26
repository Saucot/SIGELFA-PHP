<?php require_once __DIR__ . '/../../helpers/security.php'; ?>

<?php
$idCedula = (int) ($cedula['idCedula'] ?? $evento['idCedula'] ?? 0);
$idPartido = (int) ($cedula['idPartido'] ?? $partido['idPartido'] ?? 0);
$action = '/?controller=partidos&action=guardarEvento&idCedula=' . urlencode((string) $idCedula);
$equipoAId = (int) ($partido['cveEquipoA'] ?? 0);
$equipoBId = (int) ($partido['cveEquipoB'] ?? 0);
?>

<section class="page-header animate-fade-up">
    <div>
        <div class="page-eyebrow">Cedulas</div>
        <h1 class="page-title">Agregar evento</h1>
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
    <input type="hidden" name="idCedula" value="<?php echo h($idCedula); ?>">

    <div class="card-header">
        <div>
            <h2 class="card-title">Evento de cedula</h2>
            <p class="card-description">Se registrara mediante el procedimiento almacenado oficial</p>
        </div>
        <span class="badge badge-blue">sp_agregar_evento_cedula</span>
    </div>

    <div class="form-grid">
        <label class="field">
            <span class="label">Tipo de evento *</span>
            <select class="select" name="abreviaturaEvento" required>
                <option value="">Selecciona un evento</option>
                <?php foreach (($tiposEvento ?? []) as $tipo): ?>
                    <?php $abreviatura = (string) ($tipo['abreviatura'] ?? ''); ?>
                    <option value="<?php echo h($abreviatura); ?>" <?php echo (string) ($evento['abreviaturaEvento'] ?? '') === $abreviatura ? 'selected' : ''; ?>>
                        <?php echo h(($tipo['nombreEvento'] ?? '') . ' - ' . $abreviatura); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <span class="field-help">Para OBS, el jugador puede quedar vacio.</span>
        </label>

        <label class="field">
            <span class="label">Equipo *</span>
            <select class="select" name="cveEquipo" required>
                <option value="">Selecciona equipo</option>
                <option value="<?php echo h($equipoAId); ?>" <?php echo (int) ($evento['cveEquipo'] ?? 0) === $equipoAId ? 'selected' : ''; ?>>
                    <?php echo h($partido['equipoA'] ?? 'Equipo A'); ?>
                </option>
                <option value="<?php echo h($equipoBId); ?>" <?php echo (int) ($evento['cveEquipo'] ?? 0) === $equipoBId ? 'selected' : ''; ?>>
                    <?php echo h($partido['equipoB'] ?? 'Equipo B'); ?>
                </option>
            </select>
        </label>

        <label class="field">
            <span class="label">Jugador</span>
            <select class="select" name="numJug">
                <option value="">Sin jugador / observacion</option>
                <?php foreach (($jugadores ?? []) as $jugador): ?>
                    <?php
                    $numJug = (string) ($jugador['numJug'] ?? '');
                    $nombreJugador = trim(($jugador['nomJug'] ?? '') . ' ' . ($jugador['apPatJug'] ?? '') . ' ' . ($jugador['apMatJug'] ?? ''));
                    $camiseta = !empty($jugador['numeroCamiseta']) ? ' #' . $jugador['numeroCamiseta'] : '';
                    ?>
                    <option value="<?php echo h($numJug); ?>" <?php echo (string) ($evento['numJug'] ?? '') === $numJug ? 'selected' : ''; ?>>
                        <?php echo h(($jugador['nombEquipo'] ?? 'Equipo') . ' / ' . $numJug . $camiseta . ' - ' . $nombreJugador); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <label class="field">
            <span class="label">Minuto</span>
            <input class="input" type="number" name="minuto" min="0" max="150" value="<?php echo h($evento['minuto'] ?? ''); ?>">
        </label>

        <label class="field field-wide">
            <span class="label">Observacion</span>
            <textarea class="input textarea" name="observacion" maxlength="300" rows="5"><?php echo h($evento['observacion'] ?? ''); ?></textarea>
        </label>
    </div>

    <?php if (empty($tiposEvento) || empty($jugadores)): ?>
        <div class="alert alert-info">
            <span>Necesitas tipos de evento y jugadores activos de los equipos del partido para capturar eventos completos.</span>
        </div>
    <?php endif; ?>

    <div class="form-actions">
        <a class="btn btn-secondary" href="/?controller=partidos&action=show&id=<?php echo h($idPartido); ?>">Cancelar</a>
        <button class="btn btn-primary" type="submit" <?php echo empty($tiposEvento) ? 'disabled' : ''; ?>>Guardar evento</button>
    </div>
</form>

