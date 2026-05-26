# Informe de auditoria del repositorio SIGELFA

## 1. Resumen ejecutivo

Auditoria realizada sobre el repositorio local `SIGELFA-PHP`, sin modificar archivos del proyecto salvo este informe.

Hallazgos principales:

- Rama actual: `victor-sigelfa`.
- Existe la rama `main`.
- La rama actual contiene cambios respecto a `main`: 12 archivos agregados/modificados, principalmente conexion SQL Server, vista de equipos, estilos y un archivo de prueba de conexion.
- El arbol de trabajo tiene 2 eliminaciones pendientes sin commit: `app/controllers/.gitkeep` y `app/models/.gitkeep`.
- No se detectaron archivos nuevos no rastreados antes de crear este informe.
- Existe un archivo `.env` local, pero esta ignorado por `.gitignore` y no esta rastreado por Git.
- No se detectaron `DROP DATABASE`, `DROP TABLE`, `DELETE FROM` masivo ni `TRUNCATE` en `database/*.sql`.
- Hay riesgos altos en seguridad/configuracion: archivo publico de prueba `public/test_conexion.php`, errores de conexion expuestos con `die($exception->getMessage())`, configuracion de base de datos dura en PHP y salida HTML sin escape.
- Hay un riesgo alto de ejecucion SQL: `05_roles_users.sql` otorga permisos `EXECUTE` sobre procedimientos que se crean hasta `06_stored_procedures.sql`, por lo que el orden esperado `05` antes de `06` puede fallar.
- `composer.json` es JSON valido, pero exige `php >=8.4`, mas restrictivo que el contexto general PHP 8.x.

## 2. Rama actual y estado de Git

Comandos revisados:

- `git status --short --branch`
- `git branch --show-current`
- `git log --oneline --decorate --graph --all -n 20`
- `git diff --stat`
- `git diff --name-status`
- `git ls-files`

Estado observado:

- Rama actual: `victor-sigelfa`.
- Tracking: `origin/victor-sigelfa`.
- Rama `main` existente localmente y en remoto: `main`, `origin/main`.
- Ultimo commit de la rama actual: `8543903 Se establecio la conexion con SQL Server y se mostro la tabla Equipos`.
- `main` apunta a `7349544 m`.
- El arbol de trabajo no esta limpio: hay eliminaciones pendientes sin commit.

Archivos modificados sin commit:

```text
D app/controllers/.gitkeep
D app/models/.gitkeep
```

No se observaron archivos nuevos no rastreados antes de generar este informe.

## 3. Cambios detectados

Comparacion contra `main` con:

- `git diff --stat main...HEAD`
- `git diff --name-status main...HEAD`

Resumen estadistico:

```text
12 files changed, 406 insertions(+), 4 deletions(-)
```

Archivos cambiados respecto a `main`:

```text
A app/controllers/EquipoController.php
A app/models/Database.php
A app/models/Equipo.php
A app/views/equipos/index.php
A app/views/layouts/footer.php
A app/views/layouts/header.php
A app/views/layouts/sidebar.php
M database/01_create_database.sql
A public/assets/css/style.css
M public/index.php
A public/routes/web.php
A public/test_conexion.php
```

Interpretacion:

- Se agrego una primera capa MVC para equipos (`EquipoController`, `Equipo`, vistas y layouts).
- Se agrego conexion a SQL Server mediante PDO en `app/models/Database.php`.
- `public/index.php` fue modificado para consultar directamente la tabla `Equipo`.
- Se agrego `public/test_conexion.php`, archivo de prueba expuesto dentro de `public`.
- `database/01_create_database.sql` fue modificado para crear la base solo si no existe.
- `public/routes/web.php` existe pero esta vacio.

## 4. Archivos nuevos, modificados o eliminados

Archivos agregados respecto a `main`:

- `app/controllers/EquipoController.php`
- `app/models/Database.php`
- `app/models/Equipo.php`
- `app/views/equipos/index.php`
- `app/views/layouts/footer.php`
- `app/views/layouts/header.php`
- `app/views/layouts/sidebar.php`
- `public/assets/css/style.css`
- `public/routes/web.php`
- `public/test_conexion.php`

Archivos modificados respecto a `main`:

- `database/01_create_database.sql`
- `public/index.php`

Archivos eliminados en el arbol de trabajo, pendientes de commit:

- `app/controllers/.gitkeep`
- `app/models/.gitkeep`

