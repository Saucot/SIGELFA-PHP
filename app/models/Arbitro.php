<?php

require_once __DIR__ . '/Database.php';

class Arbitro {

    private PDO $conn;
    private string $table = 'dbo.Arbitro';

    public function __construct() {

        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function obtenerTodos(): array {

        $query = "
            SELECT
                numArb,
                nomArb,
                apPatArb,
                apMatArb,
                telArb,
                eMailArb,
                activo
            FROM {$this->table}
            ORDER BY activo DESC, numArb
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function obtenerPorId(string $numArb): ?array {

        $query = "
            SELECT
                numArb,
                nomArb,
                apPatArb,
                apMatArb,
                telArb,
                eMailArb,
                activo
            FROM {$this->table}
            WHERE numArb = :numArb
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':numArb', $numArb);
        $stmt->execute();

        $arbitro = $stmt->fetch();

        return $arbitro ?: null;
    }

    public function crear(array $datos): string {

        $numArb = $this->generarSiguienteNumeroArbitro();

        $query = "
            INSERT INTO {$this->table} (
                numArb,
                nomArb,
                apPatArb,
                apMatArb,
                telArb,
                eMailArb,
                activo
            )
            VALUES (
                :numArb,
                :nomArb,
                :apPatArb,
                :apMatArb,
                :telArb,
                :eMailArb,
                :activo
            )
        ";

        $params = $this->normalizarDatos($datos);
        $params['numArb'] = $numArb;

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);

        return $numArb;
    }

    public function actualizar(string $numArb, array $datos): bool {

        $query = "
            UPDATE {$this->table}
            SET
                nomArb = :nomArb,
                apPatArb = :apPatArb,
                apMatArb = :apMatArb,
                telArb = :telArb,
                eMailArb = :eMailArb,
                activo = :activo
            WHERE numArb = :numArb
        ";

        $params = $this->normalizarDatos($datos);
        $params['numArb'] = $numArb;

        $stmt = $this->conn->prepare($query);

        return $stmt->execute($params);
    }

    public function desactivar(string $numArb): bool {

        $query = "
            UPDATE {$this->table}
            SET activo = 0
            WHERE numArb = :numArb
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':numArb', $numArb);

        return $stmt->execute();
    }

    public function generarSiguienteNumeroArbitro(): string {

        $query = "
            SELECT
                MAX(TRY_CONVERT(INT, SUBSTRING(numArb, 2, 3))) AS ultimo
            FROM {$this->table}
            WHERE numArb LIKE 'A[0-9][0-9][0-9]'
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $ultimo = (int) ($stmt->fetch()['ultimo'] ?? 0);

        return 'A' . str_pad((string) ($ultimo + 1), 3, '0', STR_PAD_LEFT);
    }

    private function normalizarDatos(array $datos): array {

        return [
            'nomArb' => trim((string) ($datos['nomArb'] ?? '')),
            'apPatArb' => $this->nullSiVacio($datos['apPatArb'] ?? null),
            'apMatArb' => $this->nullSiVacio($datos['apMatArb'] ?? null),
            'telArb' => $this->nullSiVacio($datos['telArb'] ?? null),
            'eMailArb' => $this->nullSiVacio($datos['eMailArb'] ?? null),
            'activo' => (int) ($datos['activo'] ?? 1) === 1 ? 1 : 0,
        ];
    }

    private function nullSiVacio(mixed $value): ?string {

        $value = trim((string) ($value ?? ''));

        return $value === '' ? null : $value;
    }
}

