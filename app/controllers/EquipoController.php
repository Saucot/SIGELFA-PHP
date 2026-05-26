<?php

require_once __DIR__ . '/../models/Equipo.php';

class EquipoController {

    public function index() {

        $equipoModel = new Equipo();

        $equipos = $equipoModel->obtenerTodos();

        require_once __DIR__ .
            '/../views/equipos/index.php';
    }
}