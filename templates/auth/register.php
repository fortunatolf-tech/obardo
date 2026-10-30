<?php require __DIR__ . '/../layout/header.php'; ?>

<main class="mobile-container" style="max-width: 450px; margin-top: 1rem;">
  
  <!-- Banner Artístico Épico de Alta Fantasia (Tormenta & D&D) -->
  <div class="fantasy-art-banner" style="background-image: linear-gradient(180deg, rgba(10,11,16,0.15) 0%, rgba(10,11,16,0.92) 100%), url('/images/bg-tormenta-epic.jpg'); height: 170px; border-radius: 12px; border: 1.5px solid var(--border-gold); margin-bottom: 0.85rem; background-size: cover; background-position: center; display: flex; flex-direction: column; justify-content: flex-end; padding: 1.1rem; position: relative; box-shadow: 0 4px 25px rgba(0,0,0,0.75);">
    <div style="font-size: 0.65rem; color: var(--gold-bright); font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em; text-shadow: 0 2px 4px #000;">
      ⚔️ Alistamento de Aventureiros • Tormenta & D&D Art
    </div>
    <div style="font-family: var(--font-title); font-size: 1.45rem; font-weight: 900; color: #fff; text-shadow: 0 2px 8px rgba(0,0,0,0.95);">
      O Bardo
    </div>
    <div style="font-size: 0.72rem; color: var(--gold-soft); text-shadow: 0 1px 4px #000;">
      Para mestres e jogadores de todos os universos de RPG
    </div>
  </div>

  <div class="rpg-card" style="border-color: var(--border-gold); padding: 1.5rem 1.25rem; text-align: center;">
    
    <h1 style="font-size: 1.35rem; color: var(--gold-imperial); margin-bottom: 0.25rem;">CRIAR CONTA</h1>
    <p style="font-size: 0.78rem; color: var(--text-muted); margin-bottom: 1.25rem;">
      Cadastre-se para mestrar ou jogar em qualquer mesa de RPG
    </p>

    <form action="/register" method="POST" style="display: flex; flex-direction: column; gap: 0.85rem; text-align: left;">
      <div>
        <label style="font-size: 0.7rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase;">Nome ou Apelido Heróico</label>
        <input type="text" name="name" placeholder="Ex: Sir Tormenta" required style="width: 100%; padding: 0.7rem; background: var(--bg-surface); border: 1px solid var(--border-subtle); border-radius: 8px; color: #fff; font-size: 0.9rem; margin-top: 3px;">
      </div>

      <div>
        <label style="font-size: 0.7rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase;">E-mail do Jogador</label>
        <input type="email" name="email" placeholder="seuemail@exemplo.com" required style="width: 100%; padding: 0.7rem; background: var(--bg-surface); border: 1px solid var(--border-subtle); border-radius: 8px; color: #fff; font-size: 0.9rem; margin-top: 3px;">
      </div>

      <div>
        <label style="font-size: 0.7rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase;">Senha Forte (mínimo 8 dígitos)</label>
        <input type="password" name="password" required minlength="8" style="width: 100%; padding: 0.7rem; background: var(--bg-surface); border: 1px solid var(--border-subtle); border-radius: 8px; color: #fff; font-size: 0.9rem; margin-top: 3px;">
      </div>

      <div style="font-size: 0.75rem; color: var(--text-muted); display: flex; align-items: center; gap: 0.4rem; margin-top: 0.25rem;">
        <input type="checkbox" name="terms" id="terms" required checked>
        <label for="terms">Concordo com os Termos de Uso e Política de Privacidade (LGPD).</label>
      </div>

      <button type="submit" class="btn-touch btn-touch-primary" style="width: 100%; padding: 0.8rem; font-size: 0.95rem; margin-top: 0.5rem;">
        Criar Minha Conta →
      </button>
    </form>

    <div style="margin-top: 1.25rem; padding-top: 1rem; border-top: 1px solid var(--border-subtle); font-size: 0.8rem; color: var(--text-muted);">
      Já possui cadastro? <a href="/login" style="color: var(--gold-imperial); font-weight: 700; text-decoration: none;">Acessar Conta</a>
    </div>
  </div>

</main>

<?php require __DIR__ . '/../layout/footer.php'; ?>
