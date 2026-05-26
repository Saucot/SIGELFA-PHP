<?php

require_once __DIR__ . '/session.php';

if (!function_exists('auth_user')) {
    function auth_user(): ?array
    {
        sigelfa_start_session();

        return $_SESSION['auth_user'] ?? null;
    }
}

if (!function_exists('auth_check')) {
    function auth_check(): bool
    {
        return auth_user() !== null;
    }
}

if (!function_exists('auth_role')) {
    function auth_role(): ?string
    {
        $user = auth_user();

        return $user['rol'] ?? null;
    }
}

if (!function_exists('auth_require')) {
    function auth_require(): void
    {
        if (!auth_check()) {
            header('Location: /?controller=auth&action=login');
            exit;
        }
    }
}

if (!function_exists('auth_require_role')) {
    function auth_require_role(array $roles): void
    {
        auth_require();

        $rol = auth_role();

        if ($rol === null || !in_array($rol, $roles, true)) {
            http_response_code(403);
            echo 'No tienes permiso para acceder a este modulo.';
            exit;
        }
    }
}

if (!function_exists('auth_is_arbitro')) {
    function auth_is_arbitro(): bool
    {
        return auth_role() === 'ARBITRO';
    }
}

if (!function_exists('auth_num_arbitro')) {
    function auth_num_arbitro(): ?string
    {
        $user = auth_user();

        return $user['numArb'] ?? null;
    }
}

if (!function_exists('auth_login_user')) {
    function auth_login_user(array $usuario): void
    {
        sigelfa_start_session();
        session_regenerate_id(true);

        $_SESSION['auth_user'] = [
            'idUsuario' => $usuario['idUsuario'] ?? null,
            'nombreUsuario' => $usuario['nombreUsuario'] ?? '',
            'email' => $usuario['email'] ?? '',
            'rol' => $usuario['rol'] ?? '',
            'numArb' => $usuario['numArb'] ?? null,
        ];
    }
}

if (!function_exists('auth_logout_user')) {
    function auth_logout_user(): void
    {
        sigelfa_start_session();
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                (bool) $params['secure'],
                (bool) $params['httponly']
            );
        }

        session_destroy();
    }
}

