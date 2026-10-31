<?php

declare(strict_types=1);

namespace OBardo\Tests\Unit;

use PHPUnit\Framework\TestCase;
use OBardo\Domain\User\User;
use OBardo\Domain\Campaign\Campaign;
use OBardo\Core\View;

/**
 * Testes Unitários de Segurança da Informação, LGPD e Isolamento Lógico (RF17/RN06/LGPD Art. 18)
 */
final class SecurityAndLgpdTest extends TestCase
{
    public function testUserEntityNeverLeaksPasswordHashInSerialization(): void
    {
        $hash = password_hash('SenhaUltraSecreta@2026', PASSWORD_ARGON2ID);
        $user = new User(
            id: 1,
            name: 'Mestre Arton',
            email: 'mestre@obardo.com.br',
            passwordHash: $hash,
            role: 'gm',
            token: 'bardo_test_token_123',
            createdAt: '2026-10-31 12:00:00'
        );

        $publicArray = $user->toArray();
        $this->assertArrayNotHasKey('passwordHash', $publicArray);
        $this->assertArrayNotHasKey('password_hash', $publicArray);
        $this->assertArrayNotHasKey('token', $publicArray);

        $sensitiveArray = $user->toArray(true);
        $this->assertArrayNotHasKey('passwordHash', $sensitiveArray);
        $this->assertArrayNotHasKey('password_hash', $sensitiveArray);
        $this->assertSame('bardo_test_token_123', $sensitiveArray['token']);
    }

    public function testArgon2idPasswordHashingStrength(): void
    {
        $password = 'ValkariaTormenta20#';
        $hash = password_hash($password, PASSWORD_ARGON2ID);

        $this->assertStringStartsWith('$argon2id$', $hash, 'Deve utilizar algoritmo Argon2id para segurança máxima contra GPU cracking');
        $this->assertTrue(password_verify($password, $hash));
        $this->assertFalse(password_verify('SenhaErrada', $hash));
    }

    public function testCampaignGMSecretsIsolationPerRF17AndRN06(): void
    {
        $secretPlot = 'O Arquimago Vectorius é secretamente controlado por um Lorde da Tormenta!';
        $campaign = new Campaign(
            id: 1,
            gmUserId: 42,
            title: 'Fogo em Arton',
            code: 'ARTON20',
            ruleSystem: 'TORMENTA20',
            description: 'Campanha épica de Tormenta20',
            gmSecrets: $secretPlot
        );

        // Jogadores normais chamam toArray() sem flag de segredo
        $playerView = $campaign->toArray(false);
        $this->assertArrayNotHasKey('gm_secrets', $playerView, 'Segredos do Mestre JAMAIS devem estar presentes na visão dos jogadores');

        // Mestre com autorização explícita
        $gmView = $campaign->toArray(true);
        $this->assertArrayHasKey('gm_secrets', $gmView);
        $this->assertSame($secretPlot, $gmView['gm_secrets']);
    }

    public function testViewHtmlEscapingPreventsXss(): void
    {
        $maliciousInput = "<script>alert('XSS Attack');</script><img src=x onerror=alert(1)>";
        $escaped = View::escape($maliciousInput);

        $this->assertStringNotContainsString('<script>', $escaped);
        $this->assertStringNotContainsString('</script>', $escaped);
        $this->assertStringContainsString('&lt;script&gt;', $escaped);
        $this->assertStringContainsString('&lt;img', $escaped);
    }
}
