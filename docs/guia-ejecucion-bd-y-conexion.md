# Guía de ejecución de base de datos y conexión - SIGELFA

Esta guía explica cómo preparar la base de datos `SIGELFA_DB`, ejecutar los scripts SQL en orden y configurar el archivo `.env` para la futura conexión entre PHP y SQL Server.

## 1. Requisitos previos

Cada integrante del equipo debe tener instalado:

- Git
- VS Code
- PHP 8.x
- SQL Server
- SQL Server Management Studio
- Drivers de PHP para SQL Server
- ODBC Driver for SQL Server

## 2. Clonar el repositorio

Abrir PowerShell en la carpeta donde se quiera descargar el proyecto y ejecutar:

```powershell
git clone URL_DEL_REPOSITORIO
cd SIGELFA-PHP
```

Si el proyecto ya está clonado, actualizarlo con:

```powershell
git checkout main
git pull origin main
```

Si se está trabajando en una rama específica, cambiarse a ella:

```powershell
git checkout nombre-de-la-rama
git pull
```

## 3. Crear el archivo `.env`

El archivo `.env` contiene la configuración local de cada computadora.

No se sube a GitHub.

Primero copiar la plantilla:

```powershell
copy .env.example .env
```

Después abrir el archivo `.env` y ajustar los datos según la configuración local de SQL Server.

## 4. Configuración si se usa Windows Authentication

Usar esta opción si en SQL Server Management Studio se entra con:

```text
Authentication: Windows Authentication
```

Ejemplo de `.env`:

```env
APP_NAME=SIGELFA
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=sqlsrv
DB_HOST=NOMBRE_DEL_SERVIDOR
DB_PORT=1433
DB_DATABASE=SIGELFA_DB
DB_USERNAME=
DB_PASSWORD=
DB_TRUSTED_CONNECTION=true
DB_TRUST_SERVER_CERTIFICATE=true
```

Ejemplo real:

```env
DB_HOST=LAPTOP-B8PBME2K
DB_DATABASE=SIGELFA_DB
DB_USERNAME=
DB_PASSWORD=
DB_TRUSTED_CONNECTION=true
```

El valor de `DB_HOST` debe coincidir con el campo `Server name` que aparece al conectarse en SQL Server Management Studio.

Ejemplos posibles:

```text
localhost
.\SQLEXPRESS
localhost\SQLEXPRESS
NOMBRE-PC
NOMBRE-PC\SQLEXPRESS
```

## 5. Configuración si se usa SQL Server Authentication

Usar esta opción si en SQL Server Management Studio se entra con:

```text
Authentication: SQL Server Authentication
```

Ejemplo de `.env`:

```env
APP_NAME=SIGELFA
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=sqlsrv
DB_HOST=localhost
DB_PORT=1433
DB_DATABASE=SIGELFA_DB
DB_USERNAME=usuario_sql
DB_PASSWORD=contraseña_local
DB_TRUSTED_CONNECTION=false
DB_TRUST_SERVER_CERTIFICATE=true
```

Importante:

- No subir el archivo `.env`.
- No escribir contraseñas reales dentro del código.
- No poner contraseñas reales en archivos `.sql`.
- Cada integrante debe configurar su propia conexión local.

## 6. Abrir SQL Server Management Studio

Abrir SQL Server Management Studio y conectarse al servidor local.

Ejemplo:

```text
Server name: LAPTOP-B8PBME2K
Authentication: Windows Authentication
```

Después abrir una nueva consulta con:

```text
New Query
```

## 7. Orden correcto para ejecutar los scripts SQL

Los scripts se encuentran en la carpeta:

```text
database/
```

Deben ejecutarse en este orden:

```text
01_create_database.sql
02_schema.sql
03_seed.sql
04_indexes.sql
05_roles_users.sql
06_stored_procedures.sql
07_test_cedula_arbitral.sql
```

## 8. Script 01 - Crear base de datos

Archivo:

```text
database/01_create_database.sql
```

Este script crea la base de datos:

```text
SIGELFA_DB
```

Resultado esperado:

```text
Base de datos SIGELFA_DB creada correctamente.
```

O si ya existe:

