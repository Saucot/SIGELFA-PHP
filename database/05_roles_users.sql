/*
    SIGELFA - Script 05
    Roles iniciales de base de datos.

    Importante:
    - Este script crea roles dentro de SIGELFA_DB.
    - Todavía no asigna permisos finales porque aún no hemos creado todas las tablas.
    - No contiene contraseñas reales.
*/

USE SIGELFA_DB;
GO

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

SELECT 
    name AS rol,
    type_desc
FROM sys.database_principals
WHERE type = 'R'
  AND name LIKE 'rol_sigelfa_%'
ORDER BY name;
GO