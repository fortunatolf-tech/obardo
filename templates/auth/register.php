<?php require __DIR__ . '/../layout/header.php'; ?>

<main class="container mobile-container py-3" style="max-width: 480px;">
  
  <!-- Banner Artístico Épico de Alta Fantasia (Tormenta & D&D) -->
  <div class="fantasy-art-banner mb-3" style="background-image: linear-gradient(180deg, rgba(10,11,16,0.15) 0%, rgba(10,11,16,0.92) 100%), url('/images/bg-tormenta-epic.jpg'); height: 170px; display: flex; flex-direction: column; justify-content: flex-end; padding: 1.1rem;">
    <div class="text-warning fw-bold text-uppercase fs-8 letter-spacing-1 text-shadow-sm">
      ⚔️ Alistamento de Aventureiros • Tormenta & D&D Art
    </div>
    <div class="font-title fs-3 text-white fw-bold text-shadow">
      O Bardo
    </div>
    <div class="text-warning opacity-75 fs-7 text-shadow-sm">
      Para mestres e jogadores de todos os universos de RPG
    </div>
  </div>

  <div class="rpg-card p-4 text-center">
    
    <h1 class="font-mythic fs-4 text-warning mb-1">CRIAR CONTA</h1>
    <p class="text-muted fs-7 mb-4">
      Cadastre-se para mestrar ou jogar em qualquer mesa de RPG
    </p>

    <form action="/register" method="POST" class="vstack gap-3 text-start">
      <div>
        <label class="form-label text-warning">Nome ou Apelido Heróico</label>
        <input type="text" name="name" placeholder="Ex: Sir Tormenta" required class="form-control">
      </div>

      <div>
        <label class="form-label text-warning">E-mail do Jogador</label>
        <input type="email" name="email" placeholder="seuemail@exemplo.com" required class="form-control">
      </div>

      <div>
        <label class="form-label text-warning">Senha Forte (mínimo 8 dígitos)</label>
        <input type="password" name="password" required minlength="8" class="form-control">
      </div>

      <div class="form-check d-flex align-items-center gap-2 mt-1">
        <input class="form-check-input" type="checkbox" name="terms" id="terms" required checked>
        <label class="form-check-label text-muted fs-8 cursor-pointer" for="terms">
          Concordo com os Termos de Uso e Política de Privacidade (LGPD).
        </label>
      </div>

      <button type="submit" class="btn btn-solid theme-primary btn-touch w-100 py-2 fs-6 mt-2">
        Criar Minha Conta →
      </button>
    </form>

    <div class="mt-4 pt-3 border-top border-secondary text-muted fs-7">
      Já possui cadastro? <a href="/login" class="text-warning fw-bold">Acessar Conta</a>
    </div>
  </div>

</main>

<?php require __DIR__ . '/../layout/footer.php'; ?>
