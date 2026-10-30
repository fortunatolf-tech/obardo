/**
 * O BARDO - PROTÓTIPO DE ALTA FIDELIDADE
 * Motor de Interação, Web Audio API Synthesizer & Simulador de Sincronia
 */

// Web Audio API Context inicializado sob demanda (respeitando política de Autoplay)
let audioCtx = null;
let masterGainNode = null;
let currentAmbientOsc = null;
let isAudioActive = false;

function initAudioEngine() {
  if (!audioCtx) {
    const AudioContextClass = window.AudioContext || window.webkitAudioContext;
    audioCtx = new AudioContextClass();
    masterGainNode = audioCtx.createGain();
    masterGainNode.gain.setValueAtTime(0.7, audioCtx.currentTime);
    masterGainNode.connect(audioCtx.destination);
  }
  if (audioCtx.state === 'suspended') {
    audioCtx.resume();
  }
  isAudioActive = true;
  updateSyncIndicators(true);
}

// Síntese de Efeitos Sonoros (SFX) via Web Audio API Nativa
function playSynthesizedSFX(type) {
  initAudioEngine();
  const now = audioCtx.currentTime;

  if (type === 'thunder') {
    // Ruído de baixa frequência com decay longo
    const bufferSize = audioCtx.sampleRate * 2.5;
    const buffer = audioCtx.createBuffer(1, bufferSize, audioCtx.sampleRate);
    const data = buffer.getChannelData(0);
    for (let i = 0; i < bufferSize; i++) {
      data[i] = (Math.random() * 2 - 1) * Math.exp(-i / (audioCtx.sampleRate * 0.6));
    }
    const noise = audioCtx.createBufferSource();
    noise.buffer = buffer;

    const filter = audioCtx.createBiquadFilter();
    filter.type = 'lowpass';
    filter.frequency.setValueAtTime(180, now);
    filter.frequency.exponentialRampToValueAtTime(30, now + 2.5);

    const gain = audioCtx.createGain();
    gain.gain.setValueAtTime(1.0, now);
    gain.gain.exponentialRampToValueAtTime(0.01, now + 2.5);

    noise.connect(filter);
    filter.connect(gain);
    gain.connect(masterGainNode);
    noise.start(now);

  } else if (type === 'sword') {
    // Som metálico de corte de lâmina
    const osc = audioCtx.createOscillator();
    const gain = audioCtx.createGain();
    osc.type = 'triangle';
    osc.frequency.setValueAtTime(2400, now);
    osc.frequency.exponentialRampToValueAtTime(400, now + 0.35);

    gain.gain.setValueAtTime(0.6, now);
    gain.gain.exponentialRampToValueAtTime(0.001, now + 0.35);

    osc.connect(gain);
    gain.connect(masterGainNode);
    osc.start(now);
    osc.stop(now + 0.4);

  } else if (type === 'bell' || type === 'mystery') {
    // Sino místico profundo
    [220, 440, 660, 880].forEach((freq, idx) => {
      const osc = audioCtx.createOscillator();
      const gain = audioCtx.createGain();
      osc.type = 'sine';
      osc.frequency.setValueAtTime(freq * 1.25, now);
      gain.gain.setValueAtTime(0.3 / (idx + 1), now);
      gain.gain.exponentialRampToValueAtTime(0.0001, now + 3.0);
      osc.connect(gain);
      gain.connect(masterGainNode);
      osc.start(now);
      osc.stop(now + 3.2);
    });

  } else if (type === 'roar') {
    // Rugido bestial grave
    const osc = audioCtx.createOscillator();
    const gain = audioCtx.createGain();
    osc.type = 'sawtooth';
    osc.frequency.setValueAtTime(90, now);
    osc.frequency.linearRampToValueAtTime(60, now + 1.2);

    gain.gain.setValueAtTime(0.7, now);
    gain.gain.exponentialRampToValueAtTime(0.01, now + 1.5);

    osc.connect(gain);
    gain.connect(masterGainNode);
    osc.start(now);
    osc.stop(now + 1.6);

  } else if (type === 'dice') {
    // Som de rolagem rápida de dados de resina
    for (let i = 0; i < 4; i++) {
      const t = now + i * 0.08;
      const osc = audioCtx.createOscillator();
      const gain = audioCtx.createGain();
      osc.type = 'sine';
      osc.frequency.setValueAtTime(800 + Math.random() * 400, t);
      gain.gain.setValueAtTime(0.3, t);
      gain.gain.exponentialRampToValueAtTime(0.01, t + 0.06);
      osc.connect(gain);
      gain.connect(masterGainNode);
      osc.start(t);
      osc.stop(t + 0.07);
    }
  }
}

