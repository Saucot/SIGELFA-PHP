/*
    SIGELFA - Script 05
    Roles y permisos iniciales de base de datos.

    Este script:
    - Crea roles de base de datos.
    - Asigna permisos básicos.
    - No crea usuarios con contraseñas.
    - No guarda datos sensibles.

    Roles:
    - rol_sigelfa_app       : rol técnico para conexión de la aplicación PHP.
    - rol_sigelfa_gerente   : rol administrativo.
    - rol_sigelfa_asistente : rol operativo.
    - rol_sigelfa_arbitro   : rol para captura de cédulas arbitrales.
*/

USE SIGELFA_DB;
GO

/* ============================================================
   Crear roles si no existen
   ============================================================ */

IF NOT EXISTS (
    SELECT 1
    FROM sys.database_principals
    WHERE name = N'rol_sigelfa_app'
      AND type = 'R'
)
BEGIN
    CREATE ROLE rol_sigelfa_app AUTHORIZATION dbo;
    PRINT 'Rol rol_sigelfa_app creado.';
END
ELSE
BEGIN
    PRINT 'Rol rol_sigelfa_app ya existe.';
END
GO

IF NOT EXISTS (
    SELECT 1
    FROM sys.database_principals
    WHERE name = N'rol_sigelfa_gerente'
      AND type = 'R'
)
BEGIN
    CREATE ROLE rol_sigelfa_gerente AUTHORIZATION dbo;
    PRINT 'Rol rol_sigelfa_gerente creado.';
END
ELSE
BEGIN
    PRINT 'Rol rol_sigelfa_gerente ya existe.';
END
GO

IF NOT EXISTS (
    SELECT 1
    FROM sys.database_principals
    WHERE name = N'rol_sigelfa_asistente'
      AND type = 'R'
)
BEGIN
    CREATE ROLE rol_sigelfa_asistente AUTHORIZATION dbo;
    PRINT 'Rol rol_sigelfa_asistente creado.';
END
ELSE
BEGIN
    PRINT 'Rol rol_sigelfa_asistente ya existe.';
END
GO

IF NOT EXISTS (
    SELECT 1
    FROM sys.database_principals
    WHERE name = N'rol_sigelfa_arbitro'
      AND type = 'R'
)
BEGIN
    CREATE ROLE rol_sigelfa_arbitro AUTHORIZATION dbo;
    PRINT 'Rol rol_sigelfa_arbitro creado.';
END
ELSE
BEGIN
    PRINT 'Rol rol_sigelfa_arbitro ya existe.';
END
GO

/* ============================================================
   Permisos para rol técnico de la aplicación PHP

   Este rol será usado por el usuario técnico de conexión.
   La idea es que PHP no se conecte como administrador.
   ============================================================ */

GRANT SELECT ON dbo.Liga TO rol_sigelfa_app;
GRANT SELECT ON dbo.Torneo TO rol_sigelfa_app;
GRANT SELECT ON dbo.Categoria TO rol_sigelfa_app;
GRANT SELECT ON dbo.Equipo TO rol_sigelfa_app;
GRANT SELECT ON dbo.Posicion TO rol_sigelfa_app;
GRANT SELECT ON dbo.Jugador TO rol_sigelfa_app;
GRANT SELECT ON dbo.Arbitro TO rol_sigelfa_app;
GRANT SELECT ON dbo.UnDeportiva TO rol_sigelfa_app;
GRANT SELECT ON dbo.Cancha TO rol_sigelfa_app;
GRANT SELECT ON dbo.Jornada TO rol_sigelfa_app;
GRANT SELECT ON dbo.Partido TO rol_sigelfa_app;
GRANT SELECT ON dbo.CedulaArbitral TO rol_sigelfa_app;
GRANT SELECT ON dbo.TipoEventoPartido TO rol_sigelfa_app;
GRANT SELECT ON dbo.EventoPartido TO rol_sigelfa_app;

GRANT EXECUTE ON dbo.sp_registrar_cedula_arbitral TO rol_sigelfa_app;
GRANT EXECUTE ON dbo.sp_agregar_evento_cedula TO rol_sigelfa_app;
GO

/* ============================================================
   Permisos para rol árbitro

   El árbitro puede consultar datos necesarios y registrar cédulas
   mediante procedimientos almacenados.
   ============================================================ */