```text
La base de datos SIGELFA_DB ya existe.
```

## 9. Script 02 - Crear tablas

Archivo:

```text
database/02_schema.sql
```

Este script crea las tablas principales del sistema.

Tablas esperadas:

```text
Liga
Torneo
Categoria
Equipo
Posicion
Jugador
Arbitro
UnDeportiva
Cancha
Jornada
Partido
TipoEventoPartido
CedulaArbitral
EventoPartido
```

Para verificar:

```sql
USE SIGELFA_DB;
GO

SELECT 
    TABLE_NAME AS tabla
FROM INFORMATION_SCHEMA.TABLES
WHERE TABLE_TYPE = 'BASE TABLE'
ORDER BY TABLE_NAME;
GO
```

## 10. Script 03 - Insertar datos iniciales

Archivo:

```text
database/03_seed.sql
```

Este script inserta datos de prueba:

```text
1 liga
1 torneo
1 categoría
18 equipos
4 posiciones
8 jugadores
2 árbitros
1 unidad deportiva
1 cancha
9 partidos
```

Consulta de verificación:

```sql
USE SIGELFA_DB;
GO

SELECT COUNT(*) AS total_ligas FROM dbo.Liga;
SELECT COUNT(*) AS total_torneos FROM dbo.Torneo;
SELECT COUNT(*) AS total_categorias FROM dbo.Categoria;
SELECT COUNT(*) AS total_equipos FROM dbo.Equipo;
SELECT COUNT(*) AS total_posiciones FROM dbo.Posicion;
SELECT COUNT(*) AS total_jugadores FROM dbo.Jugador;
SELECT COUNT(*) AS total_arbitros FROM dbo.Arbitro;
SELECT COUNT(*) AS total_partidos FROM dbo.Partido;
GO
```

Resultado esperado:

```text
total_ligas       1
total_torneos     1
total_categorias  1
total_equipos     18
total_posiciones  4
total_jugadores   8
total_arbitros    2
total_partidos    9
```

## 11. Script 04 - Crear índices

Archivo:

```text
database/04_indexes.sql
```

Este script crea índices para optimizar consultas, principalmente sobre la tabla `Equipo`.

Índices esperados:

```text
PK_Equipo
UQ_Equipo_Nombre_Contexto
IX_Equipo_NombEquipo
IX_Equipo_CategoriaTorneoLiga
```

Verificación:

```sql
USE SIGELFA_DB;
GO

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
```

## 12. Script 05 - Roles y permisos

Archivo:

```text
database/05_roles_users.sql
```

Este script crea los roles de base de datos:

```text
rol_sigelfa_app
rol_sigelfa_gerente
rol_sigelfa_asistente
rol_sigelfa_arbitro
```

También asigna permisos básicos de consulta, inserción, actualización y ejecución de procedimientos según el rol.

Verificación:

```sql
USE SIGELFA_DB;
GO

SELECT
    name AS rol,
    type_desc
FROM sys.database_principals
WHERE type = 'R'
  AND name LIKE 'rol_sigelfa_%'
ORDER BY name;
GO
```

## 13. Script 06 - Procedimientos almacenados

Archivo:

```text
database/06_stored_procedures.sql
```

Este script crea procedimientos almacenados.

Procedimientos esperados:

```text
sp_registrar_cedula_arbitral
sp_agregar_evento_cedula
```

Verificación:

```sql
USE SIGELFA_DB;
GO

SELECT 
    name AS procedimiento,
    type_desc
FROM sys.objects
WHERE type = 'P'
  AND name IN (
      'sp_registrar_cedula_arbitral',
      'sp_agregar_evento_cedula'
  )
ORDER BY name;
GO
```

## 14. Script 07 - Prueba de cédula arbitral

Archivo:

```text
database/07_test_cedula_arbitral.sql
```

Este script prueba que la cédula arbitral y los eventos funcionen correctamente.

Usa el partido:

```text
Halcones FC vs Tigres Laguna
```

porque esos equipos tienen jugadores de prueba.

El script debe crear o reutilizar una cédula y registrar eventos como:

```text
Gol
Tarjeta amarilla
```

Consulta final esperada:

