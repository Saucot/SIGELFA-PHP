# Guia de ejecucion de base de datos y conexion - SIGELFA

Esta guia explica como preparar `SIGELFA_DB`, configurar `.env` y ejecutar el proyecto con PHP y SQL Server.

## 1. Ejecucion oficial

La forma oficial de ejecutar SIGELFA es con el servidor integrado de PHP desde la raiz del repositorio:

```powershell
php -S localhost:8000 -t public
```

Despues abrir:

```text
http://localhost:8000
```

Ruta de prueba local de conexion:

```text
http://localhost:8000/test_conexion.php
```

`test_conexion.php` solo debe responder cuando `APP_DEBUG=true`.

Laragon puede usarse como herramienta local opcional, pero no es obligatorio. El proyecto no debe depender de rutas como `/SIGELFA-PHP/public` ni de configuraciones propias de Laragon.

## 2. Requisitos previos

Cada integrante debe tener:

- Git
- VS Code
- PHP 8.x
- SQL Server
- SQL Server Management Studio
- Drivers de PHP para SQL Server
- ODBC Driver for SQL Server

Para saber que PHP se esta usando:

```powershell
where php
php --ini
```

Para verificar drivers de SQL Server:

```powershell
php -m | findstr /I "sqlsrv pdo_sqlsrv"
```

Resultado esperado:

```text
pdo_sqlsrv
sqlsrv
```

Si no aparecen, ese PHP no tiene habilitados los drivers de SQL Server.

## 3. Archivo .env

Cada integrante debe crear su propio `.env`:

```powershell
copy .env.example .env
```

Reglas:

- `.env` no se sube a Git.
- Las credenciales reales no se escriben en `.env.example`.
- Las credenciales reales no se copian en PHP, SQL, Markdown o JSON.
- Cada computadora puede tener diferente `DB_HOST`, usuario o modo de autenticacion.

## 4. Windows Authentication

Usar esta opcion si en SQL Server Management Studio se entra con Windows Authentication.

Ejemplo:

```env
APP_NAME=SIGELFA
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=sqlsrv
DB_HOST=localhost
DB_PORT=1433
DB_DATABASE=SIGELFA_DB
DB_USERNAME=
DB_PASSWORD=
DB_TRUSTED_CONNECTION=true
DB_TRUST_SERVER_CERTIFICATE=true
```

Valores validos comunes para `DB_HOST`:

```text
localhost
LAPTOP-B8PBME2K
.\SQLEXPRESS
localhost\SQLEXPRESS
NOMBRE-PC
NOMBRE-PC\SQLEXPRESS
```

El valor debe coincidir con el `Server name` usado en SQL Server Management Studio.

## 5. SQL Server Authentication

Usar esta opcion si en SQL Server Management Studio se entra con SQL Server Authentication.

Ejemplo con placeholders:

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
DB_PASSWORD=tu_password_local
DB_TRUSTED_CONNECTION=false
DB_TRUST_SERVER_CERTIFICATE=true
```

No subir ese `.env` local.

## 6. Orden de scripts SQL

Los scripts estan en `database/`. Ejecutar contra SQL Server desde SQL Server Management Studio.

Orden base:

```text
01_create_database.sql
02_schema.sql
03_seed.sql
04_indexes.sql
06_stored_procedures.sql
05_roles_users.sql
07_test_cedula_arbitral.sql
08_auth_schema.sql
```

Nota: `05_roles_users.sql` concede permisos sobre procedimientos almacenados. Por eso, para una instalacion limpia, conviene crear primero los procedimientos con `06_stored_procedures.sql` y despues aplicar permisos.

## 7. Que hace cada script

- `01_create_database.sql`: crea `SIGELFA_DB` si no existe.
- `02_schema.sql`: crea tablas principales.
- `03_seed.sql`: inserta datos iniciales de prueba.
- `04_indexes.sql`: crea indices sobre consultas frecuentes.
- `06_stored_procedures.sql`: crea procedimientos almacenados.
- `05_roles_users.sql`: crea roles y permisos.
- `07_test_cedula_arbitral.sql`: prueba cedula arbitral y eventos.
- `08_auth_schema.sql`: crea tablas de usuarios de aplicacion para login y roles de interfaz.

## 8. Autenticacion de aplicacion

Despues de ejecutar `08_auth_schema.sql`, crear usuarios manualmente en SQL Server. No subir contrasenas reales al repositorio.

Generar un hash local desde consola:

```powershell
php tools/generar_hash.php "PasswordTemporal123"
```

Ejemplo de insercion manual con un hash generado localmente:

```sql
INSERT INTO dbo.Usuario (nombreUsuario, email, passwordHash, rol)
VALUES (N'Gerente de prueba', 'gerente@example.test', 'HASH_GENERADO_LOCALMENTE', 'GERENTE');
```

Roles disponibles:

- `GERENTE`: acceso general.
- `ASISTENTE`: gestion general y partidos.
- `ARBITRO`: partidos y cedulas.

Para usuario arbitro, relacionar el usuario con un arbitro existente:

```sql
INSERT INTO dbo.UsuarioArbitro (idUsuario, numArb)
VALUES (ID_DEL_USUARIO, 'A001');
```

## 9. Probar la aplicacion

Desde la raiz:

```powershell
php -S localhost:8000 -t public
```

Abrir:

```text
http://localhost:8000
```

Si `APP_DEBUG=true`, probar:

```text
http://localhost:8000/test_conexion.php
```

Resultado esperado:

- La pagina principal carga sin depender de Laragon.
- La prueba muestra conexion exitosa y nombre de base actual.
- No se muestran usuarios, passwords ni DSN completo.

## 10. Errores comunes

### No aparecen sqlsrv ni pdo_sqlsrv

El PHP usado por consola no tiene drivers SQL Server habilitados. Revisar:

```powershell
where php
php --ini
php -m | findstr /I "sqlsrv pdo_sqlsrv"
```

### No se pudo conectar con la base de datos

Revisar:

- `DB_HOST`
- `DB_DATABASE`
- `DB_TRUSTED_CONNECTION`
- `DB_USERNAME` y `DB_PASSWORD` si se usa SQL Server Authentication
- Que SQL Server este iniciado
- Que la base `SIGELFA_DB` exista

### Invalid object name

La tabla no existe o se esta usando otra base. Revisar que se haya ejecutado `02_schema.sql` contra `SIGELFA_DB`.

## 11. Seguridad

- No subir `.env`.
- No imprimir valores reales de `.env`.
- No mostrar mensajes tecnicos de excepciones al usuario.
- Usar consultas preparadas.
- Escapar salida HTML con `htmlspecialchars`.
- Usar `test_conexion.php` solo en local con `APP_DEBUG=true`.
- Crear hashes con `password_hash` y verificar login con `password_verify`.
- No guardar contrasenas en texto plano.

