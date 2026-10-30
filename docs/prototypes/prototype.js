/**
 * O BARDO - MOTOR INTERATIVO TORMENTA20 & D&D (MOBILE FIRST)
 * Web Audio API Synthesizer, Gestão de PV/PM/Defesa, Rolador D20 & Sincronia
 */

// Web Audio Context com ativação suave em mobile (respeitando autoplay policy)
let audioCtx = null;
let masterGainNode = null;
let currentAmbientOsc = null;
let activeAmbientTrack = 'tavern';

function getAudioContext() {
  if (!audioCtx) {
    const AudioContextClass = window.AudioContext || window.webkitAudioContext;
    audioCtx = new AudioContextClass();
    masterGainNode = audioCtx.createGain();
    masterGainNode.gain.setValueAtTime(0.75, audioCtx.currentTime);
    masterGainNode.connect(audioCtx.destination);
  }
  if (audioCtx.state === 'suspended') {
    audioCtx.resume();
  }
  return audioCtx;
}

// Síntese Acústica em Tempo Real para Efeitos Sonoros T20 & D&D
function playAudioSFX(type) {
  const ctx = getAudioContext();
  const now = ctx.currentTime;

  if (type === 'sword') {
    // Corte metálico de lâmina / Ataque marcial
    const osc = ctx.createOscillator();
    const gain = ctx.createGain();
    osc.type = 'triangle';
    osc.frequency.setValueAtTime(2800, now);
    osc.frequency.exponentialRampToValueAtTime(320, now + 0.28);

    gain.gain.setValueAtTime(0.7, now);
    gain.gain.exponentialRampToValueAtTime(0.001, now + 0.28);

    osc.connect(gain);
    gain.connect(masterGainNode);
    osc.start(now);
    osc.stop(now + 0.3);

  } else if (type === 'spell' || type === 'mana') {
    // Magia arcana / Brilho de PM
    [523.25, 659.25, 783.99, 1046.50].forEach((freq, idx) => {
      const osc = ctx.createOscillator();
      const gain = ctx.createGain();
      osc.type = 'sine';
      osc.frequency.setValueAtTime(freq, now + idx * 0.05);
      gain.gain.setValueAtTime(0.25, now + idx * 0.05);
      gain.gain.exponentialRampToValueAtTime(0.001, now + idx * 0.05 + 0.8);
      osc.connect(gain);
      gain.connect(masterGainNode);
      osc.start(now + idx * 0.05);
      osc.stop(now + idx * 0.05 + 0.85);
    });

  } else if (type === 'thunder') {
    // Trovão divino / Magia de dano de área
    const bufferSize = ctx.sampleRate * 2.2;
    const buffer = ctx.createBuffer(1, bufferSize, ctx.sampleRate);
    const data = buffer.getChannelData(0);
    for (let i = 0; i < bufferSize; i++) {
      data[i] = (Math.random() * 2 - 1) * Math.exp(-i / (ctx.sampleRate * 0.55));
    }
    const noise = ctx.createBufferSource();
    noise.buffer = buffer;

    const filter = ctx.createBiquadFilter();
    filter.type = 'lowpass';
    filter.frequency.setValueAtTime(160, now);
    filter.frequency.exponentialRampToValueAtTime(28, now + 2.2);

    const gain = ctx.createGain();
    gain.gain.setValueAtTime(1.0, now);
    gain.gain.exponentialRampToValueAtTime(0.01, now + 2.2);

    noise.connect(filter);
    filter.connect(gain);
    gain.connect(masterGainNode);
    noise.start(now);

  } else if (type === 'roar') {
    // Rugido monstruoso (Lefeu / Dragão de Tormenta)
    const osc = ctx.createOscillator();
    const gain = ctx.createGain();
    osc.type = 'sawtooth';
    osc.frequency.setValueAtTime(85, now);
    osc.frequency.linearRampToValueAtTime(45, now + 1.2);

    gain.gain.setValueAtTime(0.8, now);
    gain.gain.exponentialRampToValueAtTime(0.01, now + 1.3);

    osc.connect(gain);
    gain.connect(masterGainNode);
    osc.start(now);
    osc.stop(now + 1.35);

  } else if (type === 'dice') {
    // Dados de resina quicando
    for (let i = 0; i < 3; i++) {
      const t = now + i * 0.07;
      const osc = ctx.createOscillator();
      const gain = ctx.createGain();
      osc.type = 'sine';
      osc.frequency.setValueAtTime(900 + Math.random() * 300, t);
      gain.gain.setValueAtTime(0.25, t);
      gain.gain.exponentialRampToValueAtTime(0.01, t + 0.05);
      osc.connect(gain);
      gain.connect(masterGainNode);
      osc.start(t);
      osc.stop(t + 0.06);
    }

  } else if (type === 'crit') {
    // Sucesso Crítico 20 Natural!
    [440, 554.37, 659.25, 880].forEach((freq, idx) => {
      const osc = ctx.createOscillator();
      const gain = ctx.createGain();
      osc.type = 'triangle';
      osc.frequency.setValueAtTime(freq, now + idx * 0.06);
      gain.gain.setValueAtTime(0.35, now + idx * 0.06);
      gain.gain.exponentialRampToValueAtTime(0.001, now + idx * 0.06 + 0.6);
      osc.connect(gain);
      gain.connect(masterGainNode);
      osc.start(now + idx * 0.06);
      osc.stop(now + idx * 0.06 + 0.65);
    });
  }
}

