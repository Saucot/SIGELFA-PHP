/*
    SIGELFA - Script 01
    Crear base de datos principal del sistema.

    Este script se ejecuta desde SQL Server Management Studio.
    No elimina bases de datos existentes.
*/

USE master;
GO

IF DB_ID(N'SIGELFA_DB') IS NULL
BEGIN
    CREATE DATABASE SIGELFA_DB;
    PRINT 'Base de datos SIGELFA_DB creada correctamente.';
END
ELSE
BEGIN
    PRINT 'La base de datos SIGELFA_DB ya existe. No se creó nuevamente.';
END
GO

SELECT 
    name AS nombre_base_datos,
    database_id,
    create_date
FROM sys.databases
WHERE name = N'SIGELFA_DB';
GO

USE SIGELFA_DB;
GO

SELECT DB_NAME() AS base_actual;
GO