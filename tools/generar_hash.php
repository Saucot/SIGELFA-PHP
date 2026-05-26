<?php

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "Este script solo debe ejecutarse desde consola.\n");
    exit(1);
}

$password = $argv[1] ?? '';

if ($password === '') {
    fwrite(STDERR, "Uso: php tools/generar_hash.php \"PasswordTemporal123\"\n");
    exit(1);
}

echo password_hash($password, PASSWORD_DEFAULT) . PHP_EOL;

