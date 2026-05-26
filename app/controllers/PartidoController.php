<?php

require_once __DIR__ . '/../models/Partido.php';
require_once __DIR__ . '/../helpers/security.php';
require_once __DIR__ . '/../helpers/session.php';

class PartidoController {

    private Partido $partidoModel;

    public function __construct() {

        $this->partidoModel = new Partido();
    }

    public function index(): void {

        $partidos = [];
        $error = null;

        try {
            $partidos = $this->partidoModel->obtenerTodos();
        } catch (PDOException $exception) {
            error_log('SIGELFA partidos index error: ' . $exception->getMessage());
            $error = 'No se pudieron cargar los partidos. Verifica la configuracion local.';
        }

        $flash = flash_get();
        $this->render('partidos/index', compact('partidos', 'error', 'flash'), 'Partidos');
    }

    public function show(): void {

        $idPartido = $this->idPartidoDesdeRequest();
        $partido = $this->buscarPartido($idPartido);

        if ($partido === null) {
            flash_set('error', 'Partido no encontrado.');
            $this->redirectIndex();
        }

        $cedula = null;
        $eventos = [];

        try {
            $cedula = $this->partidoModel->obtenerCedulaPorPartido($idPartido);
            if ($cedula !== null) {
                $eventos = $this->partidoModel->obtenerEventosPorCedula((int) $cedula['idCedula']);
            }
        } catch (PDOException $exception) {
            error_log('SIGELFA partidos show cedula error: ' . $exception->getMessage());
        }

        $flash = flash_get();
        $this->render('partidos/show', compact('partido', 'cedula', 'eventos', 'flash'), 'Detalle partido');
    }

    public function capturarCedula(): void {

        $idPartido = $this->idPartidoDesdeRequest();
        $partido = $this->buscarPartido($idPartido);

        if ($partido === null) {
            flash_set('error', 'Partido no encontrado.');
            $this->redirectIndex();
        }

        $cedulaExistente = $this->buscarCedulaPorPartido($idPartido);

        if ($cedulaExistente !== null) {
            flash_set('error', 'Este partido ya tiene cedula capturada.');
            header('Location: /?controller=partidos&action=show&id=' . urlencode((string) $idPartido));
            exit;
        }

        $arbitros = $this->obtenerArbitrosActivos();
        $cedula = $this->cedulaVacia($idPartido, $partido);
        $errores = [];

        $this->render('cedulas/form', compact('partido', 'cedula', 'arbitros', 'errores'), 'Capturar cedula');
    }

    public function guardarCedula(): void {

        $idPartido = $this->idPartidoDesdeRequest();
        $partido = $this->buscarPartido($idPartido);

        if ($partido === null) {
            flash_set('error', 'Partido no encontrado.');
            $this->redirectIndex();
        }

        $datos = $this->datosCedulaDesdeRequest($idPartido);
        $errores = $this->validarCedula($datos);

        if (!empty($errores)) {
            $arbitros = $this->obtenerArbitrosActivos();
            $cedula = $datos;
            $this->render('cedulas/form', compact('partido', 'cedula', 'arbitros', 'errores'), 'Capturar cedula');
            return;
        }

        try {
            $cedula = $this->partidoModel->registrarCedula($datos);
            flash_set('success', 'Cedula arbitral capturada correctamente.');
            header('Location: /?controller=partidos&action=show&id=' . urlencode((string) ($cedula['idPartido'] ?? $idPartido)));
            exit;
        } catch (PDOException $exception) {
            error_log('SIGELFA cedulas store error: ' . $exception->getMessage());
            $arbitros = $this->obtenerArbitrosActivos();
            $cedula = $datos;
            $errores = ['No se pudo guardar la cedula. Verifica los datos capturados.'];
            $this->render('cedulas/form', compact('partido', 'cedula', 'arbitros', 'errores'), 'Capturar cedula');
        }
    }

