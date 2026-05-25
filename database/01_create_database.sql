/*
    SIGELFA - Script 01
    Creación de la base de datos principal.
*/

-- Validamos si la base de datos ya existe en el servidor
IF NOT EXISTS (SELECT name FROM sys.databases WHERE name = N'SIGELFA_DB')
BEGIN
    -- Si no existe, la creamos
    CREATE DATABASE SIGELFA_DB;
    PRINT 'Base de datos SIGELFA_DB creada correctamente.';
END
ELSE
BEGIN
    -- Si ya existe, avisamos al usuario
    PRINT 'La base de datos SIGELFA_DB ya existe.';
END
GO