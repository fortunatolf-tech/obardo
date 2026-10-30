<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <title><?= $e($title ?? 'O Bardo • RPG de Mesa Tradicional') ?></title>
  <link rel="stylesheet" href="/css/bardo.css">
  <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>⚔️</text></svg>">
</head>
<body>

  <!-- Header Superior Mobile -->
  <header class="app-header">
    <a href="/dashboard" class="brand-wrapper">
      <div class="brand-shield">⚔️</div>
      <div>
        <div class="brand-name">O BARDO</div>
        <div class="brand-sub">Tormenta20 & D&D</div>
      </div>
    </a>
    <div style="display: flex; align-items: center; gap: 0.6rem;">
      <div class="sync-badge">
        <span class="sync-dot"></span>
        <span>Conectado</span>
      </div>
      <?php if (isset($user)): ?>
        <a href="/logout" class="btn-touch btn-touch-dark" style="padding: 3px 8px; font-size: 0.7rem;">Sair</a>
      <?php endif; ?>
    </div>
  </header>