No se observaron archivos eliminados respecto a `main` en `main...HEAD`; las eliminaciones actuales son cambios locales sin commit.

## 5. Revision de seguridad

Revision de `.env`:

- Existe `.env` local en la raiz del proyecto.
- `.gitignore` contiene `.env`.
- `git check-ignore -v .env` confirmo que `.env` es ignorado por la regla `.gitignore:2:.env`.
- `git ls-files .env .env.example` solo mostro `.env.example`; por tanto `.env` no esta rastreado por Git.
- Por seguridad, no se reproducen valores reales del `.env` en este informe.

Revision de `.env.example`:

- Existe.
- Contiene placeholders como `DB_USERNAME=tu_usuario` y `DB_PASSWORD=tu_password`.
- No se identifico una contrasena real evidente, pero conviene aclarar que son valores de ejemplo.

Riesgos detectados:

- `app/models/Database.php` contiene configuracion dura:
  - `host = "localhost"`
  - `port = "1433"`
  - `db_name = "SIGELFA_DB"`
  - DSN con `Encrypt=no` y `TrustServerCertificate=true`.
- `app/models/Database.php` muestra detalles sensibles al usuario en caso de error:
  - `die("Error de conexion: " . $exception->getMessage())`
- `public/test_conexion.php` es un endpoint publico de prueba que confirma si la conexion funciona.
- `public/index.php` y `app/views/equipos/index.php` imprimen datos de base de datos sin `htmlspecialchars`.
- No se detectaron archivos `.bak`, `.backup`, `.log`, `.tmp`, `.cache`, `phpinfo.php` ni `test.php`.
- No se detectaron contrasenas reales en `database/*.sql`.
- `database/05_roles_users.sql` declara explicitamente que no crea usuarios con contrasenas.

## 6. Revision de estructura del proyecto

Estructura esperada revisada:

- `app/config`: existe.
- `app/controllers`: existe.
- `app/models`: existe.
- `app/views`: existe.
- `app/helpers`: existe.
- `public/index.php`: existe.
- `public/assets/css`: existe.
- `public/assets/js`: existe.
- `public/assets/img`: existe.
- `database`: existe.
- `docs`: existe.

Carpetas o archivos fuera de lo esperado:

- `public/routes/web.php`: existe, pero esta vacio.
- `public/test_conexion.php`: archivo de prueba dentro del directorio publico.

Archivos temporales o basura:

- No se detectaron `.bak`, `.backup`, `.log`, `.tmp`, `.cache`, `phpinfo.php` ni `test.php`.

Observacion de estructura MVC:

- Hay archivos MVC nuevos, pero `public/index.php` no actua todavia como front controller limpio.
- `public/index.php` instancia `Database`, ejecuta SQL directo y renderiza HTML completo.
- `EquipoController.php`, `Equipo.php` y `app/views/equipos/index.php` existen, pero no se observa que `public/index.php` use el controlador.

## 7. Revision de scripts SQL

Scripts esperados:

- `database/01_create_database.sql`: existe.
- `database/02_schema.sql`: existe.
- `database/03_seed.sql`: existe.
- `database/04_indexes.sql`: existe.
- `database/05_roles_users.sql`: existe.
- `database/06_stored_procedures.sql`: existe.
- `database/07_test_cedula_arbitral.sql`: existe.

Resumen general:

| Script | Que parece hacer | Operaciones destructivas | Idempotencia aparente | Observaciones |
| --- | --- | --- | --- | --- |
| `01_create_database.sql` | Crea `SIGELFA_DB` si no existe. | No | Si, usa `IF NOT EXISTS`. | Seguro para repetir en general. |
| `02_schema.sql` | Crea tablas principales con llaves y relaciones. | No | Si, usa `OBJECT_ID(..., 'U') IS NULL`. | Depende de que exista la base de datos. |
| `03_seed.sql` | Inserta datos iniciales de prueba. | No | Parcialmente/si, usa validaciones `IF NOT EXISTS` y `WHERE NOT EXISTS`. | Depende de `02_schema.sql`. |
| `04_indexes.sql` | Crea indices no agrupados sobre `Equipo` y hace consultas de verificacion. | No | Si, valida existencia de indices. | Incluye `SELECT * FROM dbo.Equipo` como consulta de prueba. |
| `05_roles_users.sql` | Crea roles y otorga permisos. | No | Parcialmente, roles validados con `IF NOT EXISTS`; `GRANT` puede repetirse. | Riesgo de orden: otorga `EXECUTE` sobre procedimientos que aun no existen si se ejecuta antes de `06`. |
| `06_stored_procedures.sql` | Crea/actualiza procedimientos `sp_registrar_cedula_arbitral` y `sp_agregar_evento_cedula`. | No | Si, usa `CREATE OR ALTER`. | Depende de tablas creadas por `02_schema.sql`. |
| `07_test_cedula_arbitral.sql` | Ejecuta prueba controlada de cedula arbitral y eventos. | No | Si en general, valida cedula/eventos existentes. | Depende de `03_seed.sql` y `06_stored_procedures.sql`. |

