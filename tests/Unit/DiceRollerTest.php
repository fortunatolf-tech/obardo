<?php

declare(strict_types=1);

namespace OBardo\Tests\Unit;

use PHPUnit\Framework\TestCase;
use OBardo\Domain\Character\DiceRoller;

/**
 * Testes Unitários do Avaliador Matemático e CSPRNG de Dados (RF11, RF12, RF13)
 * Abrange D20 (Tormenta20/D&D), D100 (Cthulhu), 3d6 (GURPS) e fórmulas livres
 */
final class DiceRollerTest extends TestCase
{
    public function testStandardD20RollWithinBounds(): void
    {
        for ($i = 0; $i < 50; $i++) {
            $result = DiceRoller::roll('1d20', 'Ataque Espada');

            $this->assertSame('1d20', $result['formula']);
            $this->assertSame('Ataque Espada', $result['label']);
            $this->assertCount(1, $result['dice_results']);
            $this->assertSame(0, $result['modifier']);

            $diceVal = $result['dice_results'][0];
            $this->assertGreaterThanOrEqual(1, $diceVal);
            $this->assertLessThanOrEqual(20, $diceVal);
            $this->assertSame($diceVal, $result['total']);

            if ($diceVal === 20) {
                $this->assertTrue($result['is_critical'], 'Natural 20 deve ser crítico no D20');
                $this->assertFalse($result['is_fumble']);
            } elseif ($diceVal === 1) {
                $this->assertTrue($result['is_fumble'], 'Natural 1 deve ser falha crítica no D20');
                $this->assertFalse($result['is_critical']);
            } else {
                $this->assertFalse($result['is_critical']);
                $this->assertFalse($result['is_fumble']);
            }
        }
    }

    public function testD20WithPositiveAndNegativeModifiers(): void
    {
        $rollPlus = DiceRoller::roll('1d20+5', 'Percepção');
        $this->assertSame(5, $rollPlus['modifier']);
        $this->assertSame($rollPlus['dice_results'][0] + 5, $rollPlus['total']);

        $rollMinus = DiceRoller::roll('1d20-3', 'Desvantagem');
        $this->assertSame(-3, $rollMinus['modifier']);
        $this->assertSame($rollMinus['dice_results'][0] - 3, $rollMinus['total']);
    }

    public function testPercentileD100RollBoundsAndCriticals(): void
    {
        for ($i = 0; $i < 40; $i++) {
            $result = DiceRoller::roll('1d100', 'Sanidade Cthulhu');
            $val = $result['dice_results'][0];

            $this->assertGreaterThanOrEqual(1, $val);
            $this->assertLessThanOrEqual(100, $val);
            $this->assertSame($val, $result['total']);

            if ($val <= 5) {
                $this->assertTrue($result['is_critical'], 'Valores <= 5 são críticos no D100');
            } elseif ($val >= 96) {
                $this->assertTrue($result['is_fumble'], 'Valores >= 96 são falhas no D100');
            }
        }
    }

    public function testSystem3d6BoundsAndModifiers(): void
    {
        for ($i = 0; $i < 30; $i++) {
            $result = DiceRoller::roll('3d6+2', 'Teste de Habilidade GURPS');
            $this->assertCount(3, $result['dice_results']);
            $this->assertSame(2, $result['modifier']);

            $sumDice = array_sum($result['dice_results']);
            $this->assertGreaterThanOrEqual(3, $sumDice);
            $this->assertLessThanOrEqual(18, $sumDice);
            $this->assertSame($sumDice + 2, $result['total']);

            if ($sumDice <= 4) {
                $this->assertTrue($result['is_critical']);
            } elseif ($sumDice >= 17) {
                $this->assertTrue($result['is_fumble']);
            }
        }
    }

    public function testMultiDiceAndPolyhedrals(): void
    {
        $d8 = DiceRoller::roll('4d8+4', 'Bola de Fogo Curada');
        $this->assertCount(4, $d8['dice_results']);
        $this->assertSame(4, $d8['modifier']);
        foreach ($d8['dice_results'] as $die) {
            $this->assertGreaterThanOrEqual(1, $die);
            $this->assertLessThanOrEqual(8, $die);
        }

        $d12 = DiceRoller::roll('2d12', 'Machado de Bárbaro');
        $this->assertCount(2, $d12['dice_results']);
        $this->assertSame(0, $d12['modifier']);
    }

    public function testFallbackForInvalidFormula(): void
    {
        // Se a fórmula for inválida, deve assumir 1d20 com segurança
        $result = DiceRoller::roll('texto-invalido', 'Fallback');
        $this->assertCount(1, $result['dice_results']);
        $this->assertGreaterThanOrEqual(1, $result['dice_results'][0]);
        $this->assertLessThanOrEqual(20, $result['dice_results'][0]);
    }
}