// Síntese de Clima e Ambiência Contínua
function setAmbientTrack(trackName) {
  initAudioEngine();
  const now = audioCtx.currentTime;

  if (currentAmbientOsc) {
    try {
      currentAmbientOsc.stop(now);
    } catch(e) {}
    currentAmbientOsc = null;
  }

  // Gera acorde de fundo orquestrado/ambiente
  const osc1 = audioCtx.createOscillator();
  const osc2 = audioCtx.createOscillator();
  const filter = audioCtx.createBiquadFilter();
  const gain = audioCtx.createGain();

  if (trackName === 'combat') {
    osc1.type = 'sawtooth';
    osc2.type = 'square';
    osc1.frequency.setValueAtTime(110, now); // Lá 2
    osc2.frequency.setValueAtTime(164.81, now); // Mi 3
    filter.frequency.setValueAtTime(450, now);
    gain.gain.setValueAtTime(0.2, now);
  } else if (trackName === 'dungeon') {
    osc1.type = 'sine';
    osc2.type = 'triangle';
    osc1.frequency.setValueAtTime(65.41, now); // Dó 2
    osc2.frequency.setValueAtTime(98.0, now);  // Sol 2
    filter.frequency.setValueAtTime(250, now);
    gain.gain.setValueAtTime(0.25, now);
  } else {
    // Taverna / Viagem
    osc1.type = 'sine';
    osc2.type = 'sine';
    osc1.frequency.setValueAtTime(130.81, now); // Dó 3
    osc2.frequency.setValueAtTime(196.0, now);  // Sol 3
    filter.frequency.setValueAtTime(600, now);
    gain.gain.setValueAtTime(0.2, now);
  }

  osc1.connect(filter);
  osc2.connect(filter);
  filter.connect(gain);
  gain.connect(masterGainNode);

  osc1.start(now);
  osc2.start(now);
  currentAmbientOsc = osc1;

  // Atualiza UI
  document.querySelectorAll('.track-item').forEach(el => el.classList.remove('active'));
  const activeEl = document.querySelector(`[data-track="${trackName}"]`);
  if (activeEl) activeEl.classList.add('active');

  const nowPlayingLabel = document.getElementById('now-playing-label');
  if (nowPlayingLabel) {
    const names = {
      'tavern': 'Taverna do Javali Saltitante (Alaúdes e Murmúrio)',
      'dungeon': 'Cripta dos Ancestrais Perdidos (Vento e Gotas)',
      'combat': 'Batalha pela Glória (Tambores de Guerra)'
    };
    nowPlayingLabel.textContent = names[trackName] || trackName;
  }
}

// Simulador de Rolagem de Dados
function rollDice(formula, purpose = 'Rolagem') {
  initAudioEngine();
  playSynthesizedSFX('dice');

  // Suporte a fórmulas como 1d20+3, 3d6, 1d100
  let numDice = 1;
  let diceSides = 20;
  let modifier = 0;

  const match = formula.match(/(\d+)?d(\d+)([\+\-]\d+)?/i);
  if (match) {
    numDice = parseInt(match[1] || '1', 10);
    diceSides = parseInt(match[2], 10);
    modifier = parseInt(match[3] || '0', 10);
  }

  const rolls = [];
  let sum = 0;
  for (let i = 0; i < numDice; i++) {
    // CSPRNG
    const array = new Uint32Array(1);
    window.crypto.getRandomValues(array);
    const result = (array[0] % diceSides) + 1;
    rolls.push(result);
    sum += result;
  }
  const total = sum + modifier;

  const feed = document.getElementById('dice-feed');
  if (feed) {
    const entry = document.createElement('div');
    entry.className = 'roll-entry';
    entry.innerHTML = `
      <div>
        <div class="roll-roller">Jogador • ${purpose}</div>
        <div class="roll-detail">Dados: [${rolls.join(', ')}] ${modifier !== 0 ? (modifier > 0 ? '+ ' + modifier : '- ' + Math.abs(modifier)) : ''} (${formula})</div>
      </div>
      <div class="roll-total ${total === 20 && diceSides === 20 ? 'style="color: #10b981;"' : ''}">${total}</div>
    `;
    feed.insertBefore(entry, feed.firstChild);
  }
  return total;
}

// Disparador de Reviravolta da Trama (Plot Twist)
function triggerPlotTwist(headline, description, type = 'traicao') {
  initAudioEngine();
  playSynthesizedSFX(type === 'traicao' ? 'sword' : 'mystery');

  const overlay = document.getElementById('plot-twist-overlay');
  const headlineEl = document.getElementById('twist-headline');
  const descEl = document.getElementById('twist-desc');
  const tagEl = document.getElementById('twist-tag');

  if (overlay && headlineEl && descEl) {
    headlineEl.textContent = headline || 'A REVIRAVOLTA SE REVELA!';
    descEl.textContent = description || 'Os pilares do templo começam a tremer. Algo ancestral despertou sob seus pés.';
    if (tagEl) tagEl.textContent = type.toUpperCase();
    overlay.classList.add('active');
  }
}

function closePlotTwist() {
  const overlay = document.getElementById('plot-twist-overlay');
  if (overlay) overlay.classList.remove('active');
}

// Atualização de Pontos de Vida (HP Tracker)
function adjustHP(amount) {
  const currentEl = document.getElementById('hp-current');
  const maxEl = document.getElementById('hp-max');
  const fillEl = document.getElementById('hp-bar-fill');

  if (!currentEl || !maxEl || !fillEl) return;

  let current = parseInt(currentEl.textContent, 10);
  const max = parseInt(maxEl.textContent, 10);

  current = Math.max(0, Math.min(max, current + amount));
  currentEl.textContent = current;

  const pct = (current / max) * 100;
  fillEl.style.width = pct + '%';

  fillEl.classList.remove('warning', 'danger');
  if (pct < 25) {
    fillEl.classList.add('danger');
    if (amount < 0) playSynthesizedSFX('sword');
  } else if (pct < 50) {
    fillEl.classList.add('warning');
  }
}

function updateSyncIndicators(synced) {
  document.querySelectorAll('.sync-status-text').forEach(el => {
    el.textContent = synced ? 'Sincronizado (42ms)' : 'Desconectado';
  });
}