Busqueda de operaciones destructivas:

- `DROP DATABASE`: no encontrado.
- `DROP TABLE`: no encontrado.
- `DELETE FROM`: no encontrado.
- `TRUNCATE`: no encontrado.

Errores o riesgos evidentes:

- Orden esperado `05_roles_users.sql` antes de `06_stored_procedures.sql` puede romperse, porque `05` contiene:
  - `GRANT EXECUTE ON dbo.sp_registrar_cedula_arbitral`
  - `GRANT EXECUTE ON dbo.sp_agregar_evento_cedula`
- Esos procedimientos se crean en `06_stored_procedures.sql`.
- Recomendacion: ejecutar grants de procedimientos despues de crear procedimientos, o mover esas dos concesiones a un script posterior.

Orden de ejecucion recomendado segun dependencias actuales:

1. `01_create_database.sql`
2. `02_schema.sql`
3. `03_seed.sql`
4. `04_indexes.sql`
5. `06_stored_procedures.sql`
6. `05_roles_users.sql` o un script corregido de permisos posterior a `06`
7. `07_test_cedula_arbitral.sql`

## 8. Revision de PHP

Archivos PHP detectados en `app/` y `public/`:

- `public/test_conexion.php`
- `public/routes/web.php`
- `public/index.php`
- `app/controllers/EquipoController.php`
- `app/models/Database.php`
- `app/models/Equipo.php`
- `app/views/layouts/sidebar.php`
- `app/views/layouts/header.php`
- `app/views/layouts/footer.php`
- `app/views/equipos/index.php`

Validacion de sintaxis:

- Se ejecuto `php -l` sobre todos los archivos PHP detectados.
- Resultado: sin errores de sintaxis en los archivos revisados.

Punto de entrada:

- `public/index.php` sigue siendo el punto de entrada existente.
- Sin embargo, no delega en `public/routes/web.php` ni en `EquipoController.php`.

Conexion a base de datos:

- Existe en `app/models/Database.php`.
- Usa PDO con DSN `sqlsrv`.
- No usa todavia variables de entorno para host, puerto o base de datos.
- No se observaron usuario o contrasena duros en `Database.php`; usa conexion PDO sin usuario/contrasena explicitos, probablemente autenticacion integrada o configuracion por defecto.

Consultas SQL directas:

- `public/index.php` contiene consulta directa a `Equipo`.
- `app/models/Equipo.php` tambien contiene consulta directa a `Equipo`.
- La consulta de `public/index.php` no recibe parametros de usuario.
- La consulta de `app/models/Equipo.php` usa interpolacion de `$this->table`, aunque el valor es privado y fijo (`Equipo`).

Riesgos PHP:

- `app/models/Database.php` revela detalles de excepcion con `die(...)`.
- `public/test_conexion.php` expone una prueba publica de conexion.
- `public/index.php` imprime campos de base de datos sin escape HTML.
- `app/views/equipos/index.php` tambien imprime campos con `<?= ... ?>` sin escape.
- `public/index.php` contiene la consulta y el render completo, duplicando responsabilidades frente al MVC.
- `public/routes/web.php` esta vacio.

## 9. Revision de Composer

Archivo `composer.json`:

- Existe.
- JSON valido.
- Define paquete `sigelfa/sigelfa-php`.
- Tipo: `project`.
- Licencia: `proprietary`.
- Version requerida de PHP: `>=8.4`.
- Autoload definido:

```json
{
  "psr-4": {
    "App\\": "app/"
  }
}
```

Dependencias:

- No se detectaron dependencias externas sospechosas.
- Solo exige PHP.

Observacion:

- El proyecto se describe como PHP 8.x, pero `composer.json` exige `>=8.4`. Esto puede impedir instalacion o validacion en equipos con PHP 8.0, 8.1, 8.2 o 8.3.
- Aunque hay autoload PSR-4, los archivos PHP actuales usan `require_once` manual y clases sin namespace.

