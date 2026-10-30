<?php require __DIR__ . '/../layout/header.php'; ?>

<main class="mobile-container" style="max-width: 440px; margin-top: 1.5rem;">
  
  <div class="rpg-card" style="border-color: var(--border-gold); padding: 1.75rem 1.25rem; text-align: center;">
    <div style="width: 54px; height: 54px; margin: 0 auto 0.75rem auto; border-radius: 12px; background: linear-gradient(135deg, #24293e 0%, #131622 100%); border: 2px solid var(--border-gold); display: flex; align-items: center; justify-content: center; font-size: 1.8rem; box-shadow: 0 0 15px var(--border-gold-glow);">
      📜
    </div>
    
    <h1 style="font-size: 1.6rem; color: var(--gold-imperial); margin-bottom: 0.25rem;">NOVO AVENTUREIRO</h1>
    <p style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 1.5rem;">
      Cadastre-se para mestrar ou jogar em qualquer mesa
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
