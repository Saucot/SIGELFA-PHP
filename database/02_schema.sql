/*
    SIGELFA - Script 02
    Esquema principal de la base de datos.

    Este script crea el núcleo del sistema:
    - Liga
    - Torneo
    - Categoria
    - Equipo
    - Posicion
    - Jugador
    - Arbitro
    - UnDeportiva
    - Cancha
    - Jornada
    - Partido

    Decisiones de diseño:
    - El proyecto será pequeño, por eso Jugador pertenece directamente a Equipo.
    - Se agrega Posicion para clasificar jugadores.
    - No se maneja formalmente local/visitante.
    - Los enfrentamientos usan cveEquipoA y cveEquipoB.
*/

USE SIGELFA_DB;
GO

/* ============================================================
   TABLA: Liga
   Representa una liga de fútbol amateur.
   ============================================================ */
IF OBJECT_ID(N'dbo.Liga', N'U') IS NULL
BEGIN
    CREATE TABLE dbo.Liga (
        cveLiga VARCHAR(4) NOT NULL,
        nombLiga NVARCHAR(50) NOT NULL,
        nombAdmLiga NVARCHAR(60) NULL,
        calleYNumLiga NVARCHAR(80) NULL,
        colLiga NVARCHAR(50) NULL,
        cpLiga VARCHAR(7) NULL,
        ciudadLiga NVARCHAR(50) NULL,
        edoLiga NVARCHAR(50) NULL,
        telLiga VARCHAR(15) NULL,
        eMailLiga VARCHAR(80) NULL,
        consecutivoMov INT NOT NULL CONSTRAINT DF_Liga_ConsecutivoMov DEFAULT 1,

        CONSTRAINT PK_Liga
        PRIMARY KEY CLUSTERED (cveLiga)
    );

    PRINT 'Tabla Liga creada.';
END
ELSE
BEGIN
    PRINT 'La tabla Liga ya existe.';
END
GO

/* ============================================================
   TABLA: Torneo
   Representa un torneo dentro de una liga.
   ============================================================ */
IF OBJECT_ID(N'dbo.Torneo', N'U') IS NULL
BEGIN
    CREATE TABLE dbo.Torneo (
        perTorneo VARCHAR(5) NOT NULL,
        nombTorneo NVARCHAR(50) NOT NULL,
        fechaIni DATE NULL,
        fechaTer DATE NULL,
        cveLiga VARCHAR(4) NOT NULL,

        CONSTRAINT PK_Torneo
        PRIMARY KEY CLUSTERED (perTorneo, cveLiga),

        CONSTRAINT FK_Torneo_Liga
        FOREIGN KEY (cveLiga)
        REFERENCES dbo.Liga(cveLiga),

        CONSTRAINT CK_Torneo_Fechas
        CHECK (fechaIni IS NULL OR fechaTer IS NULL OR fechaTer >= fechaIni)
    );

    PRINT 'Tabla Torneo creada.';
END
ELSE
BEGIN
    PRINT 'La tabla Torneo ya existe.';
END
GO

/* ============================================================
   TABLA: Categoria
   Representa una categoría dentro de un torneo.
   Ejemplo: Libre, Juvenil, Infantil.
   ============================================================ */
IF OBJECT_ID(N'dbo.Categoria', N'U') IS NULL
BEGIN
    CREATE TABLE dbo.Categoria (
        nomCortoCat VARCHAR(4) NOT NULL,
        edadMin INT NULL,
        edadMaxima INT NULL,
        cveLiga VARCHAR(4) NOT NULL,
        perTorneo VARCHAR(5) NOT NULL,

        CONSTRAINT PK_Categoria
        PRIMARY KEY CLUSTERED (nomCortoCat, perTorneo, cveLiga),

        CONSTRAINT FK_Categoria_Torneo
        FOREIGN KEY (perTorneo, cveLiga)
        REFERENCES dbo.Torneo(perTorneo, cveLiga),

        CONSTRAINT CK_Categoria_Edades
        CHECK (
            edadMin IS NULL
            OR edadMaxima IS NULL
            OR edadMaxima >= edadMin
        )
    );

    PRINT 'Tabla Categoria creada.';
END
ELSE
BEGIN
    PRINT 'La tabla Categoria ya existe.';
END
GO

/* ============================================================
   TABLA: Equipo
   Representa un equipo registrado en una categoría y torneo.
   ============================================================ */
