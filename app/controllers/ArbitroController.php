<?php

require_once __DIR__ . '/../models/Arbitro.php';
require_once __DIR__ . '/../helpers/security.php';
require_once __DIR__ . '/../helpers/session.php';

class ArbitroController {

    private Arbitro $arbitroModel;

    public function __construct() {

        $this->arbitroModel = new Arbitro();
    }

    public function index(): void {

        $arbitros = [];
        $error = null;

        try {
            $arbitros = $this->arbitroModel->obtenerTodos();
        } catch (PDOException $exception) {
            error_log('SIGELFA arbitros index error: ' . $exception->getMessage());
            $error = 'No se pudieron cargar los arbitros. Verifica la configuracion local.';
        }

        $flash = flash_get();
        $this->render('arbitros/index', compact('arbitros', 'error', 'flash'), 'Arbitros');
    }

    public function show(): void {

        $id = $this->idDesdeRequest();
        $arbitro = $this->buscarArbitro($id);

        if ($arbitro === null) {
            flash_set('error', 'Arbitro no encontrado.');
            $this->redirectIndex();
        }

        $this->render('arbitros/show', compact('arbitro'), 'Detalle arbitro');
    }

    public function create(): void {

        $arbitro = $this->arbitroVacio();
        $errores = [];
        $modo = 'crear';
        $siguienteNumArb = $this->generarNumeroPreview();

        $this->render('arbitros/form', compact('arbitro', 'errores', 'modo', 'siguienteNumArb'), 'Nuevo arbitro');
    }

    public function store(): void {

        $datos = $this->datosDesdeRequest();
        $errores = $this->validar($datos);

        if (!empty($errores)) {
            $arbitro = $datos;
            $modo = 'crear';
            $siguienteNumArb = $this->generarNumeroPreview();
            $this->render('arbitros/form', compact('arbitro', 'errores', 'modo', 'siguienteNumArb'), 'Nuevo arbitro');
            return;
        }

        try {
            $numArb = $this->arbitroModel->crear($datos);
            flash_set('success', 'Arbitro registrado correctamente.');
            header('Location: /?controller=arbitros&action=show&id=' . urlencode($numArb));
            exit;
        } catch (PDOException $exception) {
            error_log('SIGELFA arbitros store error: ' . $exception->getMessage());
            $arbitro = $datos;
            $errores = ['No se pudo registrar el arbitro. Verifica los datos capturados.'];
            $modo = 'crear';
            $siguienteNumArb = $this->generarNumeroPreview();
            $this->render('arbitros/form', compact('arbitro', 'errores', 'modo', 'siguienteNumArb'), 'Nuevo arbitro');
        }
    }

    public function edit(): void {

        $id = $this->idDesdeRequest();
        $arbitro = $this->buscarArbitro($id);

        if ($arbitro === null) {
            flash_set('error', 'Arbitro no encontrado.');
            $this->redirectIndex();
        }

        $errores = [];
        $modo = 'editar';
        $siguienteNumArb = null;

        $this->render('arbitros/form', compact('arbitro', 'errores', 'modo', 'siguienteNumArb'), 'Editar arbitro');
    }

    public function update(): void {

        $id = $this->idDesdeRequest();
        $datos = $this->datosDesdeRequest();
        $datos['numArb'] = $id;
        $errores = $this->validar($datos);

        if (!empty($errores)) {
            $arbitro = $datos;
            $modo = 'editar';
            $siguienteNumArb = null;
            $this->render('arbitros/form', compact('arbitro', 'errores', 'modo', 'siguienteNumArb'), 'Editar arbitro');
            return;
        }

        try {
            $this->arbitroModel->actualizar($id, $datos);
            flash_set('success', 'Arbitro actualizado correctamente.');
            header('Location: /?controller=arbitros&action=show&id=' . urlencode($id));
            exit;
        } catch (PDOException $exception) {
            error_log('SIGELFA arbitros update error: ' . $exception->getMessage());
            $arbitro = $datos;
            $errores = ['No se pudo actualizar el arbitro. Verifica los datos capturados.'];
            $modo = 'editar';
            $siguienteNumArb = null;
            $this->render('arbitros/form', compact('arbitro', 'errores', 'modo', 'siguienteNumArb'), 'Editar arbitro');
        }
    }

    private function render(string $view, array $data, string $pageTitle): void {

        extract($data, EXTR_SKIP);

        $breadcrumbCurrent = $pageTitle === 'Arbitros' ? 'Arbitros' : 'Arbitros / ' . $pageTitle;
        $activeNav = 'arbitros';

        require __DIR__ . '/../views/layouts/header.php';
        require __DIR__ . '/../views/' . $view . '.php';
        require __DIR__ . '/../views/layouts/footer.php';
    }

    private function datosDesdeRequest(): array {

        return [
            'nomArb' => $_POST['nomArb'] ?? '',
            'apPatArb' => $_POST['apPatArb'] ?? '',
            'apMatArb' => $_POST['apMatArb'] ?? '',
            'telArb' => $_POST['telArb'] ?? '',
            'eMailArb' => $_POST['eMailArb'] ?? '',
            'activo' => (int) ($_POST['activo'] ?? 1) === 1 ? 1 : 0,
        ];
    }

    private function validar(array $datos): array {

        $errores = [];

        if (trim((string) ($datos['nomArb'] ?? '')) === '') {
            $errores[] = 'El nombre del arbitro es requerido.';
        }

        $email = trim((string) ($datos['eMailArb'] ?? ''));

        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'El correo del arbitro no tiene un formato valido.';
        }

        return $errores;
    }

    private function buscarArbitro(string $id): ?array {

        if ($id === '') {
            return null;
        }

        try {
            return $this->arbitroModel->obtenerPorId($id);
        } catch (PDOException $exception) {
            error_log('SIGELFA arbitros find error: ' . $exception->getMessage());
            return null;
        }
    }

    private function generarNumeroPreview(): string {

        try {
            return $this->arbitroModel->generarSiguienteNumeroArbitro();
        } catch (PDOException $exception) {
            error_log('SIGELFA arbitros next number error: ' . $exception->getMessage());
            return 'A---';
        }
    }

    private function arbitroVacio(): array {

        return [
            'numArb' => '',
            'nomArb' => '',
            'apPatArb' => '',
            'apMatArb' => '',
            'telArb' => '',
            'eMailArb' => '',
            'activo' => 1,
        ];
    }

    private function idDesdeRequest(): string {

        return trim((string) ($_GET['id'] ?? $_POST['id'] ?? ''));
    }

    private function redirectIndex(): void {

        header('Location: /?controller=arbitros&action=index');
        exit;
    }
}

