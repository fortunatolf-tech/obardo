<?php
/**
 * O Bardo • Taverna Compartilhada (Sessão ao Vivo em Tempo Real)
 * Sincronização Server-Sent Events (SSE) + Trilha Acústica + Tormenta20 / D&D
 */
?>
<?php require_once __DIR__ . '/../layout/header.php'; ?>

<!-- Container Mobile Principal -->
<main class="container mobile-container py-3">

  <!-- Banner Artístico da Taverna (Tormenta & D&D) -->
  <div class="fantasy-art-banner mb-3" style="background-image: linear-gradient(180deg, rgba(13,16,26,0.25) 0%, rgba(13,16,26,0.92) 100%), url('/images/taverna-art.jpg'); height: 165px; display: flex; flex-direction: column; justify-content: flex-end; padding: 1rem;">
    <div class="text-warning fw-bold text-uppercase fs-8 letter-spacing-1 text-shadow-sm">
      🍻 Ponto de Encontro • Taverna de Arton & Costa da Espada
    </div>
    <div class="font-title fs-4 text-white fw-bold text-shadow">
      <?= $e($campaign->title) ?>
    </div>
    <div class="text-light opacity-75 fs-7 text-shadow-sm">
      Sistema: <?= $e($campaign->rule_system ?? 'Universal / Tormenta20 & D&D') ?> • Código: <strong class="text-warning"><?= $e($campaign->invite_code) ?></strong>
    </div>
  </div>

  <!-- Player de Áudio Atmosférico Sincronizado com o Mestre -->
  <div class="rpg-card mb-3" style="border-color: var(--dracula-green); background: linear-gradient(145deg, #182030 0%, #10131d 100%);">
    <div class="d-flex justify-content-between align-items-center gap-2 flex-wrap">
      <div class="d-flex align-items-center gap-2">
        <div class="rounded-3 border border-success d-flex align-items-center justify-content-center fs-4 text-success" style="width: 40px; height: 40px; background: rgba(80, 250, 123, 0.15);">
          🎵
        </div>
        <div>
          <div class="text-muted fw-bold text-uppercase fs-8">
            Trilha Sonora da Sessão (Transmitida pelo Mestre)
          </div>
          <div id="current-track-label" class="fs-6 fw-bold text-warning">
            <?= $e($sync_state?->track_title ?? 'Taverna do Javali Saltitante • Alaúdes & Lareira') ?>
          </div>
        </div>
      </div>

      <div class="d-flex align-items-center gap-2">
        <button class="btn btn-solid theme-primary btn-touch py-1 px-3 fs-7" onclick="bardoPlayAmbient('tavern'); bardoPlaySFX('spell'); bardoToast('🔊 Áudio da Taverna Sintonizado!');">
          🔊 Sintonizar
        </button>
      </div>
    </div>

    <!-- Controle de Volume Local -->
    <div class="d-flex align-items-center justify-content-between mt-2 pt-2 border-top border-secondary fs-7 text-muted">
      <span>Volume Pessoal:</span>
      <div class="d-flex align-items-center gap-2">
        <span>🔈</span>
        <input type="range" min="0" max="100" value="75" class="form-range" style="width: 120px;" oninput="if(bardoMasterGain) bardoMasterGain.gain.setValueAtTime(this.value / 100, bardoAudioCtx.currentTime);">
        <span>🔊</span>
      </div>
    </div>
  </div>

  <!-- Companheiros de Grupo na Mesa (Status PV/PM em Tempo Real) -->
  <div class="rpg-card mb-3">
    <div class="card-title-bar d-flex justify-content-between align-items-center">
      <span class="card-title-text">👥 Companheiros de Mesa (<?= count($characters) ?>)</span>
      <span class="text-warning fs-7">Sincronia Ativa</span>
    </div>

    <div class="row row-cols-2 row-cols-md-4 g-2 text-center">
      <?php foreach ($characters as $char): ?>
        <div class="col">
          <div class="p-2 rounded bg-surface border border-secondary">
            <div class="fs-4">
              <?= str_contains(strtolower($char->character_class), 'guerreiro') || str_contains(strtolower($char->character_class), 'paladino') ? '🗡️' : (str_contains(strtolower($char->character_class), 'arcanista') || str_contains(strtolower($char->character_class), 'mago') ? '🔮' : '🏹') ?>
            </div>
            <div class="fw-bold fs-7 text-light text-truncate">
              <?= $e($char->name) ?>
            </div>
            <div id="party-hp-<?= $char->id ?>" class="font-code fs-8 text-danger fw-bold">
              <?= $char->hp_current ?>/<?= $char->hp_max ?> PV
            </div>
            <div id="party-mp-<?= $char->id ?>" class="font-code fs-8 text-info fw-bold">
              <?= $char->mp_current ?>/<?= $char->mp_max ?> PM
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- SEÇÕES DA TAVERNA (TABBED INTERFACE) -->

  <!-- ABA 1: FEED DE ROLAGENS E COMBATE AO VIVO -->
  <div id="section-tavern-rolls" class="tavern-tab-pane">
    <div class="rpg-card">
      <div class="card-title-bar d-flex justify-content-between align-items-center">
        <span class="card-title-text">🎲 Feed de Rolagens & Ações ao Vivo</span>
        <button class="btn btn-sm btn-solid theme-primary btn-touch py-1 px-2 fs-7" onclick="bardoRoll(<?= $campaign->id ?>, <?= $characters[0]->id ?? 'null' ?>, '1d20', 'Teste d20 ao Vivo')">Rolar d20</button>
      </div>

      <div id="session-roll-feed" class="vstack gap-2">
        <?php foreach ($rolls as $r): ?>
          <?php
            $isCrit = (bool)($r['is_critical'] ?? false);
            $isFumble = (bool)($r['is_fumble'] ?? false);
            $color = $isCrit ? '#50fa7b' : ($isFumble ? '#ff5555' : '#f1fa8c');
          ?>
          <div class="p-2 px-3 rounded bg-surface" style="border-left: 4px solid <?= $color ?>;">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <div class="fw-bold fs-6 text-warning">
                  <?= $e($r['roller_name'] ?? 'Aventureiro') ?> • <?= $e($r['label'] ?? 'Rolagem') ?>
                  <?php if ($isCrit): ?><span class="text-success ms-1">(CRÍTICO!)</span><?php endif; ?>
                  <?php if ($isFumble): ?><span class="text-danger ms-1">(FALHA CRÍTICA!)</span><?php endif; ?>
                </div>
                <div class="text-muted fs-8">
                  Fórmula: <?= $e($r['formula'] ?? '1d20') ?>
                </div>
              </div>
              <div class="font-code fs-3 fw-bold px-3 py-1 rounded bg-abyss border border-secondary" style="color: <?= $color ?>;">
                <?= $e((string)$r['total_result']) ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
        <?php if (empty($rolls)): ?>
          <p class="text-center text-muted fs-7 py-4">
            Nenhuma rolagem realizada ainda. Toque no dado flutuante para começar!
          </p>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- ABA 2: MURAL DE PISTAS & HANDOUTS -->
  <div id="section-tavern-pistas" class="tavern-tab-pane" style="display: none;">
    <div class="rpg-card">
      <div class="card-title-bar d-flex justify-content-between align-items-center">
        <span class="card-title-text">📜 Pistas & Cartas Reveladas pelo Mestre</span>
        <span class="tag-pill gold"><?= count($handouts) ?> Itens</span>
      </div>

      <div class="vstack gap-2">
        <?php foreach ($handouts as $h): ?>
          <div class="action-card cursor-pointer mb-0" onclick="alert('<?= addslashes($h->title) ?>:\n\n<?= addslashes(str_replace(["\r", "\n"], ' ', $h->content)) ?>')">
            <div class="action-header d-flex justify-content-between align-items-center">
              <span class="action-title">
                <?= $h->type === 'map' ? '🗺️' : ($h->type === 'item' ? '🗝️' : '📜') ?>
                <?= $e($h->title) ?>
              </span>
              <span class="tag-pill"><?= $e(ucfirst($h->type)) ?></span>
            </div>
            <div class="action-desc"><?= $e(mb_strimwidth($h->content, 0, 110, '...')) ?></div>
          </div>
        <?php endforeach; ?>
        <?php if (empty($handouts)): ?>
          <p class="text-center text-muted fs-7 py-4">
            Nenhum handout revelado pelo Mestre até o momento.
          </p>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- ABA 3: CRÔNICA COLETIVA DA GUILDA -->
  <div id="section-tavern-cronica" class="tavern-tab-pane" style="display: none;">
    <div class="rpg-card">
      <div class="card-title-bar d-flex justify-content-between align-items-center">
        <span class="card-title-text">📖 Diário de Bordo da Sessão</span>
        <span class="text-muted fs-8"><?= count($chronicles) ?> Entradas</span>
      </div>

      <div class="vstack gap-2 mb-3">
        <?php foreach ($chronicles as $c): ?>
          <div class="p-3 rounded bg-surface border-start border-3 border-warning fs-7 text-light">
            <strong class="text-warning d-block mb-1"><?= $e($c->title) ?></strong>
            <?= nl2br($e($c->content)) ?>
            <div class="text-muted fs-8 text-end mt-2">
              <?= date('d/m/Y H:i', strtotime($c->created_at)) ?>
            </div>
          </div>
        <?php endforeach; ?>
        <?php if (empty($chronicles)): ?>
          <p class="text-center text-muted fs-7 py-3">
            A crônica desta campanha ainda está em branco. Registre o primeiro feito!
          </p>
        <?php endif; ?>
      </div>

      <!-- Formulário Rápido de Adição de Crônica -->
      <form onsubmit="handleNewChronicle(event, <?= $campaign->id ?>)" class="d-flex gap-2">
        <input type="text" id="chronicle-entry-text" placeholder="Adicionar nota ao diário..." required class="form-control fs-7">
        <button type="submit" class="btn btn-solid theme-primary btn-touch text-nowrap px-3">Salvar</button>
      </form>
    </div>
  </div>

