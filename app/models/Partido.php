<?php

require_once __DIR__ . '/Database.php';

class Partido {

    private PDO $conn;

    public function __construct() {

        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function obtenerTodos(): array {

        $query = "
            SELECT
                p.idPartido,
                p.idJornada,
                p.fechaPart,
                CONVERT(VARCHAR(5), p.horaPart, 108) AS horaPart,
                p.numCancha,
                p.cveUd,
                p.numArb,
                p.golesEquipoA,
                p.golesEquipoB,
                p.estadoPartido,
                j.numJornada,
                j.nomCortoCat,
                j.perTorneo,
                j.cveLiga,
                j.cveEquipoA,
                j.cveEquipoB,
                ea.nombEquipo AS equipoA,
                eb.nombEquipo AS equipoB,
                a.nomArb,
                a.apPatArb,
                a.apMatArb,
                ca.idCedula,
                ca.estadoCedula
            FROM dbo.Partido p
            INNER JOIN dbo.Jornada j ON p.idJornada = j.idJornada
            INNER JOIN dbo.Equipo ea ON j.cveEquipoA = ea.cveEquipo
            INNER JOIN dbo.Equipo eb ON j.cveEquipoB = eb.cveEquipo
            LEFT JOIN dbo.Arbitro a ON p.numArb = a.numArb
            LEFT JOIN dbo.CedulaArbitral ca ON p.idPartido = ca.idPartido
            ORDER BY j.numJornada DESC, p.fechaPart DESC, p.idPartido DESC
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function obtenerPorId(int $idPartido): ?array {

        $query = "
            SELECT
                p.idPartido,
                p.idJornada,
                p.fechaPart,
                CONVERT(VARCHAR(5), p.horaPart, 108) AS horaPart,
                p.numCancha,
                p.cveUd,
                p.numArb,
                p.golesEquipoA,
                p.golesEquipoB,
                p.estadoPartido,
                p.observaciones,
                j.numJornada,
                j.nomCortoCat,
                j.perTorneo,
                j.cveLiga,
                j.cveEquipoA,
                j.cveEquipoB,
                ea.nombEquipo AS equipoA,
                eb.nombEquipo AS equipoB,
                a.nomArb,
                a.apPatArb,
                a.apMatArb,
                u.nombUd,
                ca.numCancha,
                ca.tipoCancha,
                c.idCedula,
                c.estadoCedula,
                c.fechaCaptura,
                c.observacionesGenerales
            FROM dbo.Partido p
            INNER JOIN dbo.Jornada j ON p.idJornada = j.idJornada
            INNER JOIN dbo.Equipo ea ON j.cveEquipoA = ea.cveEquipo
            INNER JOIN dbo.Equipo eb ON j.cveEquipoB = eb.cveEquipo
            LEFT JOIN dbo.Arbitro a ON p.numArb = a.numArb
            LEFT JOIN dbo.Cancha ca ON p.numCancha = ca.numCancha AND p.cveUd = ca.cveUd
            LEFT JOIN dbo.UnDeportiva u ON p.cveUd = u.cveUd
            LEFT JOIN dbo.CedulaArbitral c ON p.idPartido = c.idPartido
            WHERE p.idPartido = :idPartido
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':idPartido', $idPartido, PDO::PARAM_INT);
        $stmt->execute();

        $partido = $stmt->fetch();

        return $partido ?: null;
    }

    public function obtenerCedulaPorPartido(int $idPartido): ?array {

        $query = "
            SELECT
                c.idCedula,
                c.idPartido,
                c.numArb,
                c.fechaCaptura,
                c.estadoCedula,
                c.observacionesGenerales,
                p.golesEquipoA,
                p.golesEquipoB,
                a.nomArb,
                a.apPatArb,
                a.apMatArb
            FROM dbo.CedulaArbitral c
            INNER JOIN dbo.Partido p ON c.idPartido = p.idPartido
            INNER JOIN dbo.Arbitro a ON c.numArb = a.numArb
            WHERE c.idPartido = :idPartido
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':idPartido', $idPartido, PDO::PARAM_INT);
        $stmt->execute();

        $cedula = $stmt->fetch();

        return $cedula ?: null;
    }

    public function obtenerCedulaPorId(int $idCedula): ?array {

        $query = "
            SELECT
                c.idCedula,
                c.idPartido,
                c.numArb,
                c.fechaCaptura,
                c.estadoCedula,
                c.observacionesGenerales,
                p.golesEquipoA,
                p.golesEquipoB,
                a.nomArb,
                a.apPatArb,
                a.apMatArb
            FROM dbo.CedulaArbitral c
            INNER JOIN dbo.Partido p ON c.idPartido = p.idPartido
            INNER JOIN dbo.Arbitro a ON c.numArb = a.numArb
            WHERE c.idCedula = :idCedula
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':idCedula', $idCedula, PDO::PARAM_INT);
        $stmt->execute();