## 10. Revision de documentacion

Documentos existentes:

- `README.md`
- `docs/guia-ejecucion-bd-y-conexion.md`
- `docs/informe-auditoria-repositorio.md` (este informe)

Contenido observado:

- `README.md` describe objetivo, tecnologias y estructura inicial.
- `README.md` lista documentacion esperada como `instalacion.md`, `base-de-datos.md`, `manual-usuario.md` y `estructura-proyecto.md`, pero esos archivos no existen actualmente.
- `docs/guia-ejecucion-bd-y-conexion.md` contiene guia amplia de ejecucion de base de datos y conexion.
- La guia incluye ejemplos de `.env` y recomendaciones de no subir contrasenas reales.

Faltantes importantes para que un companero clone y ejecute:

- Guia corta de instalacion desde cero en `README.md`.
- Comando exacto para levantar el servidor PHP.
- Requisitos de extension SQL Server (`pdo_sqlsrv` / `sqlsrv`) resumidos en README.
- Orden actualizado de scripts SQL considerando el problema entre `05` y `06`.
- Explicacion de si se debe usar autenticacion integrada o usuario SQL.
- Documentacion de rutas actuales y punto de entrada.

## 11. Problemas encontrados por severidad

### Criticos

No se detectaron problemas criticos confirmados como filtracion de contrasenas reales rastreadas por Git, operaciones SQL destructivas o archivos de dump/backup sensibles dentro del repositorio rastreado.

### Altos

1. Archivo publico de prueba de conexion

- Archivo afectado: `public/test_conexion.php`
- Evidencia: el archivo instancia `Database`, abre conexion y muestra `Conexion a SQL Server exitosa`.
- Riesgo: expone a cualquier visitante informacion de diagnostico sobre la infraestructura.
- Recomendacion: eliminarlo o protegerlo antes de despliegue; conservar solo una prueba local fuera de `public`.
- Momento: debe corregirse antes de publicar o compartir como version estable.

2. Detalles sensibles de errores de base de datos visibles al usuario

- Archivo afectado: `app/models/Database.php`
- Evidencia: `die("Error de conexion: " . $exception->getMessage())`.
- Riesgo: puede revelar servidor, base de datos, driver, rutas o detalles internos.
- Recomendacion: registrar el detalle en logs locales y mostrar mensaje generico al usuario.
- Momento: debe corregirse antes de despliegue.

3. Orden de scripts SQL puede fallar

- Archivo afectado: `database/05_roles_users.sql`
- Evidencia: concede `EXECUTE` sobre `sp_registrar_cedula_arbitral` y `sp_agregar_evento_cedula`; esos procedimientos se crean en `database/06_stored_procedures.sql`.
- Riesgo: si se ejecuta el orden numerico esperado, `05` puede fallar porque los procedimientos aun no existen.
- Recomendacion: mover los grants de procedimientos despues de `06`, o ejecutar `06` antes de la parte de permisos sobre procedimientos.
- Momento: debe corregirse antes de pedir a otros que ejecuten scripts desde cero.

4. Salida HTML sin escape

- Archivos afectados: `public/index.php`, `app/views/equipos/index.php`
- Evidencia: se imprimen valores como `$equipo['nombEquipo']`, `$equipo['nombRepEq']`, `$equipo['numTelRepEq']` sin `htmlspecialchars`.
- Riesgo: XSS almacenado si algun dato de base de datos contiene HTML o JavaScript.
- Recomendacion: escapar toda salida dinamica con `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')`.
- Momento: debe corregirse antes de aceptar datos reales de usuarios.

### Medios

1. Configuracion de base de datos dura y no alineada con `.env`

- Archivo afectado: `app/models/Database.php`
- Evidencia: host, puerto y nombre de base estan definidos como propiedades privadas; no se lee `.env`.
- Riesgo: dificulta ejecucion en otros equipos y favorece cambios manuales de configuracion.
- Recomendacion: leer configuracion desde variables de entorno o archivo de configuracion local ignorado por Git.
- Momento: corregible despues, pero antes de integrar con mas ambientes.

2. DSN usa cifrado desactivado

- Archivo afectado: `app/models/Database.php`
- Evidencia: `Encrypt=no;TrustServerCertificate=true`.
- Riesgo: configuracion aceptable para laboratorio local, pero insegura para red o despliegue real.
- Recomendacion: documentar que es local o activar cifrado con certificado valido cuando aplique.
- Momento: corregible antes de usar fuera del entorno local.

