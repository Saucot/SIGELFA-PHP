/*
    SIGELFA - Script 08
    Esquema de autenticacion de aplicacion.

    Este script crea usuarios de aplicacion y la relacion opcional
    entre usuarios con rol ARBITRO y la tabla dbo.Arbitro.

    No inserta usuarios ni contrasenas reales.
    Para crear un usuario de prueba, generar passwordHash con PHP:

        php tools/generar_hash.php "PasswordTemporal123"

    Despues insertar manualmente el hash resultante en dbo.Usuario.
*/

USE SIGELFA_DB;
GO

IF OBJECT_ID(N'dbo.Usuario', N'U') IS NULL
BEGIN
    CREATE TABLE dbo.Usuario (
        idUsuario INT IDENTITY(1,1) NOT NULL,
        nombreUsuario NVARCHAR(80) NOT NULL,
        email VARCHAR(120) NOT NULL,
        passwordHash VARCHAR(255) NOT NULL,
        rol VARCHAR(20) NOT NULL,
        activo BIT NOT NULL CONSTRAINT DF_Usuario_Activo DEFAULT 1,
        fechaAlta DATETIME2(0) NOT NULL CONSTRAINT DF_Usuario_FechaAlta DEFAULT SYSDATETIME(),

        CONSTRAINT PK_Usuario
        PRIMARY KEY CLUSTERED (idUsuario),

        CONSTRAINT UQ_Usuario_Email
        UNIQUE (email),

        CONSTRAINT CK_Usuario_Rol
        CHECK (rol IN ('GERENTE', 'ASISTENTE', 'ARBITRO'))
    );

    PRINT 'Tabla Usuario creada.';
END
ELSE
BEGIN
    PRINT 'La tabla Usuario ya existe.';
END
GO

IF OBJECT_ID(N'dbo.UsuarioArbitro', N'U') IS NULL
BEGIN
    CREATE TABLE dbo.UsuarioArbitro (
        idUsuario INT NOT NULL,
        numArb VARCHAR(4) NOT NULL,

        CONSTRAINT PK_UsuarioArbitro
        PRIMARY KEY CLUSTERED (idUsuario, numArb),

        CONSTRAINT FK_UsuarioArbitro_Usuario
        FOREIGN KEY (idUsuario)
        REFERENCES dbo.Usuario(idUsuario),

        CONSTRAINT FK_UsuarioArbitro_Arbitro
        FOREIGN KEY (numArb)
        REFERENCES dbo.Arbitro(numArb),

        CONSTRAINT UQ_UsuarioArbitro_Usuario
        UNIQUE (idUsuario),

        CONSTRAINT UQ_UsuarioArbitro_Arbitro
        UNIQUE (numArb)
    );

    PRINT 'Tabla UsuarioArbitro creada.';
END
ELSE
BEGIN
    PRINT 'La tabla UsuarioArbitro ya existe.';
END
GO

/*
    Ejemplo de insercion manual, reemplazando el hash por uno generado localmente:

    INSERT INTO dbo.Usuario (nombreUsuario, email, passwordHash, rol)
    VALUES (N'Usuario de prueba', 'usuario.prueba@example.test', 'HASH_GENERADO_CON_PASSWORD_HASH', 'GERENTE');

    Para un usuario arbitro, despues de insertar dbo.Usuario:

    INSERT INTO dbo.UsuarioArbitro (idUsuario, numArb)
    VALUES (ID_DEL_USUARIO, 'A001');
*/

