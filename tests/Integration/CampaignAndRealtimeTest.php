<?php

declare(strict_types=1);

namespace OBardo\Tests\Integration;

use PHPUnit\Framework\TestCase;
use OBardo\Core\Config;
use OBardo\Core\Database;
use OBardo\Domain\Realtime\EventDispatcher;
use OBardo\Domain\Campaign\CampaignRepository;
use OBardo\Domain\Story\StoryRepository;

/**
 * Testes de Integração com MariaDB: Eventos em Tempo Real, Segredos do Mestre e Handouts
 */
final class CampaignAndRealtimeTest extends TestCase
{
    protected function setUp(): void
    {
        Config::load(__DIR__ . '/../../.env');
    }

    public function testRealtimeEventDispatcherPersistenceAndRetrieval(): void
    {
        $campaignId = 1;
        $uniqueType = 'test_event_' . bin2hex(random_bytes(4));
        $payload = [
            'hero' => 'Paladino de Khalmyr',
            'action' => 'Golpe Divino',
            'damage' => 24,
        ];

        // Dispara evento
        EventDispatcher::dispatch($campaignId, $uniqueType, $payload);

        // Busca eventos recentes
        $events = EventDispatcher::getEventsAfter($campaignId, 0, 50);
        $this->assertNotEmpty($events, 'Eventos devem ser retornados do banco de dados');

        $matched = null;
        foreach ($events as $event) {
            if ($event['event_type'] === $uniqueType) {
                $matched = $event;
                break;
            }
        }

        $this->assertNotNull($matched, "O evento com tipo {$uniqueType} deve ser encontrado no histórico");
        $this->assertSame($payload, $matched['payload']);
    }

    public function testCampaignGMSecretsUpdateAndIsolation(): void
    {
        $campaignRepo = new CampaignRepository();
        $campaignId = 1;
        $secretNote = 'O verdadeiro traidor é o lorde regente. Senha da cripta: ARTON-1404.';

        $campaignRepo->updateGMSecrets($campaignId, $secretNote);

        $campaign = $campaignRepo->findById($campaignId);
        $this->assertNotNull($campaign);
        $this->assertSame($secretNote, $campaign->gmSecrets);

        // Visão pública do jogador não deve conter os segredos
        $playerArray = $campaign->toArray(false);
        $this->assertArrayNotHasKey('gm_secrets', $playerArray);

        // Visão do mestre deve conter os segredos
        $gmArray = $campaign->toArray(true);
        $this->assertSame($secretNote, $gmArray['gm_secrets']);
    }

    public function testStoryHandoutsCreationAndVisibilityToggle(): void
    {
        $storyRepo = new StoryRepository();
        $campaignId = 1;
        $uniqueTitle = 'Carta Selada com Brasão Carmesim ' . bin2hex(random_bytes(2));

        // 1. Cria handout oculto (segredo do mestre)
        $handoutId = $storyRepo->addHandout(
            campaignId: $campaignId,
            title: $uniqueTitle,
            description: 'Instruções para envenenar o banquete de Valkaria.',
            imageUrl: null,
            isRevealed: false
        );

        $this->assertGreaterThan(0, $handoutId);

        // 2. Busca lista pública de handouts (onlyRevealed = true)
        $publicHandouts = $storyRepo->getHandouts($campaignId, true);
        $foundInPublic = false;
        foreach ($publicHandouts as $h) {
            if ($h['id'] == $handoutId) {
                $foundInPublic = true;
                break;
            }
        }
        $this->assertFalse($foundInPublic, 'Handout oculto NÃO deve aparecer para jogadores');

        // 3. Busca lista do Mestre (onlyRevealed = false)
        $gmHandouts = $storyRepo->getHandouts($campaignId, false);
        $foundInGM = false;
        foreach ($gmHandouts as $h) {
            if ($h['id'] == $handoutId) {
                $foundInGM = true;
                $this->assertSame(0, (int)$h['is_revealed']);
                break;
            }
        }
        $this->assertTrue($foundInGM, 'Handout deve estar visível para o Mestre com status oculto');

        // 4. Mestre revela o Handout à mesa
        $storyRepo->toggleHandoutVisibility($handoutId, true);

        // 5. Agora deve constar na lista pública
        $revealedPublic = $storyRepo->getHandouts($campaignId, true);
        $foundAfterReveal = false;
        foreach ($revealedPublic as $h) {
            if ($h['id'] == $handoutId) {
                $foundAfterReveal = true;
                $this->assertSame(1, (int)$h['is_revealed']);
                break;
            }
        }
        $this->assertTrue($foundAfterReveal, 'Após revelação do Mestre, o handout deve ser visível para todos os jogadores');
    }
}
