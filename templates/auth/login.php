<?php require __DIR__ . '/../layout/header.php'; ?>

<main class="container mobile-container py-3" style="max-width: 480px;">
  
  <!-- Banner Artístico Épico de Alta Fantasia (Tormenta & D&D) -->
  <div class="fantasy-art-banner mb-3" style="background-image: linear-gradient(180deg, rgba(10,11,16,0.15) 0%, rgba(10,11,16,0.92) 100%), url('/images/bg-tormenta-epic.jpg'); height: 170px; display: flex; flex-direction: column; justify-content: flex-end; padding: 1.1rem;">
    <div class="text-warning fw-bold text-uppercase fs-8 letter-spacing-1 text-shadow-sm">
      🏰 Mesa Virtual Tradicional • Tormenta & D&D Art
    </div>
    <div class="font-title fs-3 text-white fw-bold text-shadow">
      O Bardo
    </div>
    <div class="text-warning opacity-75 fs-7 text-shadow-sm">
      Para todos os sistemas de RPG de mesa • Imersão raiz
    </div>
  </div>

  <div class="rpg-card p-4 text-center">
    
    <h1 class="font-mythic fs-4 text-warning mb-1">ENTRAR NA TAVERNA</h1>
    <p class="text-muted fs-7 mb-4">
      Fichas, diário colaborativo e som sincronizado para sua mesa
    </p>

    <form action="/login" method="POST" class="vstack gap-3 text-start">
      <div>
        <label class="form-label text-warning">E-mail do Aventureiro</label>
        <input type="email" name="email" value="mestre@obardo.com.br" required class="form-control">
      </div>

      <div>
        <label class="form-label text-warning">Senha Secreta</label>
        <input type="password" name="password" value="Bardo2026!" required class="form-control">
      </div>

      <button type="submit" class="btn btn-solid theme-primary btn-touch w-100 py-2 fs-6 mt-2">
        Entrar na Taverna →
      </button>
    </form>

    <div class="mt-4 pt-3 border-top border-secondary text-muted fs-7">
      Ainda não empunha sua espada? <a href="/register" class="text-warning fw-bold">Criar Conta</a>
    </div>

    <!-- Atalho para protótipos -->
    <div class="mt-3">
      <a href="/docs/prototypes/index.html" class="tag-pill gold d-inline-block">
        🎨 Ver Protótipos de Alta Fidelidade
      </a>
    </div>
  </div>

</main>

<?php require __DIR__ . '/../layout/footer.php'; ?>
