/*
    SIGELFA - Script 03
    Datos iniciales de prueba.

    Este script inserta:
    - Liga
    - Torneo
    - Categoria
    - Equipos
    - Posiciones
    - Jugadores
    - Árbitros
    - Unidad deportiva
    - Cancha
    - Jornada 1
    - Partidos programados
*/

USE SIGELFA_DB;
GO

/* ============================================================
   Posiciones
   ============================================================ */
IF NOT EXISTS (SELECT 1 FROM dbo.Posicion)
BEGIN
    INSERT INTO dbo.Posicion (nombrePosicion, abreviatura)
    VALUES
        (N'Portero', 'POR'),
        (N'Defensa', 'DEF'),
        (N'Mediocampista', 'MED'),
        (N'Delantero', 'DEL');

    PRINT 'Posiciones insertadas.';
END
ELSE
BEGIN
    PRINT 'Las posiciones ya existen.';
END
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
        eMailLiga
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
        'contacto@sigelfa.local'
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
    SELECT 1
    FROM dbo.Equipo
    WHERE nombEquipo = N'Halcones FC'
      AND nomCortoCat = 'LIB'
      AND perTorneo = '2026A'
      AND cveLiga = 'LAF'
)
BEGIN
    INSERT INTO dbo.Equipo (
        nombEquipo,
        nombRepEq,
        numTelRepEq,
        eMailRepEq,
        nomCortoCat,
        perTorneo,
        cveLiga
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
   Árbitros iniciales
   ============================================================ */
IF NOT EXISTS (SELECT 1 FROM dbo.Arbitro WHERE numArb = 'A001')
BEGIN
    INSERT INTO dbo.Arbitro (
        numArb,
        nomArb,
        apPatArb,
        apMatArb,
        telArb,
        eMailArb
    )
    VALUES
        ('A001', N'Luis', N'Ramírez', N'Torres', '8711000001', 'luis.ramirez@sigelfa.local'),
        ('A002', N'Marco', N'Hernández', N'López', '8711000002', 'marco.hernandez@sigelfa.local');

    PRINT 'Árbitros iniciales insertados.';
END
ELSE
BEGIN
    PRINT 'Los árbitros iniciales ya existen.';
END
GO

/* ============================================================
   Unidad deportiva inicial
   ============================================================ */
IF NOT EXISTS (SELECT 1 FROM dbo.UnDeportiva WHERE cveUd = 'UD01')
BEGIN
    INSERT INTO dbo.UnDeportiva (
        cveUd,
        nombUd,
        nombAdmUd,
        calleYNumUd,
        colUd,
        cpUd,
        ciudadUd,
        edoUd,
        telUd,
        eMailUd
    )
    VALUES (
        'UD01',
        N'Unidad Deportiva SIGELFA',
        N'Administrador Unidad',
        N'Av. Principal #100',
        N'Centro',
        '27000',
        N'Torreón',
        N'Coahuila',
        '8712000000',
        'unidad@sigelfa.local'
    );

    PRINT 'Unidad deportiva inicial insertada.';
END
ELSE
BEGIN
    PRINT 'La unidad deportiva inicial ya existe.';
END
GO

/* ============================================================
   Cancha inicial
   ============================================================ */
IF NOT EXISTS (
    SELECT 1
    FROM dbo.Cancha
    WHERE numCancha = 1
      AND cveUd = 'UD01'
)
BEGIN
    INSERT INTO dbo.Cancha (
        numCancha,
        tipoCancha,
        cveUd
    )
    VALUES (
        1,
        N'Fútbol 11',
        'UD01'
    );

    PRINT 'Cancha inicial insertada.';
END
ELSE
BEGIN
    PRINT 'La cancha inicial ya existe.';
END
GO

/* ============================================================
   Jugadores iniciales
   Solo insertamos algunos jugadores de prueba.
   ============================================================ */
;WITH JugadoresSeed AS (
    SELECT 'J001' AS numJug, N'Carlos' AS nomJug, N'Pérez' AS apPatJug, N'Gómez' AS apMatJug, '2001-05-10' AS fechaNacJug, 24 AS edadJug, '8713000001' AS telJug, 'carlos@sigelfa.local' AS eMailJug, 1 AS numeroCamiseta, N'Halcones FC' AS nombEquipo, 'POR' AS abreviatura
    UNION ALL
    SELECT 'J002', N'Juan', N'López', N'Martínez', '2000-03-15', 25, '8713000002', 'juan@sigelfa.local', 9, N'Halcones FC', 'DEL'
    UNION ALL
    SELECT 'J003', N'Pedro', N'Santos', N'Ruiz', '1999-07-20', 26, '8713000003', 'pedro@sigelfa.local', 5, N'Halcones FC', 'DEF'
    UNION ALL
    SELECT 'J004', N'Raúl', N'Medina', N'Cruz', '2002-11-02', 23, '8713000004', 'raul@sigelfa.local', 10, N'Halcones FC', 'MED'
    UNION ALL
    SELECT 'J005', N'Andrés', N'Flores', N'Nava', '2001-01-12', 25, '8713000005', 'andres@sigelfa.local', 1, N'Tigres Laguna', 'POR'
    UNION ALL
    SELECT 'J006', N'Miguel', N'Castro', N'Luna', '2000-08-18', 25, '8713000006', 'miguel@sigelfa.local', 7, N'Tigres Laguna', 'MED'
    UNION ALL
    SELECT 'J007', N'Jorge', N'Rivas', N'Ortiz', '1998-04-25', 27, '8713000007', 'jorge@sigelfa.local', 11, N'Tigres Laguna', 'DEL'
    UNION ALL
    SELECT 'J008', N'Daniel', N'Vargas', N'Soto', '2003-02-09', 22, '8713000008', 'daniel@sigelfa.local', 4, N'Tigres Laguna', 'DEF'
)
INSERT INTO dbo.Jugador (
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
    idPosicion
)
SELECT
    js.numJug,
    js.nomJug,
    js.apPatJug,
    js.apMatJug,
    js.fechaNacJug,
    js.edadJug,
    js.telJug,
    js.eMailJug,
    js.numeroCamiseta,
    e.cveEquipo,
    p.idPosicion
FROM JugadoresSeed js
INNER JOIN dbo.Equipo e
    ON e.nombEquipo = js.nombEquipo
   AND e.nomCortoCat = 'LIB'
   AND e.perTorneo = '2026A'
   AND e.cveLiga = 'LAF'
INNER JOIN dbo.Posicion p
    ON p.abreviatura = js.abreviatura
WHERE NOT EXISTS (
    SELECT 1
    FROM dbo.Jugador j
    WHERE j.numJug = js.numJug
);
GO

PRINT 'Jugadores iniciales verificados.';
GO

/* ============================================================
   Jornada 1
   Usamos Equipo A y Equipo B, no local/visitante.
   ============================================================ */
;WITH ParesJornada AS (
    SELECT 1 AS numJornada, N'Halcones FC' AS equipoA, N'Tigres Laguna' AS equipoB, CAST('2026-01-20' AS DATE) AS fechaProgramada
    UNION ALL
    SELECT 1, N'Guerreros Norte', N'Atlético Centro', CAST('2026-01-20' AS DATE)
    UNION ALL
    SELECT 1, N'Deportivo Unión', N'Rayos del Sur', CAST('2026-01-20' AS DATE)
    UNION ALL
    SELECT 1, N'Leones FC', N'Panteras Rojas', CAST('2026-01-20' AS DATE)
    UNION ALL
    SELECT 1, N'Club Victoria', N'Aztecas Laguna', CAST('2026-01-20' AS DATE)
    UNION ALL
    SELECT 1, N'Fénix FC', N'Cóndores FC', CAST('2026-01-20' AS DATE)
    UNION ALL
    SELECT 1, N'Titanes FC', N'Dragones FC', CAST('2026-01-20' AS DATE)
    UNION ALL
    SELECT 1, N'Lobos Laguna', N'Real Alameda', CAST('2026-01-20' AS DATE)
    UNION ALL
    SELECT 1, N'Inter Torreón', N'Santos Amateur', CAST('2026-01-20' AS DATE)
)
INSERT INTO dbo.Jornada (
    numJornada,
    cveEquipoA,
    cveEquipoB,
    nomCortoCat,
    perTorneo,
    cveLiga,
    fechaProgramada
)
SELECT
    pj.numJornada,
    ea.cveEquipo,
    eb.cveEquipo,
    'LIB',
    '2026A',
    'LAF',
    pj.fechaProgramada
FROM ParesJornada pj
INNER JOIN dbo.Equipo ea
    ON ea.nombEquipo = pj.equipoA
   AND ea.nomCortoCat = 'LIB'
   AND ea.perTorneo = '2026A'
   AND ea.cveLiga = 'LAF'
INNER JOIN dbo.Equipo eb
    ON eb.nombEquipo = pj.equipoB
   AND eb.nomCortoCat = 'LIB'
   AND eb.perTorneo = '2026A'
   AND eb.cveLiga = 'LAF'
WHERE NOT EXISTS (
    SELECT 1
    FROM dbo.Jornada j
    WHERE j.numJornada = pj.numJornada
      AND j.cveEquipoA = ea.cveEquipo
      AND j.cveEquipoB = eb.cveEquipo
      AND j.nomCortoCat = 'LIB'
      AND j.perTorneo = '2026A'
      AND j.cveLiga = 'LAF'
);
GO

PRINT 'Jornada 1 verificada.';
GO

/* ============================================================
   Partidos programados para la Jornada 1
   ============================================================ */
INSERT INTO dbo.Partido (
    idJornada,
    fechaPart,
    horaPart,
    numCancha,
    cveUd,
    numArb
)
SELECT
    j.idJornada,
    j.fechaProgramada,
    CONVERT(TIME(0), '18:00'),
    1,
    'UD01',
    'A001'
FROM dbo.Jornada j
WHERE j.numJornada = 1
  AND j.nomCortoCat = 'LIB'
  AND j.perTorneo = '2026A'
  AND j.cveLiga = 'LAF'
  AND NOT EXISTS (
      SELECT 1
      FROM dbo.Partido p
      WHERE p.idJornada = j.idJornada
  );
GO

PRINT 'Partidos de Jornada 1 verificados.';
GO

/* ============================================================
   Verificaciones finales
   ============================================================ */
SELECT COUNT(*) AS total_ligas FROM dbo.Liga;
SELECT COUNT(*) AS total_torneos FROM dbo.Torneo;
SELECT COUNT(*) AS total_categorias FROM dbo.Categoria;
SELECT COUNT(*) AS total_equipos FROM dbo.Equipo;
SELECT COUNT(*) AS total_posiciones FROM dbo.Posicion;
SELECT COUNT(*) AS total_jugadores FROM dbo.Jugador;
SELECT COUNT(*) AS total_arbitros FROM dbo.Arbitro;
SELECT COUNT(*) AS total_unidades_deportivas FROM dbo.UnDeportiva;
SELECT COUNT(*) AS total_canchas FROM dbo.Cancha;
SELECT COUNT(*) AS total_jornadas FROM dbo.Jornada;
SELECT COUNT(*) AS total_partidos FROM dbo.Partido;
GO

SELECT
    j.numJornada,
    ea.nombEquipo AS equipoA,
    eb.nombEquipo AS equipoB,
    j.fechaProgramada,
    p.fechaPart,
    p.horaPart,
    a.nomArb + N' ' + ISNULL(a.apPatArb, N'') AS arbitro
FROM dbo.Jornada j
INNER JOIN dbo.Equipo ea
    ON j.cveEquipoA = ea.cveEquipo
INNER JOIN dbo.Equipo eb
    ON j.cveEquipoB = eb.cveEquipo
LEFT JOIN dbo.Partido p
    ON j.idJornada = p.idJornada
LEFT JOIN dbo.Arbitro a
    ON p.numArb = a.numArb
ORDER BY j.numJornada, j.idJornada;
GO

SELECT
    ju.numJug,
    ju.nomJug,
    ju.apPatJug,
    ju.numeroCamiseta,
    e.nombEquipo,
    pos.nombrePosicion
FROM dbo.Jugador ju
INNER JOIN dbo.Equipo e
    ON ju.cveEquipo = e.cveEquipo
INNER JOIN dbo.Posicion pos
    ON ju.idPosicion = pos.idPosicion
ORDER BY e.nombEquipo, ju.numeroCamiseta;
GO