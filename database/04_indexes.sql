/*
    SIGELFA - Script 04
    Índices para optimizar consultas principales.

    Este script trabaja sobre la tabla Equipo.

    Importante:
    - La tabla Equipo ya tiene una llave primaria agrupada:
      PK_Equipo sobre cveEquipo.
    - Por esa razón, no se puede crear otro índice agrupado sobre nombEquipo.
    - Se crea un índice NO agrupado sobre nombEquipo.
*/

USE SIGELFA_DB;
GO

/* ============================================================
   Ver índices actuales de la tabla Equipo
   ============================================================ */
SELECT 
    t.name AS tabla,
    i.name AS indice,
    i.type_desc AS tipo_indice,
    i.is_primary_key AS es_llave_primaria,
    i.is_unique AS es_unico
FROM sys.indexes i
INNER JOIN sys.tables t
    ON i.object_id = t.object_id
WHERE t.name = N'Equipo'
ORDER BY i.index_id;
GO

/* ============================================================
   NOTA DE PRÁCTICA:

   Si intentáramos crear este índice agrupado:

   CREATE CLUSTERED INDEX IX_Equipo_NombEquipo_Clustered
   ON dbo.Equipo(nombEquipo);

   SQL Server marcaría error porque Equipo ya tiene un índice
   agrupado creado por la llave primaria PK_Equipo.

   Por eso se usa un índice NO agrupado.
   ============================================================ */

/* ============================================================
   Índice no agrupado por nombre de equipo
   Sirve para búsquedas y ordenamientos por nombEquipo.
   ============================================================ */
IF NOT EXISTS (
    SELECT 1
    FROM sys.indexes
    WHERE name = N'IX_Equipo_NombEquipo'
      AND object_id = OBJECT_ID(N'dbo.Equipo')
)
BEGIN
    CREATE NONCLUSTERED INDEX IX_Equipo_NombEquipo
    ON dbo.Equipo(nombEquipo);

    PRINT 'Índice IX_Equipo_NombEquipo creado.';
END
ELSE
BEGIN
    PRINT 'El índice IX_Equipo_NombEquipo ya existe.';
END
GO

/* ============================================================
   Índice no agrupado para búsquedas por categoría, torneo y liga.
   Sirve cuando filtremos equipos por:
   - nomCortoCat
   - perTorneo
   - cveLiga
   ============================================================ */
IF NOT EXISTS (
    SELECT 1
    FROM sys.indexes
    WHERE name = N'IX_Equipo_CategoriaTorneoLiga'
      AND object_id = OBJECT_ID(N'dbo.Equipo')
)
BEGIN
    CREATE NONCLUSTERED INDEX IX_Equipo_CategoriaTorneoLiga
    ON dbo.Equipo(nomCortoCat, perTorneo, cveLiga);

    PRINT 'Índice IX_Equipo_CategoriaTorneoLiga creado.';
END
ELSE
BEGIN
    PRINT 'El índice IX_Equipo_CategoriaTorneoLiga ya existe.';
END
GO

/* ============================================================
   Verificación final de índices
   ============================================================ */
SELECT 
    t.name AS tabla,
    i.name AS indice,
    i.type_desc AS tipo_indice,
    i.is_primary_key AS es_llave_primaria,
    i.is_unique AS es_unico
FROM sys.indexes i
INNER JOIN sys.tables t
    ON i.object_id = t.object_id
WHERE t.name = N'Equipo'
ORDER BY i.index_id;
GO

/* ============================================================
   Consultas de prueba para revisar el plan de ejecución
   ============================================================ */

SELECT * 
FROM dbo.Equipo;
GO

SELECT 
    cveEquipo,
    nombEquipo,
    nomCortoCat,
    perTorneo,
    cveLiga
FROM dbo.Equipo
WHERE nombEquipo = N'Halcones FC';
GO

SELECT 
    cveEquipo,
    nombEquipo,
    nomCortoCat,
    perTorneo,
    cveLiga
FROM dbo.Equipo
WHERE nomCortoCat = 'LIB'
  AND perTorneo = '2026A'
  AND cveLiga = 'LAF'
ORDER BY nombEquipo;
GO