    public function agregarEvento(): void {

        $idCedula = $this->idCedulaDesdeRequest();
        $cedula = $this->buscarCedulaPorId($idCedula);

        if ($cedula === null) {
            flash_set('error', 'Cedula no encontrada.');
            $this->redirectIndex();
        }

        $partido = $this->buscarPartido((int) $cedula['idPartido']);
        $tiposEvento = $this->obtenerTiposEvento();
        $jugadores = $this->obtenerJugadoresPorPartido((int) $cedula['idPartido']);
        $evento = $this->eventoVacio($idCedula);
        $errores = [];

        $this->render('cedulas/evento_form', compact('partido', 'cedula', 'tiposEvento', 'jugadores', 'evento', 'errores'), 'Agregar evento');
    }

    public function guardarEvento(): void {

        $idCedula = $this->idCedulaDesdeRequest();
        $cedula = $this->buscarCedulaPorId($idCedula);

        if ($cedula === null) {
            flash_set('error', 'Cedula no encontrada.');
            $this->redirectIndex();
        }

        $partido = $this->buscarPartido((int) $cedula['idPartido']);
        $datos = $this->datosEventoDesdeRequest($idCedula);
        $errores = $this->validarEvento($datos);

        if (!empty($errores)) {
            $tiposEvento = $this->obtenerTiposEvento();
            $jugadores = $this->obtenerJugadoresPorPartido((int) $cedula['idPartido']);
            $evento = $datos;
            $this->render('cedulas/evento_form', compact('partido', 'cedula', 'tiposEvento', 'jugadores', 'evento', 'errores'), 'Agregar evento');
            return;
        }

        try {
            $this->partidoModel->agregarEvento($datos);
            flash_set('success', 'Evento registrado correctamente.');
            header('Location: /?controller=partidos&action=show&id=' . urlencode((string) $cedula['idPartido']));
            exit;
        } catch (PDOException $exception) {
            error_log('SIGELFA eventos store error: ' . $exception->getMessage());
            $tiposEvento = $this->obtenerTiposEvento();
            $jugadores = $this->obtenerJugadoresPorPartido((int) $cedula['idPartido']);
            $evento = $datos;
            $errores = ['No se pudo registrar el evento. Verifica equipo, jugador y tipo de evento.'];
            $this->render('cedulas/evento_form', compact('partido', 'cedula', 'tiposEvento', 'jugadores', 'evento', 'errores'), 'Agregar evento');
        }
    }

    private function render(string $view, array $data, string $pageTitle): void {

        extract($data, EXTR_SKIP);

        $breadcrumbCurrent = $pageTitle === 'Partidos' ? 'Partidos' : 'Partidos / ' . $pageTitle;
        $activeNav = 'partidos';

        require __DIR__ . '/../views/layouts/header.php';
        require __DIR__ . '/../views/' . $view . '.php';
        require __DIR__ . '/../views/layouts/footer.php';
    }

    private function datosCedulaDesdeRequest(int $idPartido): array {

        return [
            'idPartido' => $idPartido,
            'numArb' => $_POST['numArb'] ?? '',
            'golesEquipoA' => $_POST['golesEquipoA'] ?? '',
            'golesEquipoB' => $_POST['golesEquipoB'] ?? '',
            'observacionesGenerales' => $_POST['observacionesGenerales'] ?? '',
        ];
    }

    private function datosEventoDesdeRequest(int $idCedula): array {

        return [
            'idCedula' => $idCedula,
            'abreviaturaEvento' => $_POST['abreviaturaEvento'] ?? '',
            'numJug' => $_POST['numJug'] ?? '',
            'cveEquipo' => $_POST['cveEquipo'] ?? '',
            'minuto' => $_POST['minuto'] ?? '',
            'observacion' => $_POST['observacion'] ?? '',
        ];
    }

    private function validarCedula(array $datos): array {

        $errores = [];

        if ((int) ($datos['idPartido'] ?? 0) <= 0) {
            $errores[] = 'El partido es requerido.';
        }

        if (trim((string) ($datos['numArb'] ?? '')) === '') {
            $errores[] = 'El arbitro es requerido.';
        }

        foreach (['golesEquipoA' => 'goles del equipo A', 'golesEquipoB' => 'goles del equipo B'] as $campo => $label) {
            $valor = trim((string) ($datos[$campo] ?? ''));

            if ($valor === '' || filter_var($valor, FILTER_VALIDATE_INT) === false || (int) $valor < 0) {
                $errores[] = 'Los ' . $label . ' deben ser un entero mayor o igual a 0.';
            }
        }

        return $errores;
    }

