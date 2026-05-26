<?php

require_once __DIR__ . '/../models/Equipo.php';
require_once __DIR__ . '/../helpers/security.php';
require_once __DIR__ . '/../helpers/session.php';

class EquipoController {

    private Equipo $equipoModel;

    public function __construct() {

        $this->equipoModel = new Equipo();
    }

    public function index(): void {

        $equipos = [];
        $error = null;

        try {
            $equipos = $this->equipoModel->obtenerTodos();
        } catch (PDOException $exception) {
            error_log('SIGELFA equipos index error: ' . $exception->getMessage());
            $error = 'No se pudieron cargar los equipos. Verifica la configuracion local.';
        }

        $flash = flash_get();
        $this->render('equipos/index', compact('equipos', 'error', 'flash'), 'Equipos');
    }

    public function create(): void {

        $categorias = $this->obtenerCategoriasParaFormulario();
        $equipo = $this->equipoVacio();
        $errores = [];
        $modo = 'crear';

        $this->render('equipos/form', compact('categorias', 'equipo', 'errores', 'modo'), 'Nuevo equipo');
    }

    public function show(): void {

        $id = $this->idDesdeRequest();

        if ($id <= 0) {
            flash_set('error', 'Equipo no encontrado.');
            $this->redirectIndex();
        }

        $equipo = $this->buscarEquipo($id);

        if ($equipo === null) {
            flash_set('error', 'Equipo no encontrado.');
            $this->redirectIndex();
        }

        $this->render('equipos/show', compact('equipo'), 'Detalle equipo');
    }

    public function store(): void {

        $datos = $this->datosDesdeRequest();
        $errores = $this->validar($datos);

        if (!empty($errores)) {
            $categorias = $this->obtenerCategoriasParaFormulario();
            $equipo = $datos;
            $modo = 'crear';
            $this->render('equipos/form', compact('categorias', 'equipo', 'errores', 'modo'), 'Nuevo equipo');
            return;
        }

        try {
            $this->equipoModel->crear($datos);
            flash_set('success', 'Equipo registrado correctamente.');
            $this->redirectIndex();
        } catch (PDOException $exception) {
            error_log('SIGELFA equipos store error: ' . $exception->getMessage());
            $categorias = $this->obtenerCategoriasParaFormulario();
            $equipo = $datos;
            $errores = ['No se pudo registrar el equipo. Verifica los datos capturados.'];
            $modo = 'crear';
            $this->render('equipos/form', compact('categorias', 'equipo', 'errores', 'modo'), 'Nuevo equipo');
        }
    }

    public function edit(): void {

        $id = $this->idDesdeRequest();

        if ($id <= 0) {
            flash_set('error', 'Equipo no encontrado.');
            $this->redirectIndex();
        }

        $equipo = $this->buscarEquipo($id);

        if ($equipo === null) {
            flash_set('error', 'Equipo no encontrado.');
            $this->redirectIndex();
        }

        $categorias = $this->obtenerCategoriasParaFormulario();
        $errores = [];
        $modo = 'editar';

        $this->render('equipos/form', compact('categorias', 'equipo', 'errores', 'modo'), 'Editar equipo');
    }

    public function update(): void {

        $id = $this->idDesdeRequest();

        if ($id <= 0) {
            flash_set('error', 'Equipo no encontrado.');
            $this->redirectIndex();
        }

        $datos = $this->datosDesdeRequest();
        $datos['cveEquipo'] = $id;
        $errores = $this->validar($datos);

        if (!empty($errores)) {
            $categorias = $this->obtenerCategoriasParaFormulario();
            $equipo = $datos;
            $modo = 'editar';
            $this->render('equipos/form', compact('categorias', 'equipo', 'errores', 'modo'), 'Editar equipo');
            return;
        }

        try {
            $this->equipoModel->actualizar($id, $datos);
            flash_set('success', 'Equipo actualizado correctamente.');
            $this->redirectIndex();
        } catch (PDOException $exception) {
            error_log('SIGELFA equipos update error: ' . $exception->getMessage());
            $categorias = $this->obtenerCategoriasParaFormulario();
            $equipo = $datos;
            $errores = ['No se pudo actualizar el equipo. Verifica los datos capturados.'];
            $modo = 'editar';
            $this->render('equipos/form', compact('categorias', 'equipo', 'errores', 'modo'), 'Editar equipo');
        }
    }