// Troca de Trilha Sonora Sincronizada (Taverna, Combate, Masmorra)
function switchAmbientTrack(trackName) {
  const ctx = getAudioContext();
  const now = ctx.currentTime;
  activeAmbientTrack = trackName;

  if (currentAmbientOsc) {
    try { currentAmbientOsc.stop(now); } catch(e) {}
    currentAmbientOsc = null;
  }

  const osc = ctx.createOscillator();
  const filter = ctx.createBiquadFilter();
  const gain = ctx.createGain();

  if (trackName === 'combat') {
    osc.type = 'sawtooth';
    osc.frequency.setValueAtTime(110, now); // A2
    filter.frequency.setValueAtTime(380, now);
    gain.gain.setValueAtTime(0.18, now);
  } else if (trackName === 'dungeon') {
    osc.type = 'sine';
    osc.frequency.setValueAtTime(65.41, now); // C2
    filter.frequency.setValueAtTime(220, now);
    gain.gain.setValueAtTime(0.22, now);
  } else {
    // Taverna / Viagem
    osc.type = 'sine';
    osc.frequency.setValueAtTime(130.81, now); // C3
    filter.frequency.setValueAtTime(500, now);
    gain.gain.setValueAtTime(0.15, now);
  }

  osc.connect(filter);
  filter.connect(gain);
  gain.connect(masterGainNode);

  osc.start(now);
  currentAmbientOsc = osc;

  // Atualização visual dos seletores de trilha
  document.querySelectorAll('.ambient-track-btn').forEach(btn => {
    btn.classList.toggle('active', btn.getAttribute('data-track') === trackName);
  });

  const label = document.getElementById('current-track-label');
  if (label) {
    const titles = {
      'tavern': 'Taverna do Javali Saltitante • Alaúdes & Lareira',
      'dungeon': 'Cripta dos Antigos • Ecos & Tensão',
      'combat': 'Batalha pela Vida • Tambores & Aço'
    };
    label.textContent = titles[trackName] || trackName;
  }
}

// ==========================================================================
// GESTÃO DE PONTOS DE VIDA (PV) & PONTOS DE MANA (PM) - TORMENTA20 & D&D
// ==========================================================================

