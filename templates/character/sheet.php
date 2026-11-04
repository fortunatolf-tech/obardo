<?php require __DIR__ . '/../layout/header.php'; ?>

<main class="container mobile-container py-3">
  
  <?php if (!$character): ?>
    <div class="rpg-card text-center p-4">
      <h2 class="font-mythic fs-4 text-warning mb-2">Nenhum Personagem Criado</h2>
      <p class="text-muted fs-6 mb-4">Você ainda não possui uma ficha nesta campanha.</p>
      <button class="btn btn-solid theme-primary btn-touch" data-bs-toggle="modal" data-bs-target="#modal-new-character">
        + Criar Ficha Tormenta20 / D&D
      </button>
    </div>
  <?php else: ?>

    <!-- Hero Card do Personagem -->
    <div class="rpg-card mb-3">
      <div class="hero-compact hstack gap-3 align-items-center mb-3 flex-wrap flex-sm-nowrap">
        <div class="hero-portrait-frame flex-shrink-0">
          <img src="/images/hero-portrait.jpg" alt="<?= $e($character->name) ?>" class="img-fluid rounded-3" style="width: 100%; height: 100%; object-fit: cover;">
          <div class="level-badge"><?= $e(explode(' ', $character->classAndLevel)[1] ?? 'NV 1') ?></div>
        </div>
        <div class="hero-meta vstack gap-1 flex-grow-1">
          <div class="hero-name font-mythic fs-4 text-warning"><?= $e($character->name) ?></div>
          <div class="hero-class-race text-light fs-6 opacity-75"><?= $e($character->classAndLevel) ?> • <?= $e($character->raceOrigin) ?></div>
          <div class="hero-tags hstack gap-2 flex-wrap mt-1">
            <span class="tag-pill gold"><?= $e($campaign->title) ?></span>
            <span class="tag-pill"><?= $e($campaign->ruleSystem) ?></span>
            <button type="button" class="btn btn-sm btn-subtle theme-warning py-1 px-2 fs-7" data-bs-toggle="modal" data-bs-target="#modal-new-character">+ Nova Ficha</button>
          </div>
        </div>
      </div>

      <!-- Medidores Vitais: PV (Vida), PM (Mana) & Defesa -->
      <div class="vitals-grid">
        <!-- Barra de PV (Pontos de Vida - Carmesim) -->
        <div class="vital-box" onclick="openVitalsModal()">
          <div class="vital-header d-flex justify-content-between align-items-center">
            <span class="vital-title text-danger">❤️ PV (VIDA)</span>
            <span class="vital-numbers"><span id="hp-current-val"><?= (int)$character->hpCurrent ?></span>/<span id="hp-max-val"><?= (int)$character->hpMax ?></span></span>
          </div>
          <div class="bar-track">
            <?php $hpPct = $character->hpMax > 0 ? min(100, max(0, round(($character->hpCurrent / $character->hpMax) * 100))) : 0; ?>
            <div id="hp-fill-bar" class="bar-fill ruby" style="width: <?= $hpPct ?>%;"></div>
          </div>
          <div class="text-muted fs-8 text-end mt-1">
            Toque p/ Dano/Cura
          </div>
        </div>

        <!-- Barra de PM (Pontos de Mana - Cyan Arcana) -->
        <div class="vital-box" onclick="bardoModifyPM(<?= $character->id ?>, -1)">
          <div class="vital-header d-flex justify-content-between align-items-center">
            <span class="vital-title text-info">💎 PM (MANA)</span>
            <span class="vital-numbers"><span id="pm-current-val"><?= (int)$character->mpCurrent ?></span>/<span id="pm-max-val"><?= (int)$character->mpMax ?></span></span>
          </div>
          <div class="bar-track">
            <?php $mpPct = $character->mpMax > 0 ? min(100, max(0, round(($character->mpCurrent / $character->mpMax) * 100))) : 0; ?>
            <div id="pm-fill-bar" class="bar-fill mana" style="width: <?= $mpPct ?>%;"></div>
          </div>
          <div class="text-muted fs-8 text-end mt-1">
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

    <div class="row row-cols-3 row-cols-md-6 g-2 mb-3 attributes-row">
      <div class="col">
        <div class="attr-card" onclick="bardoRoll(<?= $campaign->id ?>, <?= $character->id ?>, '1d20<?= $forMod >= 0 ? '+' . $forMod : $forMod ?>', 'Teste de Força')">
          <span class="attr-tag">FOR</span>
          <span class="attr-mod"><?= $forMod >= 0 ? '+' . $forMod : $forMod ?></span>
          <span class="attr-raw"><?= $forVal ?></span>
        </div>
      </div>
      <div class="col">
        <div class="attr-card" onclick="bardoRoll(<?= $campaign->id ?>, <?= $character->id ?>, '1d20<?= $desMod >= 0 ? '+' . $desMod : $desMod ?>', 'Teste de Destreza')">
          <span class="attr-tag">DES</span>
          <span class="attr-mod"><?= $desMod >= 0 ? '+' . $desMod : $desMod ?></span>
          <span class="attr-raw"><?= $desVal ?></span>
        </div>
      </div>
      <div class="col">
        <div class="attr-card" onclick="bardoRoll(<?= $campaign->id ?>, <?= $character->id ?>, '1d20<?= $conMod >= 0 ? '+' . $conMod : $conMod ?>', 'Teste de Constituição')">
          <span class="attr-tag">CON</span>
          <span class="attr-mod"><?= $conMod >= 0 ? '+' . $conMod : $conMod ?></span>
          <span class="attr-raw"><?= $conVal ?></span>
        </div>
      </div>
      <div class="col">
        <div class="attr-card" onclick="bardoRoll(<?= $campaign->id ?>, <?= $character->id ?>, '1d20<?= $intMod >= 0 ? '+' . $intMod : $intMod ?>', 'Teste de Inteligência')">
          <span class="attr-tag">INT</span>
          <span class="attr-mod"><?= $intMod >= 0 ? '+' . $intMod : $intMod ?></span>
          <span class="attr-raw"><?= $intVal ?></span>
        </div>
      </div>
      <div class="col">
        <div class="attr-card" onclick="bardoRoll(<?= $campaign->id ?>, <?= $character->id ?>, '1d20<?= $sabMod >= 0 ? '+' . $sabMod : $sabMod ?>', 'Teste de Sabedoria')">
          <span class="attr-tag">SAB</span>
          <span class="attr-mod"><?= $sabMod >= 0 ? '+' . $sabMod : $sabMod ?></span>
          <span class="attr-raw"><?= $sabVal ?></span>
        </div>
      </div>
      <div class="col">
        <div class="attr-card" onclick="bardoRoll(<?= $campaign->id ?>, <?= $character->id ?>, '1d20<?= $carMod >= 0 ? '+' . $carMod : $carMod ?>', 'Teste de Carisma')">
          <span class="attr-tag">CAR</span>
          <span class="attr-mod"><?= $carMod >= 0 ? '+' . $carMod : $carMod ?></span>
          <span class="attr-raw"><?= $carVal ?></span>
        </div>
      </div>
    </div>

    <!-- ABAS DINÂMICAS -->

    <!-- ABA 1: COMBATE & ATAQUES -->
    <div id="section-combate" class="tab-pane">
      <div class="rpg-card">
        <div class="card-title-bar d-flex justify-content-between align-items-center">
          <span class="card-title-text">⚔️ Ataques & Ações de Combate</span>
          <span class="text-muted fs-7">Bônus Base: +<?= max(1, $forMod + 2) ?></span>
        </div>

        <div class="vstack gap-3">
          <div class="action-card ruby">
            <div class="action-header d-flex justify-content-between align-items-center">
              <span class="action-title">Espada Longa de Aço-Rubi</span>
              <span class="tag-pill gold">Crítico 19-20/x2</span>
            </div>
            <div class="action-desc">
              1 mão (dano 1d8+<?= $forMod ?>) ou 2 mãos (dano 1d10+<?= $forMod ?>) • Cortante
            </div>
            <div class="action-buttons hstack gap-2 flex-wrap">
              <button class="btn btn-solid theme-primary btn-touch" onclick="bardoRoll(<?= $campaign->id ?>, <?= $character->id ?>, '1d20+<?= max(1, $forMod + 4) ?>', 'Ataque Espada Longa')">
                ⚔️ Atacar (+<?= max(1, $forMod + 4) ?>)
              </button>
              <button class="btn btn-subtle theme-secondary btn-touch" onclick="bardoRoll(<?= $campaign->id ?>, <?= $character->id ?>, '1d8+<?= $forMod ?>', 'Dano Espada (1 mão)')">
                1d8+<?= $forMod ?>
              </button>
              <button class="btn btn-subtle theme-secondary btn-touch" onclick="bardoRoll(<?= $campaign->id ?>, <?= $character->id ?>, '1d10+<?= $forMod ?>', 'Dano Espada (2 mãos)')">
                1d10+<?= $forMod ?>
              </button>
            </div>
          </div>

          <div class="action-card mana">
            <div class="action-header d-flex justify-content-between align-items-center">
              <span class="action-title">Golpe Especial Flamejante</span>
              <span class="action-cost">Custo: 2 PM</span>
            </div>
            <div class="action-desc">
              Envolve a arma em chamas arcanas. Concede +2d6 de dano de fogo no ataque.
            </div>
            <button class="btn btn-solid theme-info btn-touch w-100" onclick="bardoModifyPM(<?= $character->id ?>, -2); bardoRoll(<?= $campaign->id ?>, <?= $character->id ?>, '1d20+<?= max(1, $forMod + 4) ?>', 'Golpe Especial (Ataque)'); bardoRoll(<?= $campaign->id ?>, <?= $character->id ?>, '2d6', 'Dano Extra de Fogo');">
              🔥 Desferir Golpe com Chamas (2 PM)
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- ABA 2: MAGIAS & GRIMÓRIO (COM CUSTO EM PM) -->
    <div id="section-magias" class="tab-pane" style="display: none;">
      <div class="rpg-card">
        <div class="card-title-bar d-flex justify-content-between align-items-center">
          <span class="card-title-text">🔮 Grimório & Habilidades Arcanas</span>
          <span class="text-info fw-bold fs-7">Mana Atual: <?= (int)$character->mpCurrent ?> PM</span>
        </div>

        <?php if (empty($character->spellsData)): ?>
          <p class="text-muted text-center py-3 fs-7">Nenhuma magia registrada no momento.</p>
        <?php else: ?>
          <div class="vstack gap-3">
            <?php foreach ($character->spellsData as $spell): ?>
              <div class="action-card mana">
                <div class="action-header d-flex justify-content-between align-items-center">
                  <span class="action-title"><?= $e($spell['name']) ?></span>
                  <span class="action-cost"><?= (int)($spell['cost_pm'] ?? 1) ?> PM • <?= $e($spell['action'] ?? 'Padrão') ?></span>
                </div>
                <div class="action-desc"><?= $e($spell['desc'] ?? '') ?></div>
                <button class="btn btn-solid theme-info btn-touch w-100" onclick="bardoModifyPM(<?= $character->id ?>, -<?= (int)($spell['cost_pm'] ?? 1) ?>); bardoPlaySFX('spell'); bardoToast('🔮 Magia <?= $e($spell['name']) ?> conjurada!');">
                  Conjurar <?= $e($spell['name']) ?> (-<?= (int)($spell['cost_pm'] ?? 1) ?> PM)
                </button>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- ABA 3: PERÍCIAS (TORMENTA20 & D&D OGL) -->
    <div id="section-pericias" class="tab-pane" style="display: none;">
      <div class="rpg-card py-2">
        <div class="d-flex justify-content-between align-items-center px-3 py-2">
          <span class="card-title-text">📜 Perícias de Campanha</span>
          <span class="text-warning fs-8 fw-bold">[T] = Treinada</span>
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

        <div class="vstack gap-1">
          <?php foreach ($defaultSkills as $sk): ?>
            <div class="skill-row" onclick="bardoRoll(<?= $campaign->id ?>, <?= $character->id ?>, '1d20<?= $sk['bonus'] >= 0 ? '+' . $sk['bonus'] : $sk['bonus'] ?>', 'Perícia: <?= $e($sk['name']) ?>')">
              <div class="skill-left d-flex align-items-center gap-2">
                <div class="skill-train-badge <?= $sk['trained'] ? 'trained' : '' ?>">
                  <?= $sk['trained'] ? 'T' : '' ?>
                </div>
                <div>
                  <span class="skill-name"><?= $e($sk['name']) ?></span>
                  <span class="skill-attr-ref text-muted">(<?= $e($sk['attr']) ?>)</span>
                </div>
              </div>
              <span class="skill-bonus"><?= $sk['bonus'] >= 0 ? '+' . $sk['bonus'] : $sk['bonus'] ?> 🎲</span>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <!-- ABA 4: MOCHILA & CARGA -->
    <div id="section-mochila" class="tab-pane" style="display: none;">
      <div class="rpg-card">
        <div class="card-title-bar d-flex justify-content-between align-items-center">
          <span class="card-title-text">🎒 Mochila & Carga</span>
          <span class="text-success fw-bold fs-7">Capacidade: <?= $forVal ?> Espaços</span>
        </div>

        <div class="vstack gap-2 fs-7">
          <?php foreach ($character->inventoryData as $item): ?>
            <div class="p-2 rounded bg-surface d-flex justify-content-between align-items-center">
              <span><?= $item['equipped'] ? '🛡️' : '🎒' ?> <?= $e($item['name']) ?></span>
              <span class="text-warning fw-bold"><?= (int)($item['slots'] ?? 1) ?> Espaço(s)</span>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <!-- FEED DE ROLAGENS EM TEMPO REAL -->
    <div class="rpg-card mt-3">
      <div class="card-title-bar d-flex justify-content-between align-items-center">
        <span class="card-title-text">🎲 Histórico de Rolagens da Sessão</span>
        <button class="btn btn-sm btn-subtle theme-secondary py-1 px-2 fs-8" onclick="document.getElementById('session-roll-feed').innerHTML=''">Limpar</button>
      </div>
      <div id="session-roll-feed" class="vstack gap-2"></div>
    </div>

    <!-- BOTAO FLUTUANTE (FAB) PARA ROLAGEM RAPIDA DE DADOS -->
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
        <form onsubmit="event.preventDefault(); const f=document.getElementById('custom-dice-formula').value; if(f) bardoRoll(<?= $campaign->id ?>, <?= $character->id ?>, f, 'Rolagem Livre');" class="d-flex gap-2 mb-3">
          <input type="text" id="custom-dice-formula" class="form-control font-code fs-7" placeholder="Fórmula livre (ex: 3d6+2, 2d8, 1d100)">
          <button type="submit" class="btn btn-solid theme-primary btn-touch px-3">Rolar</button>
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

        <button class="btn btn-subtle theme-secondary btn-touch w-100 mt-2" onclick="closeDiceSheet()">Fechar</button>
      </div>
    </div>

    <!-- BOTTOM SHEET MODAL DE CONTROLE DE PV (DANO & CURA) -->
    <div id="vitals-modal-sheet" class="bottom-sheet" onclick="if(event.target === this) closeVitalsModal();">
      <div class="bottom-sheet-content">
        <div class="sheet-handle"></div>
        <h3 class="font-mythic fs-5 text-center text-danger mb-1">Ajuste Rápido de Pontos de Vida (PV)</h3>
        <p class="text-muted text-center fs-7 mb-3">Aplique dano ou cura com um toque:</p>

        <div class="row row-cols-3 g-2 mb-3">
          <div class="col"><button class="btn btn-solid theme-danger btn-touch w-100" onclick="bardoModifyHP(<?= $character->id ?>, -1)">-1 Dano</button></div>
          <div class="col"><button class="btn btn-solid theme-danger btn-touch w-100" onclick="bardoModifyHP(<?= $character->id ?>, -5)">-5 Dano</button></div>
          <div class="col"><button class="btn btn-solid theme-danger btn-touch w-100" onclick="bardoModifyHP(<?= $character->id ?>, -10)">-10 Dano</button></div>
          <div class="col"><button class="btn btn-solid theme-primary btn-touch w-100" onclick="bardoModifyHP(<?= $character->id ?>, 1)">+1 Cura</button></div>
          <div class="col"><button class="btn btn-solid theme-primary btn-touch w-100" onclick="bardoModifyHP(<?= $character->id ?>, 5)">+5 Cura</button></div>
          <div class="col"><button class="btn btn-solid theme-primary btn-touch w-100" onclick="bardoModifyHP(<?= $character->id ?>, 10)">+10 Cura</button></div>
        </div>

        <button class="btn btn-subtle theme-secondary btn-touch w-100" onclick="closeVitalsModal()">Concluir</button>
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

  <!-- MODAL DE CRIAÇÃO DE NOVA FICHA (BOOTSTRAP 6 MODAL COMPONENT) -->
  <div class="modal fade" id="modal-new-character" tabindex="-1" aria-labelledby="modalNewCharLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content rpg-card p-0">
        <div class="modal-header d-flex justify-content-between align-items-center">
          <div>
            <h3 class="modal-title font-mythic fs-5 text-warning mb-0" id="modalNewCharLabel">⚔️ Forja de Heróis</h3>
            <small class="text-muted fs-8">Criação de Ficha Tormenta20, D&D e Sistemas d20</small>
          </div>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
        </div>

        <div class="modal-body p-3 p-md-4">
          <form onsubmit="submitNewCharacter(event, <?= $campaign->id ?>)">
            <div class="mb-3">
              <label class="form-label text-warning">NOME DO PERSONAGEM *</label>
              <input type="text" name="name" required placeholder="Ex: Sir Lothar, Aylin..." class="form-control">
            </div>

            <div class="row g-2 mb-3">
              <div class="col-12 col-sm-6">
                <label class="form-label text-warning">RAÇA / ORIGEM</label>
                <input type="text" name="race_origin" placeholder="Humano, Elfo, Qareen..." value="Humano" class="form-control">
              </div>
              <div class="col-12 col-sm-6">
                <label class="form-label text-warning">CLASSE & NÍVEL</label>
                <input type="text" name="class_and_level" placeholder="Guerreiro 1, Arcanista 1..." value="Guerreiro 1" class="form-control">
              </div>
            </div>

            <div class="row g-2 mb-3 text-center">
              <div class="col-4">
                <div class="p-2 rounded border border-danger bg-surface">
                  <label class="form-label text-danger mb-1 fs-8">PV MÁX</label>
                  <input type="number" name="hp_max" value="20" min="1" max="999" class="form-control text-center font-code fw-bold fs-5 p-1 border-0 bg-transparent">
                </div>
              </div>
              <div class="col-4">
                <div class="p-2 rounded border border-info bg-surface">
                  <label class="form-label text-info mb-1 fs-8">PM MÁX</label>
                  <input type="number" name="mp_max" value="6" min="0" max="999" class="form-control text-center font-code fw-bold fs-5 p-1 border-0 bg-transparent">
                </div>
              </div>
              <div class="col-4">
                <div class="p-2 rounded border border-warning bg-surface">
                  <label class="form-label text-warning mb-1 fs-8">DEFESA</label>
                  <input type="number" name="armor_class" value="15" min="1" max="99" class="form-control text-center font-code fw-bold fs-5 p-1 border-0 bg-transparent">
                </div>
              </div>
            </div>

            <div class="mb-4">
              <label class="form-label text-warning mb-2">ATRIBUTOS BASE (D20 / T20 / 5E)</label>
              <div class="row row-cols-3 row-cols-sm-6 g-2">
                <?php foreach (['for' => 'FORÇA', 'des' => 'DESTREZA', 'con' => 'CONSTITUIÇÃO', 'int' => 'INTELIGÊNCIA', 'sab' => 'SABEDORIA', 'car' => 'CARISMA'] as $k => $label): ?>
                  <div class="col">
                    <div class="p-2 rounded bg-surface border border-secondary text-center">
                      <span class="d-block text-warning font-ui fs-8 fw-bold mb-1"><?= strtoupper(substr($k, 0, 3)) ?></span>
                      <input type="number" name="attr_<?= $k ?>" value="10" min="1" max="30" oninput="updateAttrPreview(this, 'mod_<?= $k ?>')" class="form-control text-center font-code fw-bold fs-5 p-0 border-0 bg-transparent">
                      <span id="mod_<?= $k ?>" class="d-block text-info fw-bold fs-8 mt-1">+0</span>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>

            <div class="hstack gap-2 justify-content-end">
              <button type="button" class="btn btn-subtle theme-secondary btn-touch" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-solid theme-primary btn-touch px-4">⚔️ Forjar Ficha</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script>
    function updateAttrPreview(input, modSpanId) {
      const val = parseInt(input.value) || 10;
      const mod = Math.floor((val - 10) / 2);
      const span = document.getElementById(modSpanId);
      if (span) {
        span.textContent = (mod >= 0 ? '+' : '') + mod;
      }
    }

    async function submitNewCharacter(e, campaignId) {
      e.preventDefault();
      const form = e.target;
      const btn = form.querySelector('button[type="submit"]');
      btn.disabled = true;
      btn.innerText = 'Forjando Herói...';

      const forVal = parseInt(form.attr_for.value) || 10;
      const desVal = parseInt(form.attr_des.value) || 10;
      const conVal = parseInt(form.attr_con.value) || 10;
      const intVal = parseInt(form.attr_int.value) || 10;
      const sabVal = parseInt(form.attr_sab.value) || 10;
      const carVal = parseInt(form.attr_car.value) || 10;

      const payload = {
        name: form.name.value.trim(),
        race_origin: form.race_origin.value.trim(),
        class_and_level: form.class_and_level.value.trim(),
        hp_max: parseInt(form.hp_max.value) || 20,
        mp_max: parseInt(form.mp_max.value) || 6,
        armor_class: parseInt(form.armor_class.value) || 15,
        attributes: {
          for: forVal,
          for_mod: Math.floor((forVal - 10) / 2),
          des: desVal,
          des_mod: Math.floor((desVal - 10) / 2),
          con: conVal,
          con_mod: Math.floor((conVal - 10) / 2),
          int: intVal,
          int_mod: Math.floor((intVal - 10) / 2),
          sab: sabVal,
          sab_mod: Math.floor((sabVal - 10) / 2),
          car: carVal,
          car_mod: Math.floor((carVal - 10) / 2)
        },
        skills: [
          { name: "Luta", mod: Math.floor((forVal - 10) / 2) + 2 },
          { name: "Percepção", mod: Math.floor((sabVal - 10) / 2) + 2 },
          { name: "Iniciativa", mod: Math.floor((desVal - 10) / 2) + 2 },
          { name: "Fortitude", mod: Math.floor((conVal - 10) / 2) + 2 },
          { name: "Reflexos", mod: Math.floor((desVal - 10) / 2) + 2 },
          { name: "Vontade", mod: Math.floor((sabVal - 10) / 2) + 2 }
        ],
        inventory: [
          { name: "Espada Longa", qty: 1, damage: "1d8+FOR" },
          { name: "Armadura de Couro Batido", qty: 1, def: "+3" },
          { name: "Kit de Aventureiro", qty: 1, weight: "12kg" },
          { name: "Tochas", qty: 5 },
          { name: "Rações de Viagem (dias)", qty: 7 }
        ],
        spells: []
      };

      try {
        const res = await fetch(`/api/v1/campaigns/${campaignId}/characters`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        });
        const data = await res.json();
        if (res.ok && data.status === 'success') {
          bardoToast('Ficha forjada com sucesso! Que os Deuses guiem sua lâmina!');
          setTimeout(() => window.location.reload(), 800);
        } else {
          bardoToast(data.message || 'Falha ao forjar ficha');
          btn.disabled = false;
          btn.innerText = '⚔️ Forjar Ficha';
        }
      } catch (err) {
        bardoToast('Erro de conexão ao forjar ficha.');
        btn.disabled = false;
        btn.innerText = '⚔️ Forjar Ficha';
      }
    }
  </script>
</main>

<?php require __DIR__ . '/../layout/footer.php'; ?>
