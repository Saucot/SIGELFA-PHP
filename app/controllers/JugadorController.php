<?php

require_once __DIR__ . '/../models/Jugador.php';
require_once __DIR__ . '/../helpers/security.php';
require_once __DIR__ . '/../helpers/session.php';

class JugadorController {

    private Jugador $jugadorModel;

    public function __construct() {

        $this->jugadorModel = new Jugador();
    }

    public function index(): void {

        $jugadores = [];
        $error = null;

        try {
            $jugadores = $this->jugadorModel->obtenerTodos();
        } catch (PDOException $exception) {
            error_log('SIGELFA jugadores index error: ' . $exception->getMessage());
            $error = 'No se pudieron cargar los jugadores. Verifica la configuracion local.';
        }

        $flash = flash_get();
        $this->render('jugadores/index', compact('jugadores', 'error', 'flash'), 'Jugadores');
    }

    public function show(): void {

        $id = $this->idDesdeRequest();
        $jugador = $this->buscarJugador($id);

        if ($jugador === null) {
            flash_set('error', 'Jugador no encontrado.');
            $this->redirectIndex();
        }

        $this->render('jugadores/show', compact('jugador'), 'Detalle jugador');
    }

    public function create(): void {

        $jugador = $this->jugadorVacio();
        $errores = [];
        $modo = 'crear';
        $siguienteNumJug = $this->generarNumeroPreview();
        [$equipos, $posiciones] = $this->catalogosFormulario();

        $this->render('jugadores/form', compact('jugador', 'errores', 'modo', 'siguienteNumJug', 'equipos', 'posiciones'), 'Nuevo jugador');
    }

    public function store(): void {

        $datos = $this->datosDesdeRequest();
        $errores = $this->validar($datos);

        if (!empty($errores)) {
            $jugador = $datos;
            $modo = 'crear';
            $siguienteNumJug = $this->generarNumeroPreview();
            [$equipos, $posiciones] = $this->catalogosFormulario();
            $this->render('jugadores/form', compact('jugador', 'errores', 'modo', 'siguienteNumJug', 'equipos', 'posiciones'), 'Nuevo jugador');
            return;
        }

        try {
            $numJug = $this->jugadorModel->crear($datos);
            flash_set('success', 'Jugador registrado correctamente.');
            header('Location: /?controller=jugadores&action=show&id=' . urlencode($numJug));
            exit;
        } catch (PDOException $exception) {
            error_log('SIGELFA jugadores store error: ' . $exception->getMessage());
            $jugador = $datos;
            $errores = ['No se pudo registrar el jugador. Verifica los datos capturados.'];
            $modo = 'crear';
            $siguienteNumJug = $this->generarNumeroPreview();
            [$equipos, $posiciones] = $this->catalogosFormulario();
            $this->render('jugadores/form', compact('jugador', 'errores', 'modo', 'siguienteNumJug', 'equipos', 'posiciones'), 'Nuevo jugador');
        }
    }

    public function edit(): void {

        $id = $this->idDesdeRequest();
        $jugador = $this->buscarJugador($id);

        if ($jugador === null) {
            flash_set('error', 'Jugador no encontrado.');
            $this->redirectIndex();
        }

        $errores = [];
        $modo = 'editar';
        $siguienteNumJug = null;
        [$equipos, $posiciones] = $this->catalogosFormulario();

        $this->render('jugadores/form', compact('jugador', 'errores', 'modo', 'siguienteNumJug', 'equipos', 'posiciones'), 'Editar jugador');
    }

    public function update(): void {

        $id = $this->idDesdeRequest();
        $datos = $this->datosDesdeRequest();
        $datos['numJug'] = $id;
        $errores = $this->validar($datos);

        if (!empty($errores)) {
            $jugador = $datos;
            $modo = 'editar';
            $siguienteNumJug = null;
            [$equipos, $posiciones] = $this->catalogosFormulario();
            $this->render('jugadores/form', compact('jugador', 'errores', 'modo', 'siguienteNumJug', 'equipos', 'posiciones'), 'Editar jugador');
            return;
        }

        try {
            $this->jugadorModel->actualizar($id, $datos);
            flash_set('success', 'Jugador actualizado correctamente.');
            header('Location: /?controller=jugadores&action=show&id=' . urlencode($id));
            exit;
        } catch (PDOException $exception) {
            error_log('SIGELFA jugadores update error: ' . $exception->getMessage());
            $jugador = $datos;
            $errores = ['No se pudo actualizar el jugador. Verifica los datos capturados.'];
            $modo = 'editar';
            $siguienteNumJug = null;
            [$equipos, $posiciones] = $this->catalogosFormulario();
            $this->render('jugadores/form', compact('jugador', 'errores', 'modo', 'siguienteNumJug', 'equipos', 'posiciones'), 'Editar jugador');
        }
    }