        $cedula = $stmt->fetch();

        return $cedula ?: null;
    }

    public function obtenerEventosPorCedula(int $idCedula): array {

        $query = "
            SELECT
                ep.idEvento,
                ep.idCedula,
                ep.idTipoEvento,
                ep.numJug,
                ep.cveEquipo,
                ep.minuto,
                ep.observacion,
                te.nombreEvento,
                te.abreviatura,
                e.nombEquipo,
                j.nomJug,
                j.apPatJug,
                j.apMatJug
            FROM dbo.EventoPartido ep
            INNER JOIN dbo.TipoEventoPartido te ON ep.idTipoEvento = te.idTipoEvento
            INNER JOIN dbo.Equipo e ON ep.cveEquipo = e.cveEquipo
            LEFT JOIN dbo.Jugador j ON ep.numJug = j.numJug
            WHERE ep.idCedula = :idCedula
            ORDER BY COALESCE(ep.minuto, 999), ep.idEvento
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':idCedula', $idCedula, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function obtenerArbitrosActivos(): array {

        $query = "
            SELECT
                numArb,
                nomArb,
                apPatArb,
                apMatArb
            FROM dbo.Arbitro
            WHERE activo = 1
            ORDER BY nomArb, apPatArb, numArb
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function obtenerTiposEvento(): array {

        $query = "
            SELECT
                idTipoEvento,
                nombreEvento,
                abreviatura,
                afectaMarcador
            FROM dbo.TipoEventoPartido
            ORDER BY idTipoEvento
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function obtenerJugadoresPorPartido(int $idPartido): array {

        $query = "
            SELECT
                j.numJug,
                j.nomJug,
                j.apPatJug,
                j.apMatJug,
                j.numeroCamiseta,
                j.cveEquipo,
                e.nombEquipo
            FROM dbo.Jugador j
            INNER JOIN dbo.Equipo e ON j.cveEquipo = e.cveEquipo
            INNER JOIN dbo.Jornada jo ON j.cveEquipo IN (jo.cveEquipoA, jo.cveEquipoB)
            INNER JOIN dbo.Partido p ON jo.idJornada = p.idJornada
            WHERE p.idPartido = :idPartido
              AND j.activo = 1
            ORDER BY e.nombEquipo, j.numeroCamiseta, j.nomJug, j.apPatJug
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':idPartido', $idPartido, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function registrarCedula(array $datos): ?array {

        $query = "
            DECLARE @idCedula INT;
            EXEC dbo.sp_registrar_cedula_arbitral
                @idPartido = :idPartido,
                @numArb = :numArb,
                @golesEquipoA = :golesEquipoA,
                @golesEquipoB = :golesEquipoB,
                @observacionesGenerales = :observacionesGenerales,
                @idCedula = @idCedula OUTPUT;
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            'idPartido' => (int) $datos['idPartido'],
            'numArb' => (string) $datos['numArb'],
            'golesEquipoA' => (int) $datos['golesEquipoA'],
            'golesEquipoB' => (int) $datos['golesEquipoB'],
            'observacionesGenerales' => $this->nullSiVacio($datos['observacionesGenerales'] ?? null),
        ]);

        return $this->obtenerCedulaPorPartido((int) $datos['idPartido']);
    }

    public function agregarEvento(array $datos): ?array {

        $query = "
            DECLARE @idEvento INT;
            EXEC dbo.sp_agregar_evento_cedula
                @idCedula = :idCedula,
                @abreviaturaEvento = :abreviaturaEvento,
                @numJug = :numJug,
                @cveEquipo = :cveEquipo,
                @minuto = :minuto,
                @observacion = :observacion,
                @idEvento = @idEvento OUTPUT;
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            'idCedula' => (int) $datos['idCedula'],
            'abreviaturaEvento' => (string) $datos['abreviaturaEvento'],
            'numJug' => $this->nullSiVacio($datos['numJug'] ?? null),
            'cveEquipo' => (int) $datos['cveEquipo'],
            'minuto' => $this->intONull($datos['minuto'] ?? null),
            'observacion' => $this->nullSiVacio($datos['observacion'] ?? null),
        ]);

        return $this->obtenerCedulaPorId((int) $datos['idCedula']);
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

