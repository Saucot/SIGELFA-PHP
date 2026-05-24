/*
    SIGELFA - Script 03
    Datos iniciales para pruebas.

    Este script inserta:
    - 1 liga
    - 1 torneo
    - 1 categoría
    - 18 equipos
*/

USE SIGELFA_DB;
GO

/* ============================================================
   Liga inicial
   ============================================================ */
IF NOT EXISTS (
    SELECT 1 FROM dbo.Liga WHERE cveLiga = 'LAF'
)
BEGIN
    INSERT INTO dbo.Liga (
        cveLiga,
        nombLiga,
        nombAdmLiga,
        calleYNumLiga,
        colLiga,
        cpLiga,
        ciudadLiga,
        edoLiga,
        telLiga,
        eMailLiga,
        consecutivoMov
    )
    VALUES (
        'LAF',
        N'Liga Amateur de Fútbol',
        N'Administrador SIGELFA',
        N'Domicilio conocido',
        N'Centro',
        '27000',
        N'Torreón',
        N'Coahuila',
        '8710000000',
        'contacto@sigelfa.local',
        1
    );

    PRINT 'Liga inicial insertada.';
END
ELSE
BEGIN
    PRINT 'La liga inicial ya existe.';
END
GO

/* ============================================================
   Torneo inicial
   ============================================================ */
IF NOT EXISTS (
    SELECT 1 
    FROM dbo.Torneo 
    WHERE perTorneo = '2026A' 
      AND cveLiga = 'LAF'
)
BEGIN
    INSERT INTO dbo.Torneo (
        perTorneo,
        nombTorneo,
        fechaIni,
        fechaTer,
        cveLiga
    )
    VALUES (
        '2026A',
        N'Torneo Apertura 2026',
        '2026-01-15',
        '2026-06-30',
        'LAF'
    );

    PRINT 'Torneo inicial insertado.';
END
ELSE
BEGIN
    PRINT 'El torneo inicial ya existe.';
END
GO

/* ============================================================
   Categoría inicial
   ============================================================ */
IF NOT EXISTS (
    SELECT 1 
    FROM dbo.Categoria 
    WHERE nomCortoCat = 'LIB'
      AND perTorneo = '2026A'
      AND cveLiga = 'LAF'
)
BEGIN
    INSERT INTO dbo.Categoria (
        nomCortoCat,
        edadMin,
        edadMaxima,
        cveLiga,
        perTorneo
    )
    VALUES (
        'LIB',
        18,
        99,
        'LAF',
        '2026A'
    );

    PRINT 'Categoría inicial insertada.';
END
ELSE
BEGIN
    PRINT 'La categoría inicial ya existe.';
END
GO

/* ============================================================
   Equipos iniciales
   ============================================================ */
IF NOT EXISTS (
    SELECT 1 FROM dbo.Equipo 
    WHERE nombEquipo = N'Halcones FC'
)
BEGIN
    INSERT INTO dbo.Equipo (
        nombEquipo, nombRepEq, numTelRepEq, eMailRepEq,
        nomCortoCat, perTorneo, cveLiga
    )
    VALUES
    (N'Halcones FC', N'Representante Halcones', '8710000001', 'halcones@sigelfa.local', 'LIB', '2026A', 'LAF'),
    (N'Tigres Laguna', N'Representante Tigres', '8710000002', 'tigres@sigelfa.local', 'LIB', '2026A', 'LAF'),
    (N'Guerreros Norte', N'Representante Guerreros', '8710000003', 'guerreros@sigelfa.local', 'LIB', '2026A', 'LAF'),
    (N'Atlético Centro', N'Representante Atlético', '8710000004', 'atletico@sigelfa.local', 'LIB', '2026A', 'LAF'),
    (N'Deportivo Unión', N'Representante Unión', '8710000005', 'union@sigelfa.local', 'LIB', '2026A', 'LAF'),
    (N'Rayos del Sur', N'Representante Rayos', '8710000006', 'rayos@sigelfa.local', 'LIB', '2026A', 'LAF'),
    (N'Leones FC', N'Representante Leones', '8710000007', 'leones@sigelfa.local', 'LIB', '2026A', 'LAF'),
    (N'Panteras Rojas', N'Representante Panteras', '8710000008', 'panteras@sigelfa.local', 'LIB', '2026A', 'LAF'),
    (N'Club Victoria', N'Representante Victoria', '8710000009', 'victoria@sigelfa.local', 'LIB', '2026A', 'LAF'),
    (N'Aztecas Laguna', N'Representante Aztecas', '8710000010', 'aztecas@sigelfa.local', 'LIB', '2026A', 'LAF'),
    (N'Fénix FC', N'Representante Fénix', '8710000011', 'fenix@sigelfa.local', 'LIB', '2026A', 'LAF'),
    (N'Cóndores FC', N'Representante Cóndores', '8710000012', 'condores@sigelfa.local', 'LIB', '2026A', 'LAF'),
    (N'Titanes FC', N'Representante Titanes', '8710000013', 'titanes@sigelfa.local', 'LIB', '2026A', 'LAF'),
    (N'Dragones FC', N'Representante Dragones', '8710000014', 'dragones@sigelfa.local', 'LIB', '2026A', 'LAF'),
    (N'Lobos Laguna', N'Representante Lobos', '8710000015', 'lobos@sigelfa.local', 'LIB', '2026A', 'LAF'),
    (N'Real Alameda', N'Representante Alameda', '8710000016', 'alameda@sigelfa.local', 'LIB', '2026A', 'LAF'),
    (N'Inter Torreón', N'Representante Inter', '8710000017', 'inter@sigelfa.local', 'LIB', '2026A', 'LAF'),
    (N'Santos Amateur', N'Representante Santos', '8710000018', 'santos@sigelfa.local', 'LIB', '2026A', 'LAF');

    PRINT 'Equipos iniciales insertados.';
END
ELSE
BEGIN
    PRINT 'Los equipos iniciales ya existen.';
END
GO

/* ============================================================
   Verificación
   ============================================================ */
SELECT * FROM dbo.Liga;
SELECT * FROM dbo.Torneo;
SELECT * FROM dbo.Categoria;
SELECT * FROM dbo.Equipo;
GO