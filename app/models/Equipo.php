<?php

require_once __DIR__ . '/Database.php';

class Equipo {

    private PDO $conn;
    private string $table = 'dbo.Equipo';

    public function __construct() {

        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function obtenerTodos(): array {

        $query = "
            SELECT
                cveEquipo,
                nombEquipo,
                nombRepEq,
                numTelRepEq,
                eMailRepEq,
                nomCortoCat,
                perTorneo,
                cveLiga,
                activo
            FROM {$this->table}
            ORDER BY activo DESC, nombEquipo
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function obtenerPorId(int $id): ?array {

        $query = "
            SELECT
                cveEquipo,
                nombEquipo,
                nombRepEq,
                numTelRepEq,
                eMailRepEq,
                nomCortoCat,
                perTorneo,
                cveLiga,
                activo
            FROM {$this->table}
            WHERE cveEquipo = :id
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $equipo = $stmt->fetch();

        return $equipo ?: null;
    }

    public function crear(array $datos): bool {

        $query = "
            INSERT INTO {$this->table} (
                nombEquipo,
                nombRepEq,
                numTelRepEq,
                eMailRepEq,
                nomCortoCat,
                perTorneo,
                cveLiga,
                activo
            )
            VALUES (
                :nombEquipo,
                :nombRepEq,
                :numTelRepEq,
                :eMailRepEq,
                :nomCortoCat,
                :perTorneo,
                :cveLiga,
                :activo
            )
        ";

        $stmt = $this->conn->prepare($query);

        return $stmt->execute($this->normalizarDatos($datos));
    }

    public function actualizar(int $id, array $datos): bool {

        $query = "
            UPDATE {$this->table}
            SET
                nombEquipo = :nombEquipo,
                nombRepEq = :nombRepEq,
                numTelRepEq = :numTelRepEq,
                eMailRepEq = :eMailRepEq,
                nomCortoCat = :nomCortoCat,
                perTorneo = :perTorneo,
                cveLiga = :cveLiga,
                activo = :activo
            WHERE cveEquipo = :id
        ";

        $params = $this->normalizarDatos($datos);
        $params['id'] = $id;

        $stmt = $this->conn->prepare($query);

        return $stmt->execute($params);
    }

    public function desactivar(int $id): bool {

        $query = "
            UPDATE {$this->table}
            SET activo = 0
            WHERE cveEquipo = :id
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function obtenerCategoriasDisponibles(): array {

        $query = "
            SELECT
                nomCortoCat,
                perTorneo,
                cveLiga
            FROM dbo.Categoria
            ORDER BY cveLiga, perTorneo, nomCortoCat
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    private function normalizarDatos(array $datos): array {

        return [
            'nombEquipo' => trim((string) ($datos['nombEquipo'] ?? '')),
            'nombRepEq' => $this->nullSiVacio($datos['nombRepEq'] ?? null),
            'numTelRepEq' => $this->nullSiVacio($datos['numTelRepEq'] ?? null),
            'eMailRepEq' => $this->nullSiVacio($datos['eMailRepEq'] ?? null),
            'nomCortoCat' => trim((string) ($datos['nomCortoCat'] ?? '')),
            'perTorneo' => trim((string) ($datos['perTorneo'] ?? '')),
            'cveLiga' => trim((string) ($datos['cveLiga'] ?? '')),
            'activo' => !empty($datos['activo']) ? 1 : 0,
        ];
    }

    private function nullSiVacio(mixed $value): ?string {

        $value = trim((string) ($value ?? ''));

        return $value === '' ? null : $value;
    }
}

