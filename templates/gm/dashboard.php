<?php require __DIR__ . '/../layout/header.php'; ?>

<main class="container mobile-container py-3">
  
  <!-- Banner Artístico do Mestre (Tormenta & D&D) -->
  <div class="fantasy-art-banner mb-3" style="background-image: linear-gradient(180deg, rgba(13,16,26,0.2) 0%, rgba(13,16,26,0.95) 100%), url('/images/gm-dragon-twist.jpg'); height: 165px; display: flex; flex-direction: column; justify-content: flex-end; padding: 1rem;">
    <div class="text-danger fw-bold text-uppercase fs-8 letter-spacing-1 text-shadow-sm">
      🐉 Covil do Mestre • Destino & Reviravoltas da Trama
    </div>
    <div class="font-title fs-4 text-warning fw-bold text-shadow">
      <?= $e($campaign->title) ?>
    </div>
    <div class="text-light opacity-75 fs-7 text-shadow-sm">
      Sistema: <?= $e($campaign->ruleSystem) ?> • Mestre da Mesa: <strong class="text-white"><?= $e($user->name) ?></strong>
    </div>
  </div>

  <!-- Ações Rápidas do Mestre & Rolador Oculto (RF12) -->
  <div class="rpg-card mb-3">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
      <div>
        <h2 class="font-mythic fs-5 text-warning mb-1">Comando da Sessão</h2>
        <p class="text-muted fs-7 mb-0">
          Controle atmosfera acústica, reviravoltas e segredos em tempo real.
        </p>
      </div>
      <button class="btn btn-solid theme-danger btn-touch py-1 px-3 fs-7" onclick="bardoGMPlotTwist(<?= $campaign->id ?>, 'A EMBOSCADA DA TORMENTA!', 'Nuvens púrpuras cobrem o céu. O solo sangra e aberrantes lefeu saltam das ruínas!', 'combate')">
        ⚡ Disparar Reviravolta Épica
      </button>
    </div>

    <!-- Rolador Tático do Mestre com Modo Oculto -->
    <div class="p-2 rounded bg-surface border border-secondary">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <span class="font-mythic fs-7 text-warning">🎲 Rolador Tático do Mestre</span>
        <div class="form-check d-flex align-items-center gap-2 m-0">
          <input class="form-check-input mt-0" type="checkbox" id="gm-dice-secret-toggle" checked>
          <label class="form-check-label text-danger fw-bold fs-8 cursor-pointer" for="gm-dice-secret-toggle">
            🔒 Rolagem Oculta (Apenas GM)
          </label>
        </div>
      </div>
      <div class="row row-cols-5 g-1">
        <div class="col"><button type="button" class="btn btn-subtle theme-secondary btn-touch py-1 px-0 w-100 fs-7" onclick="bardoGMRoll(<?= $campaign->id ?>, '1d20', 'Percepção Oculta')">d20</button></div>
        <div class="col"><button type="button" class="btn btn-subtle theme-secondary btn-touch py-1 px-0 w-100 fs-7" onclick="bardoGMRoll(<?= $campaign->id ?>, '1d20+5', 'Ataque Monstro')">d20+5</button></div>
        <div class="col"><button type="button" class="btn btn-subtle theme-secondary btn-touch py-1 px-0 w-100 fs-7" onclick="bardoGMRoll(<?= $campaign->id ?>, '1d100', 'Sorte / Tabela')">d100</button></div>
        <div class="col"><button type="button" class="btn btn-subtle theme-secondary btn-touch py-1 px-0 w-100 fs-7" onclick="bardoGMRoll(<?= $campaign->id ?>, '3d6', 'GURPS / Teste')">3d6</button></div>
        <div class="col"><button type="button" class="btn btn-subtle theme-secondary btn-touch py-1 px-0 w-100 fs-7" onclick="bardoGMRoll(<?= $campaign->id ?>, '2d6+3', 'Dano Monstro')">2d6+3</button></div>
      </div>
      <div id="gm-roll-result-display" class="mt-2" style="display: none;"></div>
    </div>
  </div>

  <!-- SEÇÃO 1: MESA DE SOM & AMBIENTAÇÃO SINCRONIZADA -->
  <div id="section-gm-audio" class="gm-tab-pane">
    <div class="rpg-card">
      <div class="card-title-bar d-flex justify-content-between align-items-center">
        <span class="card-title-text">🎵 Som & Ambientação Sincronizada</span>
        <span class="tag-pill gold">Broadcast Ativo</span>
      </div>

      <p class="text-muted fs-7 mb-3">
        Comande a trilha que toca simultaneamente nos celulares de todos os jogadores conectados:
      </p>

      <div class="vstack gap-2 mb-4">
        <div class="action-card ambient-track-btn cursor-pointer mb-0" data-track="tavern" onclick="bardoGMSync(<?= $campaign->id ?>, 'PLAY', 1, 'music'); bardoPlayAmbient('tavern');">
          <div class="action-header d-flex justify-content-between align-items-center">
            <span class="action-title">🍻 Taverna do Javali Saltitante</span>
            <span class="tag-pill">Loop Contínuo</span>
          </div>
          <div class="action-desc">Alaúdes alegres, murmúrio de conversas e taças brindando.</div>
        </div>

        <div class="action-card ambient-track-btn cursor-pointer mb-0" data-track="dungeon" onclick="bardoGMSync(<?= $campaign->id ?>, 'PLAY', 2, 'music'); bardoPlayAmbient('dungeon');">
          <div class="action-header d-flex justify-content-between align-items-center">
            <span class="action-title">💀 Masmorra & Cripta Antiga</span>
            <span class="tag-pill">Suspense</span>
          </div>
          <div class="action-desc">Gotas de água fria ecoando, vento espectral e tensão.</div>
        </div>

        <div class="action-card ambient-track-btn cursor-pointer mb-0" data-track="combat" onclick="bardoGMSync(<?= $campaign->id ?>, 'PLAY', 3, 'music'); bardoPlayAmbient('combat');">
          <div class="action-header d-flex justify-content-between align-items-center">
            <span class="action-title">⚔️ Batalha pela Vida (Combate T20)</span>
            <span class="tag-pill text-danger border-danger">Clímax</span>
          </div>
          <div class="action-desc">Tambores de guerra urgentes, trompas heróicas e adrenalina.</div>
        </div>
      </div>

      <!-- Soundboard de Disparo Imediato -->
      <div class="card-title-bar d-flex justify-content-between align-items-center">
        <span class="card-title-text">⚡ Soundboard Instantâneo (SFX)</span>
        <span class="text-muted fs-8">Sincronia Imediata</span>
      </div>

      <div class="row row-cols-5 g-2 sfx-touch-grid">
        <div class="col">
          <div class="sfx-pad text-center" onclick="bardoGMSFX(<?= $campaign->id ?>, 'sword')">
            <span class="sfx-pad-icon d-block fs-3">⚔️</span>
            <span class="sfx-pad-label font-ui fs-8 text-white">Aço / Golpe</span>
          </div>
        </div>
        <div class="col">
          <div class="sfx-pad text-center" onclick="bardoGMSFX(<?= $campaign->id ?>, 'thunder')">
            <span class="sfx-pad-icon d-block fs-3">⚡</span>
            <span class="sfx-pad-label font-ui fs-8 text-white">Trovão</span>
          </div>
        </div>
        <div class="col">
          <div class="sfx-pad text-center" onclick="bardoGMSFX(<?= $campaign->id ?>, 'spell')">
            <span class="sfx-pad-icon d-block fs-3">🔮</span>
            <span class="sfx-pad-label font-ui fs-8 text-white">Magia</span>
          </div>
        </div>
        <div class="col">
          <div class="sfx-pad text-center" onclick="bardoGMSFX(<?= $campaign->id ?>, 'roar')">
            <span class="sfx-pad-icon d-block fs-3">🐉</span>
            <span class="sfx-pad-label font-ui fs-8 text-white">Rugido</span>
          </div>
        </div>
        <div class="col">
          <div class="sfx-pad text-center" onclick="bardoGMSFX(<?= $campaign->id ?>, 'dice')">
            <span class="sfx-pad-icon d-block fs-3">🎲</span>
            <span class="sfx-pad-label font-ui fs-8 text-white">Dados</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- SEÇÃO 2: ESTÚDIO DE REVIRAVOLTAS DA TRAMA -->
  <div id="section-gm-twists" class="gm-tab-pane" style="display: none;">
    <div class="rpg-card border-danger">
      <div class="card-title-bar d-flex justify-content-between align-items-center">
        <span class="card-title-text text-danger">🔥 Desencadear Reviravolta da Trama</span>
        <span class="tag-pill bg-danger text-white border-0">Ao Vivo</span>
      </div>

      <p class="text-muted fs-7 mb-3">
        Ao clicar, a tela de todos os celulares escurece com pulso carmesim e o som dramático ecoa simultaneamente:
      </p>

      <div class="vstack gap-3">
        <div>
          <label class="form-label text-warning">Manchete Dramática</label>
          <input id="gm-twist-title" type="text" value="A MÁSCARA DO CONSUL CAI!" class="form-control font-title fs-5 text-warning">
        </div>

        <div>
          <label class="form-label text-warning">Descrição de Impacto</label>
          <textarea id="gm-twist-text" rows="3" class="form-control fs-6">O nobre retira o capuz e desembainha uma lâmina negra. As portas da sala se fecham com estrondo de ferro!</textarea>
        </div>

        <button class="btn btn-solid theme-danger btn-touch w-100 py-3 fs-6 mt-2" onclick="bardoGMPlotTwist(<?= $campaign->id ?>, document.getElementById('gm-twist-title').value, document.getElementById('gm-twist-text').value, 'combate')">
          ⚡ EXECUTAR REVIRAVOLTA AO VIVO (TODOS OS DISPOSITIVOS)
        </button>
      </div>
    </div>
  </div>

  <!-- SEÇÃO 3: MONITOR DE FICHAS DA MESA & GESTÃO DE MEMBROS (RF05) -->
  <div id="section-gm-party" class="gm-tab-pane" style="display: none;">
    <div class="rpg-card">
      <div class="card-title-bar d-flex justify-content-between align-items-center">
        <span class="card-title-text">🛡️ Status do Grupo (PV & PM)</span>
        <span class="text-success fs-7">Tempo Real</span>
      </div>

      <div class="vstack gap-2">
        <?php if (empty($characters)): ?>
          <p class="text-muted text-center py-3 fs-7">Nenhuma ficha criada na campanha ainda.</p>
        <?php endif; ?>
        <?php foreach ($characters as $char): ?>
          <div class="action-card ruby mb-0">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
              <div>
                <div class="fw-bold fs-6 text-white"><?= $e($char->name) ?> <span class="text-muted fs-7">(<?= $e($char->classAndLevel) ?>)</span></div>
                <div class="text-muted fs-8">Defesa: <?= (int)$char->armorClass ?></div>
              </div>
              <div class="text-end">
                <div id="party-hp-<?= $char->id ?>" class="font-code fw-bold fs-7 text-danger">
                  <?= (int)$char->hpCurrent ?>/<?= (int)$char->hpMax ?> PV
                </div>
                <div id="party-mp-<?= $char->id ?>" class="font-code fw-bold fs-7 text-info">
                  <?= (int)$char->mpCurrent ?>/<?= (int)$char->mpMax ?> PM
                </div>
              </div>
            </div>
            <div class="hstack gap-2 mt-2">
              <button class="btn btn-sm btn-subtle theme-secondary btn-touch py-1 px-2 fs-8" onclick="bardoModifyHP(<?= $char->id ?>, -5)">-5 PV</button>
              <button class="btn btn-sm btn-subtle theme-secondary btn-touch py-1 px-2 fs-8" onclick="bardoModifyHP(<?= $char->id ?>, 5)">+5 PV</button>
              <button class="btn btn-sm btn-solid theme-info btn-touch py-1 px-2 fs-8" onclick="bardoModifyPM(<?= $char->id ?>, -2)">-2 PM</button>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Gestão de Membros & Co-Mestres da Campanha (RF05) -->
    <div class="rpg-card mt-3">
      <div class="card-title-bar d-flex justify-content-between align-items-center">
        <span class="card-title-text">👥 Aventureiros & Membros da Mesa</span>
        <span class="tag-pill gold"><?= count($members ?? []) ?> Presentes</span>
      </div>
      <p class="text-muted fs-8 mb-3">
        Gerencie permissões de Mestre/Co-Mestre e participantes da campanha:
      </p>

      <div class="vstack gap-2">
        <?php foreach ($members ?? [] as $m): ?>
          <div class="d-flex justify-content-between align-items-center p-2 rounded bg-surface border border-secondary flex-wrap gap-2">
            <div>
              <div class="fs-6 fw-bold text-white">
                <?= $e($m['name']) ?>
                <?php if ($m['role'] === 'gm'): ?>
                  <span class="tag-pill gold ms-1 fs-8">MESTRE</span>
                <?php elseif ($m['role'] === 'co_gm'): ?>
                  <span class="tag-pill text-info border-info ms-1 fs-8">CO-MESTRE</span>
                <?php else: ?>
                  <span class="tag-pill ms-1 fs-8">JOGADOR</span>
                <?php endif; ?>
              </div>
              <div class="text-muted fs-8"><?= $e($m['email']) ?></div>
            </div>
            <?php if ($m['user_id'] != $user->id): ?>
              <div class="hstack gap-1">
                <?php if ($m['role'] === 'co_gm'): ?>
                  <button type="button" class="btn btn-sm btn-subtle theme-secondary py-1 px-2 fs-8" onclick="bardoUpdateMemberRole(<?= $campaign->id ?>, <?= $m['user_id'] ?>, 'player')">Tornar Jogador</button>
                <?php else: ?>
                  <button type="button" class="btn btn-sm btn-solid theme-info py-1 px-2 fs-8" onclick="bardoUpdateMemberRole(<?= $campaign->id ?>, <?= $m['user_id'] ?>, 'co_gm')">+ Co-Mestre</button>
                <?php endif; ?>
                <button type="button" class="btn btn-sm btn-solid theme-danger py-1 px-2 fs-8" onclick="bardoKickMember(<?= $campaign->id ?>, <?= $m['user_id'] ?>, '<?= $e($m['name']) ?>')">Expulsar</button>
              </div>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <!-- SEÇÃO 4: SEGREDOS DO MESTRE & PISTAS / HANDOUTS (RF15 & RF17) -->
  <div id="section-gm-secrets" class="gm-tab-pane" style="display: none;">
    <!-- Segredos Criptografados do Mestre (RF17) -->
    <div class="rpg-card">
      <div class="card-title-bar d-flex justify-content-between align-items-center">
        <span class="card-title-text">📜 Anotações Secretas do Mestre</span>
        <span class="tag-pill gold">Isolamento RF17/RN06</span>
      </div>

      <p class="text-muted fs-8 mb-2">
        Anotações protegidas por isolamento lógico. Jamais são transmitidas para jogadores via API ou SSE:
      </p>

      <textarea id="gm-secrets-text" rows="6" placeholder="Escreva aqui segredos da masmorra, fraquezas de monstros, senhas de portas místicas..." class="form-control fs-6 mb-3"><?= $e($campaign->gmSecrets ?? '') ?></textarea>

      <button type="button" class="btn btn-solid theme-primary btn-touch w-100" onclick="bardoSaveGMSecrets(<?= $campaign->id ?>)">
        💾 Salvar Segredos do Mestre
      </button>
    </div>

    <!-- Pistas & Handouts Reveláveis (RF15) -->
    <div class="rpg-card mt-3">
      <div class="card-title-bar d-flex justify-content-between align-items-center">
        <span class="card-title-text">🗺️ Pistas & Handouts da Aventura</span>
        <button type="button" class="btn btn-sm btn-subtle theme-warning py-1 px-2 fs-8" data-bs-toggle="modal" data-bs-target="#modal-new-handout">
          + Nova Pista
        </button>
      </div>

      <p class="text-muted fs-8 mb-3">
        Crie cartas, pergaminhos ou mapas e revele na hora certa para toda a mesa:
      </p>

      <div class="vstack gap-2">
        <?php if (empty($handouts)): ?>
          <p class="text-muted text-center py-3 fs-7">Nenhuma pista cadastrada ainda.</p>
        <?php endif; ?>
        <?php foreach ($handouts ?? [] as $h): ?>
          <div class="p-3 rounded bg-surface border <?= $h['is_revealed'] ? 'border-warning' : 'border-secondary' ?>">
            <div class="d-flex justify-content-between align-items-start gap-2 flex-wrap">
              <div class="flex-grow-1">
                <div class="fw-bold fs-6 text-warning">
                  <?= $e($h['title']) ?>
                  <?php if ($h['is_revealed']): ?>
                    <span class="tag-pill gold ms-1 fs-8">👁️ Revelada à Mesa</span>
                  <?php else: ?>
                    <span class="tag-pill ms-1 fs-8">🔒 Oculta (Segredo)</span>
                  <?php endif; ?>
                </div>
                <?php if (!empty($h['description'])): ?>
                  <div class="fs-7 text-light opacity-75 mt-1 font-book">
                    <?= nl2br($e($h['description'])) ?>
                  </div>
                <?php endif; ?>
              </div>
              <button type="button" class="btn btn-sm <?= $h['is_revealed'] ? 'btn-subtle theme-secondary' : 'btn-solid theme-primary' ?> btn-touch py-1 px-2 fs-8 text-nowrap" onclick="bardoToggleHandout(<?= $campaign->id ?>, <?= $h['id'] ?>, <?= $h['is_revealed'] ? 'false' : 'true' ?>)">
                <?= $h['is_revealed'] ? '🔒 Ocultar' : '👁️ Revelar' ?>
              </button>
            </div>
            <?php if (!empty($h['image_url'])): ?>
              <div class="mt-2 rounded overflow-hidden" style="max-height: 140px;">
                <img src="<?= $e($h['image_url']) ?>" alt="<?= $e($h['title']) ?>" class="img-fluid w-100 h-100" style="object-fit: cover;">
              </div>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <!-- MODAL DE NOVO HANDOUT / PISTA (BOOTSTRAP 6 MODAL) -->
  <div class="modal fade" id="modal-new-handout" tabindex="-1" aria-labelledby="modalNewHandoutLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content rpg-card p-0">
        <div class="modal-header d-flex justify-content-between align-items-center">
          <div>
            <h3 class="modal-title font-mythic fs-5 text-warning mb-0" id="modalNewHandoutLabel">📜 Forjar Nova Pista / Handout</h3>
            <small class="text-muted fs-8">Compartilhe documentos, mapas ou enigmas com o grupo</small>
          </div>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
        </div>

        <div class="modal-body p-3 p-md-4">
          <form onsubmit="bardoCreateHandout(event, <?= $campaign->id ?>)">
            <div class="mb-3">
              <label class="form-label text-warning">TÍTULO DA PISTA *</label>
              <input type="text" name="title" required placeholder="Ex: Diário do Cultista, Fragmento do Mapa..." class="form-control">
            </div>

            <div class="mb-3">
              <label class="form-label text-warning">CONTEÚDO / TEXTO DA PISTA</label>
              <textarea name="description" rows="3" placeholder="O texto transcrito ou descrição do objeto..." class="form-control"></textarea>
            </div>

            <div class="mb-3">
              <label class="form-label text-warning">URL DA IMAGEM (OPCIONAL)</label>
              <input type="text" name="image_url" placeholder="/images/parchment-grimoire.jpg ou URL externa" class="form-control font-code fs-7">
            </div>

            <div class="mb-4">
              <div class="form-check d-flex align-items-center gap-2">
                <input class="form-check-input" type="checkbox" name="is_revealed" id="check-reveal-now">
                <label class="form-check-label text-info fw-bold fs-7 cursor-pointer" for="check-reveal-now">
                  Revelar imediatamente para todos os jogadores na Taverna
                </label>
              </div>
            </div>

            <div class="hstack gap-2 justify-content-end">
              <button type="button" class="btn btn-subtle theme-secondary btn-touch" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-solid theme-primary btn-touch px-4">📜 Criar Pista</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