```text
Gol - Juan López - Halcones FC
Tarjeta amarilla - Miguel Castro - Tigres Laguna
```

## 15. Consulta general para revisar partidos

```sql
USE SIGELFA_DB;
GO

SELECT
    p.idPartido,
    j.numJornada,
    ea.nombEquipo AS equipoA,
    eb.nombEquipo AS equipoB,
    p.fechaPart,
    p.horaPart,
    p.estadoPartido
FROM dbo.Partido p
INNER JOIN dbo.Jornada j
    ON p.idJornada = j.idJornada
INNER JOIN dbo.Equipo ea
    ON j.cveEquipoA = ea.cveEquipo
INNER JOIN dbo.Equipo eb
    ON j.cveEquipoB = eb.cveEquipo
ORDER BY p.idPartido;
GO
```

## 16. Consulta para revisar jugadores

```sql
USE SIGELFA_DB;
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
```

## 17. Consulta para revisar cédulas y eventos

```sql
USE SIGELFA_DB;
GO

SELECT
    ca.idCedula,
    ca.idPartido,
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
ORDER BY ca.idCedula, ep.minuto, ep.idEvento;
GO
```

## 18. Probar el servidor PHP

Desde la raíz del proyecto ejecutar:

```powershell
php -S localhost:8000 -t public
```

Abrir en el navegador:

```text
http://localhost:8000
```

Resultado esperado:

```text
SIGELFA
Sistema gestor de liga de fútbol amateur.
```

Para detener el servidor:

```text
Ctrl + C
```

## 19. Verificar extensiones de SQL Server en PHP

Ejecutar en PowerShell:

```powershell
php -m | findstr /I "sqlsrv pdo_sqlsrv"
```

Resultado esperado:

```text
pdo_sqlsrv
sqlsrv
```

Si no aparece nada, PHP todavía no tiene habilitados los drivers de SQL Server.

En ese caso se deben instalar los drivers correspondientes antes de conectar PHP con SQL Server.

## 20. Notas importantes para la conexión PHP

La conexión PHP todavía se configurará en una fase posterior.

Cuando se implemente, se usarán las variables del `.env`:

```env
DB_CONNECTION=sqlsrv
DB_HOST=
DB_PORT=1433
DB_DATABASE=SIGELFA_DB
DB_USERNAME=
DB_PASSWORD=
DB_TRUSTED_CONNECTION=
DB_TRUST_SERVER_CERTIFICATE=true
```

Reglas importantes:

- No conectar PHP como administrador.
- Usar un usuario técnico para la aplicación cuando sea posible.
- No subir contraseñas reales.
- Usar consultas preparadas.
- Manejar errores sin mostrar información sensible al usuario.
- Usar procedimientos almacenados para operaciones críticas como cédulas arbitrales.

## 21. Errores comunes

### Error: `Invalid object name`

Significa que la tabla no existe o se está ejecutando la consulta en otra base de datos.

Solución:

```sql
USE SIGELFA_DB;
GO
```

### Error: `Foreign key constraint`

Significa que se intentó insertar un dato que depende de otro que no existe.

Ejemplo:

```text
No se puede insertar un jugador si el equipo no existe.
```

Solución:

Ejecutar los scripts en orden.

### Error: `El equipo indicado no pertenece al partido de la cédula`

Significa que se intentó registrar un evento con un equipo que no participa en ese partido.

Solución:

Usar el script:

```text
07_test_cedula_arbitral.sql
```

### Error: `Este tipo de evento requiere indicar un jugador`

Significa que se intentó registrar un gol, tarjeta u otro evento sin indicar jugador.

Solución:

Verificar que el jugador exista y pertenezca al equipo del partido.

## 22. Orden resumido de ejecución

Ejecutar siempre en este orden:

```text
01_create_database.sql
02_schema.sql
03_seed.sql
04_indexes.sql
05_roles_users.sql
06_stored_procedures.sql
07_test_cedula_arbitral.sql
```

## 23. Estado esperado al finalizar

La base debe contener:

```text
SIGELFA_DB
```

Con tablas, datos iniciales, índices, roles, procedimientos y una prueba funcional de cédula arbitral.
