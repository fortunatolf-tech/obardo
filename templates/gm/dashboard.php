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

  <!-- Ações Rápidas do Mestre & Rolador Oculto (RF12) -->
  <div class="rpg-card" style="border-color: var(--border-gold);">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 0.75rem;">
      <div>
        <h2 style="font-size: 1.05rem; color: var(--gold-imperial);">Comando da Sessão</h2>
        <p style="font-size: 0.75rem; color: var(--text-muted);">
          Controle atmosfera acústica, reviravoltas e segredos em tempo real.
        </p>
      </div>
      <button class="btn-touch btn-touch-ruby" style="padding: 5px 12px; font-size: 0.78rem;" onclick="bardoGMPlotTwist(<?= $campaign->id ?>, 'A EMBOSCADA DA TORMENTA!', 'Nuvens púrpuras cobrem o céu. O solo sangra e aberrantes lefeu saltam das ruínas!', 'combate')">
        ⚡ Disparar Reviravolta Épica
      </button>
    </div>

    <!-- Rolador Tático do Mestre com Modo Oculto -->
    <div style="background: rgba(0,0,0,0.45); border: 1px solid var(--border-gold-dark); border-radius: 8px; padding: 0.6rem;">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem;">
        <span style="font-family: var(--font-mythic); font-size: 0.78rem; color: var(--gold-imperial);">🎲 Rolador Tático do Mestre</span>
        <label style="display: flex; align-items: center; gap: 0.35rem; font-size: 0.72rem; color: var(--t20-ruby); font-weight: 700; cursor: pointer;">
          <input type="checkbox" id="gm-dice-secret-toggle" checked style="accent-color: var(--t20-ruby);">
          <span>🔒 Rolagem Oculta (Apenas GM)</span>
        </label>
      </div>
      <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 0.35rem;">
        <button type="button" class="btn-touch btn-touch-dark" style="padding: 3px; font-size: 0.75rem;" onclick="bardoGMRoll(<?= $campaign->id ?>, '1d20', 'Percepção Oculta')">d20</button>
        <button type="button" class="btn-touch btn-touch-dark" style="padding: 3px; font-size: 0.75rem;" onclick="bardoGMRoll(<?= $campaign->id ?>, '1d20+5', 'Ataque Monstro')">d20+5</button>
        <button type="button" class="btn-touch btn-touch-dark" style="padding: 3px; font-size: 0.75rem;" onclick="bardoGMRoll(<?= $campaign->id ?>, '1d100', 'Sorte / Tabela')">d100</button>
        <button type="button" class="btn-touch btn-touch-dark" style="padding: 3px; font-size: 0.75rem;" onclick="bardoGMRoll(<?= $campaign->id ?>, '3d6', 'GURPS / Teste')">3d6</button>
        <button type="button" class="btn-touch btn-touch-dark" style="padding: 3px; font-size: 0.75rem;" onclick="bardoGMRoll(<?= $campaign->id ?>, '2d6+3', 'Dano Monstro')">2d6+3</button>
      </div>
      <div id="gm-roll-result-display" style="display: none; margin-top: 0.4rem;"></div>
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

  <!-- SEÇÃO 3: MONITOR DE FICHAS DA MESA & GESTÃO DE MEMBROS (RF05) -->
  <div id="section-gm-party" class="gm-tab-pane" style="display: none;">
    <div class="rpg-card">
      <div class="card-title-bar">
        <span class="card-title-text">🛡️ Status do Grupo (PV & PM)</span>
        <span style="font-size: 0.7rem; color: var(--emerald-life);">Tempo Real</span>
      </div>

      <div style="display: flex; flex-direction: column; gap: 0.65rem;">
        <?php if (empty($characters)): ?>
          <p style="font-size: 0.8rem; color: var(--text-muted); text-align: center; padding: 1rem;">Nenhuma ficha criada na campanha ainda.</p>
        <?php endif; ?>
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

    <!-- Gestão de Membros & Co-Mestres da Campanha (RF05) -->
    <div class="rpg-card" style="margin-top: 0.85rem;">
      <div class="card-title-bar">
        <span class="card-title-text">👥 Aventureiros & Membros da Mesa</span>
        <span class="tag-pill gold"><?= count($members ?? []) ?> Presentes</span>
      </div>
      <p style="font-size: 0.72rem; color: var(--text-muted); margin-bottom: 0.75rem;">
        Gerencie permissões de Mestre/Co-Mestre e participantes da campanha:
      </p>

      <div style="display: flex; flex-direction: column; gap: 0.5rem;">
        <?php foreach ($members ?? [] as $m): ?>
          <div style="display: flex; justify-content: space-between; align-items: center; background: rgba(0,0,0,0.4); border: 1px solid var(--border-subtle); border-radius: 8px; padding: 0.6rem 0.8rem;">
            <div>
              <div style="font-size: 0.85rem; font-weight: 700; color: #fff;">
                <?= $e($m['name']) ?>
                <?php if ($m['role'] === 'gm'): ?>
                  <span class="tag-pill gold" style="font-size: 0.62rem; margin-left: 4px;">MESTRE</span>
                <?php elseif ($m['role'] === 'co_gm'): ?>
                  <span class="tag-pill" style="font-size: 0.62rem; margin-left: 4px; color: var(--mana-cyan-light); border-color: var(--mana-cyan-light);">CO-MESTRE</span>
                <?php else: ?>
                  <span class="tag-pill" style="font-size: 0.62rem; margin-left: 4px;">JOGADOR</span>
                <?php endif; ?>
              </div>
              <div style="font-size: 0.7rem; color: var(--text-muted);"><?= $e($m['email']) ?></div>
            </div>
            <?php if ($m['user_id'] != $user->id): ?>
              <div style="display: flex; gap: 0.35rem;">
                <?php if ($m['role'] === 'co_gm'): ?>
                  <button type="button" class="btn-touch btn-touch-dark" style="font-size: 0.68rem; padding: 3px 8px;" onclick="bardoUpdateMemberRole(<?= $campaign->id ?>, <?= $m['user_id'] ?>, 'player')">Tornar Jogador</button>
                <?php else: ?>
                  <button type="button" class="btn-touch btn-touch-mana" style="font-size: 0.68rem; padding: 3px 8px;" onclick="bardoUpdateMemberRole(<?= $campaign->id ?>, <?= $m['user_id'] ?>, 'co_gm')">+ Co-Mestre</button>
                <?php endif; ?>
                <button type="button" class="btn-touch btn-touch-ruby" style="font-size: 0.68rem; padding: 3px 8px;" onclick="bardoKickMember(<?= $campaign->id ?>, <?= $m['user_id'] ?>, '<?= $e($m['name']) ?>')">Expulsar</button>
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
    <div class="rpg-card" style="border-color: var(--border-gold);">
      <div class="card-title-bar">
        <span class="card-title-text">📜 Anotações Secretas do Mestre</span>
        <span class="tag-pill" style="color: var(--gold-imperial);">Isolamento RF17/RN06</span>
      </div>

      <p style="font-size: 0.72rem; color: var(--text-muted); margin-bottom: 0.5rem;">
        Anotações protegidas por isolamento lógico. Jamais são transmitidas para jogadores via API ou SSE:
      </p>

      <textarea id="gm-secrets-text" rows="6" placeholder="Escreva aqui segredos da masmorra, fraquezas de monstros, senhas de portas místicas..." style="width: 100%; padding: 0.65rem; background: var(--bg-surface); border: 1px solid var(--border-gold-dark); border-radius: 8px; color: var(--text-parchment); font-size: 0.85rem; line-height: 1.5; margin-bottom: 0.75rem;"><?= $e($campaign->gmSecrets ?? '') ?></textarea>

      <button type="button" class="btn-touch btn-touch-primary" style="width: 100%;" onclick="bardoSaveGMSecrets(<?= $campaign->id ?>)">
        💾 Salvar Segredos do Mestre
      </button>
    </div>

    <!-- Pistas & Handouts Reveláveis (RF15) -->
    <div class="rpg-card" style="margin-top: 0.85rem;">
      <div class="card-title-bar">
        <span class="card-title-text">🗺️ Pistas & Handouts da Aventura</span>
        <button type="button" class="btn-touch btn-touch-dark" style="font-size: 0.72rem; padding: 3px 8px;" onclick="document.getElementById('modal-new-handout').style.display='flex'">
          + Nova Pista
        </button>
      </div>

      <p style="font-size: 0.72rem; color: var(--text-muted); margin-bottom: 0.75rem;">
        Crie cartas, pergaminhos ou mapas e revele na hora certa para toda a mesa:
      </p>

      <div style="display: flex; flex-direction: column; gap: 0.6rem;">
        <?php if (empty($handouts)): ?>
          <p style="font-size: 0.8rem; color: var(--text-muted); text-align: center; padding: 1rem;">Nenhuma pista cadastrada ainda.</p>
        <?php endif; ?>
        <?php foreach ($handouts ?? [] as $h): ?>
          <div style="background: rgba(0,0,0,0.5); border: 1px solid <?= $h['is_revealed'] ? 'var(--gold-imperial)' : 'var(--border-subtle)' ?>; border-radius: 8px; padding: 0.75rem;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 0.5rem;">
              <div>
                <div style="font-weight: 700; font-size: 0.9rem; color: var(--gold-imperial);">
                  <?= $e($h['title']) ?>
                  <?php if ($h['is_revealed']): ?>
                    <span class="tag-pill gold" style="font-size: 0.6rem; margin-left: 4px;">👁️ Revelada à Mesa</span>
                  <?php else: ?>
                    <span class="tag-pill" style="font-size: 0.6rem; margin-left: 4px;">🔒 Oculta (Segredo)</span>
                  <?php endif; ?>
                </div>
                <?php if (!empty($h['description'])): ?>
                  <div style="font-size: 0.78rem; color: var(--text-parchment); margin-top: 3px; font-family: var(--font-lore);">
                    <?= nl2br($e($h['description'])) ?>
                  </div>
                <?php endif; ?>
              </div>
              <button type="button" class="btn-touch <?= $h['is_revealed'] ? 'btn-touch-dark' : 'btn-touch-primary' ?>" style="font-size: 0.72rem; padding: 4px 10px; white-space: nowrap;" onclick="bardoToggleHandout(<?= $campaign->id ?>, <?= $h['id'] ?>, <?= $h['is_revealed'] ? 'false' : 'true' ?>)">
                <?= $h['is_revealed'] ? '🔒 Ocultar' : '👁️ Revelar' ?>
              </button>
            </div>
            <?php if (!empty($h['image_url'])): ?>
              <div style="margin-top: 0.5rem; border-radius: 6px; overflow: hidden; max-height: 140px;">
                <img src="<?= $e($h['image_url']) ?>" alt="<?= $e($h['title']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
              </div>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <!-- MODAL DE NOVO HANDOUT / PISTA (RF15) -->
  <div id="modal-new-handout" style="display: none; position: fixed; inset: 0; z-index: 200; background: rgba(5,7,12,0.85); backdrop-filter: blur(8px); align-items: center; justify-content: center; padding: 1rem;">
    <div style="background: linear-gradient(145deg, rgba(23, 27, 42, 0.98), rgba(12, 15, 24, 0.99)), url('/images/parchment-grimoire.jpg') center / cover; border: 2px solid var(--border-gold); box-shadow: 0 0 35px rgba(212, 175, 55, 0.35); border-radius: 16px; padding: 1.5rem; max-width: 480px; width: 100%;">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; border-bottom: 1px solid var(--border-gold-dark); padding-bottom: 0.5rem;">
        <div>
          <h3 style="font-family: var(--font-mythic); font-size: 1.15rem; color: var(--gold-imperial); margin: 0;">📜 Forjar Nova Pista / Handout</h3>
          <p style="font-size: 0.72rem; color: var(--text-muted); margin: 2px 0 0 0;">Compartilhe documentos, mapas ou enigmas com o grupo</p>
        </div>
        <button type="button" onclick="document.getElementById('modal-new-handout').style.display='none'" style="background: none; border: none; color: var(--gold-imperial); font-size: 1.4rem; cursor: pointer;">✕</button>
      </div>

      <form onsubmit="bardoCreateHandout(event, <?= $campaign->id ?>)">
        <div style="margin-bottom: 0.85rem;">
          <label style="display: block; font-family: var(--font-ui); font-size: 0.75rem; color: var(--gold-imperial); font-weight: 700; margin-bottom: 4px;">TÍTULO DA PISTA *</label>
          <input type="text" name="title" required placeholder="Ex: Diário do Cultista, Fragmento do Mapa..." style="width: 100%; background: rgba(0,0,0,0.6); border: 1px solid var(--border-gold); border-radius: 8px; color: #fff; padding: 0.6rem 0.8rem; font-family: var(--font-lore); font-size: 0.9rem;">
        </div>

        <div style="margin-bottom: 0.85rem;">
          <label style="display: block; font-family: var(--font-ui); font-size: 0.75rem; color: var(--gold-imperial); font-weight: 700; margin-bottom: 4px;">CONTEÚDO / TEXTO DA PISTA</label>
          <textarea name="description" rows="3" placeholder="O texto transcrito ou descrição do objeto..." style="width: 100%; background: rgba(0,0,0,0.6); border: 1px solid var(--border-gold); border-radius: 8px; color: #fff; padding: 0.6rem 0.8rem; font-family: var(--font-lore); font-size: 0.85rem;"></textarea>
        </div>

        <div style="margin-bottom: 0.85rem;">
          <label style="display: block; font-family: var(--font-ui); font-size: 0.75rem; color: var(--gold-imperial); font-weight: 700; margin-bottom: 4px;">URL DA IMAGEM (OPCIONAL)</label>
          <input type="text" name="image_url" placeholder="/images/parchment-grimoire.jpg ou URL externa" style="width: 100%; background: rgba(0,0,0,0.6); border: 1px solid var(--border-gold-dark); border-radius: 8px; color: #fff; padding: 0.5rem 0.8rem; font-size: 0.8rem;">
        </div>

        <div style="margin-bottom: 1.25rem;">
          <label style="display: flex; align-items: center; gap: 0.5rem; font-family: var(--font-ui); font-size: 0.8rem; color: var(--mana-cyan-light); font-weight: 700; cursor: pointer;">
            <input type="checkbox" name="is_revealed" style="accent-color: var(--mana-cyan-light);">
            <span>Revelar imediatamente para todos os jogadores na Taverna</span>
          </label>
        </div>

        <div style="display: flex; gap: 0.75rem;">
          <button type="button" class="btn-touch btn-touch-dark" style="flex: 1;" onclick="document.getElementById('modal-new-handout').style.display='none'">Cancelar</button>
          <button type="submit" class="btn-touch btn-touch-primary" style="flex: 2;">📜 Criar Pista</button>
        </div>
      </form>
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