    private function validarEvento(array $datos): array {

        $errores = [];
        $tipo = trim((string) ($datos['abreviaturaEvento'] ?? ''));
        $minuto = trim((string) ($datos['minuto'] ?? ''));

        if ((int) ($datos['idCedula'] ?? 0) <= 0) {
            $errores[] = 'La cedula es requerida.';
        }

        if ($tipo === '') {
            $errores[] = 'El tipo de evento es requerido.';
        }

        if ((int) ($datos['cveEquipo'] ?? 0) <= 0) {
            $errores[] = 'El equipo es requerido.';
        }

        if ($minuto !== '' && (filter_var($minuto, FILTER_VALIDATE_INT) === false || (int) $minuto < 0 || (int) $minuto > 150)) {
            $errores[] = 'El minuto debe estar entre 0 y 150.';
        }

        if (in_array($tipo, ['GOL', 'AUTOGOL', 'AMARILLA', 'ROJA'], true) && trim((string) ($datos['numJug'] ?? '')) === '') {
            $errores[] = 'Este tipo de evento requiere seleccionar un jugador.';
        }

        return $errores;
    }

    private function buscarPartido(int $idPartido): ?array {

        if ($idPartido <= 0) {
            return null;
        }

        try {
            return $this->partidoModel->obtenerPorId($idPartido);
        } catch (PDOException $exception) {
            error_log('SIGELFA partidos find error: ' . $exception->getMessage());
            return null;
        }
    }

    private function buscarCedulaPorPartido(int $idPartido): ?array {

        try {
            return $this->partidoModel->obtenerCedulaPorPartido($idPartido);
        } catch (PDOException $exception) {
            error_log('SIGELFA cedulas find by match error: ' . $exception->getMessage());
            return null;
        }
    }

    private function buscarCedulaPorId(int $idCedula): ?array {

        if ($idCedula <= 0) {
            return null;
        }

        try {
            return $this->partidoModel->obtenerCedulaPorId($idCedula);
        } catch (PDOException $exception) {
            error_log('SIGELFA cedulas find error: ' . $exception->getMessage());
            return null;
        }
    }

    private function obtenerArbitrosActivos(): array {

        try {
            return $this->partidoModel->obtenerArbitrosActivos();
        } catch (PDOException $exception) {
            error_log('SIGELFA arbitros activos error: ' . $exception->getMessage());
            return [];
        }
    }

    private function obtenerTiposEvento(): array {

        try {
            return $this->partidoModel->obtenerTiposEvento();
        } catch (PDOException $exception) {
            error_log('SIGELFA tipos evento error: ' . $exception->getMessage());
            return [];
        }
    }

    private function obtenerJugadoresPorPartido(int $idPartido): array {

        try {
            return $this->partidoModel->obtenerJugadoresPorPartido($idPartido);
        } catch (PDOException $exception) {
            error_log('SIGELFA jugadores partido error: ' . $exception->getMessage());
            return [];
        }
    }

    private function cedulaVacia(int $idPartido, array $partido): array {

        return [
            'idPartido' => $idPartido,
            'numArb' => $partido['numArb'] ?? '',
            'golesEquipoA' => $partido['golesEquipoA'] ?? 0,
            'golesEquipoB' => $partido['golesEquipoB'] ?? 0,
            'observacionesGenerales' => $partido['observaciones'] ?? '',
        ];
    }

    private function eventoVacio(int $idCedula): array {

        return [
            'idCedula' => $idCedula,
            'abreviaturaEvento' => '',
            'numJug' => '',
            'cveEquipo' => '',
            'minuto' => '',
            'observacion' => '',
        ];
    }

    private function idPartidoDesdeRequest(): int {

        return (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
    }

    private function idCedulaDesdeRequest(): int {

        return (int) ($_GET['idCedula'] ?? $_POST['idCedula'] ?? 0);
    }

    private function redirectIndex(): void {

        header('Location: /?controller=partidos&action=index');
        exit;
    }
}