IF OBJECT_ID(N'dbo.Equipo', N'U') IS NULL
BEGIN
    CREATE TABLE dbo.Equipo (
        cveEquipo INT IDENTITY(1,1) NOT NULL,
        nombEquipo NVARCHAR(50) NOT NULL,
        nombRepEq NVARCHAR(60) NULL,
        numTelRepEq VARCHAR(15) NULL,
        eMailRepEq VARCHAR(80) NULL,
        nomCortoCat VARCHAR(4) NOT NULL,
        perTorneo VARCHAR(5) NOT NULL,
        cveLiga VARCHAR(4) NOT NULL,
        activo BIT NOT NULL CONSTRAINT DF_Equipo_Activo DEFAULT 1,

        CONSTRAINT PK_Equipo
        PRIMARY KEY CLUSTERED (cveEquipo),

        CONSTRAINT FK_Equipo_Categoria
        FOREIGN KEY (nomCortoCat, perTorneo, cveLiga)
        REFERENCES dbo.Categoria(nomCortoCat, perTorneo, cveLiga),

        CONSTRAINT UQ_Equipo_Nombre_Contexto
        UNIQUE (nombEquipo, nomCortoCat, perTorneo, cveLiga)
    );

    PRINT 'Tabla Equipo creada.';
END
ELSE
BEGIN
    PRINT 'La tabla Equipo ya existe.';
END
GO

/* ============================================================
   TABLA: Posicion
   Catálogo de posiciones de jugadores.
   Ejemplo: Portero, Defensa, Mediocampista, Delantero.
   ============================================================ */
IF OBJECT_ID(N'dbo.Posicion', N'U') IS NULL
BEGIN
    CREATE TABLE dbo.Posicion (
        idPosicion TINYINT IDENTITY(1,1) NOT NULL,
        nombrePosicion NVARCHAR(30) NOT NULL,
        abreviatura VARCHAR(5) NOT NULL,

        CONSTRAINT PK_Posicion
        PRIMARY KEY CLUSTERED (idPosicion),

        CONSTRAINT UQ_Posicion_Nombre
        UNIQUE (nombrePosicion),

        CONSTRAINT UQ_Posicion_Abreviatura
        UNIQUE (abreviatura)
    );

    PRINT 'Tabla Posicion creada.';
END
ELSE
BEGIN
    PRINT 'La tabla Posicion ya existe.';
END
GO

/* ============================================================
   TABLA: Jugador
   Representa a un jugador de un equipo.
   Para mantener el proyecto pequeño, cada jugador pertenece
   directamente a un equipo.
   ============================================================ */
IF OBJECT_ID(N'dbo.Jugador', N'U') IS NULL
BEGIN
    CREATE TABLE dbo.Jugador (
        numJug VARCHAR(4) NOT NULL,
        nomJug NVARCHAR(40) NOT NULL,
        apPatJug NVARCHAR(40) NULL,
        apMatJug NVARCHAR(40) NULL,
        fechaNacJug DATE NULL,
        edadJug INT NULL,
        telJug VARCHAR(15) NULL,
        eMailJug VARCHAR(80) NULL,
        numeroCamiseta INT NULL,
        cveEquipo INT NOT NULL,
        idPosicion TINYINT NOT NULL,
        activo BIT NOT NULL CONSTRAINT DF_Jugador_Activo DEFAULT 1,

        CONSTRAINT PK_Jugador
        PRIMARY KEY CLUSTERED (numJug),

        CONSTRAINT FK_Jugador_Equipo
        FOREIGN KEY (cveEquipo)
        REFERENCES dbo.Equipo(cveEquipo),

        CONSTRAINT FK_Jugador_Posicion
        FOREIGN KEY (idPosicion)
        REFERENCES dbo.Posicion(idPosicion),

        CONSTRAINT CK_Jugador_Edad
        CHECK (edadJug IS NULL OR edadJug >= 0),

        CONSTRAINT CK_Jugador_NumeroCamiseta
        CHECK (numeroCamiseta IS NULL OR numeroCamiseta BETWEEN 1 AND 999)
    );

    PRINT 'Tabla Jugador creada.';
END
ELSE
BEGIN
    PRINT 'La tabla Jugador ya existe.';
END
GO

/* ============================================================
   TABLA: Arbitro
   Representa a los árbitros registrados en el sistema.
   ============================================================ */
