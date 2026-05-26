<?php

require_once __DIR__ . '/../config/env.php';

class Database {

    private $conn;

    public function getConnection() {

        $this->conn = null;

        try {

            $host = env_value('DB_HOST', 'localhost');
            $port = env_value('DB_PORT', '1433');
            $database = env_value('DB_DATABASE', 'SIGELFA_DB');
            $trustedConnection = env_bool('DB_TRUSTED_CONNECTION', true);
            $trustServerCertificate = env_bool('DB_TRUST_SERVER_CERTIFICATE', true) ? '1' : '0';
            $server = $this->buildServerName($host, $port);

            $dsn = "sqlsrv:Server={$server};Database={$database};TrustServerCertificate={$trustServerCertificate}";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ];

            if ($trustedConnection) {
                $this->conn = new PDO($dsn, null, null, $options);
            } else {
                $username = env_value('DB_USERNAME', '');
                $password = env_value('DB_PASSWORD', '');
                $this->conn = new PDO($dsn, $username, $password, $options);
            }

            return $this->conn;

        } catch(PDOException $exception) {

            error_log('SIGELFA database connection error: ' . $exception->getMessage());
            http_response_code(500);
            exit('No se pudo conectar con la base de datos. Verifica la configuracion local.');
        }
    }

    private function buildServerName(string $host, string $port): string
    {
        $host = trim($host) !== '' ? trim($host) : 'localhost';
        $port = trim($port);

        if ($port === '' || str_contains($host, '\\')) {
            return $host;
        }

        return $host . ',' . $port;
    }
}
