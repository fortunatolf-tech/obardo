<?php

declare(strict_types=1);

namespace OBardo\Controllers;

use OBardo\Core\Request;
use OBardo\Core\Response;
use OBardo\Domain\Character\CharacterRepository;
use OBardo\Domain\Character\DiceRoller;
use OBardo\Domain\Realtime\EventDispatcher;

/**
 * Controlador de Fichas de Personagem e Rolador de Dados D20
 */
final class CharacterController
{
    private CharacterRepository $charRepo;

    public function __construct()
    {
        $this->charRepo = new CharacterRepository();
    }

    public function index(Request $request, array $params): void
    {
        $campaignId = (int)$params['id'];
        $characters = $this->charRepo->findByCampaign($campaignId);

        $data = array_map(fn($c) => $c->toArray(), $characters);

        Response::json([
            'status' => 'success',
            'data' => [
                'characters' => $data,
            ]
        ]);
    }

    public function show(Request $request, array $params): void
    {
        $charId = (int)$params['id'];
        $character = $this->charRepo->findById($charId);

        if (!$character) {
            Response::error('Personagem Não Encontrado', 'A ficha solicitada não existe.', 404);
        }

        Response::json([
            'status' => 'success',
            'data' => [
                'character' => $character->toArray(),
            ]
        ]);
    }

    public function store(Request $request, array $params): void
    {
        $user = $GLOBALS['auth_user'];
        $campaignId = (int)$params['id'];

        $name = trim((string)$request->input('name', ''));
        $classAndLevel = (string)$request->input('class_and_level', 'Guerreiro 1');
        $raceOrigin = (string)$request->input('race_origin', 'Humano');
        $hpMax = (int)$request->input('hp_max', 20);
        $mpMax = (int)$request->input('mp_max', 6);
        $armorClass = (int)$request->input('armor_class', 15);
        $attributes = (array)$request->input('attributes', []);
        $skills = (array)$request->input('skills', []);
        $inventory = (array)$request->input('inventory', []);
        $spells = (array)$request->input('spells', []);

        if ($name === '') {
            Response::error('Validação Inválida', 'Nome do personagem é obrigatório.', 422);
        }

        $character = $this->charRepo->create(
            $campaignId,
            $user->id,
            $name,
            $classAndLevel,
            $raceOrigin,
            $hpMax,
            $mpMax,
            $armorClass,
            $attributes,
            $skills,
            $inventory,
            $spells
        );

        // Notifica a mesa em tempo real
        EventDispatcher::dispatch($campaignId, 'character_created', [
            'character' => $character->toArray(),
            'created_by' => $user->name,
        ]);

        Response::json([
            'status' => 'success',
            'message' => 'Ficha criada com sucesso!',
            'data' => [
                'character' => $character->toArray(),
            ]
        ], 201);
    }

    public function updateStatus(Request $request, array $params): void
    {
        $charId = (int)$params['id'];
        $character = $this->charRepo->findById($charId);

        if (!$character) {
            Response::error('Personagem Não Encontrado', 'A ficha solicitada não existe.', 404);
        }

        $hp = $request->input('hp_current') !== null ? (int)$request->input('hp_current') : null;
        $mp = $request->input('mp_current') !== null ? (int)$request->input('mp_current') : null;
        $ca = $request->input('armor_class') !== null ? (int)$request->input('armor_class') : null;

        $this->charRepo->updateStatus($charId, $hp, $mp, $ca);
        $updated = $this->charRepo->findById($charId);

        // Transmissão instantânea do status atualizado para a Taverna e Mestre
        EventDispatcher::dispatch($character->campaignId, 'character_status_updated', [
            'character_id' => $charId,
            'name' => $character->name,
            'hp_current' => $updated->hpCurrent,
            'hp_max' => $updated->hpMax,
            'mp_current' => $updated->mpCurrent,
            'mp_max' => $updated->mpMax,
            'armor_class' => $updated->armorClass,
        ]);

        Response::json([
            'status' => 'success',
            'message' => 'Status atualizado com sucesso.',
            'data' => [
                'character' => $updated->toArray(),
            ]
        ]);
    }

    public function roll(Request $request, array $params): void
    {
        $user = $GLOBALS['auth_user'];
        $campaignId = (int)$params['id'];
        $formula = (string)$request->input('formula', '1d20');
        $label = (string)$request->input('label', 'Rolagem');
        $characterId = $request->input('character_id') ? (int)$request->input('character_id') : null;
        $rollerName = (string)$request->input('roller_name', $user->name);
        $isSecret = (bool)$request->input('is_secret', false);

        $roll = DiceRoller::roll($formula, $label);

        $rollId = $this->charRepo->addDiceRoll(
            $campaignId,
            $user->id,
            $characterId,
            $rollerName,
            $formula,
            $label,
            $roll['dice_results'],
            $roll['modifier'],
            $roll['total'],
            $roll['is_critical'],
            $roll['is_fumble'],
            $isSecret
        );

        $roll['id'] = $rollId;
        $roll['roller_name'] = $rollerName;
        $roll['campaign_id'] = $campaignId;

        // Se não for rolagem secreta do Mestre, transmite para todos na mesa em tempo real
        if (!$isSecret) {
            EventDispatcher::dispatch($campaignId, 'dice_roll_broadcast', $roll);
        }

        Response::json([
            'status' => 'success',
            'data' => [
                'roll' => $roll,
            ]
        ]);
    }

    public function rollHistory(Request $request, array $params): void
    {
        $campaignId = (int)$params['id'];
        $rolls = $this->charRepo->getRecentRolls($campaignId, 30);

        Response::json([
            'status' => 'success',
            'data' => [
                'rolls' => $rolls,
            ]
        ]);
    }
}
