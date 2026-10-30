<?php require __DIR__ . '/../layout/header.php'; ?>

<main class="mobile-container">
  
  <!-- Saudação ao Usuário -->
  <div class="rpg-card" style="border-color: var(--border-gold); padding: 1.25rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem;">
      <div>
        <h1 style="font-size: 1.35rem; color: var(--gold-imperial);">Olá, <?= $e($user->name) ?>!</h1>
        <p style="font-size: 0.8rem; color: var(--text-secondary);">Bem-vindo ao salão de campanhas d'O Bardo.</p>
      </div>
      <div style="display: flex; gap: 0.5rem;">
        <button class="btn-touch btn-touch-primary" onclick="document.getElementById('modal-new-campaign').style.display='flex'">
          + Nova Campanha
        </button>
      </div>
    </div>
  </div>

  <!-- Ingressar por Código -->
  <div class="rpg-card" style="padding: 1rem;">
    <form action="/join" method="POST" style="display: flex; gap: 0.5rem; align-items: center;">
      <input type="text" name="code" placeholder="Código de Convite (ex: BARDO-T20X)" required style="flex-grow: 1; padding: 0.65rem; background: var(--bg-surface); border: 1px solid var(--border-subtle); border-radius: 8px; color: #fff; font-size: 0.85rem; text-transform: uppercase; font-family: var(--font-code);">
      <button type="submit" class="btn-touch btn-touch-dark" style="border-color: var(--gold-imperial); color: var(--gold-imperial);">
        Ingressar
      </button>
    </form>
  </div>

  <!-- Lista de Campanhas -->
  <h2 style="font-size: 1.1rem; color: var(--gold-imperial); margin: 1.25rem 0 0.5rem 0;">Suas Campanhas Ativas</h2>

  <?php if (empty($campaigns)): ?>
    <div class="rpg-card" style="text-align: center; padding: 2rem;">
      <div style="font-size: 2rem; margin-bottom: 0.5rem;">🏕️</div>
      <p style="color: var(--text-muted); font-size: 0.9rem;">Você ainda não participa de nenhuma campanha.</p>
      <button class="btn-touch btn-touch-primary" style="margin-top: 1rem;" onclick="document.getElementById('modal-new-campaign').style.display='flex'">
        Criar Minha Primeira Campanha
      </button>
    </div>
  <?php else: ?>
    <div style="display: flex; flex-direction: column; gap: 0.75rem;">
      <?php foreach ($campaigns as $camp): ?>
        <div class="rpg-card" style="border-left: 4px solid <?= $camp['user_role'] === 'gm' ? 'var(--gold-imperial)' : 'var(--emerald-life)' ?>;">
          <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
            <div>
              <h3 style="font-size: 1.1rem; color: var(--gold-imperial);"><?= $e($camp['title']) ?></h3>
              <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 2px;">
                Sistema: <strong><?= $e($camp['rule_system']) ?></strong> • Código: <span class="tag-pill gold" style="font-family: var(--font-code);"><?= $e($camp['code']) ?></span>
              </div>
            </div>
            <span class="tag-pill <?= $camp['user_role'] === 'gm' ? 'gold' : '' ?>">
              <?= $camp['user_role'] === 'gm' ? '👑 Você é o Mestre' : '🗡️ Jogador' ?>
            </span>
          </div>

          <?php if (!empty($camp['description'])): ?>
            <p style="font-size: 0.8rem; color: var(--text-secondary); line-height: 1.4; margin-bottom: 0.75rem;">
              <?= $e($camp['description']) ?>
            </p>
          <?php endif; ?>

          <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
            <a href="/campaigns/<?= $camp['id'] ?>/sheet" class="btn-touch btn-touch-ruby" style="flex: 1; font-size: 0.75rem;">
              🛡️ Abrir Ficha
            </a>
            <a href="/campaigns/<?= $camp['id'] ?>/taverna" class="btn-touch btn-touch-dark" style="flex: 1; font-size: 0.75rem;">
              🍻 Entrar na Taverna
            </a>
            <?php if ($camp['user_role'] === 'gm'): ?>
              <a href="/campaigns/<?= $camp['id'] ?>/gm" class="btn-touch btn-touch-primary" style="flex: 1; font-size: 0.75rem;">
                👑 Painel do Mestre
              </a>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

</main>

<!-- Modal Nova Campanha -->
<div id="modal-new-campaign" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.8); z-index: 100; align-items: center; justify-content: center; padding: 1rem;">
  <div class="rpg-card" style="max-width: 480px; width: 100%; border-color: var(--border-gold); padding: 1.5rem;">
    <h3 style="font-size: 1.2rem; color: var(--gold-imperial); margin-bottom: 1rem;">Criar Nova Campanha de RPG</h3>
    <form action="/campaigns" method="POST" style="display: flex; flex-direction: column; gap: 0.75rem;">
      <div>
        <label style="font-size: 0.7rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase;">Título da Campanha</label>
        <input type="text" name="title" placeholder="Ex: O Templo dos Deuses Esquecidos" required style="width: 100%; padding: 0.65rem; background: var(--bg-surface); border: 1px solid var(--border-subtle); border-radius: 8px; color: #fff; font-size: 0.85rem; margin-top: 3px;">
      </div>

      <div>
        <label style="font-size: 0.7rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase;">Sistema de Regras</label>
        <select name="rule_system" style="width: 100%; padding: 0.65rem; background: var(--bg-surface); border: 1px solid var(--border-subtle); border-radius: 8px; color: #fff; font-size: 0.85rem; margin-top: 3px;">
          <option value="TORMENTA20">Tormenta20 (T20 D20 System)</option>
          <option value="DND5E">D&D 5ª Edição / D20 SRD</option>
          <option value="OSR_BX">Old-School Essentials (OSR / B/X)</option>
          <option value="CUSTOM">Livre / Personalizado</option>
        </select>
      </div>

      <div>
        <label style="font-size: 0.7rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase;">Sinopse / Descrição da Aventura</label>
        <textarea name="description" rows="3" placeholder="Breve resumo da premissa para seus jogadores..." style="width: 100%; padding: 0.65rem; background: var(--bg-surface); border: 1px solid var(--border-subtle); border-radius: 8px; color: #fff; font-size: 0.85rem; margin-top: 3px;"></textarea>
      </div>

      <div style="display: flex; gap: 0.5rem; margin-top: 0.5rem;">
        <button type="submit" class="btn-touch btn-touch-primary" style="flex: 1;">Criar Campanha →</button>
        <button type="button" class="btn-touch btn-touch-dark" onclick="document.getElementById('modal-new-campaign').style.display='none'">Cancelar</button>
      </div>
    </form>
  </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
