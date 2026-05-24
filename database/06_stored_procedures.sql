/*
    SIGELFA - Script 06
    Procedimientos almacenados.

    Este script crea procedimientos para operaciones importantes
    de la base de datos.

    Procedimiento incluido:
    - sp_registrar_cedula_arbitral

    Objetivo:
    Registrar una cédula arbitral dentro de una transacción.
*/

USE SIGELFA_DB;
GO

CREATE OR ALTER PROCEDURE dbo.sp_registrar_cedula_arbitral
    @idPartido INT,
    @numArb VARCHAR(4),
    @golesEquipoA INT,
    @golesEquipoB INT,
    @observacionesGenerales NVARCHAR(800) = NULL,
    @idCedula INT OUTPUT
AS
BEGIN
    SET NOCOUNT ON;
    SET XACT_ABORT ON;

    BEGIN TRY
        BEGIN TRANSACTION;

        /* Validar partido */
        IF NOT EXISTS (
            SELECT 1
            FROM dbo.Partido
            WHERE idPartido = @idPartido
        )
        BEGIN
            THROW 50001, 'El partido indicado no existe.', 1;
        END;

        /* Validar árbitro */
        IF NOT EXISTS (
            SELECT 1
            FROM dbo.Arbitro
            WHERE numArb = @numArb
              AND activo = 1
        )
        BEGIN
            THROW 50002, 'El árbitro indicado no existe o no está activo.', 1;
        END;

        /* Validar que no exista ya una cédula para ese partido */
        IF EXISTS (
            SELECT 1
            FROM dbo.CedulaArbitral
            WHERE idPartido = @idPartido
        )
        BEGIN
            THROW 50003, 'El partido ya tiene una cédula registrada.', 1;
        END;

        /* Validar goles */
        IF @golesEquipoA < 0 OR @golesEquipoB < 0
        BEGIN
            THROW 50004, 'Los goles no pueden ser negativos.', 1;
        END;

        /* Insertar encabezado de cédula */
        INSERT INTO dbo.CedulaArbitral (
            idPartido,
            numArb,
            estadoCedula,
            observacionesGenerales
        )
        VALUES (
            @idPartido,
            @numArb,
            'CERRADA',
            @observacionesGenerales
        );

        SET @idCedula = SCOPE_IDENTITY();

        /* Actualizar datos del partido */
        UPDATE dbo.Partido
        SET
            numArb = @numArb,
            golesEquipoA = @golesEquipoA,
            golesEquipoB = @golesEquipoB,
            estadoPartido = 'JUGADO',
            observaciones = @observacionesGenerales
        WHERE idPartido = @idPartido;

        /* Actualizar estado de la jornada relacionada */
        UPDATE j
        SET j.estadoJornada = 'JUGADA'
        FROM dbo.Jornada j
        INNER JOIN dbo.Partido p
            ON j.idJornada = p.idJornada
        WHERE p.idPartido = @idPartido;

        COMMIT TRANSACTION;
    END TRY
    BEGIN CATCH
        IF @@TRANCOUNT > 0
        BEGIN
            ROLLBACK TRANSACTION;
        END;

        DECLARE @MensajeError NVARCHAR(4000);
        SET @MensajeError = ERROR_MESSAGE();

        THROW 51000, @MensajeError, 1;
    END CATCH
END;
GO

/* ============================================================
   Verificación de procedimiento creado
   ============================================================ */
SELECT 
    name AS procedimiento,
    type_desc
FROM sys.objects
WHERE type = 'P'
  AND name = 'sp_registrar_cedula_arbitral';
GO







/* ============================================================
   Procedimiento: sp_agregar_evento_cedula

   Objetivo:
   Registrar un evento dentro de una cédula arbitral.

   Ejemplos de eventos:
   - Gol
   - Autogol
   - Tarjeta amarilla
   - Tarjeta roja
   - Observación

   Este procedimiento usa transacción para evitar registros incompletos.
   ============================================================ */
