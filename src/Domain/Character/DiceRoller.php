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
        $isCritical = ($sides === 20 && $numDice === 1 && $diceResults[0] === 20);
        $isFumble = ($sides === 20 && $numDice === 1 && $diceResults[0] === 1);

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
