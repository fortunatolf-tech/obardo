<?php

declare(strict_types=1);

namespace OBardo\Domain\Character;

/**
 * Entidade de Ficha de Personagem (Tormenta20 & D&D D20 System)
 */
final readonly class CharacterSheet
{
    public function __construct(
        public int $id,
        public int $campaignId,
        public int $userId,
        public string $name,
        public string $classAndLevel = 'Guerreiro 1',
        public string $raceOrigin = 'Humano',
        public int $hpCurrent = 20,
        public int $hpMax = 20,
        public int $mpCurrent = 6,
        public int $mpMax = 6,
        public int $armorClass = 15,
        public array $attributesData = [],
        public array $skillsData = [],
        public array $inventoryData = [],
        public array $spellsData = [],
        public ?string $notes = null,
        public ?string $updatedAt = null
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'campaign_id' => $this->campaignId,
            'user_id' => $this->userId,
            'name' => $this->name,
            'class_and_level' => $this->classAndLevel,
            'race_origin' => $this->raceOrigin,
            'hp_current' => $this->hpCurrent,
            'hp_max' => $this->hpMax,
            'mp_current' => $this->mpCurrent,
            'mp_max' => $this->mpMax,
            'armor_class' => $this->armorClass,
            'attributes' => $this->attributesData,
            'skills' => $this->skillsData,
            'inventory' => $this->inventoryData,
            'spells' => $this->spellsData,
            'notes' => $this->notes,
            'updated_at' => $this->updatedAt,
        ];
    }
}
