/*
    SIGELFA - Script 07
    Prueba controlada de cédula arbitral y eventos.

    Este script:
    - Busca el partido Halcones FC vs Tigres Laguna.
    - Crea la cédula si no existe.
    - Registra eventos de prueba solo si no existen.
    - Evita usar TOP 1 sobre cualquier partido.
*/

USE SIGELFA_DB;
GO

DECLARE @idPartido INT;
DECLARE @idCedula INT;
DECLARE @idEventoGol INT;
DECLARE @idEventoAmarilla INT;
DECLARE @equipoHalcones INT;
DECLARE @equipoTigres INT;

/* ============================================================
   1. Buscar partido Halcones FC vs Tigres Laguna
   ============================================================ */
SELECT
    @idPartido = p.idPartido,
    @equipoHalcones = ea.cveEquipo,
    @equipoTigres = eb.cveEquipo
FROM dbo.Partido p
INNER JOIN dbo.Jornada j
    ON p.idJornada = j.idJornada
INNER JOIN dbo.Equipo ea
    ON j.cveEquipoA = ea.cveEquipo
INNER JOIN dbo.Equipo eb
    ON j.cveEquipoB = eb.cveEquipo
WHERE ea.nombEquipo = N'Halcones FC'
  AND eb.nombEquipo = N'Tigres Laguna';

IF @idPartido IS NULL
BEGIN
    THROW 53001, 'No existe el partido Halcones FC vs Tigres Laguna. Revisa 03_seed.sql.', 1;
END;

PRINT 'Partido encontrado correctamente.';

/* ============================================================
   2. Crear cédula si no existe
   ============================================================ */
SELECT
    @idCedula = idCedula
FROM dbo.CedulaArbitral
WHERE idPartido = @idPartido;

IF @idCedula IS NULL
BEGIN
    EXEC dbo.sp_registrar_cedula_arbitral
        @idPartido = @idPartido,
        @numArb = 'A001',
        @golesEquipoA = 2,
        @golesEquipoB = 1,
        @observacionesGenerales = N'Cédula de prueba para Halcones FC vs Tigres Laguna.',
        @idCedula = @idCedula OUTPUT;

    PRINT 'Cédula creada correctamente.';
END
ELSE
BEGIN
    PRINT 'La cédula ya existía. Se usará la cédula existente.';
END;

/* ============================================================
   3. Registrar gol de J002 si no existe
   ============================================================ */
IF NOT EXISTS (
    SELECT 1
    FROM dbo.EventoPartido ep
    INNER JOIN dbo.TipoEventoPartido te
        ON ep.idTipoEvento = te.idTipoEvento
    WHERE ep.idCedula = @idCedula
      AND te.abreviatura = 'GOL'
      AND ep.numJug = 'J002'
      AND ep.minuto = 15
)
BEGIN
    EXEC dbo.sp_agregar_evento_cedula
        @idCedula = @idCedula,
        @abreviaturaEvento = 'GOL',
        @numJug = 'J002',
        @cveEquipo = @equipoHalcones,
        @minuto = 15,
        @observacion = N'Gol de prueba para Halcones FC.',
        @idEvento = @idEventoGol OUTPUT;

    PRINT 'Evento GOL insertado correctamente.';
END
ELSE
BEGIN
    PRINT 'El evento GOL de prueba ya existía.';
END;

/* ============================================================
   4. Registrar amarilla de J006 si no existe
   ============================================================ */
IF NOT EXISTS (
    SELECT 1
    FROM dbo.EventoPartido ep
    INNER JOIN dbo.TipoEventoPartido te
        ON ep.idTipoEvento = te.idTipoEvento
    WHERE ep.idCedula = @idCedula
      AND te.abreviatura = 'AMARILLA'
      AND ep.numJug = 'J006'
      AND ep.minuto = 42
)
BEGIN
    EXEC dbo.sp_agregar_evento_cedula
        @idCedula = @idCedula,
        @abreviaturaEvento = 'AMARILLA',
        @numJug = 'J006',
        @cveEquipo = @equipoTigres,
        @minuto = 42,
        @observacion = N'Tarjeta amarilla de prueba para Tigres Laguna.',
        @idEvento = @idEventoAmarilla OUTPUT;

    PRINT 'Evento AMARILLA insertado correctamente.';
END
ELSE
BEGIN
    PRINT 'El evento AMARILLA de prueba ya existía.';
END;

/* ============================================================
   5. Consulta final de comprobación
   ============================================================ */
SELECT
    ca.idCedula,
    te.nombreEvento,
    ep.minuto,
    j.numJug,
    j.nomJug,
    j.apPatJug,
    e.nombEquipo,
    ep.observacion
FROM dbo.EventoPartido ep
INNER JOIN dbo.CedulaArbitral ca
    ON ep.idCedula = ca.idCedula
INNER JOIN dbo.TipoEventoPartido te
    ON ep.idTipoEvento = te.idTipoEvento
LEFT JOIN dbo.Jugador j
    ON ep.numJug = j.numJug
INNER JOIN dbo.Equipo e
    ON ep.cveEquipo = e.cveEquipo
WHERE ca.idCedula = @idCedula
ORDER BY ep.minuto, ep.idEvento;
GO