function modifyHP(delta) {
  playAudioSFX(delta < 0 ? 'sword' : 'spell');
  const curEl = document.getElementById('hp-current-val');
  const maxEl = document.getElementById('hp-max-val');
  const barEl = document.getElementById('hp-fill-bar');

  if (!curEl || !maxEl) return;

  let current = parseInt(curEl.textContent, 10);
  const max = parseInt(maxEl.textContent, 10);

  current = Math.max(0, Math.min(max, current + delta));
  curEl.textContent = current;

  const pct = Math.round((current / max) * 100);
  if (barEl) {
    barEl.style.width = pct + '%';
  }

  // Notificação visual de estado
  if (current === 0) {
    showToast('⚠️ ATENÇÃO: Personagem Inconsciente / Sangrando!');
  } else if (delta < 0) {
    showToast(`⚔️ Sofreu ${Math.abs(delta)} de dano! (${current}/${max} PV)`);
  } else {
    showToast(`💚 Recuperou ${delta} de vida! (${current}/${max} PV)`);
  }
}

function modifyPM(delta) {
  playAudioSFX('mana');
  const curEl = document.getElementById('pm-current-val');
  const maxEl = document.getElementById('pm-max-val');
  const barEl = document.getElementById('pm-fill-bar');

  if (!curEl || !maxEl) return;

  let current = parseInt(curEl.textContent, 10);
  const max = parseInt(maxEl.textContent, 10);

  current = Math.max(0, Math.min(max, current + delta));
  curEl.textContent = current;

  const pct = Math.round((current / max) * 100);
  if (barEl) {
    barEl.style.width = pct + '%';
  }

  if (current === 0 && delta < 0) {
    showToast('🔮 MANA ESGOTADA! Não é possível sustentar magias.');
  } else if (delta < 0) {
    showToast(`🔮 Gastou ${Math.abs(delta)} PM (${current}/${max} PM restantes)`);
  } else {
    showToast(`✨ Recuperou ${delta} PM (${current}/${max} PM)`);
  }
}

// ==========================================================================
// ROLADOR DE DADOS D20 / T20 COM DETECÇÃO DE CRÍTICO & FEED
// ==========================================================================

function executeDiceRoll(formula, label = 'Rolagem') {
  playAudioSFX('dice');

  let numDice = 1;
  let sides = 20;
  let modifier = 0;

  const match = formula.match(/(\d+)?d(\d+)([\+\-]\d+)?/i);
  if (match) {
    numDice = parseInt(match[1] || '1', 10);
    sides = parseInt(match[2], 10);
    modifier = parseInt(match[3] || '0', 10);
  }

  const rolls = [];
  let sum = 0;
  for (let i = 0; i < numDice; i++) {
    const array = new Uint32Array(1);
    window.crypto.getRandomValues(array);
    const val = (array[0] % sides) + 1;
    rolls.push(val);
    sum += val;
  }
  const total = sum + modifier;
  const isNatural20 = (sides === 20 && numDice === 1 && rolls[0] === 20);
  const isNatural1 = (sides === 20 && numDice === 1 && rolls[0] === 1);

  if (isNatural20) {
    playAudioSFX('crit');
  }

  // Adiciona ao feed de rolagens
  appendRollToFeed(label, formula, rolls, modifier, total, isNatural20, isNatural1);

  // Exibe toast com resultado instantâneo no mobile
  const critBadge = isNatural20 ? ' 🌟 20 NATURAL! CRÍTICO!' : (isNatural1 ? ' 💀 1 NATURAL! DESASTRE!' : '');
  showToast(`🎲 ${label}: ${total} (Dados: [${rolls.join(',')}]) ${critBadge}`);

  // Fecha gaveta de dados se estiver aberta
  closeDiceSheet();

  return total;
}