IF OBJECT_ID(N'dbo.Arbitro', N'U') IS NULL
BEGIN
    CREATE TABLE dbo.Arbitro (
        numArb VARCHAR(4) NOT NULL,
        nomArb NVARCHAR(40) NOT NULL,
        apPatArb NVARCHAR(40) NULL,
        apMatArb NVARCHAR(40) NULL,
        telArb VARCHAR(15) NULL,
        eMailArb VARCHAR(80) NULL,
        activo BIT NOT NULL CONSTRAINT DF_Arbitro_Activo DEFAULT 1,

        CONSTRAINT PK_Arbitro
        PRIMARY KEY CLUSTERED (numArb)
    );

    PRINT 'Tabla Arbitro creada.';
END
ELSE
BEGIN
    PRINT 'La tabla Arbitro ya existe.';
END
GO

/* ============================================================
   TABLA: UnDeportiva
   Representa una unidad deportiva donde se juegan partidos.
   ============================================================ */
IF OBJECT_ID(N'dbo.UnDeportiva', N'U') IS NULL
BEGIN
    CREATE TABLE dbo.UnDeportiva (
        cveUd VARCHAR(4) NOT NULL,
        nombUd NVARCHAR(60) NOT NULL,
        nombAdmUd NVARCHAR(60) NULL,
        calleYNumUd NVARCHAR(80) NULL,
        colUd NVARCHAR(50) NULL,
        cpUd VARCHAR(7) NULL,
        ciudadUd NVARCHAR(50) NULL,
        edoUd NVARCHAR(50) NULL,
        telUd VARCHAR(15) NULL,
        eMailUd VARCHAR(80) NULL,

        CONSTRAINT PK_UnDeportiva
        PRIMARY KEY CLUSTERED (cveUd)
    );

    PRINT 'Tabla UnDeportiva creada.';
END
ELSE
BEGIN
    PRINT 'La tabla UnDeportiva ya existe.';
END
GO

/* ============================================================
   TABLA: Cancha
   Representa una cancha dentro de una unidad deportiva.
   ============================================================ */
IF OBJECT_ID(N'dbo.Cancha', N'U') IS NULL
BEGIN
    CREATE TABLE dbo.Cancha (
        numCancha INT NOT NULL,
        tipoCancha NVARCHAR(30) NULL,
        cveUd VARCHAR(4) NOT NULL,

        CONSTRAINT PK_Cancha
        PRIMARY KEY CLUSTERED (numCancha, cveUd),

        CONSTRAINT FK_Cancha_UnDeportiva
        FOREIGN KEY (cveUd)
        REFERENCES dbo.UnDeportiva(cveUd)
    );

    PRINT 'Tabla Cancha creada.';
END
ELSE
BEGIN
    PRINT 'La tabla Cancha ya existe.';
END
GO

/* ============================================================
   TABLA: Jornada
   Representa un enfrentamiento generado en una jornada.

   No usamos local/visitante.
   Usamos Equipo A y Equipo B porque el proyecto será pequeño.
   ============================================================ */
IF OBJECT_ID(N'dbo.Jornada', N'U') IS NULL
BEGIN
    CREATE TABLE dbo.Jornada (
        idJornada INT IDENTITY(1,1) NOT NULL,
        numJornada INT NOT NULL,
        cveEquipoA INT NOT NULL,
        cveEquipoB INT NOT NULL,
        nomCortoCat VARCHAR(4) NOT NULL,
        perTorneo VARCHAR(5) NOT NULL,
        cveLiga VARCHAR(4) NOT NULL,
        fechaProgramada DATE NULL,
        estadoJornada VARCHAR(20) NOT NULL CONSTRAINT DF_Jornada_Estado DEFAULT 'PROGRAMADA',

        CONSTRAINT PK_Jornada
        PRIMARY KEY CLUSTERED (idJornada),

        CONSTRAINT FK_Jornada_Categoria
        FOREIGN KEY (nomCortoCat, perTorneo, cveLiga)
        REFERENCES dbo.Categoria(nomCortoCat, perTorneo, cveLiga),

        CONSTRAINT FK_Jornada_EquipoA
        FOREIGN KEY (cveEquipoA)
        REFERENCES dbo.Equipo(cveEquipo),

        CONSTRAINT FK_Jornada_EquipoB
        FOREIGN KEY (cveEquipoB)
        REFERENCES dbo.Equipo(cveEquipo),

        CONSTRAINT CK_Jornada_Equipos_Diferentes
        CHECK (cveEquipoA <> cveEquipoB),

        CONSTRAINT CK_Jornada_Numero
        CHECK (numJornada > 0),

        CONSTRAINT CK_Jornada_Estado
        CHECK (estadoJornada IN ('PROGRAMADA', 'JUGADA', 'CANCELADA')),

        CONSTRAINT UQ_Jornada_Encuentro
        UNIQUE (
            numJornada,
            cveEquipoA,
            cveEquipoB,
            nomCortoCat,
            perTorneo,
            cveLiga
        )
    );

    PRINT 'Tabla Jornada creada.';
