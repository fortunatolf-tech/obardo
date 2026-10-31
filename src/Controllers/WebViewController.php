<?php

declare(strict_types=1);

namespace OBardo\Controllers;

use OBardo\Core\Request;
use OBardo\Core\Response;
use OBardo\Core\View;
use OBardo\Domain\Audio\AudioRepository;
use OBardo\Domain\Campaign\CampaignRepository;
use OBardo\Domain\Character\CharacterRepository;
use OBardo\Domain\Story\StoryRepository;
use OBardo\Domain\User\UserRepository;

/**
 * Controlador de Visualizações Web (Frontend SSR + Client Interactivity)
 */
final class WebViewController
{
    private CampaignRepository $campaignRepo;
    private CharacterRepository $charRepo;
    private AudioRepository $audioRepo;
    private StoryRepository $storyRepo;

    public function __construct()
    {
        $this->campaignRepo = new CampaignRepository();
        $this->charRepo = new CharacterRepository();
        $this->audioRepo = new AudioRepository();
        $this->storyRepo = new StoryRepository();
    }

    public function home(Request $request): void
    {
        $token = $request->getBearerToken();
        if ($token) {
            $userRepo = new UserRepository();
            if ($userRepo->findByToken($token)) {
                Response::redirect('/dashboard');
            }
        }
        View::render('auth.login', ['title' => 'O Bardo • Iniciar Sessão']);
    }

    public function login(Request $request): void
    {
        View::render('auth.login', ['title' => 'Entrar • O Bardo']);
    }

    public function register(Request $request): void
    {
        View::render('auth.register', ['title' => 'Criar Conta • O Bardo']);
    }

    public function dashboard(Request $request): void
    {
        $user = $GLOBALS['auth_user'];
        $campaigns = $this->campaignRepo->findByUser($user->id);

        View::render('campaign.dashboard', [
            'title' => 'Minhas Campanhas • O Bardo',
            'user' => $user,
            'campaigns' => $campaigns,
        ]);
    }

    public function playerSheet(Request $request, array $params): void
    {
        $user = $GLOBALS['auth_user'];
        $campaignId = (int)$params['id'];
        $campaign = $this->campaignRepo->findById($campaignId);

        if (!$campaign) {
            Response::redirect('/dashboard');
        }

        // Busca a ficha do usuário nesta campanha ou a primeira da campanha se for o Mestre
        $character = $this->charRepo->findByUserAndCampaign($user->id, $campaignId);
        if (!$character) {
            $characters = $this->charRepo->findByCampaign($campaignId);
            $character = $characters[0] ?? null;
        }

        View::render('character.sheet', [
            'title' => ($character ? $character->name : 'Ficha') . ' • O Bardo',
            'user' => $user,
            'campaign' => $campaign,
            'character' => $character,
            'is_gm' => $this->campaignRepo->isGM($campaignId, $user->id),
        ]);
    }

    public function gmDashboard(Request $request, array $params): void
    {
        $user = $GLOBALS['auth_user'];
        $campaignId = (int)$params['id'];
        $campaign = $this->campaignRepo->findById($campaignId);

        if (!$campaign || !$this->campaignRepo->isGM($campaignId, $user->id)) {
            Response::redirect('/campaigns/' . $campaignId . '/sheet');
        }

        $characters = $this->charRepo->findByCampaign($campaignId);
        $syncState = $this->audioRepo->getSyncState($campaignId);
        $tracks = $this->audioRepo->getTracks($campaignId);
        $rolls = $this->charRepo->getRecentRolls($campaignId, 15);
        $handouts = $this->storyRepo->getHandouts($campaignId, false);
        $members = $this->campaignRepo->getMembers($campaignId);

        View::render('gm.dashboard', [
            'title' => 'Comando do Mestre • ' . $campaign->title,
            'user' => $user,
            'campaign' => $campaign,
            'characters' => $characters,
            'sync_state' => $syncState,
            'tracks' => $tracks,
            'rolls' => $rolls,
            'handouts' => $handouts,
            'members' => $members,
        ]);
    }

    public function liveTaverna(Request $request, array $params): void
    {
        $user = $GLOBALS['auth_user'];
        $campaignId = (int)$params['id'];
        $campaign = $this->campaignRepo->findById($campaignId);

        if (!$campaign) {
            Response::redirect('/dashboard');
        }

        $characters = $this->charRepo->findByCampaign($campaignId);
        $syncState = $this->audioRepo->getSyncState($campaignId);
        $chronicles = $this->storyRepo->getChronicles($campaignId);
        $handouts = $this->storyRepo->getHandouts($campaignId, true);
        $rolls = $this->charRepo->getRecentRolls($campaignId, 25);

        View::render('session.taverna', [
            'title' => 'A Taverna • ' . $campaign->title,
            'user' => $user,
            'campaign' => $campaign,
            'characters' => $characters,
            'sync_state' => $syncState,
            'chronicles' => $chronicles,
            'handouts' => $handouts,
            'rolls' => $rolls,
            'is_gm' => $this->campaignRepo->isGM($campaignId, $user->id),
        ]);
    }
}