function appendRollToFeed(label, formula, rolls, modifier, total, isCrit, isFumble) {
  const feed = document.getElementById('session-roll-feed');
  if (!feed) return;

  const entry = document.createElement('div');
  entry.className = 'rpg-card';
  entry.style.padding = '0.65rem 0.85rem';
  entry.style.marginBottom = '0.5rem';
  entry.style.borderLeft = isCrit ? '3px solid #10b981' : (isFumble ? '3px solid #e11d48' : '3px solid #d4af37');

  entry.innerHTML = `
    <div style="display: flex; justify-content: space-between; align-items: center;">
      <div>
        <div style="font-weight: 700; font-size: 0.85rem; color: var(--gold-imperial);">
          ${label} ${isCrit ? '<span style="color:#10b981;">(CRÍTICO!)</span>' : ''}
        </div>
        <div style="font-size: 0.72rem; color: var(--text-muted);">
          Dados: [${rolls.join(', ')}] ${modifier !== 0 ? (modifier > 0 ? '+ ' + modifier : '- ' + Math.abs(modifier)) : ''} (${formula})
        </div>
      </div>
      <div style="font-family: var(--font-code); font-size: 1.3rem; font-weight: 900; color: ${isCrit ? '#10b981' : (isFumble ? '#e11d48' : '#fff')}; background: var(--bg-surface); padding: 2px 10px; border-radius: 6px; border: 1px solid var(--border-subtle);">
        ${total}
      </div>
    </div>
  `;
  feed.insertBefore(entry, feed.firstChild);
}

// ==========================================================================
// CONTROLE DE GAVETAS (BOTTOM SHEETS) & MODAIS MOBILE
// ==========================================================================

function openDiceSheet() {
  getAudioContext();
  const sheet = document.getElementById('dice-bottom-sheet');
  if (sheet) sheet.classList.add('open');
}

function closeDiceSheet() {
  const sheet = document.getElementById('dice-bottom-sheet');
  if (sheet) sheet.classList.remove('open');
}

function openVitalsModal() {
  getAudioContext();
  const modal = document.getElementById('vitals-modal-sheet');
  if (modal) modal.classList.add('open');
}

function closeVitalsModal() {
  const modal = document.getElementById('vitals-modal-sheet');
  if (modal) modal.classList.remove('open');
}

// Disparador Dramático de Reviravolta da Trama (Plot Twist)
function triggerDramaticTwist(headline, desc, type = 'traicao') {
  getAudioContext();
  playAudioSFX(type === 'combate' ? 'roar' : 'thunder');

  const screen = document.getElementById('dramatic-twist-screen');
  const headlineEl = document.getElementById('dramatic-headline');
  const descEl = document.getElementById('dramatic-desc');

  if (screen && headlineEl && descEl) {
    headlineEl.textContent = headline || 'REVIRAVOLTA NA TRAMA!';
    descEl.textContent = desc || 'As portas de ferro da masmorra se fecham abruptamente. Vocês ouvem o som de riso vil ecoando pelas paredes.';
    screen.classList.add('active');
  }
}

function closeDramaticTwist() {
  const screen = document.getElementById('dramatic-twist-screen');
  if (screen) screen.classList.remove('active');
}

// Toast Notifier para Mobile UX
function showToast(message) {
  let toast = document.getElementById('bardo-mobile-toast');
  if (!toast) {
    toast = document.createElement('div');
    toast.id = 'bardo-mobile-toast';
    toast.style.cssText = `
      position: fixed;
      top: 60px;
      left: 50%;
      transform: translateX(-50%);
      background: rgba(19, 22, 34, 0.95);
      border: 1px solid var(--border-gold);
      color: #fff;
      font-size: 0.8rem;
      font-weight: 700;
      padding: 0.5rem 1rem;
      border-radius: 99px;
      box-shadow: 0 4px 16px rgba(0,0,0,0.6);
      z-index: 150;
      pointer-events: none;
      transition: opacity 0.3s;
      white-space: nowrap;
      backdrop-filter: blur(8px);
    `;
    document.body.appendChild(toast);
  }
  toast.textContent = message;
  toast.style.opacity = '1';
  clearTimeout(toast._timeout);
  toast._timeout = setTimeout(() => {
    toast.style.opacity = '0';
  }, 2400);
}
