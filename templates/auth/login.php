<?php require __DIR__ . '/../layout/header.php'; ?>

<main class="mobile-container" style="max-width: 450px; margin-top: 1rem;">
  
  <!-- Banner Artístico Épico de Alta Fantasia (Tormenta & D&D) -->
  <div class="fantasy-art-banner" style="background-image: linear-gradient(180deg, rgba(10,11,16,0.15) 0%, rgba(10,11,16,0.92) 100%), url('/images/bg-tormenta-epic.jpg'); height: 170px; border-radius: 12px; border: 1.5px solid var(--border-gold); margin-bottom: 0.85rem; background-size: cover; background-position: center; display: flex; flex-direction: column; justify-content: flex-end; padding: 1.1rem; position: relative; box-shadow: 0 4px 25px rgba(0,0,0,0.75);">
    <div style="font-size: 0.65rem; color: var(--gold-bright); font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em; text-shadow: 0 2px 4px #000;">
      🏰 Mesa Virtual Tradicional • Tormenta & D&D Art
    </div>
    <div style="font-family: var(--font-title); font-size: 1.45rem; font-weight: 900; color: #fff; text-shadow: 0 2px 8px rgba(0,0,0,0.95);">
      O Bardo
    </div>
    <div style="font-size: 0.72rem; color: var(--gold-soft); text-shadow: 0 1px 4px #000;">
      Para todos os sistemas de RPG de mesa • Imersão raiz
    </div>
  </div>

  <div class="rpg-card" style="border-color: var(--border-gold); padding: 1.5rem 1.25rem; text-align: center;">
    
    <h1 style="font-size: 1.35rem; color: var(--gold-imperial); margin-bottom: 0.25rem;">ENTRAR NA TAVERNA</h1>
    <p style="font-size: 0.78rem; color: var(--text-muted); margin-bottom: 1.25rem;">
      Fichas, diário colaborativo e som sincronizado para sua mesa
    </p>

    <form action="/login" method="POST" style="display: flex; flex-direction: column; gap: 0.85rem; text-align: left;">
      <div>
        <label style="font-size: 0.7rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase;">E-mail do Aventureiro</label>
        <input type="email" name="email" value="mestre@obardo.com.br" required style="width: 100%; padding: 0.7rem; background: var(--bg-surface); border: 1px solid var(--border-subtle); border-radius: 8px; color: #fff; font-size: 0.9rem; margin-top: 3px;">
      </div>

      <div>
        <label style="font-size: 0.7rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase;">Senha Secreta</label>
        <input type="password" name="password" value="Bardo2026!" required style="width: 100%; padding: 0.7rem; background: var(--bg-surface); border: 1px solid var(--border-subtle); border-radius: 8px; color: #fff; font-size: 0.9rem; margin-top: 3px;">
      </div>

      <button type="submit" class="btn-touch btn-touch-primary" style="width: 100%; padding: 0.8rem; font-size: 0.95rem; margin-top: 0.5rem;">
        Entrar na Taverna →
      </button>
    </form>

    <div style="margin-top: 1.25rem; padding-top: 1rem; border-top: 1px solid var(--border-subtle); font-size: 0.8rem; color: var(--text-muted);">
      Ainda não empunha sua espada? <a href="/register" style="color: var(--gold-imperial); font-weight: 700; text-decoration: none;">Criar Conta</a>
    </div>

    <!-- Atalho para protótipos -->
    <div style="margin-top: 1rem;">
      <a href="/docs/prototypes/index.html" class="tag-pill gold" style="text-decoration: none; display: inline-block;">
        🎨 Ver Protótipos de Alta Fidelidade
      </a>
    </div>
  </div>

</main>

<?php require __DIR__ . '/../layout/footer.php'; ?>
