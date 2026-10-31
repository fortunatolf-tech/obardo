<?php

declare(strict_types=1);

namespace OBardo\Domain\Character;

use OBardo\Core\Database;
use PDO;

/**
 * Repositório de Fichas de Personagens e Registro de Rolagens
 */
final class CharacterRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findById(int $id): ?CharacterSheet
    {
        $stmt = $this->db->prepare("SELECT * FROM `characters` WHERE `id` = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ? $this->hydrate($row) : null;
    }

    public function findByCampaign(int $campaignId): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM `characters` 
            WHERE `campaign_id` = :cid 
            ORDER BY `name` ASC
        ");
        $stmt->execute(['cid' => $campaignId]);

        $list = [];
        while ($row = $stmt->fetch()) {
            $list[] = $this->hydrate($row);
        }

        return $list;
    }

    public function findByUserAndCampaign(int $userId, int $campaignId): ?CharacterSheet
    {
        $stmt = $this->db->prepare("
            SELECT * FROM `characters` 
            WHERE `user_id` = :uid AND `campaign_id` = :cid 
            LIMIT 1
        ");
        $stmt->execute(['uid' => $userId, 'cid' => $campaignId]);
        $row = $stmt->fetch();

        return $row ? $this->hydrate($row) : null;
    }

    public function create(
        int $campaignId,
        int $userId,
        string $name,
        string $classAndLevel = 'Guerreiro 1',
        string $raceOrigin = 'Humano',
        int $hpMax = 20,
        int $mpMax = 6,
        int $armorClass = 15,
        array $attributes = [],
        array $skills = [],
        array $inventory = [],
        array $spells = []
    ): CharacterSheet {
        $stmt = $this->db->prepare("
            INSERT INTO `characters` 
            (`campaign_id`, `user_id`, `name`, `class_and_level`, `race_origin`, `hp_current`, `hp_max`, `mp_current`, `mp_max`, `armor_class`, `attributes_data`, `skills_data`, `inventory_data`, `spells_data`)
            VALUES 
            (:cid, :uid, :name, :class, :race, :hp_current, :hp_max, :mp_current, :mp_max, :ca, :attrs, :skills, :inv, :spells)
        ");

        $stmt->execute([
            'cid' => $campaignId,
            'uid' => $userId,
            'name' => trim($name),
            'class' => $classAndLevel,
            'race' => $raceOrigin,
            'hp_current' => $hpMax,
            'hp_max' => $hpMax,
            'mp_current' => $mpMax,
            'mp_max' => $mpMax,
            'ca' => $armorClass,
            'attrs' => json_encode($attributes, JSON_UNESCAPED_UNICODE),
            'skills' => json_encode($skills, JSON_UNESCAPED_UNICODE),
            'inv' => json_encode($inventory, JSON_UNESCAPED_UNICODE),
            'spells' => json_encode($spells, JSON_UNESCAPED_UNICODE),
        ]);

        $id = (int)$this->db->lastInsertId();
        return $this->findById($id);
    }

    public function updateStatus(int $characterId, ?int $hpCurrent = null, ?int $mpCurrent = null, ?int $armorClass = null): bool
    {
        $fields = [];
        $params = ['id' => $characterId];

        if ($hpCurrent !== null) {
            $fields[] = "`hp_current` = :hp";
            $params['hp'] = $hpCurrent;
        }
        if ($mpCurrent !== null) {
            $fields[] = "`mp_current` = :mp";
            $params['mp'] = $mpCurrent;
        }
        if ($armorClass !== null) {
            $fields[] = "`armor_class` = :ca";
            $params['ca'] = $armorClass;
        }

        if (empty($fields)) {
            return false;
        }

        $sql = "UPDATE `characters` SET " . implode(', ', $fields) . ", `updated_at` = NOW() WHERE `id` = :id";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute($params);
    }

    public function addDiceRoll(
        int $campaignId,
        int $userId,
        ?int $characterId,
        string $rollerName,
        string $formula,
        string $label,
        array $diceResults,
        int $modifier,
        int $total,
        bool $isCritical = false,
        bool $isFumble = false,
        bool $isSecret = false
    ): int {
        $stmt = $this->db->prepare("
            INSERT INTO `dice_rolls` 
            (`campaign_id`, `user_id`, `character_id`, `roller_name`, `formula`, `label`, `dice_breakdown`, `modifier`, `total_result`, `is_critical`, `is_fumble`, `is_secret`)
            VALUES 
            (:cid, :uid, :chid, :roller, :formula, :label, :breakdown, :mod, :total, :crit, :fumble, :secret)
        ");

        $stmt->execute([
            'cid' => $campaignId,
            'uid' => $userId,
            'chid' => $characterId,
            'roller' => $rollerName,
            'formula' => $formula,
            'label' => $label,
            'breakdown' => json_encode($diceResults),
            'mod' => $modifier,
            'total' => $total,
            'crit' => (int)$isCritical,
            'fumble' => (int)$isFumble,
            'secret' => (int)$isSecret,
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function getRecentRolls(int $campaignId, int $limit = 20): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM `dice_rolls` 
            WHERE `campaign_id` = :cid AND `is_secret` = 0
            ORDER BY `rolled_at` DESC 
            LIMIT :lim
        ");
        $stmt->bindValue(':cid', $campaignId, PDO::PARAM_INT);
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll();
        foreach ($rows as &$r) {
            $r['dice_breakdown'] = json_decode($r['dice_breakdown'], true);
        }

        return $rows;
    }

    private function hydrate(array $row): CharacterSheet
    {
        return new CharacterSheet(
            (int)$row['id'],
            (int)$row['campaign_id'],
            (int)$row['user_id'],
            $row['name'],
            $row['class_and_level'],
            $row['race_origin'],
            (int)$row['hp_current'],
            (int)$row['hp_max'],
            (int)$row['mp_current'],
            (int)$row['mp_max'],
            (int)$row['armor_class'],
            json_decode($row['attributes_data'] ?? '{}', true) ?: [],
            json_decode($row['skills_data'] ?? '{}', true) ?: [],
            json_decode($row['inventory_data'] ?? '[]', true) ?: [],
            json_decode($row['spells_data'] ?? '[]', true) ?: [],
            $row['notes'],
            $row['updated_at']
        );
    }
}
