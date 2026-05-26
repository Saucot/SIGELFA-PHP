<?php

require_once __DIR__ . '/../app/helpers/auth.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/EquipoController.php';
require_once __DIR__ . '/../app/controllers/JugadorController.php';
require_once __DIR__ . '/../app/controllers/ArbitroController.php';
require_once __DIR__ . '/../app/controllers/PartidoController.php';

$controller = $_GET['controller'] ?? 'equipos';
$action = $_GET['action'] ?? 'index';

$controllers = [
    'auth' => [
        'class' => AuthController::class,
        'actions' => ['loginForm', 'login', 'logout'],
        'post' => [],
        'redirect' => '/?controller=auth&action=login',
        'public' => ['loginForm', 'login', 'logout'],
        'roles' => [],
    ],
    'equipos' => [
        'class' => EquipoController::class,
        'actions' => ['index', 'create', 'store', 'show', 'edit', 'update', 'deactivate'],
        'post' => ['store', 'update', 'deactivate'],
        'redirect' => '/?controller=equipos&action=index',
        'public' => [],
        'roles' => ['GERENTE', 'ASISTENTE'],
    ],
    'jugadores' => [
        'class' => JugadorController::class,
        'actions' => ['index', 'create', 'store', 'show', 'edit', 'update'],
        'post' => ['store', 'update'],
        'redirect' => '/?controller=jugadores&action=index',
        'public' => [],
        'roles' => ['GERENTE', 'ASISTENTE'],
    ],
    'arbitros' => [
        'class' => ArbitroController::class,
        'actions' => ['index', 'create', 'store', 'show', 'edit', 'update'],
        'post' => ['store', 'update'],
        'redirect' => '/?controller=arbitros&action=index',
        'public' => [],
        'roles' => ['GERENTE', 'ASISTENTE'],
    ],
    'partidos' => [
        'class' => PartidoController::class,
        'actions' => ['index', 'show', 'capturarCedula', 'guardarCedula', 'agregarEvento', 'guardarEvento'],
        'post' => ['guardarCedula', 'guardarEvento'],
        'redirect' => '/?controller=partidos&action=index',
        'public' => [],
        'roles' => ['GERENTE', 'ASISTENTE', 'ARBITRO'],
    ],
];

if (!isset($controllers[$controller])) {
    http_response_code(404);
    echo 'Modulo no encontrado.';
    exit;
}

$controllerConfig = $controllers[$controller];

if (!in_array($action, $controllerConfig['actions'], true)) {
    http_response_code(404);
    echo 'Accion no encontrada.';
    exit;
}

$isPublicAction = in_array($action, $controllerConfig['public'] ?? [], true);

if (!$isPublicAction) {
    auth_require_role($controllerConfig['roles'] ?? []);
}

$controllerClass = $controllerConfig['class'];
$controllerInstance = new $controllerClass();

if (!method_exists($controllerInstance, $action)) {
    http_response_code(404);
    echo 'Accion no encontrada.';
    exit;
}

if (in_array($action, $controllerConfig['post'], true) && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . $controllerConfig['redirect']);
    exit;
}

$controllerInstance->{$action}();