CREATE OR ALTER PROCEDURE dbo.sp_agregar_evento_cedula
    @idCedula INT,
    @abreviaturaEvento VARCHAR(10),
    @numJug VARCHAR(4) = NULL,
    @cveEquipo INT,
    @minuto INT = NULL,
    @observacion NVARCHAR(300) = NULL,
    @idEvento INT OUTPUT
AS
BEGIN
    SET NOCOUNT ON;
    SET XACT_ABORT ON;

    DECLARE @idTipoEvento TINYINT;
    DECLARE @idPartido INT;
    DECLARE @idJornada INT;
    DECLARE @estadoCedula VARCHAR(20);

    BEGIN TRY
        BEGIN TRANSACTION;

        /* Validar que exista la cédula */
        SELECT 
            @idPartido = ca.idPartido,
            @estadoCedula = ca.estadoCedula
        FROM dbo.CedulaArbitral ca
        WHERE ca.idCedula = @idCedula;

        IF @idPartido IS NULL
        BEGIN
            THROW 52001, 'La cédula indicada no existe.', 1;
        END;

        IF @estadoCedula = 'CANCELADA'
        BEGIN
            THROW 52002, 'No se pueden agregar eventos a una cédula cancelada.', 1;
        END;

        /* Obtener jornada del partido */
        SELECT @idJornada = p.idJornada
        FROM dbo.Partido p
        WHERE p.idPartido = @idPartido;

        IF @idJornada IS NULL
        BEGIN
            THROW 52003, 'El partido relacionado con la cédula no existe.', 1;
        END;

        /* Validar tipo de evento */
        SELECT @idTipoEvento = idTipoEvento
        FROM dbo.TipoEventoPartido
        WHERE abreviatura = @abreviaturaEvento;

        IF @idTipoEvento IS NULL
        BEGIN
            THROW 52004, 'El tipo de evento indicado no existe.', 1;
        END;

        /* Validar minuto */
        IF @minuto IS NOT NULL AND (@minuto < 0 OR @minuto > 150)
        BEGIN
            THROW 52005, 'El minuto del evento debe estar entre 0 y 150.', 1;
        END;

        /* Validar que el equipo pertenezca al partido */
        IF NOT EXISTS (
            SELECT 1
            FROM dbo.Jornada j
            WHERE j.idJornada = @idJornada
              AND @cveEquipo IN (j.cveEquipoA, j.cveEquipoB)
        )
        BEGIN
            THROW 52006, 'El equipo indicado no pertenece al partido de la cédula.', 1;
        END;

        /* Para eventos que no son observación, pedimos jugador */
        IF @abreviaturaEvento <> 'OBS' AND @numJug IS NULL
        BEGIN
            THROW 52007, 'Este tipo de evento requiere indicar un jugador.', 1;
        END;

        /* Si se indicó jugador, validar que exista y pertenezca al equipo */
        IF @numJug IS NOT NULL
        BEGIN
            IF NOT EXISTS (
                SELECT 1
                FROM dbo.Jugador
                WHERE numJug = @numJug
                  AND cveEquipo = @cveEquipo
                  AND activo = 1
            )
            BEGIN
                THROW 52008, 'El jugador no existe, no está activo o no pertenece al equipo indicado.', 1;
            END;
        END;

        /* Insertar evento */
        INSERT INTO dbo.EventoPartido (
            idCedula,
            idTipoEvento,
            numJug,
            cveEquipo,
            minuto,
            observacion
        )
        VALUES (
            @idCedula,
            @idTipoEvento,
            @numJug,
            @cveEquipo,
            @minuto,
            @observacion
        );

        SET @idEvento = SCOPE_IDENTITY();

        COMMIT TRANSACTION;
    END TRY
    BEGIN CATCH
        IF @@TRANCOUNT > 0
        BEGIN
            ROLLBACK TRANSACTION;
        END;

        DECLARE @MensajeError NVARCHAR(4000);
        SET @MensajeError = ERROR_MESSAGE();

        THROW 52099, @MensajeError, 1;
    END CATCH
END;
GO