# SIGELFA-PHP

Sistema gestor de liga de futbol amateur desarrollado con PHP 8.x y SQL Server.

## Ejecucion oficial

La forma oficial de ejecutar el proyecto es con el servidor integrado de PHP desde la raiz del repositorio:

```powershell
php -S localhost:8000 -t public
```

Despues abrir:

```text
http://localhost:8000
```

## Configuracion local

```powershell
copy .env.example .env
```

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