END
ELSE
BEGIN
    PRINT 'La tabla Jornada ya existe.';
END
GO

/* ============================================================
   TABLA: Partido
   Representa el partido asociado a una jornada.

   No usamos golesLocal/golesVisita.
   Usamos golesEquipoA/golesEquipoB.
   ============================================================ */
IF OBJECT_ID(N'dbo.Partido', N'U') IS NULL
BEGIN
    CREATE TABLE dbo.Partido (
        idPartido INT IDENTITY(1,1) NOT NULL,
        idJornada INT NOT NULL,
        fechaPart DATE NULL,
        horaPart TIME(0) NULL,
        numCancha INT NULL,
        cveUd VARCHAR(4) NULL,
        numArb VARCHAR(4) NULL,
        golesEquipoA INT NULL,
        golesEquipoB INT NULL,
        estadoPartido VARCHAR(20) NOT NULL CONSTRAINT DF_Partido_Estado DEFAULT 'PROGRAMADO',
        observaciones NVARCHAR(500) NULL,

        CONSTRAINT PK_Partido
        PRIMARY KEY CLUSTERED (idPartido),

        CONSTRAINT FK_Partido_Jornada
        FOREIGN KEY (idJornada)
        REFERENCES dbo.Jornada(idJornada),

        CONSTRAINT FK_Partido_Cancha
        FOREIGN KEY (numCancha, cveUd)
        REFERENCES dbo.Cancha(numCancha, cveUd),

        CONSTRAINT FK_Partido_Arbitro
        FOREIGN KEY (numArb)
        REFERENCES dbo.Arbitro(numArb),

        CONSTRAINT CK_Partido_GolesEquipoA
        CHECK (golesEquipoA IS NULL OR golesEquipoA >= 0),

        CONSTRAINT CK_Partido_GolesEquipoB
        CHECK (golesEquipoB IS NULL OR golesEquipoB >= 0),

        CONSTRAINT CK_Partido_Estado
        CHECK (estadoPartido IN ('PROGRAMADO', 'JUGADO', 'CANCELADO'))
    );

    PRINT 'Tabla Partido creada.';
END
ELSE
BEGIN
    PRINT 'La tabla Partido ya existe.';
END
GO

/* ============================================================
   Verificación final de tablas
   ============================================================ */
SELECT 
    TABLE_SCHEMA AS esquema,
    TABLE_NAME AS tabla
FROM INFORMATION_SCHEMA.TABLES
WHERE TABLE_TYPE = 'BASE TABLE'
ORDER BY TABLE_NAME;
GO

 
 /* ============================================================
   TABLA: TipoEventoPartido
   Catálogo de eventos que pueden ocurrir en un partido.
   Ejemplo: gol, autogol, tarjeta amarilla, tarjeta roja.
   ============================================================ */
IF OBJECT_ID(N'dbo.TipoEventoPartido', N'U') IS NULL
BEGIN
    CREATE TABLE dbo.TipoEventoPartido (
        idTipoEvento TINYINT IDENTITY(1,1) NOT NULL,
        nombreEvento NVARCHAR(40) NOT NULL,
        abreviatura VARCHAR(10) NOT NULL,
        afectaMarcador BIT NOT NULL CONSTRAINT DF_TipoEvento_AfectaMarcador DEFAULT 0,

        CONSTRAINT PK_TipoEventoPartido
        PRIMARY KEY CLUSTERED (idTipoEvento),

        CONSTRAINT UQ_TipoEventoPartido_Nombre
        UNIQUE (nombreEvento),

        CONSTRAINT UQ_TipoEventoPartido_Abreviatura
        UNIQUE (abreviatura)
    );

    PRINT 'Tabla TipoEventoPartido creada.';
