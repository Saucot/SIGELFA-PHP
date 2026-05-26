# SIGELFA-PHP

Sistema gestor de liga de futbol amateur desarrollado con PHP 8.x y SQL Server.

## Ejecucion oficial

La forma oficial de ejecutar el proyecto es con el servidor integrado de PHP desde la raiz del repositorio:

```powershell
php -S localhost:8000 -t public
```

Despues abre:

```text
http://localhost:8000
```

Laragon puede usarse como herramienta local opcional, pero el proyecto no debe depender de rutas, hosts ni URLs de Laragon.

## Configuracion local

Cada integrante debe crear su propio archivo `.env` a partir de la plantilla:

```powershell
copy .env.example .env
```

El archivo `.env` no se sube a Git. No escribas credenciales reales en `.env.example`, archivos PHP, SQL, Markdown o JSON.

Ejemplo para Windows Authentication:

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

`DB_HOST` puede ser `localhost`, `LAPTOP-B8PBME2K`, `.\SQLEXPRESS` o `localhost\SQLEXPRESS`, segun el nombre del servidor en SQL Server Management Studio.

Para SQL Server Authentication, usa `DB_TRUSTED_CONNECTION=false` y define `DB_USERNAME` y `DB_PASSWORD` solo en tu `.env` local.

## Verificar PHP y drivers SQL Server

Para saber que PHP se esta usando:

```powershell
where php
php --ini
```

Para verificar drivers de SQL Server:

```powershell
php -m | findstr /I "sqlsrv pdo_sqlsrv"
```

El resultado esperado debe incluir:

```text
pdo_sqlsrv
sqlsrv
```

## Rutas utiles

```text
http://localhost:8000
http://localhost:8000/test_conexion.php
```

`test_conexion.php` solo debe funcionar cuando `APP_DEBUG=true`.

## Estructura base

```text
SIGELFA-PHP/
|-- app/
|   |-- config/
|   |-- controllers/
|   |-- helpers/
|   |-- models/
|   `-- views/
|-- public/
|   |-- index.php
|   `-- assets/
|-- database/
|-- docs/
|-- .env.example
|-- .gitignore
|-- README.md
`-- composer.json
```