    public function deactivate(): void {

        $id = $this->idDesdeRequest();

        if ($id <= 0) {
            flash_set('error', 'Equipo no encontrado.');
            $this->redirectIndex();
        }

        try {
            $this->equipoModel->desactivar($id);
            flash_set('success', 'Equipo desactivado correctamente.');
        } catch (PDOException $exception) {
            error_log('SIGELFA equipos deactivate error: ' . $exception->getMessage());
            flash_set('error', 'No se pudo desactivar el equipo.');
        }

        $this->redirectIndex();
    }

    private function render(string $view, array $data, string $pageTitle): void {

        extract($data, EXTR_SKIP);

        $breadcrumbCurrent = $pageTitle === 'Equipos' ? 'Equipos' : 'Equipos / ' . $pageTitle;
        $activeNav = 'equipos';

        require __DIR__ . '/../views/layouts/header.php';
        require __DIR__ . '/../views/' . $view . '.php';
        require __DIR__ . '/../views/layouts/footer.php';
    }

    private function datosDesdeRequest(): array {

        $datos = [
            'nombEquipo' => $_POST['nombEquipo'] ?? '',
            'nombRepEq' => $_POST['nombRepEq'] ?? '',
            'numTelRepEq' => $_POST['numTelRepEq'] ?? '',
            'eMailRepEq' => $_POST['eMailRepEq'] ?? '',
            'nomCortoCat' => $_POST['nomCortoCat'] ?? '',
            'perTorneo' => $_POST['perTorneo'] ?? '',
            'cveLiga' => $_POST['cveLiga'] ?? '',
            'activo' => (int) ($_POST['activo'] ?? 1) === 1 ? 1 : 0,
        ];

        if (!empty($_POST['categoriaClave'])) {
            $partes = explode('|', (string) $_POST['categoriaClave']);

            if (count($partes) === 3) {
                [$datos['nomCortoCat'], $datos['perTorneo'], $datos['cveLiga']] = $partes;
            }
        }

        return $datos;
    }

    private function validar(array $datos): array {

        $errores = [];

        if (trim((string) ($datos['nombEquipo'] ?? '')) === '') {
            $errores[] = 'El nombre del equipo es requerido.';
        }

        if (trim((string) ($datos['nomCortoCat'] ?? '')) === '') {
            $errores[] = 'La categoria es requerida.';
        }

        if (trim((string) ($datos['perTorneo'] ?? '')) === '') {
            $errores[] = 'El torneo es requerido.';
        }

        if (trim((string) ($datos['cveLiga'] ?? '')) === '') {
            $errores[] = 'La liga es requerida.';
        }

        $email = trim((string) ($datos['eMailRepEq'] ?? ''));

        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'El correo del representante no tiene un formato valido.';
        }

        return $errores;
    }

    private function obtenerCategoriasParaFormulario(): array {

        try {
            return $this->equipoModel->obtenerCategoriasDisponibles();
        } catch (PDOException $exception) {
            error_log('SIGELFA categorias form error: ' . $exception->getMessage());
            return [];
        }
    }

    private function buscarEquipo(int $id): ?array {

        try {
            return $this->equipoModel->obtenerPorId($id);
        } catch (PDOException $exception) {
            error_log('SIGELFA equipos find error: ' . $exception->getMessage());
            return null;
        }
    }

    private function equipoVacio(): array {

        return [
            'cveEquipo' => null,
            'nombEquipo' => '',
            'nombRepEq' => '',
            'numTelRepEq' => '',
            'eMailRepEq' => '',
            'nomCortoCat' => '',
            'perTorneo' => '',
            'cveLiga' => '',
            'activo' => 1,
        ];
    }

    private function idDesdeRequest(): int {

        return (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
    }

    private function redirectIndex(): void {

        header('Location: /?controller=equipos&action=index');
        exit;
    }
}
