<?php require_once __DIR__ . '/../../helpers/security.php'; ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo h($pageTitle ?? 'Iniciar sesion'); ?> - SIGELFA</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="auth-body">
    <main class="auth-shell">
        <section class="auth-panel animate-fade-up">
            <div class="auth-brand">
                <span class="brand-mark">SG</span>
                <div>
                    <strong>SIGELFA</strong>
                    <span>Sistema Gestor de Liga de Futbol Amateur</span>
                </div>
            </div>

            <div class="auth-copy">
                <p class="page-eyebrow">Acceso seguro</p>
                <h1>Iniciar sesion</h1>
                <p>Ingresa con tu usuario de aplicacion para administrar la liga o capturar cedulas arbitrales.</p>
            </div>

            <?php if (!empty($errores)): ?>
                <div class="alert alert-danger">
                    <div>
                        <?php foreach ($errores as $mensaje): ?>
                            <p><?php echo h($mensaje); ?></p>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <form class="auth-form" method="post" action="/?controller=auth&action=login">
                <label class="field">
                    <span class="label">Correo</span>
                    <input class="input" type="email" name="email" autocomplete="username" required value="<?php echo h($email ?? ''); ?>">
                </label>

                <label class="field">
                    <span class="label">Contrasena</span>
                    <input class="input" type="password" name="password" autocomplete="current-password" required>
                </label>

                <button class="btn btn-primary" type="submit">Iniciar sesion</button>
            </form>

            <p class="auth-note">
                Las credenciales se crean localmente en SQL Server. No uses ni compartas contrasenas reales en el repositorio.
            </p>
        </section>
    </main>
</body>
</html>

