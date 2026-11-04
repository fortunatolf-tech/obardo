<?php require __DIR__ . '/../layout/header.php'; ?>

<main class="container mobile-container py-3">
  
  <!-- Banner Épico de Alta Fantasia (Tormenta & D&D) -->
  <div class="fantasy-art-banner mb-3" style="background-image: linear-gradient(180deg, rgba(10,11,16,0.2) 0%, rgba(10,11,16,0.92) 100%), url('/images/bg-tormenta-epic.jpg'); height: 160px; display: flex; flex-direction: column; justify-content: flex-end; padding: 1.1rem;">
    <div class="text-warning fw-bold text-uppercase fs-8 letter-spacing-1 text-shadow-sm">
      🏰 Salão das Guildas • Arte & Ambientação Tormenta & D&D
    </div>
    <div class="font-title fs-4 text-white fw-bold text-shadow">
      Minhas Campanhas
    </div>
    <div class="text-light opacity-75 fs-7 text-shadow-sm">
      Compatível com qualquer sistema de RPG de mesa tradicional
    </div>
  </div>

  <!-- Saudação ao Usuário -->
  <div class="rpg-card mb-3 p-3 p-md-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
      <div>
        <h1 class="font-mythic fs-4 text-warning mb-1">Olá, <?= $e($user->name) ?>!</h1>
        <p class="text-muted fs-7 mb-0">Empunhe sua espada e reúna seu grupo para a próxima sessão.</p>
      </div>
      <div>
        <button class="btn btn-solid theme-primary btn-touch" data-bs-toggle="modal" data-bs-target="#modal-new-campaign">
          + Nova Campanha
        </button>
      </div>
    </div>
  </div>

  <!-- Ingressar por Código -->
  <div class="rpg-card mb-3 p-3">
    <form action="/join" method="POST" class="d-flex gap-2 align-items-center">
      <input type="text" name="code" placeholder="Código de Convite (ex: BARDO-T20X)" required class="form-control font-code text-uppercase fs-7 flex-grow-1">
      <button type="submit" class="btn btn-subtle theme-warning btn-touch text-nowrap px-3">
        Ingressar
      </button>
    </form>
  </div>

  <!-- Lista de Campanhas -->
  <h2 class="font-mythic fs-5 text-warning mb-3">Suas Campanhas Ativas</h2>

  <?php if (empty($campaigns)): ?>
    <div class="rpg-card text-center p-4">
      <div class="fs-1 mb-2">🏕️</div>
      <p class="text-muted fs-6 mb-3">Você ainda não participa de nenhuma campanha.</p>
      <button class="btn btn-solid theme-primary btn-touch" data-bs-toggle="modal" data-bs-target="#modal-new-campaign">
        Criar Minha Primeira Campanha
      </button>
    </div>
  <?php else: ?>
    <div class="vstack gap-3">
      <?php foreach ($campaigns as $camp): ?>
        <div class="rpg-card mb-0" style="border-left: 4px solid <?= $camp['user_role'] === 'gm' ? 'var(--dracula-yellow)' : 'var(--dracula-green)' ?>;">
          <div class="d-flex justify-content-between align-items-start gap-2 mb-2 flex-wrap">
            <div>
              <h3 class="font-title fs-5 text-warning mb-1"><?= $e($camp['title']) ?></h3>
              <div class="text-muted fs-8">
                Sistema: <strong class="text-light"><?= $e($camp['rule_system']) ?></strong> • Código: <span class="tag-pill gold font-code"><?= $e($camp['code']) ?></span>
              </div>
            </div>
            <span class="tag-pill <?= $camp['user_role'] === 'gm' ? 'gold' : '' ?>">
              <?= $camp['user_role'] === 'gm' ? '👑 Você é o Mestre' : '🗡️ Jogador' ?>
            </span>
          </div>

          <?php if (!empty($camp['description'])): ?>
            <p class="text-light opacity-75 fs-7 mb-3 font-book">
              <?= $e($camp['description']) ?>
            </p>
          <?php endif; ?>

          <div class="row g-2">
            <div class="col-12 col-sm-4">
              <a href="/campaigns/<?= $camp['id'] ?>/sheet" class="btn btn-solid theme-danger btn-touch w-100 py-2 fs-7">
                🛡️ Abrir Ficha
              </a>
            </div>
            <div class="col-12 col-sm-4">
              <a href="/campaigns/<?= $camp['id'] ?>/taverna" class="btn btn-subtle theme-secondary btn-touch w-100 py-2 fs-7">
                🍻 Entrar na Taverna
              </a>
            </div>
            <?php if ($camp['user_role'] === 'gm'): ?>
              <div class="col-12 col-sm-4">
                <a href="/campaigns/<?= $camp['id'] ?>/gm" class="btn btn-solid theme-primary btn-touch w-100 py-2 fs-7">
                  👑 Painel do Mestre
                </a>
              </div>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <!-- Modal Nova Campanha (BOOTSTRAP 6 MODAL) -->
  <div class="modal fade" id="modal-new-campaign" tabindex="-1" aria-labelledby="modalNewCampLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content rpg-card p-0">
        <div class="modal-header d-flex justify-content-between align-items-center">
          <div>
            <h3 class="modal-title font-mythic fs-5 text-warning mb-0" id="modalNewCampLabel">Criar Nova Campanha de RPG</h3>
            <small class="text-muted fs-8">Inicie uma nova saga no universo de sua escolha</small>
          </div>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
        </div>

        <div class="modal-body p-3 p-md-4">
          <form action="/campaigns" method="POST" class="vstack gap-3">
            <div>
              <label class="form-label text-warning">Título da Campanha *</label>
              <input type="text" name="title" placeholder="Ex: O Templo dos Deuses Esquecidos" required class="form-control">
            </div>

            <div>
              <label class="form-label text-warning">Sistema de Regras (Universal p/ Todos os RPGs)</label>
              <select name="rule_system" class="form-select">
                <option value="Tormenta20">Tormenta20 (T20 - Sistema D20)</option>
                <option value="D&D 5e">Dungeons & Dragons 5ª Edição (D&D 5e / D20)</option>
                <option value="Pathfinder 2e">Pathfinder 2ª Edição</option>
                <option value="Call of Cthulhu">Call of Cthulhu 7e (Investigação & d100)</option>
                <option value="Old Dragon">Old Dragon / OSR Clássico</option>
                <option value="3D&T Alpha">3D&T Alpha / Victory (Dados d6)</option>
                <option value="Vampiro: A Máscara">Vampiro: A Máscara / Storyteller (d10)</option>
                <option value="GURPS">GURPS (Sistema 3d6)</option>
                <option value="Sistema Próprio / Livre">Sistema Próprio / Universal (Qualquer RPG de Mesa)</option>
              </select>
            </div>

            <div>
              <label class="form-label text-warning">Sinopse / Descrição da Aventura</label>
              <textarea name="description" rows="3" placeholder="Breve resumo da premissa para seus jogadores..." class="form-control"></textarea>
            </div>

            <div class="hstack gap-2 justify-content-end mt-2">
              <button type="button" class="btn btn-subtle theme-secondary btn-touch" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-solid theme-primary btn-touch px-4">Criar Campanha →</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

</main>

<?php require __DIR__ . '/../layout/footer.php'; ?>
