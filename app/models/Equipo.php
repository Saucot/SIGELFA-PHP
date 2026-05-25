<?php

require_once __DIR__ . '/Database.php';

class Equipo {

    private $conn;
    private $table = "Equipo";

    public function __construct() {

        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function obtenerTodos() {

        $query = "
            SELECT
                cveEquipo,
                nombEquipo,
                nombRepEq,
                numTelRepEq,
                nomCortoCat,
                perTorneo
            FROM {$this->table}
            ORDER BY nombEquipo
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}