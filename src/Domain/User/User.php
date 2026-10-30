<?php

declare(strict_types=1);

namespace OBardo\Domain\User;

/**
 * Entidade de Usuário
 */
final readonly class User
{
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
        public string $passwordHash,
        public string $role = 'user',
        public ?string $token = null,
        public ?string $createdAt = null
    ) {}

    public function toArray(bool $includeSensitive = false): array
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'created_at' => $this->createdAt,
        ];

        if ($includeSensitive) {
            $data['token'] = $this->token;
        }

        return $data;
    }
}