GRANT SELECT ON dbo.Equipo TO rol_sigelfa_arbitro;
GRANT SELECT ON dbo.Posicion TO rol_sigelfa_arbitro;
GRANT SELECT ON dbo.Jugador TO rol_sigelfa_arbitro;
GRANT SELECT ON dbo.Arbitro TO rol_sigelfa_arbitro;
GRANT SELECT ON dbo.Jornada TO rol_sigelfa_arbitro;
GRANT SELECT ON dbo.Partido TO rol_sigelfa_arbitro;
GRANT SELECT ON dbo.CedulaArbitral TO rol_sigelfa_arbitro;
GRANT SELECT ON dbo.TipoEventoPartido TO rol_sigelfa_arbitro;
GRANT SELECT ON dbo.EventoPartido TO rol_sigelfa_arbitro;

GRANT EXECUTE ON dbo.sp_registrar_cedula_arbitral TO rol_sigelfa_arbitro;
GRANT EXECUTE ON dbo.sp_agregar_evento_cedula TO rol_sigelfa_arbitro;
GO

/* ============================================================
   Permisos para rol asistente

   Por ahora puede consultar y registrar información básica.
   Más adelante ajustaremos cuando existan movimientos económicos.
   ============================================================ */

GRANT SELECT ON dbo.Liga TO rol_sigelfa_asistente;
GRANT SELECT ON dbo.Torneo TO rol_sigelfa_asistente;
GRANT SELECT ON dbo.Categoria TO rol_sigelfa_asistente;
GRANT SELECT, INSERT, UPDATE ON dbo.Equipo TO rol_sigelfa_asistente;
GRANT SELECT, INSERT, UPDATE ON dbo.Jugador TO rol_sigelfa_asistente;
GRANT SELECT ON dbo.Posicion TO rol_sigelfa_asistente;
GRANT SELECT ON dbo.Arbitro TO rol_sigelfa_asistente;
GRANT SELECT ON dbo.Jornada TO rol_sigelfa_asistente;
GRANT SELECT ON dbo.Partido TO rol_sigelfa_asistente;
GO

/* ============================================================
   Permisos para rol gerente

   El gerente tiene permisos más amplios.
   Evitamos dar db_owner para no darle control total innecesario.
   ============================================================ */

GRANT SELECT, INSERT, UPDATE ON dbo.Liga TO rol_sigelfa_gerente;
GRANT SELECT, INSERT, UPDATE ON dbo.Torneo TO rol_sigelfa_gerente;
GRANT SELECT, INSERT, UPDATE ON dbo.Categoria TO rol_sigelfa_gerente;
GRANT SELECT, INSERT, UPDATE ON dbo.Equipo TO rol_sigelfa_gerente;
GRANT SELECT, INSERT, UPDATE ON dbo.Posicion TO rol_sigelfa_gerente;
GRANT SELECT, INSERT, UPDATE ON dbo.Jugador TO rol_sigelfa_gerente;
GRANT SELECT, INSERT, UPDATE ON dbo.Arbitro TO rol_sigelfa_gerente;
GRANT SELECT, INSERT, UPDATE ON dbo.UnDeportiva TO rol_sigelfa_gerente;
GRANT SELECT, INSERT, UPDATE ON dbo.Cancha TO rol_sigelfa_gerente;
GRANT SELECT, INSERT, UPDATE ON dbo.Jornada TO rol_sigelfa_gerente;
GRANT SELECT, INSERT, UPDATE ON dbo.Partido TO rol_sigelfa_gerente;
GRANT SELECT ON dbo.CedulaArbitral TO rol_sigelfa_gerente;
GRANT SELECT ON dbo.TipoEventoPartido TO rol_sigelfa_gerente;
GRANT SELECT ON dbo.EventoPartido TO rol_sigelfa_gerente;

GRANT EXECUTE ON dbo.sp_registrar_cedula_arbitral TO rol_sigelfa_gerente;
GRANT EXECUTE ON dbo.sp_agregar_evento_cedula TO rol_sigelfa_gerente;
GO

/* ============================================================
   Verificación de roles creados
   ============================================================ */

SELECT
    name AS rol,
    type_desc
FROM sys.database_principals
WHERE type = 'R'
  AND name LIKE 'rol_sigelfa_%'
ORDER BY name;
GO

/* ============================================================
   Verificación de permisos asignados
   ============================================================ */

SELECT
    dp_principal.name AS rol,
    perm.permission_name AS permiso,
    perm.state_desc AS estado,
    OBJECT_SCHEMA_NAME(perm.major_id) AS esquema,
    OBJECT_NAME(perm.major_id) AS objeto
FROM sys.database_permissions perm
INNER JOIN sys.database_principals dp_principal
    ON perm.grantee_principal_id = dp_principal.principal_id
WHERE dp_principal.name LIKE 'rol_sigelfa_%'
ORDER BY
    dp_principal.name,
    OBJECT_NAME(perm.major_id),
    perm.permission_name;
GO