END
ELSE
BEGIN
    PRINT 'La tabla TipoEventoPartido ya existe.';
END
GO

/* ============================================================
   TABLA: CedulaArbitral
   Encabezado de la cédula capturada por el árbitro.
   ============================================================ */
IF OBJECT_ID(N'dbo.CedulaArbitral', N'U') IS NULL
BEGIN
    CREATE TABLE dbo.CedulaArbitral (
        idCedula INT IDENTITY(1,1) NOT NULL,
        idPartido INT NOT NULL,
        numArb VARCHAR(4) NOT NULL,
        fechaCaptura DATETIME2(0) NOT NULL CONSTRAINT DF_Cedula_FechaCaptura DEFAULT SYSDATETIME(),
        estadoCedula VARCHAR(20) NOT NULL CONSTRAINT DF_Cedula_Estado DEFAULT 'BORRADOR',
        observacionesGenerales NVARCHAR(800) NULL,

        CONSTRAINT PK_CedulaArbitral
        PRIMARY KEY CLUSTERED (idCedula),

        CONSTRAINT FK_CedulaArbitral_Partido
        FOREIGN KEY (idPartido)
        REFERENCES dbo.Partido(idPartido),

        CONSTRAINT FK_CedulaArbitral_Arbitro
        FOREIGN KEY (numArb)
        REFERENCES dbo.Arbitro(numArb),

        CONSTRAINT UQ_CedulaArbitral_Partido
        UNIQUE (idPartido),

        CONSTRAINT CK_CedulaArbitral_Estado
        CHECK (estadoCedula IN ('BORRADOR', 'CERRADA', 'CANCELADA'))
    );

    PRINT 'Tabla CedulaArbitral creada.';
END
ELSE
BEGIN
    PRINT 'La tabla CedulaArbitral ya existe.';
END
GO

/* ============================================================
   TABLA: EventoPartido
   Detalle de eventos registrados en la cédula arbitral.
   ============================================================ */
IF OBJECT_ID(N'dbo.EventoPartido', N'U') IS NULL
BEGIN
    CREATE TABLE dbo.EventoPartido (
        idEvento INT IDENTITY(1,1) NOT NULL,
        idCedula INT NOT NULL,
        idTipoEvento TINYINT NOT NULL,
        numJug VARCHAR(4) NULL,
        cveEquipo INT NOT NULL,
        minuto INT NULL,
        observacion NVARCHAR(300) NULL,

        CONSTRAINT PK_EventoPartido
        PRIMARY KEY CLUSTERED (idEvento),

        CONSTRAINT FK_EventoPartido_Cedula
        FOREIGN KEY (idCedula)
        REFERENCES dbo.CedulaArbitral(idCedula),

        CONSTRAINT FK_EventoPartido_TipoEvento
        FOREIGN KEY (idTipoEvento)
        REFERENCES dbo.TipoEventoPartido(idTipoEvento),

        CONSTRAINT FK_EventoPartido_Jugador
        FOREIGN KEY (numJug)
        REFERENCES dbo.Jugador(numJug),

        CONSTRAINT FK_EventoPartido_Equipo
        FOREIGN KEY (cveEquipo)
        REFERENCES dbo.Equipo(cveEquipo),

        CONSTRAINT CK_EventoPartido_Minuto
        CHECK (minuto IS NULL OR minuto BETWEEN 0 AND 150)
    );

    PRINT 'Tabla EventoPartido creada.';
END
ELSE
BEGIN
    PRINT 'La tabla EventoPartido ya existe.';
END
GO





/* ============================================================
   Tipos de eventos de partido
   ============================================================ */
IF NOT EXISTS (SELECT 1 FROM dbo.TipoEventoPartido)
BEGIN
    INSERT INTO dbo.TipoEventoPartido (
        nombreEvento,
        abreviatura,
        afectaMarcador
    )
    VALUES
        (N'Gol', 'GOL', 1),
        (N'Autogol', 'AUTOGOL', 1),
        (N'Tarjeta amarilla', 'AMARILLA', 0),
        (N'Tarjeta roja', 'ROJA', 0),
        (N'Observación', 'OBS', 0);

    PRINT 'Tipos de eventos insertados.';
END
ELSE
BEGIN
    PRINT 'Los tipos de eventos ya existen.';
END
GO