    private function render(string $view, array $data, string $pageTitle): void {

        extract($data, EXTR_SKIP);

        $breadcrumbCurrent = $pageTitle === 'Jugadores' ? 'Jugadores' : 'Jugadores / ' . $pageTitle;
        $activeNav = 'jugadores';

        require __DIR__ . '/../views/layouts/header.php';
        require __DIR__ . '/../views/' . $view . '.php';
        require __DIR__ . '/../views/layouts/footer.php';
    }

    private function datosDesdeRequest(): array {

        return [
            'nomJug' => $_POST['nomJug'] ?? '',
            'apPatJug' => $_POST['apPatJug'] ?? '',
            'apMatJug' => $_POST['apMatJug'] ?? '',
            'fechaNacJug' => $_POST['fechaNacJug'] ?? '',
            'edadJug' => $_POST['edadJug'] ?? '',
            'telJug' => $_POST['telJug'] ?? '',
            'eMailJug' => $_POST['eMailJug'] ?? '',
            'numeroCamiseta' => $_POST['numeroCamiseta'] ?? '',
            'cveEquipo' => $_POST['cveEquipo'] ?? '',
            'idPosicion' => $_POST['idPosicion'] ?? '',
            'activo' => (int) ($_POST['activo'] ?? 1) === 1 ? 1 : 0,
        ];
    }

    private function validar(array $datos): array {

        $errores = [];

        if (trim((string) ($datos['nomJug'] ?? '')) === '') {
            $errores[] = 'El nombre del jugador es requerido.';
        }

        if ((int) ($datos['cveEquipo'] ?? 0) <= 0) {
            $errores[] = 'El equipo es requerido.';
        }

        if ((int) ($datos['idPosicion'] ?? 0) <= 0) {
            $errores[] = 'La posicion es requerida.';
        }

        $email = trim((string) ($datos['eMailJug'] ?? ''));

        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'El correo del jugador no tiene un formato valido.';
        }

        $numero = trim((string) ($datos['numeroCamiseta'] ?? ''));

        if ($numero !== '' && (!ctype_digit($numero) || (int) $numero < 1 || (int) $numero > 999)) {
            $errores[] = 'El numero de camiseta debe ser entero entre 1 y 999.';
        }

        $edad = trim((string) ($datos['edadJug'] ?? ''));

        if ($edad !== '' && (!ctype_digit($edad) || (int) $edad < 0)) {
            $errores[] = 'La edad debe ser un entero mayor o igual a 0.';
        }

        $fecha = trim((string) ($datos['fechaNacJug'] ?? ''));

        if ($fecha !== '') {
            $fechaValida = DateTime::createFromFormat('Y-m-d', $fecha);

            if (!$fechaValida || $fechaValida->format('Y-m-d') !== $fecha) {
                $errores[] = 'La fecha de nacimiento no tiene un formato valido.';
            }
        }

        return $errores;
    }

    private function catalogosFormulario(): array {

        try {
            return [
                $this->jugadorModel->obtenerEquiposDisponibles(),
                $this->jugadorModel->obtenerPosicionesDisponibles(),
            ];
        } catch (PDOException $exception) {
            error_log('SIGELFA jugadores catalogos error: ' . $exception->getMessage());
            return [[], []];
        }
    }

    private function buscarJugador(string $id): ?array {

        if ($id === '') {
            return null;
        }

        try {
            return $this->jugadorModel->obtenerPorId($id);
        } catch (PDOException $exception) {
            error_log('SIGELFA jugadores find error: ' . $exception->getMessage());
            return null;
        }
    }

    private function generarNumeroPreview(): string {

        try {
            return $this->jugadorModel->generarSiguienteNumeroJugador();
        } catch (PDOException $exception) {
            error_log('SIGELFA jugadores next number error: ' . $exception->getMessage());
            return 'J---';
        }
    }

    private function jugadorVacio(): array {

        return [
            'numJug' => '',
            'nomJug' => '',
            'apPatJug' => '',
            'apMatJug' => '',
            'fechaNacJug' => '',
            'edadJug' => '',
            'telJug' => '',
            'eMailJug' => '',
            'numeroCamiseta' => '',
            'cveEquipo' => '',
            'idPosicion' => '',
            'activo' => 1,
        ];
    }

    private function idDesdeRequest(): string {

        return trim((string) ($_GET['id'] ?? $_POST['id'] ?? ''));
    }

    private function redirectIndex(): void {

        header('Location: /?controller=jugadores&action=index');
        exit;
    }
}