</main>

<!-- OVERLAY DRAMÁTICO PARA REVIRAVOLTAS DA TRAMA -->
<div id="dramatic-twist-screen" class="dramatic-overlay" onclick="closeDramaticTwist()">
  <div class="dramatic-content text-center">
    <div class="fs-1 mb-2 animate-pulse">⚡</div>
    <h2 id="dramatic-headline" class="dramatic-title font-mythic fs-3 text-danger mb-2">REVIRAVOLTA NA TRAMA!</h2>
    <p id="dramatic-desc" class="dramatic-subtitle text-light fs-6 mb-4">As sombras se dissipam e uma presença terrível surge diante dos aventureiros.</p>
    <div>
      <button class="btn btn-solid theme-primary btn-touch px-4 py-2 fs-6" onclick="closeDramaticTwist()">Continuar a Batalha</button>
    </div>
  </div>
</div>

<!-- BOTAO FLUTUANTE (FAB) PARA ROLAR DADOS -->
<div class="fab-dice" onclick="openDiceSheet()" title="Abrir Rolador de Dados">
  🎲
</div>

<!-- BOTTOM SHEET MODAL DE ROLAGEM RAPIDA DE DADOS -->
<div id="dice-bottom-sheet" class="bottom-sheet" onclick="if(event.target === this) closeDiceSheet();">
  <div class="bottom-sheet-content">
    <div class="sheet-handle"></div>
    <h3 class="font-mythic fs-5 text-center text-warning mb-1">Bandeja Universal de Dados</h3>
    <p class="text-muted text-center fs-7 mb-3">Suporta Sistema D20, D100, 3d6 e qualquer RPG de mesa:</p>

    <!-- Fórmula Personalizada Livre -->
    <form onsubmit="event.preventDefault(); const f=document.getElementById('tavern-dice-formula').value; if(f) bardoRoll(<?= $campaign->id ?>, <?= $characters[0]->id ?? 'null' ?>, f, 'Rolagem Especial');" class="d-flex gap-2 mb-3">
      <input type="text" id="tavern-dice-formula" class="form-control font-code fs-7" placeholder="Fórmula livre (ex: 3d6+2, 2d8, 1d100)">
      <button type="submit" class="btn btn-solid theme-primary btn-touch px-3">Rolar</button>
    </form>

    <div class="dice-tray-grid">
      <div class="dice-pill" onclick="bardoRoll(<?= $campaign->id ?>, <?= $characters[0]->id ?? 'null' ?>, '1d4', 'd4')">
        <span class="dice-pill-icon">🔺</span>
        <span class="dice-pill-name">d4</span>
      </div>
      <div class="dice-pill" onclick="bardoRoll(<?= $campaign->id ?>, <?= $characters[0]->id ?? 'null' ?>, '1d6', 'd6')">
        <span class="dice-pill-icon">🎲</span>
        <span class="dice-pill-name">d6</span>
      </div>
      <div class="dice-pill" onclick="bardoRoll(<?= $campaign->id ?>, <?= $characters[0]->id ?? 'null' ?>, '1d8', 'd8')">
        <span class="dice-pill-icon">💎</span>
        <span class="dice-pill-name">d8</span>
      </div>
      <div class="dice-pill" onclick="bardoRoll(<?= $campaign->id ?>, <?= $characters[0]->id ?? 'null' ?>, '1d10', 'd10')">
        <span class="dice-pill-icon">🔟</span>
        <span class="dice-pill-name">d10</span>
      </div>
      <div class="dice-pill" onclick="bardoRoll(<?= $campaign->id ?>, <?= $characters[0]->id ?? 'null' ?>, '1d12', 'd12')">
        <span class="dice-pill-icon">⭐</span>
        <span class="dice-pill-name">d12</span>
      </div>
      <div class="dice-pill selected" onclick="bardoRoll(<?= $campaign->id ?>, <?= $characters[0]->id ?? 'null' ?>, '1d20', 'd20 Puro')">
        <span class="dice-pill-icon">👑</span>
        <span class="dice-pill-name">d20</span>
      </div>
      <div class="dice-pill" onclick="bardoRoll(<?= $campaign->id ?>, <?= $characters[0]->id ?? 'null' ?>, '1d20+7', 'd20 + 7')">
        <span class="dice-pill-icon">⚔️</span>
        <span class="dice-pill-name">+7</span>
      </div>
      <div class="dice-pill" onclick="bardoRoll(<?= $campaign->id ?>, <?= $characters[0]->id ?? 'null' ?>, '1d100', 'd100 Porcentual')">
        <span class="dice-pill-icon">💯</span>
        <span class="dice-pill-name">d%</span>
      </div>
    </div>

    <button class="btn btn-subtle theme-secondary btn-touch w-100 mt-2" onclick="closeDiceSheet()">Fechar</button>
  </div>
