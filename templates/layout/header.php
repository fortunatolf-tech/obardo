<!DOCTYPE html>
<html lang="pt-BR" data-bs-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <meta name="theme-color" content="#282a36">
  <title><?= $e($title ?? 'O Bardo • RPG de Mesa Tradicional') ?></title>
  
  <!-- Fontes Disruptivas de Alta Fantasia (Tormenta & D&D) -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Almendra:ital,wght@0,400;0,700;1,400&family=Cinzel+Decorative:wght@700;900&family=Grenze+Gotisch:wght@600;700;800;900&family=MedievalSharp&family=Uncial+Antiqua&family=JetBrains+Mono:wght@500;700;800&display=swap" rel="stylesheet">

  <!-- Bootstrap 6 Framework (CDN com fallback local) -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@6.0.0-alpha.1/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
  
  <!-- O Bardo Design System: Bootstrap 6 x Dracula Theme x Tormenta/D&D -->
  <link rel="stylesheet" href="/css/bardo.css">
  <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>⚔️</text></svg>">
</head>
<body>

  <!-- Header Superior com Bootstrap 6 Flexbox & Insígnia Rúnica -->
  <header class="app-header py-2 px-3 sticky-top">
    <div class="container-fluid d-flex align-items-center justify-content-between p-0">
      <a href="/dashboard" class="brand-wrapper hstack gap-2 text-decoration-none">
        <div class="brand-shield">
          <span style="font-size: 1.25rem; filter: drop-shadow(0 0 6px var(--dracula-yellow));">⚔️</span>
        </div>
        <div>
          <div class="brand-name">O BARDO</div>
          <div class="brand-sub">✦ MESA VIRTUAL DISRUPTIVA ✦</div>
        </div>
      </a>
      <div class="hstack gap-2 align-items-center">
        <div class="sync-badge">
          <span class="sync-dot"></span>
          <span>Conectado</span>
        </div>
        <?php if (isset($user)): ?>
          <a href="/logout" class="btn btn-sm btn-touch btn-touch-dark py-1 px-3" style="font-size: 0.72rem;">Sair</a>
        <?php endif; ?>
      </div>
    </div>
  </header>
