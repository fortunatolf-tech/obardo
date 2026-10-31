<?php

declare(strict_types=1);

namespace OBardo\Domain\Campaign;

/**
 * Entidade de Campanha de RPG
 */
final readonly class Campaign
{
    public function __construct(
        public int $id,
        public int $gmUserId,
        public string $title,
        public string $code,
        public string $ruleSystem = 'TORMENTA20',
        public ?string $description = null,
        public ?string $gmSecrets = null,
        public ?string $coverUrl = null,
        public bool $isActive = true,
        public ?string $createdAt = null
    ) {}

    public function toArray(bool $includeSecrets = false): array
    {
        $data = [
            'id' => $this->id,
            'gm_user_id' => $this->gmUserId,
            'title' => $this->title,
            'code' => $this->code,
            'rule_system' => $this->ruleSystem,
            'description' => $this->description,
            'cover_url' => $this->coverUrl,
            'is_active' => $this->isActive,
            'created_at' => $this->createdAt,
        ];

        if ($includeSecrets) {
            $data['gm_secrets'] = $this->gmSecrets;
        }

        return $data;
    }
}
