<?php

require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../helpers/security.php';
require_once __DIR__ . '/../helpers/session.php';
require_once __DIR__ . '/../helpers/auth.php';

class AuthController {

    private Usuario $usuarioModel;

    public function __construct() {

        $this->usuarioModel = new Usuario();
    }

    public function loginForm(): void {

        $this->mostrarLogin();
    }

    public function login(): void {

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->mostrarLogin();
            return;
        }

        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $errores = [];

        if ($email === '' || $password === '') {
            $errores[] = 'Credenciales invalidas.';
            $this->mostrarLogin($email, $errores);
            return;
        }

        try {
            $usuario = $this->usuarioModel->verificarCredenciales($email, $password);
        } catch (PDOException $exception) {
            error_log('SIGELFA auth login error: ' . $exception->getMessage());
            $usuario = null;
        }

        if ($usuario === null) {
            $errores[] = 'Credenciales invalidas.';
            $this->mostrarLogin($email, $errores);
            return;
        }

        auth_login_user($usuario);

        $destino = ($usuario['rol'] ?? '') === 'ARBITRO'
            ? '/?controller=partidos&action=index'
            : '/?controller=equipos&action=index';

        header('Location: ' . $destino);
        exit;
    }

    public function logout(): void {

        auth_logout_user();
        header('Location: /?controller=auth&action=login');
        exit;
    }

    private function mostrarLogin(string $email = '', array $errores = []): void {

        $pageTitle = 'Iniciar sesion';
        require __DIR__ . '/../views/auth/login.php';
    }
}

