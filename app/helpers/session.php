<?php

if (!function_exists('sigelfa_start_session')) {
    function sigelfa_start_session(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_set_cookie_params([
                'lifetime' => 0,
                'path' => '/',
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
            session_start();
        }
    }
}

if (!function_exists('flash_set')) {
    function flash_set(string $type, string $message): void
    {
        sigelfa_start_session();
        $_SESSION['flash'] = [
            'type' => $type,
            'message' => $message,
        ];
    }
}

if (!function_exists('flash_get')) {
    function flash_get(): ?array
    {
        sigelfa_start_session();
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        return $flash;
    }
}
