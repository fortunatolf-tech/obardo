<?php
/**
 * O Bardo • Taverna Compartilhada (Sessão ao Vivo em Tempo Real)
 * Sincronização Server-Sent Events (SSE) + Trilha Acústica + Tormenta20 / D&D
 */
?>
<?php require_once __DIR__ . '/../layout/header.php'; ?>

<!-- Container Mobile Principal -->
<main class="mobile-container">

  <!-- Banner Artístico da Taverna (Tormenta & D&D) -->
  <div class="fantasy-art-banner" style="background-image: linear-gradient(180deg, rgba(13,16,26,0.25) 0%, rgba(13,16,26,0.92) 100%), url('/images/taverna-art.jpg'); height: 165px; border-radius: 12px; border: 1.5px solid var(--border-gold); margin-bottom: 0.85rem; background-size: cover; background-position: center; display: flex; flex-direction: column; justify-content: flex-end; padding: 1rem; position: relative; box-shadow: 0 4px 20px rgba(0,0,0,0.7);">
    <div style="font-size: 0.65rem; color: var(--gold-bright); font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em; text-shadow: 0 2px 4px #000;">
      🍻 Ponto de Encontro • Taverna de Arton & Costa da Espada
    </div>
    <div style="font-family: var(--font-title); font-size: 1.35rem; font-weight: 900; color: #fff; text-shadow: 0 2px 8px rgba(0,0,0,0.95);">
      <?= $e($campaign->title) ?>
    </div>
    <div style="font-size: 0.72rem; color: var(--text-secondary); text-shadow: 0 1px 4px #000;">
      Sistema: <?= $e($campaign->rule_system ?? 'Universal / Tormenta20 & D&D') ?> • Código: <strong style="color:var(--gold-imperial);"><?= $e($campaign->invite_code) ?></strong>
    </div>
  </div>

  <!-- Player de Áudio Atmosférico Sincronizado com o Mestre -->
  <div class="rpg-card" style="border-color: var(--emerald-life); background: linear-gradient(145deg, #182030 0%, #10131d 100%);">
    <div style="display: flex; justify-content: space-between; align-items: center; gap: 0.75rem;">
      <div style="display: flex; align-items: center; gap: 0.65rem;">
        <div style="width: 38px; height: 38px; border-radius: 8px; background: rgba(16, 185, 129, 0.15); border: 1px solid var(--emerald-life); display: flex; align-items: center; justify-content: center; font-size: 1.2rem; color: var(--emerald-life);">
          🎵
        </div>
        <div>
          <div style="font-size: 0.62rem; color: var(--text-muted); font-weight: 800; text-transform: uppercase;">
            Trilha Sonora da Sessão (Transmitida pelo Mestre)
          </div>
          <div id="current-track-label" style="font-size: 0.85rem; font-weight: 700; color: var(--gold-imperial);">
            <?= $e($sync_state?->track_title ?? 'Taverna do Javali Saltitante • Alaúdes & Lareira') ?>
          </div>
        </div>
      </div>

      <div style="display: flex; align-items: center; gap: 0.4rem;">
        <button class="btn-touch btn-touch-primary" style="padding: 4px 8px; font-size: 0.72rem;" onclick="bardoPlayAmbient('tavern'); bardoPlaySFX('spell'); showToast('🔊 Áudio da Taverna Sintonizado!');">
          🔊 Sintonizar
        </button>
      </div>
    </div>

    <!-- Controle de Volume Local -->
    <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 0.65rem; padding-top: 0.45rem; border-top: 1px solid rgba(255,255,255,0.08); font-size: 0.72rem; color: var(--text-muted);">
      <span>Volume Pessoal:</span>
      <div style="display: flex; align-items: center; gap: 0.4rem;">
        <span>🔈</span>
        <input type="range" min="0" max="100" value="75" style="width: 110px; cursor: pointer;" oninput="if(bardoMasterGain) bardoMasterGain.gain.setValueAtTime(this.value / 100, bardoAudioCtx.currentTime);">
        <span>🔊</span>
      </div>
    </div>
  </div>

  <!-- Companheiros de Grupo na Mesa (Status PV/PM em Tempo Real) -->
  <div class="rpg-card">
    <div class="card-title-bar">
      <span class="card-title-text">👥 Companheiros de Mesa (<?= count($characters) ?>)</span>
      <span style="font-size: 0.7rem; color: var(--gold-imperial);">Sincronia Ativa</span>
    </div>

    <div style="display: grid; grid-template-columns: repeat(<?= max(2, min(4, count($characters))) ?>, 1fr); gap: 0.5rem; text-align: center;">
      <?php foreach ($characters as $char): ?>
        <div style="background: var(--bg-surface); padding: 0.5rem 0.25rem; border-radius: 8px; border: 1px solid var(--border-subtle);">
          <div style="font-size: 1.1rem;">
            <?= str_contains(strtolower($char->character_class), 'guerreiro') || str_contains(strtolower($char->character_class), 'paladino') ? '🗡️' : (str_contains(strtolower($char->character_class), 'arcanista') || str_contains(strtolower($char->character_class), 'mago') ? '🔮' : '🏹') ?>
          </div>
          <div style="font-weight: 700; font-size: 0.75rem; color: var(--text-parchment); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
            <?= $e($char->name) ?>
          </div>
          <div id="party-hp-<?= $char->id ?>" style="font-family: var(--font-code); font-size: 0.68rem; color: var(--t20-ruby); font-weight: 800;">
            <?= $char->hp_current ?>/<?= $char->hp_max ?> PV
          </div>
          <div id="party-mp-<?= $char->id ?>" style="font-family: var(--font-code); font-size: 0.68rem; color: var(--mana-cyan-light); font-weight: 800;">
            <?= $char->mp_current ?>/<?= $char->mp_max ?> PM
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- SEÇÕES DA TAVERNA (TABBED INTERFACE) -->

  <!-- ABA 1: FEED DE ROLAGENS E COMBATE AO VIVO -->
  <div id="section-tavern-rolls" class="tavern-tab-pane">
    <div class="rpg-card">
      <div class="card-title-bar">
        <span class="card-title-text">🎲 Feed de Rolagens & Ações ao Vivo</span>
        <button class="btn-touch btn-touch-primary" style="padding: 2px 8px; font-size: 0.72rem;" onclick="bardoRoll(<?= $campaign->id ?>, <?= $characters[0]->id ?? 'null' ?>, '1d20', 'Teste d20 ao Vivo')">Rolar d20</button>
      </div>

      <div id="session-roll-feed">
        <?php foreach ($rolls as $r): ?>
          <?php
            $isCrit = (bool)($r['is_critical'] ?? false);
            $isFumble = (bool)($r['is_fumble'] ?? false);
            $color = $isCrit ? '#10b981' : ($isFumble ? '#e11d48' : '#d4af37');
          ?>
          <div class="rpg-card" style="padding: 0.65rem 0.85rem; margin-bottom: 0.5rem; border-left: 3px solid <?= $color ?>;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
              <div>
                <div style="font-weight: 700; font-size: 0.85rem; color: var(--gold-imperial);">
                  <?= $e($r['roller_name'] ?? 'Aventureiro') ?> • <?= $e($r['label'] ?? 'Rolagem') ?>
                  <?php if ($isCrit): ?><span style="color:#10b981;">(CRÍTICO!)</span><?php endif; ?>
                  <?php if ($isFumble): ?><span style="color:#e11d48;">(FALHA CRÍTICA!)</span><?php endif; ?>
                </div>
                <div style="font-size: 0.72rem; color: var(--text-muted);">
                  Fórmula: <?= $e($r['formula'] ?? '1d20') ?>
                </div>
              </div>
              <div style="font-family: var(--font-code); font-size: 1.3rem; font-weight: 900; color: <?= $color ?>; background: var(--bg-surface); padding: 2px 10px; border-radius: 6px; border: 1px solid var(--border-subtle);">
                <?= $e((string)$r['total_result']) ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
        <?php if (empty($rolls)): ?>
          <p style="text-align: center; font-size: 0.8rem; color: var(--text-muted); padding: 1.5rem 0;">
            Nenhuma rolagem realizada ainda. Toque no dado flutuante para começar!
          </p>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- ABA 2: MURAL DE PISTAS & HANDOUTS -->
  <div id="section-tavern-pistas" class="tavern-tab-pane" style="display: none;">
    <div class="rpg-card">
      <div class="card-title-bar">
        <span class="card-title-text">📜 Pistas & Cartas Reveladas pelo Mestre</span>
        <span class="tag-pill gold"><?= count($handouts) ?> Itens</span>
      </div>

      <div style="display: flex; flex-direction: column; gap: 0.65rem;">
        <?php foreach ($handouts as $h): ?>
          <div class="action-card" onclick="alert('<?= addslashes($h->title) ?>:\n\n<?= addslashes(str_replace(["\r", "\n"], ' ', $h->content)) ?>')" style="cursor: pointer;">
            <div class="action-header">
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
          <p style="text-align: center; font-size: 0.8rem; color: var(--text-muted); padding: 1.5rem 0;">
            Nenhum handout revelado pelo Mestre até o momento.
          </p>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- ABA 3: CRÔNICA COLETIVA DA GUILDA -->
  <div id="section-tavern-cronica" class="tavern-tab-pane" style="display: none;">
    <div class="rpg-card">
      <div class="card-title-bar">
        <span class="card-title-text">📖 Diário de Bordo da Sessão</span>
        <span style="font-size: 0.7rem; color: var(--text-muted);"><?= count($chronicles) ?> Entradas</span>
      </div>

      <div style="display: flex; flex-direction: column; gap: 0.65rem; margin-bottom: 0.75rem;">
        <?php foreach ($chronicles as $c): ?>
          <div style="background: var(--bg-surface); padding: 0.85rem; border-radius: 8px; border-left: 3px solid var(--border-gold); font-size: 0.85rem; line-height: 1.5; color: var(--text-secondary);">
            <strong style="color: var(--gold-imperial);"><?= $e($c->title) ?></strong><br>
            <?= nl2br($e($c->content)) ?>
            <div style="font-size: 0.68rem; color: var(--text-muted); margin-top: 0.4rem; text-align: right;">
              <?= date('d/m/Y H:i', strtotime($c->created_at)) ?>
            </div>
          </div>
        <?php endforeach; ?>
        <?php if (empty($chronicles)): ?>
          <p style="text-align: center; font-size: 0.8rem; color: var(--text-muted); padding: 1rem 0;">
            A crônica desta campanha ainda está em branco. Registre o primeiro feito!
          </p>
        <?php endif; ?>
      </div>

      <!-- Formulário Rápido de Adição de Crônica -->
      <form onsubmit="handleNewChronicle(event, <?= $campaign->id ?>)" style="display: flex; gap: 0.4rem;">
        <input type="text" id="chronicle-entry-text" placeholder="Adicionar nota ao diário..." required style="flex-grow: 1; padding: 0.55rem; background: var(--bg-surface); border: 1px solid var(--border-subtle); border-radius: 6px; color: #fff; font-size: 0.8rem;">
        <button type="submit" class="btn-touch btn-touch-primary">Salvar</button>
      </form>
    </div>
  </div>

