<?php require __DIR__ . '/../layout/header.php'; ?>

<main class="mobile-container">
  
  <?php if (!$character): ?>
    <div class="rpg-card" style="text-align: center; padding: 2rem;">
      <h2 style="font-size: 1.2rem; color: var(--gold-imperial); margin-bottom: 0.5rem;">Nenhum Personagem Criado</h2>
      <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 1.25rem;">Você ainda não possui uma ficha nesta campanha.</p>
      <button class="btn-touch btn-touch-primary" onclick="document.getElementById('modal-new-character').style.display='flex'">
        + Criar Ficha Tormenta20 / D&D
      </button>
    </div>
  <?php else: ?>

    <!-- Hero Card do Personagem -->
    <div class="rpg-card">
      <div class="hero-compact">
        <div class="hero-portrait-frame">
          <img src="/images/hero-portrait.jpg" alt="<?= $e($character->name) ?>" style="width: 100%; height: 100%; object-fit: cover; border-radius: 10px;">
          <div class="level-badge"><?= $e(explode(' ', $character->classAndLevel)[1] ?? 'NV 1') ?></div>
        </div>
        <div class="hero-meta">
          <div class="hero-name"><?= $e($character->name) ?></div>
          <div class="hero-class-race"><?= $e($character->classAndLevel) ?> • <?= $e($character->raceOrigin) ?></div>
          <div class="hero-tags">
            <span class="tag-pill gold"><?= $e($campaign->title) ?></span>
            <span class="tag-pill"><?= $e($campaign->ruleSystem) ?></span>
          </div>
        </div>
      </div>

      <!-- Medidores Vitais: PV (Vida), PM (Mana) & Defesa -->
      <div class="vitals-grid">
        <!-- Barra de PV (Pontos de Vida - Carmesim) -->
        <div class="vital-box" onclick="openVitalsModal()">
          <div class="vital-header">
            <span class="vital-title" style="color: var(--t20-ruby);">❤️ PV (VIDA)</span>
            <span class="vital-numbers"><span id="hp-current-val"><?= (int)$character->hpCurrent ?></span>/<span id="hp-max-val"><?= (int)$character->hpMax ?></span></span>
          </div>
          <div class="bar-track">
            <?php $hpPct = $character->hpMax > 0 ? min(100, max(0, round(($character->hpCurrent / $character->hpMax) * 100))) : 0; ?>
            <div id="hp-fill-bar" class="bar-fill ruby" style="width: <?= $hpPct ?>%;"></div>
          </div>
          <div style="font-size: 0.65rem; color: var(--text-muted); margin-top: 4px; text-align: right;">
            Toque p/ Dano/Cura
          </div>
        </div>

        <!-- Barra de PM (Pontos de Mana - Cyan Arcana) -->
        <div class="vital-box" onclick="bardoModifyPM(<?= $character->id ?>, -1)">
          <div class="vital-header">
            <span class="vital-title" style="color: var(--mana-cyan-light);">💎 PM (MANA)</span>
            <span class="vital-numbers"><span id="pm-current-val"><?= (int)$character->mpCurrent ?></span>/<span id="pm-max-val"><?= (int)$character->mpMax ?></span></span>
          </div>
          <div class="bar-track">
            <?php $mpPct = $character->mpMax > 0 ? min(100, max(0, round(($character->mpCurrent / $character->mpMax) * 100))) : 0; ?>
            <div id="pm-fill-bar" class="bar-fill mana" style="width: <?= $mpPct ?>%;"></div>
          </div>
          <div style="font-size: 0.65rem; color: var(--text-muted); margin-top: 4px; text-align: right;">
            Toque p/ Gastar 1 PM
          </div>
        </div>

        <!-- Defesa / CA (Escudo Nobre T20/D&D) -->
        <div class="defense-shield-box" onclick="bardoRoll(<?= $campaign->id ?>, <?= $character->id ?>, '1d20+1', 'Iniciativa')">
          <span class="defense-label">DEFESA</span>
          <span class="defense-value"><?= (int)$character->armorClass ?></span>
          <span class="defense-sub">Inic +1 🎲</span>
        </div>
      </div>
    </div>

    <!-- Atributos Primários com Modificador Gigante -->
    <?php 
      $attrs = $character->attributesData;
      $forVal = $attrs['for'] ?? 10;
      $forMod = $attrs['for_mod'] ?? (int)floor(($forVal - 10) / 2);
      $desVal = $attrs['des'] ?? 10;
      $desMod = $attrs['des_mod'] ?? (int)floor(($desVal - 10) / 2);
      $conVal = $attrs['con'] ?? 10;
      $conMod = $attrs['con_mod'] ?? (int)floor(($conVal - 10) / 2);
      $intVal = $attrs['int'] ?? 10;
      $intMod = $attrs['int_mod'] ?? (int)floor(($intVal - 10) / 2);
      $sabVal = $attrs['sab'] ?? 10;
      $sabMod = $attrs['sab_mod'] ?? (int)floor(($sabVal - 10) / 2);
      $carVal = $attrs['car'] ?? 10;
      $carMod = $attrs['car_mod'] ?? (int)floor(($carVal - 10) / 2);
    ?>

    <div class="attributes-row">
      <div class="attr-card" onclick="bardoRoll(<?= $campaign->id ?>, <?= $character->id ?>, '1d20<?= $forMod >= 0 ? '+' . $forMod : $forMod ?>', 'Teste de Força')">
        <span class="attr-tag">FOR</span>
        <span class="attr-mod"><?= $forMod >= 0 ? '+' . $forMod : $forMod ?></span>
        <span class="attr-raw"><?= $forVal ?></span>
      </div>
      <div class="attr-card" onclick="bardoRoll(<?= $campaign->id ?>, <?= $character->id ?>, '1d20<?= $desMod >= 0 ? '+' . $desMod : $desMod ?>', 'Teste de Destreza')">
        <span class="attr-tag">DES</span>
        <span class="attr-mod"><?= $desMod >= 0 ? '+' . $desMod : $desMod ?></span>
        <span class="attr-raw"><?= $desVal ?></span>
      </div>
      <div class="attr-card" onclick="bardoRoll(<?= $campaign->id ?>, <?= $character->id ?>, '1d20<?= $conMod >= 0 ? '+' . $conMod : $conMod ?>', 'Teste de Constituição')">
        <span class="attr-tag">CON</span>
        <span class="attr-mod"><?= $conMod >= 0 ? '+' . $conMod : $conMod ?></span>
        <span class="attr-raw"><?= $conVal ?></span>
      </div>
      <div class="attr-card" onclick="bardoRoll(<?= $campaign->id ?>, <?= $character->id ?>, '1d20<?= $intMod >= 0 ? '+' . $intMod : $intMod ?>', 'Teste de Inteligência')">
        <span class="attr-tag">INT</span>
        <span class="attr-mod"><?= $intMod >= 0 ? '+' . $intMod : $intMod ?></span>
        <span class="attr-raw"><?= $intVal ?></span>
      </div>
      <div class="attr-card" onclick="bardoRoll(<?= $campaign->id ?>, <?= $character->id ?>, '1d20<?= $sabMod >= 0 ? '+' . $sabMod : $sabMod ?>', 'Teste de Sabedoria')">
        <span class="attr-tag">SAB</span>
        <span class="attr-mod"><?= $sabMod >= 0 ? '+' . $sabMod : $sabMod ?></span>
        <span class="attr-raw"><?= $sabVal ?></span>
      </div>
      <div class="attr-card" onclick="bardoRoll(<?= $campaign->id ?>, <?= $character->id ?>, '1d20<?= $carMod >= 0 ? '+' . $carMod : $carMod ?>', 'Teste de Carisma')">
        <span class="attr-tag">CAR</span>
        <span class="attr-mod"><?= $carMod >= 0 ? '+' . $carMod : $carMod ?></span>
        <span class="attr-raw"><?= $carVal ?></span>
      </div>
    </div>

    <!-- ABAS DINÂMICAS -->

    <!-- ABA 1: COMBATE & ATAQUES -->
    <div id="section-combate" class="tab-pane">
      <div class="rpg-card">
        <div class="card-title-bar">
          <span class="card-title-text">⚔️ Ataques & Ações de Combate</span>
          <span style="font-size: 0.7rem; color: var(--text-muted);">Bônus Base: +<?= max(1, $forMod + 2) ?></span>
        </div>

        <div class="action-card ruby">
          <div class="action-header">
            <span class="action-title">Espada Longa de Aço-Rubi</span>
            <span class="tag-pill gold">Crítico 19-20/x2</span>
          </div>
          <div class="action-desc">
            1 mão (dano 1d8+<?= $forMod ?>) ou 2 mãos (dano 1d10+<?= $forMod ?>) • Cortante
          </div>
          <div class="action-buttons">
            <button class="btn-touch btn-touch-primary" onclick="bardoRoll(<?= $campaign->id ?>, <?= $character->id ?>, '1d20+<?= max(1, $forMod + 4) ?>', 'Ataque Espada Longa')">
              ⚔️ Atacar (+<?= max(1, $forMod + 4) ?>)
            </button>
            <button class="btn-touch btn-touch-dark" onclick="bardoRoll(<?= $campaign->id ?>, <?= $character->id ?>, '1d8+<?= $forMod ?>', 'Dano Espada (1 mão)')">
              1d8+<?= $forMod ?>
            </button>
            <button class="btn-touch btn-touch-dark" onclick="bardoRoll(<?= $campaign->id ?>, <?= $character->id ?>, '1d10+<?= $forMod ?>', 'Dano Espada (2 mãos)')">
              1d10+<?= $forMod ?>
            </button>
          </div>
        </div>

        <div class="action-card mana">
          <div class="action-header">
            <span class="action-title">Golpe Especial Flamejante</span>
            <span class="action-cost">Custo: 2 PM</span>
          </div>
          <div class="action-desc">
            Envolve a arma em chamas arcanas. Concede +2d6 de dano de fogo no ataque.
          </div>
          <button class="btn-touch btn-touch-mana" style="width: 100%;" onclick="bardoModifyPM(<?= $character->id ?>, -2); bardoRoll(<?= $campaign->id ?>, <?= $character->id ?>, '1d20+<?= max(1, $forMod + 4) ?>', 'Golpe Especial (Ataque)'); bardoRoll(<?= $campaign->id ?>, <?= $character->id ?>, '2d6', 'Dano Extra de Fogo');">
            🔥 Desferir Golpe com Chamas (2 PM)
          </button>
        </div>
      </div>
    </div>

    <!-- ABA 2: MAGIAS & GRIMÓRIO (COM CUSTO EM PM) -->
    <div id="section-magias" class="tab-pane" style="display: none;">
      <div class="rpg-card">
        <div class="card-title-bar">
          <span class="card-title-text">🔮 Grimório & Habilidades Arcanas</span>
          <span style="font-size: 0.75rem; color: var(--mana-cyan-light); font-weight: 700;">Mana Atual: <?= (int)$character->mpCurrent ?> PM</span>
        </div>

        <?php if (empty($character->spellsData)): ?>
          <p style="font-size: 0.8rem; color: var(--text-muted); text-align: center; padding: 1rem;">Nenhuma magia registrada no momento.</p>
        <?php else: ?>
          <?php foreach ($character->spellsData as $spell): ?>
            <div class="action-card mana">
              <div class="action-header">
                <span class="action-title"><?= $e($spell['name']) ?></span>
                <span class="action-cost"><?= (int)($spell['cost_pm'] ?? 1) ?> PM • <?= $e($spell['action'] ?? 'Padrão') ?></span>
              </div>
              <div class="action-desc"><?= $e($spell['desc'] ?? '') ?></div>
              <button class="btn-touch btn-touch-mana" style="width: 100%;" onclick="bardoModifyPM(<?= $character->id ?>, -<?= (int)($spell['cost_pm'] ?? 1) ?>); bardoPlaySFX('spell'); bardoToast('🔮 Magia <?= $e($spell['name']) ?> conjurada!');">
                Conjurar <?= $e($spell['name']) ?> (-<?= (int)($spell['cost_pm'] ?? 1) ?> PM)
              </button>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>

    <!-- ABA 3: PERÍCIAS (TORMENTA20 & D&D OGL) -->
    <div id="section-pericias" class="tab-pane" style="display: none;">
      <div class="rpg-card" style="padding: 0.5rem 0;">
        <div style="padding: 0.5rem 0.75rem 0.25rem 0.75rem; display: flex; justify-content: space-between; align-items: center;">
          <span class="card-title-text">📜 Perícias de Campanha</span>
          <span style="font-size: 0.7rem; color: var(--gold-imperial);">[T] = Treinada</span>
        </div>

        <?php 
          $skills = $character->skillsData;
          $defaultSkills = [
            ['name' => 'Atletismo', 'attr' => 'FOR', 'mod' => $forMod, 'trained' => true, 'bonus' => $forMod + 4],
            ['name' => 'Luta', 'attr' => 'FOR', 'mod' => $forMod, 'trained' => true, 'bonus' => $forMod + 4],
            ['name' => 'Fortitude', 'attr' => 'CON', 'mod' => $conMod, 'trained' => true, 'bonus' => $conMod + 4],
            ['name' => 'Guerra', 'attr' => 'INT', 'mod' => $intMod, 'trained' => true, 'bonus' => $intMod + 4],
            ['name' => 'Misticismo', 'attr' => 'INT', 'mod' => $intMod, 'trained' => true, 'bonus' => $intMod + 4],
            ['name' => 'Iniciativa', 'attr' => 'DES', 'mod' => $desMod, 'trained' => false, 'bonus' => $desMod],
            ['name' => 'Percepção', 'attr' => 'SAB', 'mod' => $sabMod, 'trained' => false, 'bonus' => $sabMod],
            ['name' => 'Pontaria', 'attr' => 'DES', 'mod' => $desMod, 'trained' => true, 'bonus' => $desMod + 4],
            ['name' => 'Vontade', 'attr' => 'SAB', 'mod' => $sabMod, 'trained' => true, 'bonus' => $sabMod + 4],
          ];
        ?>

        <?php foreach ($defaultSkills as $sk): ?>
          <div class="skill-row" onclick="bardoRoll(<?= $campaign->id ?>, <?= $character->id ?>, '1d20<?= $sk['bonus'] >= 0 ? '+' . $sk['bonus'] : $sk['bonus'] ?>', 'Perícia: <?= $e($sk['name']) ?>')">
            <div class="skill-left">
              <div class="skill-train-badge <?= $sk['trained'] ? 'trained' : '' ?>">
                <?= $sk['trained'] ? 'T' : '' ?>
              </div>
              <div>
                <span class="skill-name"><?= $e($sk['name']) ?></span>
                <span class="skill-attr-ref">(<?= $e($sk['attr']) ?>)</span>
              </div>
            </div>
            <span class="skill-bonus"><?= $sk['bonus'] >= 0 ? '+' . $sk['bonus'] : $sk['bonus'] ?> 🎲</span>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- ABA 4: MOCHILA & CARGA -->
    <div id="section-mochila" class="tab-pane" style="display: none;">
      <div class="rpg-card">
        <div class="card-title-bar">
          <span class="card-title-text">🎒 Mochila & Carga</span>
          <span style="font-size: 0.75rem; color: var(--emerald-life); font-weight: 700;">Capacidade: <?= $forVal ?> Espaços</span>
        </div>

        <div style="display: flex; flex-direction: column; gap: 0.5rem; font-size: 0.85rem;">
          <?php foreach ($character->inventoryData as $item): ?>
            <div style="background: var(--bg-surface); padding: 0.65rem 0.85rem; border-radius: 8px; display: flex; justify-content: space-between;">
              <span><?= $item['equipped'] ? '🛡️' : '🎒' ?> <?= $e($item['name']) ?></span>
              <span style="color: var(--gold-imperial); font-weight: 700;"><?= (int)($item['slots'] ?? 1) ?> Espaço(s)</span>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <!-- FEED DE ROLAGENS EM TEMPO REAL -->
    <div class="rpg-card" style="margin-top: 1rem;">
      <div class="card-title-bar">
        <span class="card-title-text">🎲 Histórico de Rolagens da Sessão</span>
        <button class="btn-touch btn-touch-dark" style="padding: 2px 8px; font-size: 0.7rem;" onclick="document.getElementById('session-roll-feed').innerHTML=''">Limpar</button>
      </div>
      <div id="session-roll-feed"></div>
    </div>

    <!-- BOTAO FLUTUANTE (FAB) PARA ROLAGEM RAPIDA DE DADOS -->
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
        <form onsubmit="event.preventDefault(); const f=document.getElementById('custom-dice-formula').value; if(f) bardoRoll(<?= $campaign->id ?>, <?= $character->id ?>, f, 'Rolagem Livre');" style="display: flex; gap: 0.4rem; margin-bottom: 0.85rem;">
          <input type="text" id="custom-dice-formula" placeholder="Fórmula livre (ex: 3d6+2, 2d8, 1d100)" style="flex: 1; padding: 0.6rem; background: var(--bg-surface); border: 1px solid var(--border-gold); border-radius: 8px; color: #fff; font-family: var(--font-code); font-size: 0.85rem;">
          <button type="submit" class="btn-touch btn-touch-primary" style="padding: 0 1rem;">Rolar</button>
        </form>

        <div class="dice-tray-grid">
          <div class="dice-pill" onclick="bardoRoll(<?= $campaign->id ?>, <?= $character->id ?>, '1d4', 'd4')">
            <span class="dice-pill-icon">🔺</span>
            <span class="dice-pill-name">d4</span>
          </div>
          <div class="dice-pill" onclick="bardoRoll(<?= $campaign->id ?>, <?= $character->id ?>, '1d6', 'd6')">
            <span class="dice-pill-icon">🎲</span>
            <span class="dice-pill-name">d6</span>
          </div>
          <div class="dice-pill" onclick="bardoRoll(<?= $campaign->id ?>, <?= $character->id ?>, '1d8', 'd8')">
            <span class="dice-pill-icon">💎</span>
            <span class="dice-pill-name">d8</span>
          </div>
          <div class="dice-pill" onclick="bardoRoll(<?= $campaign->id ?>, <?= $character->id ?>, '1d10', 'd10')">
            <span class="dice-pill-icon">🔟</span>
            <span class="dice-pill-name">d10</span>
          </div>
          <div class="dice-pill" onclick="bardoRoll(<?= $campaign->id ?>, <?= $character->id ?>, '1d12', 'd12')">
            <span class="dice-pill-icon">⭐</span>
            <span class="dice-pill-name">d12</span>
          </div>
          <div class="dice-pill selected" onclick="bardoRoll(<?= $campaign->id ?>, <?= $character->id ?>, '1d20', 'd20 Puro')">
            <span class="dice-pill-icon">👑</span>
            <span class="dice-pill-name">d20</span>
          </div>
          <div class="dice-pill" onclick="bardoRoll(<?= $campaign->id ?>, <?= $character->id ?>, '1d20+<?= max(1, $forMod + 4) ?>', 'd20 + Ataque')">
            <span class="dice-pill-icon">⚔️</span>
            <span class="dice-pill-name">+<?= max(1, $forMod + 4) ?></span>
          </div>
          <div class="dice-pill" onclick="bardoRoll(<?= $campaign->id ?>, <?= $character->id ?>, '1d100', 'd100 Porcentual')">
            <span class="dice-pill-icon">💯</span>
            <span class="dice-pill-name">d%</span>
          </div>
        </div>

        <button class="btn-touch btn-touch-dark" style="width: 100%; margin-top: 0.5rem;" onclick="closeDiceSheet()">Fechar</button>
      </div>
    </div>

    <!-- BOTTOM SHEET MODAL DE CONTROLE DE PV (DANO & CURA) -->
    <div id="vitals-modal-sheet" class="bottom-sheet" onclick="if(event.target === this) closeVitalsModal();">
      <div class="bottom-sheet-content">
        <div class="sheet-handle"></div>
        <h3 style="font-size: 1.1rem; text-align: center; color: var(--t20-ruby);">Ajuste Rápido de Pontos de Vida (PV)</h3>
        <p style="font-size: 0.75rem; color: var(--text-muted); text-align: center; margin-bottom: 1rem;">Aplique dano ou cura com um toque:</p>

        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.6rem; margin-bottom: 1rem;">
          <button class="btn-touch btn-touch-ruby" onclick="bardoModifyHP(<?= $character->id ?>, -1)">-1 Dano</button>
          <button class="btn-touch btn-touch-ruby" onclick="bardoModifyHP(<?= $character->id ?>, -5)">-5 Dano</button>
          <button class="btn-touch btn-touch-ruby" onclick="bardoModifyHP(<?= $character->id ?>, -10)">-10 Dano</button>
          <button class="btn-touch btn-touch-primary" onclick="bardoModifyHP(<?= $character->id ?>, 1)">+1 Cura</button>
          <button class="btn-touch btn-touch-primary" onclick="bardoModifyHP(<?= $character->id ?>, 5)">+5 Cura</button>
          <button class="btn-touch btn-touch-primary" onclick="bardoModifyHP(<?= $character->id ?>, 10)">+10 Cura</button>
        </div>

        <button class="btn-touch btn-touch-dark" style="width: 100%;" onclick="closeVitalsModal()">Concluir</button>
      </div>
    </div>

    <!-- BARRA DE NAVEGAÇÃO INFERIOR DOCK (FIXA NO MOBILE) -->
    <nav class="bottom-dock">
      <div class="dock-item active" onclick="switchMobileTab('combate', this)">
        <span class="dock-icon">⚔️</span>
        <span>Combate</span>
      </div>
      <div class="dock-item" onclick="switchMobileTab('magias', this)">
        <span class="dock-icon">🔮</span>
        <span>Magias</span>
      </div>
      <div class="dock-item" onclick="switchMobileTab('pericias', this)">
        <span class="dock-icon">📜</span>
        <span>Perícias</span>
      </div>
      <div class="dock-item" onclick="switchMobileTab('mochila', this)">
        <span class="dock-icon">🎒</span>
        <span>Mochila</span>
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

      function switchMobileTab(tabId, el) {
        document.querySelectorAll('.tab-pane').forEach(p => p.style.display = 'none');
        document.querySelectorAll('.dock-item').forEach(d => d.classList.remove('active'));
        const target = document.getElementById('section-' + tabId);
        if (target) target.style.display = 'block';
        if (el) el.classList.add('active');
      }

      function openDiceSheet() {
        document.getElementById('dice-bottom-sheet').classList.add('open');
      }
      function closeDiceSheet() {
        document.getElementById('dice-bottom-sheet').classList.remove('open');
      }
      function openVitalsModal() {
        document.getElementById('vitals-modal-sheet').classList.add('open');
      }
      function closeVitalsModal() {
        document.getElementById('vitals-modal-sheet').classList.remove('open');
      }
    </script>
  <?php endif; ?>

</main>

<?php require __DIR__ . '/../layout/footer.php'; ?>