</div>

<!-- BARRA DE NAVEGAÇÃO INFERIOR DOCK (FIXA NO MOBILE) -->
<nav class="bottom-dock">
  <div class="dock-item active" onclick="switchTavernTab('rolls', this)">
    <span class="dock-icon">🎲</span>
    <span>Rolagens</span>
  </div>
  <div class="dock-item" onclick="switchTavernTab('pistas', this)">
    <span class="dock-icon">📜</span>
    <span>Pistas</span>
  </div>
  <div class="dock-item" onclick="switchTavernTab('cronica', this)">
    <span class="dock-icon">📖</span>
    <span>Crônica</span>
  </div>
  <a href="/campaigns/<?= $campaign->id ?>/sheet" class="dock-item">
    <span class="dock-icon">🛡️</span>
    <span>Minha Ficha</span>
  </a>
  <?php if ($is_gm): ?>
    <a href="/campaigns/<?= $campaign->id ?>/gm" class="dock-item">
      <span class="dock-icon">👑</span>
      <span>Mestre</span>
    </a>
  <?php endif; ?>
</nav>

<script>
  function switchTavernTab(tabId, el) {
    document.querySelectorAll('.tavern-tab-pane').forEach(p => p.style.display = 'none');
    document.querySelectorAll('.dock-item').forEach(d => d.classList.remove('active'));
    const target = document.getElementById('section-tavern-' + tabId);
    if (target) target.style.display = 'block';
    if (el) el.classList.add('active');
  }

  async function handleNewChronicle(e, campaignId) {
    e.preventDefault();
    const input = document.getElementById('chronicle-entry-text');
    const content = input.value.trim();
    if (!content) return;

    try {
      const res = await fetch(`/api/v1/campaigns/${campaignId}/chronicles`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          title: 'Anotação da Sessão',
          content: content,
          category: 'session'
        })
      });
      const data = await res.json();
      if (data.status === 'success') {
        bardoToast('📖 Crônica registrada com sucesso!');
        input.value = '';
        setTimeout(() => location.reload(), 800);
      }
    } catch(err) {
      console.error(err);
    }
  }

  // Inicializa SSE na Campanha
  document.addEventListener('DOMContentLoaded', () => {
    connectBardoSSE(<?= $campaign->id ?>);
  });
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
