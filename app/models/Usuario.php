<?php

require_once __DIR__ . '/Database.php';

class Usuario {

    private PDO $conn;

    public function __construct() {

        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function obtenerPorEmail(string $email): ?array {

        $query = "
            SELECT
                idUsuario,
                nombreUsuario,
                email,
                passwordHash,
                rol,
                activo
            FROM dbo.Usuario
            WHERE email = :email
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':email', trim($email));
        $stmt->execute();

        $usuario = $stmt->fetch();

        return $usuario ?: null;
    }

    public function obtenerArbitroRelacionado(int $idUsuario): ?array {

        $query = "
            SELECT
                ua.idUsuario,
                ua.numArb,
                a.nomArb,
                a.apPatArb,
                a.apMatArb,
                a.activo
            FROM dbo.UsuarioArbitro ua
            INNER JOIN dbo.Arbitro a ON ua.numArb = a.numArb
            WHERE ua.idUsuario = :idUsuario
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':idUsuario', $idUsuario, PDO::PARAM_INT);
        $stmt->execute();

        $arbitro = $stmt->fetch();

        return $arbitro ?: null;
    }

    public function crearUsuario(array $datos): bool {

        $query = "
            INSERT INTO dbo.Usuario (
                nombreUsuario,
                email,
                passwordHash,
                rol,
                activo
            )
            VALUES (
                :nombreUsuario,
                :email,
                :passwordHash,
                :rol,
                :activo
            )
        ";

        $password = (string) ($datos['password'] ?? '');
        $passwordHash = (string) ($datos['passwordHash'] ?? '');

        if ($passwordHash === '') {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        }

        $stmt = $this->conn->prepare($query);

        return $stmt->execute([
            'nombreUsuario' => trim((string) ($datos['nombreUsuario'] ?? '')),
            'email' => trim((string) ($datos['email'] ?? '')),
            'passwordHash' => $passwordHash,
            'rol' => trim((string) ($datos['rol'] ?? 'ASISTENTE')),
            'activo' => (int) ($datos['activo'] ?? 1) === 1 ? 1 : 0,
        ]);
    }

    public function verificarCredenciales(string $email, string $password): ?array {

        $usuario = $this->obtenerPorEmail($email);

        if ($usuario === null || (int) ($usuario['activo'] ?? 0) !== 1) {
            return null;
        }

        if (!password_verify($password, (string) ($usuario['passwordHash'] ?? ''))) {
            return null;
        }

        $usuarioSeguro = $this->sinPasswordHash($usuario);

        if (($usuarioSeguro['rol'] ?? '') === 'ARBITRO') {
            $arbitro = $this->obtenerArbitroRelacionado((int) $usuarioSeguro['idUsuario']);
            $usuarioSeguro['numArb'] = $arbitro['numArb'] ?? null;
        }

        return $usuarioSeguro;
    }

    private function sinPasswordHash(array $usuario): array {

        unset($usuario['passwordHash']);

        return $usuario;
    }
}