</main>

<!-- OVERLAY DRAMÁTICO PARA REVIRAVOLTAS DA TRAMA -->
<div id="dramatic-twist-screen" class="dramatic-overlay" onclick="closeDramaticTwist()">
  <div class="dramatic-content">
    <div style="font-size: 2.8rem; margin-bottom: 0.5rem; animation: pulseGlow 1.5s infinite;">⚡</div>
    <h2 id="dramatic-headline" class="dramatic-title">REVIRAVOLTA NA TRAMA!</h2>
    <p id="dramatic-desc" class="dramatic-subtitle">As sombras se dissipam e uma presença terrível surge diante dos aventureiros.</p>
    <div style="margin-top: 1.5rem;">
      <button class="btn-touch btn-touch-primary" style="padding: 8px 24px; font-size: 0.9rem;" onclick="closeDramaticTwist()">Continuar a Batalha</button>
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
    <h3 style="font-size: 1.1rem; text-align: center; margin-bottom: 0.25rem;">Bandeja Universal de Dados</h3>
    <p style="font-size: 0.75rem; color: var(--text-muted); text-align: center; margin-bottom: 0.75rem;">Suporta Sistema D20, D100, 3d6 e qualquer RPG de mesa:</p>

    <!-- Fórmula Personalizada Livre -->
    <form onsubmit="event.preventDefault(); const f=document.getElementById('tavern-dice-formula').value; if(f) bardoRoll(<?= $campaign->id ?>, <?= $characters[0]->id ?? 'null' ?>, f, 'Rolagem Especial');" style="display: flex; gap: 0.4rem; margin-bottom: 0.85rem;">
      <input type="text" id="tavern-dice-formula" placeholder="Fórmula livre (ex: 3d6+2, 2d8, 1d100)" style="flex: 1; padding: 0.6rem; background: var(--bg-surface); border: 1px solid var(--border-gold); border-radius: 8px; color: #fff; font-family: var(--font-code); font-size: 0.85rem;">
      <button type="submit" class="btn-touch btn-touch-primary" style="padding: 0 1rem;">Rolar</button>
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

    <button class="btn-touch btn-touch-dark" style="width: 100%; margin-top: 0.5rem;" onclick="closeDiceSheet()">Fechar</button>
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
