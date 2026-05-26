<?php
require_once __DIR__ . '/../../helpers/security.php';
require_once __DIR__ . '/../../helpers/auth.php';

$pageTitle = $pageTitle ?? 'SIGELFA';
$breadcrumbCurrent = $breadcrumbCurrent ?? $pageTitle;
$authUser = auth_user();
$authName = trim((string) ($authUser['nombreUsuario'] ?? ''));
$authRole = trim((string) ($authUser['rol'] ?? ''));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo h($pageTitle); ?> - SIGELFA</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <div class="app-shell">
        <?php require __DIR__ . '/sidebar.php'; ?>

        <div class="app-main">
            <header class="topbar">
                <div class="breadcrumb">
                    <button class="btn btn-secondary btn-icon btn-sm mobile-menu-btn" type="button" data-sidebar-toggle aria-label="Abrir menu">
                        =
                    </button>
                    <span>SIGELFA</span>
                    <span>/</span>
                    <span class="breadcrumb-current"><?php echo h($breadcrumbCurrent); ?></span>
                </div>

                <div class="topbar-actions">
                    <?php if ($authUser !== null): ?>
                        <span class="user-chip">
                            <span class="user-avatar"><?php echo h(strtoupper(substr($authName !== '' ? $authName : 'U', 0, 1))); ?></span>
                            <span>
                                <?php echo h($authName !== '' ? $authName : 'Usuario'); ?>
                                <small><?php echo h($authRole); ?></small>
                            </span>
                        </span>
                        <a class="btn btn-ghost btn-sm" href="/?controller=auth&action=logout">Cerrar sesion</a>
                    <?php endif; ?>
                </div>
            </header>

            <main class="main-content">
