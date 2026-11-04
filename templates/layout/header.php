<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <meta name="theme-color" content="#282a36">
  <title><?= $e($title ?? 'O Bardo • RPG de Mesa Tradicional') ?></title>
  
  <!-- Fontes Disruptivas de Alta Fantasia (Tormenta & D&D) -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Almendra:ital,wght@0,400;0,700;1,400&family=Cinzel+Decorative:wght@700;900&family=Grenze+Gotisch:wght@600;700;800;900&family=MedievalSharp&family=Uncial+Antiqua&family=JetBrains+Mono:wght@500;700;800&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="/css/bardo.css">
  <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>⚔️</text></svg>">
</head>
<body>

  <!-- Header Superior Disruptivo -->
  <header class="app-header">
    <a href="/dashboard" class="brand-wrapper">
      <div class="brand-shield">
        <span style="font-size: 1.25rem; filter: drop-shadow(0 0 6px var(--gold-bright));">⚔️</span>
      </div>
      <div>
        <div class="brand-name">O BARDO</div>
        <div class="brand-sub">✦ MESA VIRTUAL DISRUPTIVA ✦</div>
      </div>
    </a>
    <div style="display: flex; align-items: center; gap: 0.6rem;">
      <div class="sync-badge">
        <span class="sync-dot"></span>
        <span>Conectado</span>
      </div>
      <?php if (isset($user)): ?>
        <a href="/logout" class="btn-touch btn-touch-dark" style="padding: 4px 10px; font-size: 0.72rem;">Sair</a>
      <?php endif; ?>
    </div>
  </header>
