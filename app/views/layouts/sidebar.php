<?php
require_once __DIR__ . '/../../helpers/auth.php';

$activeNav = $activeNav ?? 'equipos';
$rolActual = auth_role();
$puedeGestion = in_array($rolActual, ['GERENTE', 'ASISTENTE'], true);
$puedeCompetencia = in_array($rolActual, ['GERENTE', 'ASISTENTE', 'ARBITRO'], true);
$homeUrl = $rolActual === 'ARBITRO' ? '/?controller=partidos&action=index' : '/';
?>

<aside class="sidebar" aria-label="Navegacion principal">
    <a class="sidebar-brand" href="<?php echo h($homeUrl); ?>">
        <span class="brand-mark">SG</span>
        <span>
            <span class="brand-title">SIGELFA</span>
            <span class="brand-subtitle">Liga Amateur</span>
        </span>
    </a>

    <nav class="sidebar-nav">
        <div class="sidebar-section">Principal</div>
        <a class="sidebar-item <?php echo $activeNav === 'dashboard' ? 'active' : ''; ?>" href="<?php echo h($homeUrl); ?>">
            <span class="sidebar-icon">D</span>
            <span>Dashboard</span>
        </a>

        <?php if ($puedeGestion): ?>
            <div class="sidebar-section">Gestion</div>
            <a class="sidebar-item <?php echo $activeNav === 'equipos' ? 'active' : ''; ?>" href="/">
                <span class="sidebar-icon">EQ</span>
                <span>Equipos</span>
            </a>
            <a class="sidebar-item <?php echo $activeNav === 'jugadores' ? 'active' : ''; ?>" href="/?controller=jugadores&action=index">
                <span class="sidebar-icon">JG</span>
                <span>Jugadores</span>
            </a>
            <a class="sidebar-item <?php echo $activeNav === 'arbitros' ? 'active' : ''; ?>" href="/?controller=arbitros&action=index">
                <span class="sidebar-icon">AR</span>
                <span>Arbitros</span>
            </a>
        <?php endif; ?>

        <?php if ($puedeCompetencia): ?>
            <div class="sidebar-section">Competencia</div>
            <?php if ($rolActual !== 'ARBITRO'): ?>
                <a class="sidebar-item <?php echo $activeNav === 'torneos' ? 'active' : ''; ?>" href="#">
                    <span class="sidebar-icon">TR</span>
                    <span>Torneos</span>
                </a>
                <a class="sidebar-item <?php echo $activeNav === 'jornadas' ? 'active' : ''; ?>" href="#">
                    <span class="sidebar-icon">JR</span>
                    <span>Jornadas</span>
                </a>
            <?php endif; ?>
            <a class="sidebar-item <?php echo $activeNav === 'partidos' ? 'active' : ''; ?>" href="/?controller=partidos&action=index">
                <span class="sidebar-icon">PT</span>
                <span>Partidos</span>
            </a>
            <a class="sidebar-item <?php echo $activeNav === 'cedulas' ? 'active' : ''; ?>" href="/?controller=partidos&action=index">
                <span class="sidebar-icon">CD</span>
                <span>Cedulas</span>
            </a>
        <?php endif; ?>

        <?php if ($rolActual === 'GERENTE'): ?>
            <div class="sidebar-section">Administracion</div>
            <a class="sidebar-item <?php echo $activeNav === 'reportes' ? 'active' : ''; ?>" href="#">
                <span class="sidebar-icon">RP</span>
                <span>Reportes</span>
            </a>
        <?php endif; ?>
    </nav>
</aside>
