<?php require __DIR__ . '/../layout/header.php'; ?>

<main class="mobile-container">
  
  <!-- Banner Artístico do Mestre (Tormenta & D&D) -->
  <div class="fantasy-art-banner" style="background-image: linear-gradient(180deg, rgba(13,16,26,0.2) 0%, rgba(13,16,26,0.95) 100%), url('/images/gm-dragon-twist.jpg'); height: 165px; border-radius: 12px; border: 1.5px solid var(--border-gold); margin-bottom: 0.85rem; background-size: cover; background-position: center; display: flex; flex-direction: column; justify-content: flex-end; padding: 1rem; position: relative; box-shadow: 0 4px 20px rgba(0,0,0,0.7);">
    <div style="font-size: 0.65rem; color: var(--t20-ruby); font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em; text-shadow: 0 2px 4px #000;">
      🐉 Covil do Mestre • Destino & Reviravoltas da Trama
    </div>
    <div style="font-family: var(--font-title); font-size: 1.35rem; font-weight: 900; color: var(--gold-imperial); text-shadow: 0 2px 8px rgba(0,0,0,0.95);">
      <?= $e($campaign->title) ?>
    </div>
    <div style="font-size: 0.72rem; color: var(--text-secondary); text-shadow: 0 1px 4px #000;">
      Sistema: <?= $e($campaign->ruleSystem) ?> • Mestre da Mesa: <strong style="color:#fff;"><?= $e($user->name) ?></strong>
    </div>
  </div>

  <!-- Ações Rápidas do Mestre -->
  <div class="rpg-card" style="border-color: var(--border-gold);">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
      <div>
        <h2 style="font-size: 1.05rem; color: var(--gold-imperial);">Comando da Sessão</h2>
        <p style="font-size: 0.75rem; color: var(--text-muted);">
          Controle a atmosfera acústica e os segredos narrativos em tempo real.
        </p>
      </div>
      <button class="btn-touch btn-touch-ruby" style="padding: 5px 12px; font-size: 0.78rem;" onclick="bardoGMPlotTwist(<?= $campaign->id ?>, 'A EMBOSCADA DA TORMENTA!', 'Nuvens púrpuras cobrem o céu. O solo sangra e aberrantes lefeu saltam das ruínas!', 'combate')">
        ⚡ Disparar Reviravolta Épica
      </button>
    </div>
  </div>

  <!-- SEÇÃO 1: MESA DE SOM & AMBIENTAÇÃO SINCRONIZADA -->
  <div id="section-gm-audio" class="gm-tab-pane">
    <div class="rpg-card">
      <div class="card-title-bar">
        <span class="card-title-text">🎵 Som & Ambientação Sincronizada</span>
        <span class="tag-pill gold">Broadcast Ativo</span>
      </div>

      <p style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.75rem;">
        Comande a trilha que toca simultaneamente nos celulares de todos os jogadores conectados:
      </p>

      <div style="display: flex; flex-direction: column; gap: 0.5rem; margin-bottom: 1.25rem;">
        <div class="action-card ambient-track-btn" data-track="tavern" onclick="bardoGMSync(<?= $campaign->id ?>, 'PLAY', 1, 'music'); bardoPlayAmbient('tavern');" style="cursor: pointer; margin-bottom: 0;">
          <div class="action-header">
            <span class="action-title">🍻 Taverna do Javali Saltitante</span>
            <span class="tag-pill">Loop Contínuo</span>
          </div>
          <div class="action-desc">Alaúdes alegres, murmúrio de conversas e taças brindando.</div>
        </div>

        <div class="action-card ambient-track-btn" data-track="dungeon" onclick="bardoGMSync(<?= $campaign->id ?>, 'PLAY', 2, 'music'); bardoPlayAmbient('dungeon');" style="cursor: pointer; margin-bottom: 0;">
          <div class="action-header">
            <span class="action-title">💀 Masmorra & Cripta Antiga</span>
            <span class="tag-pill">Suspense</span>
          </div>
          <div class="action-desc">Gotas de água fria ecoando, vento espectral e tensão.</div>
        </div>

        <div class="action-card ambient-track-btn" data-track="combat" onclick="bardoGMSync(<?= $campaign->id ?>, 'PLAY', 3, 'music'); bardoPlayAmbient('combat');" style="cursor: pointer; margin-bottom: 0;">
          <div class="action-header">
            <span class="action-title">⚔️ Batalha pela Vida (Combate T20)</span>
            <span class="tag-pill" style="color: var(--t20-ruby); border-color: var(--t20-ruby);">Clímax</span>
          </div>
          <div class="action-desc">Tambores de guerra urgentes, trompas heróicas e adrenalina.</div>
        </div>
      </div>

      <!-- Soundboard de Disparo Imediato -->
      <div class="card-title-bar">
        <span class="card-title-text">⚡ Soundboard Instantâneo (SFX)</span>
        <span style="font-size: 0.7rem; color: var(--text-muted);">Sincronia Imediata</span>
      </div>

      <div class="sfx-touch-grid">
        <div class="sfx-pad" onclick="bardoGMSFX(<?= $campaign->id ?>, 'sword')">
          <span class="sfx-pad-icon">⚔️</span>
          <span class="sfx-pad-label">Aço / Golpe</span>
        </div>
        <div class="sfx-pad" onclick="bardoGMSFX(<?= $campaign->id ?>, 'thunder')">
          <span class="sfx-pad-icon">⚡</span>
          <span class="sfx-pad-label">Trovão</span>
        </div>
        <div class="sfx-pad" onclick="bardoGMSFX(<?= $campaign->id ?>, 'spell')">
          <span class="sfx-pad-icon">🔮</span>
          <span class="sfx-pad-label">Magia Arcana</span>
        </div>
        <div class="sfx-pad" onclick="bardoGMSFX(<?= $campaign->id ?>, 'roar')">
          <span class="sfx-pad-icon">🐉</span>
          <span class="sfx-pad-label">Rugido</span>
        </div>
        <div class="sfx-pad" onclick="bardoGMSFX(<?= $campaign->id ?>, 'dice')">
          <span class="sfx-pad-icon">🎲</span>
          <span class="sfx-pad-label">Dados Mesa</span>
        </div>
      </div>
    </div>
  </div>

  <!-- SEÇÃO 2: ESTÚDIO DE REVIRAVOLTAS DA TRAMA -->
  <div id="section-gm-twists" class="gm-tab-pane" style="display: none;">
    <div class="rpg-card" style="border-color: var(--t20-ruby);">
      <div class="card-title-bar">
        <span class="card-title-text" style="color: var(--t20-ruby);">🔥 Desencadear Reviravolta da Trama</span>
        <span class="tag-pill" style="background: var(--t20-ruby); color: #fff;">Ao Vivo</span>
      </div>

      <p style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.75rem;">
        Ao clicar, a tela de todos os celulares escurece com pulso carmesim e o som dramático ecoa simultaneamente:
      </p>

      <div style="display: flex; flex-direction: column; gap: 0.75rem;">
        <div>
          <label style="font-size: 0.7rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase;">Manchete Dramática</label>
          <input id="gm-twist-title" type="text" value="A MÁSCARA DO CONSUL CAI!" style="width: 100%; padding: 0.65rem; background: var(--bg-surface); border: 1px solid var(--border-subtle); border-radius: 8px; color: var(--gold-imperial); font-family: var(--font-title); font-size: 1rem; margin-top: 3px;">
        </div>

        <div>
          <label style="font-size: 0.7rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase;">Descrição de Impacto</label>
          <textarea id="gm-twist-text" rows="3" style="width: 100%; padding: 0.65rem; background: var(--bg-surface); border: 1px solid var(--border-subtle); border-radius: 8px; color: var(--text-parchment); font-size: 0.85rem; margin-top: 3px;">O nobre retira o capuz e desembainha uma lâmina negra. As portas da sala se fecham com estrondo de ferro!</textarea>
        </div>

        <button class="btn-touch btn-touch-ruby" style="width: 100%; padding: 0.85rem; font-size: 0.95rem; margin-top: 0.5rem;" onclick="bardoGMPlotTwist(<?= $campaign->id ?>, document.getElementById('gm-twist-title').value, document.getElementById('gm-twist-text').value, 'combate')">
          ⚡ EXECUTAR REVIRAVOLTA AO VIVO (TODOS OS DISPOSITIVOS)
        </button>
      </div>
    </div>
  </div>

  <!-- SEÇÃO 3: MONITOR DE FICHAS DA MESA -->
  <div id="section-gm-party" class="gm-tab-pane" style="display: none;">
    <div class="rpg-card">
      <div class="card-title-bar">
        <span class="card-title-text">🛡️ Status do Grupo (PV & PM)</span>
        <span style="font-size: 0.7rem; color: var(--emerald-life);">Tempo Real</span>
      </div>

      <div style="display: flex; flex-direction: column; gap: 0.65rem;">
        <?php foreach ($characters as $char): ?>
          <div class="action-card ruby" style="margin-bottom: 0;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
              <div>
                <div style="font-weight: 700; font-size: 0.9rem;"><?= $e($char->name) ?> (<?= $e($char->classAndLevel) ?>)</div>
                <div style="font-size: 0.75rem; color: var(--text-muted);">Defesa: <?= (int)$char->armorClass ?></div>
              </div>
              <div style="text-align: right;">
                <div id="party-hp-<?= $char->id ?>" style="font-family: var(--font-code); font-weight: 800; font-size: 0.85rem; color: var(--t20-ruby);">
                  <?= (int)$char->hpCurrent ?>/<?= (int)$char->hpMax ?> PV
                </div>
                <div id="party-mp-<?= $char->id ?>" style="font-family: var(--font-code); font-weight: 800; font-size: 0.85rem; color: var(--mana-cyan-light);">
                  <?= (int)$char->mpCurrent ?>/<?= (int)$char->mpMax ?> PM
                </div>
              </div>
            </div>
            <div style="display: flex; gap: 0.35rem; margin-top: 0.5rem;">
              <button class="btn-touch btn-touch-dark" style="font-size: 0.7rem; padding: 2px 6px;" onclick="bardoModifyHP(<?= $char->id ?>, -5)">-5 PV</button>
              <button class="btn-touch btn-touch-dark" style="font-size: 0.7rem; padding: 2px 6px;" onclick="bardoModifyHP(<?= $char->id ?>, 5)">+5 PV</button>
              <button class="btn-touch btn-touch-mana" style="font-size: 0.7rem; padding: 2px 6px;" onclick="bardoModifyPM(<?= $char->id ?>, -2)">-2 PM</button>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <!-- SEÇÃO 4: SEGREDOS DO MESTRE -->
  <div id="section-gm-secrets" class="gm-tab-pane" style="display: none;">
    <div class="rpg-card">
      <div class="card-title-bar">
        <span class="card-title-text">📜 Anotações Secretas do Mestre</span>
        <span class="tag-pill" style="color: var(--gold-imperial);">Nunca vaza via API</span>
      </div>

      <textarea rows="6" style="width: 100%; padding: 0.65rem; background: var(--bg-surface); border: 1px solid var(--border-subtle); border-radius: 8px; color: var(--text-parchment); font-size: 0.85rem; line-height: 1.5;">
• O artefato no altar exige 3 gotas de sangue de um nobre ou 5 PM canalizados.
• Os cultistas têm vulnerabilidade a dano de fogo (fraqueza oculta).
• O mapa da saída secreta fica atrás do brasão de aço na parede leste.
      </textarea>
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
