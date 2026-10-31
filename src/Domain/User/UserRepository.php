<?php

declare(strict_types=1);

namespace OBardo\Domain\User;

use OBardo\Core\Database;
use PDO;

/**
 * Repositório de Usuários via MariaDB PDO com Hashing Argon2id
 */
final class UserRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findByEmail(string $email): ?User
    {
        $stmt = $this->db->prepare("SELECT * FROM `users` WHERE `email` = :email LIMIT 1");
        $stmt->execute(['email' => strtolower(trim($email))]);
        $row = $stmt->fetch();

        return $row ? $this->hydrate($row) : null;
    }

    public function findById(int $id): ?User
    {
        $stmt = $this->db->prepare("SELECT * FROM `users` WHERE `id` = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ? $this->hydrate($row) : null;
    }

    public function findByToken(string $token): ?User
    {
        $stmt = $this->db->prepare("SELECT * FROM `users` WHERE `token` = :token AND (`token_expires_at` IS NULL OR `token_expires_at` > NOW()) LIMIT 1");
        $stmt->execute(['token' => $token]);
        $row = $stmt->fetch();

        return $row ? $this->hydrate($row) : null;
    }

    public function create(string $name, string $email, string $password, string $role = 'user'): User
    {
        $passwordHash = password_hash($password, PASSWORD_ARGON2ID, [
            'memory_cost' => 65536,
            'time_cost' => 4,
            'threads' => 1
        ]);

        $token = bin2hex(random_bytes(32));

        $stmt = $this->db->prepare("
            INSERT INTO `users` (`name`, `email`, `password_hash`, `role`, `token`, `token_expires_at`)
            VALUES (:name, :email, :hash, :role, :token, DATE_ADD(NOW(), INTERVAL 30 DAY))
        ");

        $stmt->execute([
            'name' => trim($name),
            'email' => strtolower(trim($email)),
            'hash' => $passwordHash,
            'role' => $role,
            'token' => $token,
        ]);

        $id = (int)$this->db->lastInsertId();
        return new User($id, $name, $email, $passwordHash, $role, $token);
    }

    public function updateToken(int $userId): string
    {
        $token = bin2hex(random_bytes(32));
        $stmt = $this->db->prepare("
            UPDATE `users` 
            SET `token` = :token, `token_expires_at` = DATE_ADD(NOW(), INTERVAL 30 DAY) 
            WHERE `id` = :id
        ");
        $stmt->execute(['token' => $token, 'id' => $userId]);

        return $token;
    }

    public function delete(int $userId): bool
    {
        $stmt = $this->db->prepare("DELETE FROM `users` WHERE `id` = :id");
        return $stmt->execute(['id' => $userId]);
    }

    public function exportUserData(int $userId): array
    {
        $user = $this->findById($userId);
        if (!$user) {
            return [];
        }

        $stmtC = $this->db->prepare("SELECT id, title, code, rule_system, created_at FROM `campaigns` WHERE `gm_user_id` = :uid");
        $stmtC->execute(['uid' => $userId]);
        $campaigns = $stmtC->fetchAll();

        $stmtCh = $this->db->prepare("SELECT id, campaign_id, name, class_and_level, race_origin, hp_current, hp_max, mp_current, mp_max, armor_class, created_at FROM `characters` WHERE `user_id` = :uid");
        $stmtCh->execute(['uid' => $userId]);
        $characters = $stmtCh->fetchAll();

        $stmtCr = $this->db->prepare("SELECT id, campaign_id, title, session_date, created_at FROM `chronicles` WHERE `author_user_id` = :uid");
        $stmtCr->execute(['uid' => $userId]);
        $chronicles = $stmtCr->fetchAll();

        return [
            'lgpd_export_notice' => 'Relatório de Portabilidade de Dados - Lei Geral de Proteção de Dados (Art. 18 LGPD)',
            'exported_at' => date('Y-m-d H:i:s'),
            'user' => $user->toArray(),
            'characters' => $characters,
            'campaigns' => $campaigns,
            'chronicles' => $chronicles,
        ];
    }

    private function hydrate(array $row): User
    {
        return new User(
            (int)$row['id'],
            $row['name'],
            $row['email'],
            $row['password_hash'],
            $row['role'],
            $row['token'],
            $row['created_at']
        );
    }
}
