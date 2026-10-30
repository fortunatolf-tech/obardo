<?php

declare(strict_types=1);

namespace OBardo\Domain\Character;

/**
 * Avaliador Matemático de Fórmulas de Dados com Semente Criptográfica Segura (CSPRNG)
 */
final class DiceRoller
{
    public static function roll(string $formula, string $label = 'Rolagem'): array
    {
        $formula = trim($formula);
        $numDice = 1;
        $sides = 20;
        $modifier = 0;

        if (preg_match('/^(\d+)?d(\d+)([\+\-]\d+)?$/i', $formula, $matches)) {
            $numDice = max(1, min(50, (int)($matches[1] ?: 1)));
            $sides = max(2, min(1000, (int)$matches[2]));
            $modifier = isset($matches[3]) ? (int)$matches[3] : 0;
        }

        $diceResults = [];
        $sum = 0;

        for ($i = 0; $i < $numDice; $i++) {
            $val = random_int(1, $sides);
            $diceResults[] = $val;
            $sum += $val;
        }

        $total = $sum + $modifier;

        // Avaliação de Sucessos Críticos e Falhas Críticas conforme o Sistema de RPG
        $isCritical = false;
        $isFumble = false;

        if ($sides === 20 && $numDice === 1) {
            // Sistema D20 (Tormenta20, D&D 5e, Pathfinder, OSR)
            $isCritical = ($diceResults[0] === 20);
            $isFumble = ($diceResults[0] === 1);
        } elseif ($sides === 100 && $numDice === 1) {
            // Sistema D100 (Call of Cthulhu, BRP, RuneQuest)
            $isCritical = ($diceResults[0] <= 5);
            $isFumble = ($diceResults[0] >= 96);
        } elseif ($sides === 6 && $numDice === 3) {
            // Sistema 3d6 (GURPS, Hero System)
            $isCritical = ($sum <= 4);
            $isFumble = ($sum >= 17);
        } elseif ($sides === 6 && $numDice === 1) {
            // Sistema 3D&T / 1d6
            $isCritical = ($diceResults[0] === 6);
            $isFumble = ($diceResults[0] === 1);
        }

        return [
            'formula' => $formula,
            'label' => $label,
            'dice_results' => $diceResults,
            'modifier' => $modifier,
            'total' => $total,
            'is_critical' => $isCritical,
            'is_fumble' => $isFumble,
            'rolled_at' => date('Y-m-d H:i:s'),
        ];
    }
}
