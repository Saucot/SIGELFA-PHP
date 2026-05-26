<?php

require_once __DIR__ . '/Database.php';

class Jugador {

    private PDO $conn;
    private string $table = 'dbo.Jugador';

    public function __construct() {

        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function obtenerTodos(): array {

        $query = "
            SELECT
                j.numJug,
                j.nomJug,
                j.apPatJug,
                j.apMatJug,
                j.fechaNacJug,
                j.edadJug,
                j.telJug,
                j.eMailJug,
                j.numeroCamiseta,
                j.cveEquipo,
                j.idPosicion,
                j.activo,
                e.nombEquipo,
                e.nomCortoCat,
                e.perTorneo,
                p.nombrePosicion,
                p.abreviatura
            FROM {$this->table} j
            INNER JOIN dbo.Equipo e ON j.cveEquipo = e.cveEquipo
            INNER JOIN dbo.Posicion p ON j.idPosicion = p.idPosicion
            ORDER BY j.activo DESC, j.numJug
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function obtenerPorId(string $numJug): ?array {

        $query = "
            SELECT
                j.numJug,
                j.nomJug,
                j.apPatJug,
                j.apMatJug,
                j.fechaNacJug,
                j.edadJug,
                j.telJug,
                j.eMailJug,
                j.numeroCamiseta,
                j.cveEquipo,
                j.idPosicion,
                j.activo,
                e.nombEquipo,
                e.nomCortoCat,
                e.perTorneo,
                p.nombrePosicion,
                p.abreviatura
            FROM {$this->table} j
            INNER JOIN dbo.Equipo e ON j.cveEquipo = e.cveEquipo
            INNER JOIN dbo.Posicion p ON j.idPosicion = p.idPosicion
            WHERE j.numJug = :numJug
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':numJug', $numJug);
        $stmt->execute();

        $jugador = $stmt->fetch();

        return $jugador ?: null;
    }

    public function crear(array $datos): string {

        $numJug = $this->generarSiguienteNumeroJugador();

        $query = "
            INSERT INTO {$this->table} (
                numJug,
                nomJug,
                apPatJug,
                apMatJug,
                fechaNacJug,
                edadJug,
                telJug,
                eMailJug,
                numeroCamiseta,
                cveEquipo,
                idPosicion,
                activo
            )
            VALUES (
                :numJug,
                :nomJug,
                :apPatJug,
                :apMatJug,
                :fechaNacJug,
                :edadJug,
                :telJug,
                :eMailJug,
                :numeroCamiseta,
                :cveEquipo,
                :idPosicion,
                :activo
            )
        ";

        $params = $this->normalizarDatos($datos);
        $params['numJug'] = $numJug;

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);

        return $numJug;
    }

    public function actualizar(string $numJug, array $datos): bool {

        $query = "
            UPDATE {$this->table}
            SET
                nomJug = :nomJug,
                apPatJug = :apPatJug,
                apMatJug = :apMatJug,
                fechaNacJug = :fechaNacJug,
                edadJug = :edadJug,
                telJug = :telJug,
                eMailJug = :eMailJug,
                numeroCamiseta = :numeroCamiseta,
                cveEquipo = :cveEquipo,
                idPosicion = :idPosicion,
                activo = :activo
            WHERE numJug = :numJug
        ";

        $params = $this->normalizarDatos($datos);
        $params['numJug'] = $numJug;

        $stmt = $this->conn->prepare($query);

        return $stmt->execute($params);
    }

    public function desactivar(string $numJug): bool {

        $query = "
            UPDATE {$this->table}
            SET activo = 0
            WHERE numJug = :numJug
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':numJug', $numJug);

        return $stmt->execute();
    }

    public function obtenerEquiposDisponibles(): array {

        $query = "
            SELECT
                cveEquipo,
                nombEquipo,
                nomCortoCat,
                perTorneo,
                activo
            FROM dbo.Equipo
            ORDER BY activo DESC, nombEquipo
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function obtenerPosicionesDisponibles(): array {

        $query = "
            SELECT
                idPosicion,
                nombrePosicion,
                abreviatura
            FROM dbo.Posicion
            ORDER BY nombrePosicion
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function generarSiguienteNumeroJugador(): string {

        $query = "
            SELECT
                MAX(TRY_CONVERT(INT, SUBSTRING(numJug, 2, 3))) AS ultimo
            FROM {$this->table}
            WHERE numJug LIKE 'J[0-9][0-9][0-9]'
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $ultimo = (int) ($stmt->fetch()['ultimo'] ?? 0);

        return 'J' . str_pad((string) ($ultimo + 1), 3, '0', STR_PAD_LEFT);
    }

    private function normalizarDatos(array $datos): array {

        return [
            'nomJug' => trim((string) ($datos['nomJug'] ?? '')),
            'apPatJug' => $this->nullSiVacio($datos['apPatJug'] ?? null),
            'apMatJug' => $this->nullSiVacio($datos['apMatJug'] ?? null),
            'fechaNacJug' => $this->nullSiVacio($datos['fechaNacJug'] ?? null),
            'edadJug' => $this->intONull($datos['edadJug'] ?? null),
            'telJug' => $this->nullSiVacio($datos['telJug'] ?? null),
            'eMailJug' => $this->nullSiVacio($datos['eMailJug'] ?? null),
            'numeroCamiseta' => $this->intONull($datos['numeroCamiseta'] ?? null),
            'cveEquipo' => (int) ($datos['cveEquipo'] ?? 0),
            'idPosicion' => (int) ($datos['idPosicion'] ?? 0),
            'activo' => (int) ($datos['activo'] ?? 1) === 1 ? 1 : 0,
        ];
    }

    private function nullSiVacio(mixed $value): ?string {

        $value = trim((string) ($value ?? ''));

        return $value === '' ? null : $value;
    }

    private function intONull(mixed $value): ?int {

        $value = trim((string) ($value ?? ''));

        return $value === '' ? null : (int) $value;
    }
}