</main>

<!-- BARRA DE NAVEGAÇÃO INFERIOR DOCK (FIXA NO MOBILE) -->
<nav class="bottom-dock">
  <div class="dock-item active" onclick="switchGmTab('audio', this)">
    <span class="dock-icon">🎵</span>
    <span>Áudio</span>
  </div>
  <div class="dock-item" onclick="switchGmTab('twists', this)">
    <span class="dock-icon">🔥</span>
    <span>Reviravoltas</span>
  </div>
  <div class="dock-item" onclick="switchGmTab('party', this)">
    <span class="dock-icon">🛡️</span>
    <span>Fichas</span>
  </div>
  <div class="dock-item" onclick="switchGmTab('secrets', this)">
    <span class="dock-icon">📜</span>
    <span>Segredos</span>
  </div>
  <a href="/campaigns/<?= $campaign->id ?>/taverna" class="dock-item">
    <span class="dock-icon">🍻</span>
    <span>Taverna</span>
  </a>
</nav>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    connectBardoSSE(<?= $campaign->id ?>);
  });

  function switchGmTab(tabId, el) {
    document.querySelectorAll('.gm-tab-pane').forEach(p => p.style.display = 'none');
    document.querySelectorAll('.dock-item').forEach(d => d.classList.remove('active'));
    const target = document.getElementById('section-gm-' + tabId);
    if (target) target.style.display = 'block';
    if (el) el.classList.add('active');
  }
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