3. MVC incompleto o bypass del controlador

- Archivos afectados: `public/index.php`, `app/controllers/EquipoController.php`, `public/routes/web.php`
- Evidencia: `public/index.php` consulta base y renderiza HTML directamente; `public/routes/web.php` esta vacio; `EquipoController.php` no se usa desde el punto de entrada.
- Riesgo: duplicacion, confusion de flujo y mayor dificultad para mantener rutas.
- Recomendacion: decidir un flujo unico: front controller + rutas + controlador + modelo + vista.
- Momento: corregible antes de seguir agregando modulos.

4. Requisito de PHP demasiado estricto

- Archivo afectado: `composer.json`
- Evidencia: `"php": ">=8.4"`.
- Riesgo: companeros con PHP 8.1, 8.2 o 8.3 no podran cumplir Composer aunque el proyecto diga PHP 8.x.
- Recomendacion: confirmar version objetivo real; si basta PHP 8.1/8.2, ajustar restriccion.
- Momento: corregible antes de documentar instalacion final.

### Bajos

1. Eliminaciones pendientes de `.gitkeep`

- Archivos afectados: `app/controllers/.gitkeep`, `app/models/.gitkeep`
- Evidencia: `git status` muestra ambos como eliminados.
- Riesgo: bajo, porque las carpetas ya contienen archivos reales; pero ensucia el arbol de trabajo.
- Recomendacion: decidir si se aceptan las eliminaciones y commitearlas, o restaurarlas si el equipo quiere mantenerlas.
- Momento: corregible despues.

2. Archivo de rutas vacio

- Archivo afectado: `public/routes/web.php`
- Evidencia: archivo vacio.
- Riesgo: confusion sobre arquitectura actual.
- Recomendacion: implementarlo o retirarlo cuando se defina el flujo MVC.
- Momento: corregible despues de decidir arquitectura.

3. README desactualizado frente a docs reales

- Archivo afectado: `README.md`
- Evidencia: menciona docs esperados que no existen (`instalacion.md`, `base-de-datos.md`, `manual-usuario.md`, `estructura-proyecto.md`).
- Riesgo: un companero puede buscar documentos inexistentes.
- Recomendacion: actualizar README con documentos reales o crear los documentos faltantes en una tarea posterior.
- Momento: corregible despues, pero antes de entrega.

4. Rutas CSS absolutas dependientes de carpeta

- Archivos afectados: `public/index.php`, `app/views/layouts/header.php`, `app/views/layouts/sidebar.php`
- Evidencia: rutas como `/SIGELFA-PHP/public/assets/css/style.css` y `/SIGELFA-PHP/public/index.php`.
- Riesgo: puede romperse si el proyecto se sirve con otra raiz o con `public` como document root.
- Recomendacion: definir `APP_URL` o helpers de rutas.
- Momento: corregible antes de despliegue o demo en otro equipo.

## 12. Recomendaciones antes de continuar

1. Corregir el orden SQL: crear procedimientos antes de otorgar permisos `EXECUTE`, o separar permisos de procedimientos en un script posterior.
2. Retirar o proteger `public/test_conexion.php` antes de cualquier demo publica.
3. Cambiar manejo de errores de `Database.php` para no mostrar detalles internos al usuario.
4. Escapar salidas dinamicas en vistas y en `public/index.php`.
5. Decidir si `public/index.php` sera front controller MVC o una pagina temporal directa.
6. Alinear `composer.json` con la version PHP real disponible para el equipo.
7. Actualizar README con instrucciones minimas de instalacion, servidor PHP, extensiones SQL Server y orden correcto de scripts.
8. Mantener `.env` fuera de Git y revisar que `.env.example` siga usando solo placeholders.

## 13. Preguntas o dudas para el equipo

1. La version oficial sera PHP 8.4 o cualquier PHP 8.x compatible?
2. La aplicacion debe conectarse con autenticacion integrada de Windows o con usuario SQL definido en `.env`?
3. `public/index.php` es una prueba temporal o ya debe convertirse en front controller MVC?
4. `public/test_conexion.php` se necesita solo para laboratorio local o debe eliminarse antes de integrar?
5. El orden numerico de scripts SQL es obligatorio para la entrega? Si si, conviene ajustar `05` y `06`.
6. Los datos de `03_seed.sql` son solo de prueba escolar o algunos representan datos reales que deban anonimizarse?

