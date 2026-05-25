<?php

class Database {

    private $host = "localhost";
    private $port = "1433";
    private $db_name = "SIGELFA_DB";

    private $conn;

    public function getConnection() {

        $this->conn = null;

        try {

            $dsn = "sqlsrv:Server={$this->host},{$this->port};Database={$this->db_name};Encrypt=no;TrustServerCertificate=true";

            $this->conn = new PDO($dsn);

            $this->conn->setAttribute(
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION
            );

            return $this->conn;

        } catch(PDOException $exception) {

            die(
                "Error de conexión: " .
                $exception->getMessage()
            );
        }
    }
}