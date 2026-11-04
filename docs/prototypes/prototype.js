/**
 * O BARDO - MOTOR INTERATIVO TORMENTA20 & D&D (MOBILE FIRST)
 * Sons Reais CC0 / Domínio Público, Gestão de PV/PM/Defesa, Rolador D20 & Sincronia
 */

// Mapeamento de Efeitos Sonoros Reais (CC0 / Domínio Público / Freesound / Kenney)
const BARDO_SFX_MAP = {
  // Combate & Armas
  'espada_golpe': '/audio/sfx/espada_golpe.ogg',
  'sword': '/audio/sfx/espada_golpe.ogg',
  'espada_choque': '/audio/sfx/espada_choque.ogg',
  'escudo_bloqueio': '/audio/sfx/escudo_bloqueio.wav',
  'flecha_disparo': '/audio/sfx/flecha_disparo.wav',
  'sacar_adaga': '/audio/sfx/sacar_adaga.ogg',
  'soco_impacto': '/audio/sfx/soco_impacto.ogg',
  'corte_lamina': '/audio/sfx/corte_lamina.ogg',

  // Magia & Feitiços
  'bola_de_fogo': '/audio/sfx/bola_de_fogo.ogg',
  'raio_trovao': '/audio/sfx/raio_trovao.ogg',
  'spell': '/audio/sfx/raio_trovao.ogg',
  'cura_divina': '/audio/sfx/cura_divina.ogg',
  'mana': '/audio/sfx/cura_divina.ogg',
  'trovao_impacto': '/audio/sfx/trovao_impacto.wav',
  'thunder': '/audio/sfx/trovao_impacto.wav',
  'pocao_beber': '/audio/sfx/pocao_beber.wav',

  // Monstros & Criaturas
  'dragao_rugido': '/audio/sfx/dragao_rugido.ogg',
  'roar': '/audio/sfx/dragao_rugido.ogg',
  'lobo_uivo': '/audio/sfx/lobo_uivo.ogg',
  'goblin_grito': '/audio/sfx/goblin_grito.ogg',
  'risada_maligna': '/audio/sfx/risada_maligna.ogg',
  'coracao_pulso': '/audio/sfx/coracao_pulso.ogg',

  // Taverna & Social
  'brinde_tacas': '/audio/sfx/brinde_tacas.wav',
  'cheers': '/audio/sfx/brinde_tacas.wav',
  'moedas_ouro': '/audio/sfx/moedas_ouro.ogg',
  'coins': '/audio/sfx/moedas_ouro.ogg',
  'dados_mesa': '/audio/sfx/dados_mesa.ogg',
  'dice': '/audio/sfx/dados_mesa.ogg',
  'crit': '/audio/sfx/brinde_tacas.wav',
  'sino_alarme': '/audio/sfx/sino_alarme.wav',

  // Masmorra & Exploração
  'bau_tesouro': '/audio/sfx/bau_tesouro.ogg',
  'porta_madeira': '/audio/sfx/porta_madeira.ogg',
  'porta_fechando': '/audio/sfx/porta_fechando.ogg',
  'porta_ferro': '/audio/sfx/porta_ferro.wav',
  'rangido_madeira': '/audio/sfx/rangido_madeira.ogg',
  'passos_pedra': '/audio/sfx/passos_pedra.ogg'
};

const BARDO_AMBIENT_MAP = {
  'tavern': '/audio/ambient/taverna.ogg',
  'taverna': '/audio/ambient/taverna.ogg',
  'lute': '/audio/ambient/tavern_lute.wav',
  'combat': '/audio/ambient/combat_drums.wav',
  'dungeon': '/audio/ambient/caverna.ogg',
  'horror': '/audio/ambient/dungeon_drone.wav',
  'campfire': '/audio/ambient/fogueira.ogg',
  'forest': '/audio/ambient/floresta_noite.ogg',
  'wind': '/audio/ambient/vento.ogg',
  'rain': '/audio/ambient/chuva.ogg',
  'storm': '/audio/ambient/tempestade.ogg',
  'city': '/audio/ambient/cidade_medieval.ogg',
  'sea': '/audio/ambient/mar.ogg'
};

// Web Audio Context & Controles de Volume
let audioCtx = null;
let masterGainNode = null;
let currentAmbientAudio = null;
let activeAmbientTrack = 'tavern';
let masterVolume = 0.75;

function getAudioContext() {
  if (!audioCtx) {
    const AudioContextClass = window.AudioContext || window.webkitAudioContext;
    if (AudioContextClass) {
      audioCtx = new AudioContextClass();
      masterGainNode = audioCtx.createGain();
      masterGainNode.gain.setValueAtTime(masterVolume, audioCtx.currentTime);
      masterGainNode.connect(audioCtx.destination);
    }
  }
  if (audioCtx && audioCtx.state === 'suspended') {
    audioCtx.resume().catch(() => {});
  }
  return audioCtx;
}

function setMasterVolume(val) {
  masterVolume = Math.max(0, Math.min(1, parseFloat(val)));
  if (masterGainNode && audioCtx) {
    try {
      masterGainNode.gain.setValueAtTime(masterVolume, audioCtx.currentTime);
    } catch (e) {}
  }
  if (currentAmbientAudio) {
    currentAmbientAudio.volume = masterVolume;
  }
}

// Reprodução de Efeitos Sonoros Reais (SFX)
function playAudioSFX(type) {
  getAudioContext();
  const url = BARDO_SFX_MAP[type] || BARDO_SFX_MAP[type.toLowerCase()] || '/audio/sfx/dados_mesa.ogg';
  try {
    const audio = new Audio(url);
    audio.volume = masterVolume;
    const p = audio.play();
    if (p && p.catch) {
      p.catch(err => {
        console.warn(`[O Bardo] Autoplay bloqueado para SFX "${type}":`, err.message);
      });
    }
  } catch (err) {
    console.warn(`[O Bardo] Erro ao carregar SFX "${type}":`, err);
  }
}

// Troca de Trilha Sonora Real Sincronizada (Taverna, Combate, Masmorra)
function switchAmbientTrack(trackName) {
  getAudioContext();
  activeAmbientTrack = trackName;

  if (currentAmbientAudio) {
    try {
      currentAmbientAudio.pause();
      currentAmbientAudio.currentTime = 0;
    } catch(e) {}
    currentAmbientAudio = null;
  }

  const url = BARDO_AMBIENT_MAP[trackName] || `/audio/ambient/${trackName}.ogg`;
  try {
    currentAmbientAudio = new Audio(url);
    currentAmbientAudio.loop = true;
    currentAmbientAudio.volume = masterVolume;
    const p = currentAmbientAudio.play();
    if (p && p.catch) {
      p.catch(err => {
        console.warn(`[O Bardo] Autoplay bloqueado para Trilha "${trackName}":`, err.message);
      });
    }
  } catch (err) {
    console.warn(`[O Bardo] Erro ao carregar trilha "${trackName}":`, err);
  }

  // Atualização visual dos seletores de trilha
  document.querySelectorAll('.ambient-track-btn').forEach(btn => {
    btn.classList.toggle('active', btn.getAttribute('data-track') === trackName);
  });

  const label = document.getElementById('current-track-label');
  if (label) {
    const titles = {
      'tavern': 'Taverna do Javali Saltitante • Alaúdes & Lareira',
      'dungeon': 'Cripta dos Antigos • Ecos & Tensão',
      'combat': 'Batalha pela Vida • Tambores & Aço',
      'lute': 'Alaúdes de Arton • Melodias de Taverna',
      'city': 'Feira Livre da Cidade Medieval',
      'campfire': 'Acampamento & Fogueira Crepitante',
      'forest': 'Floresta Sombria à Noite',
      'wind': 'Vento Uivante nas Montanhas',
      'rain': 'Chuva na Estrada & Jornada',
      'storm': 'Tempestade Furiosa & Trovões',
      'sea': 'Ondas do Mar & Porto dos Aventureiros'
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
  if (isCrit) {
    playAudioSFX('crit');
    triggerRunicFlash('gold');
  } else if (isFumble) {
    playAudioSFX('thunder');
    triggerRunicFlash('ruby');
  }

  feed.insertBefore(entry, feed.firstChild);
}

function triggerRunicFlash(type) {
  const flash = document.createElement('div');
  flash.style.cssText = `
    position: fixed;
    inset: 0;
    pointer-events: none;
    z-index: 140;
    transition: opacity 0.5s ease;
    box-shadow: inset 0 0 70px ${type === 'gold' ? 'rgba(251, 191, 36, 0.75)' : 'rgba(225, 29, 72, 0.85)'};
    opacity: 1;
  `;
  document.body.appendChild(flash);
  if (type === 'ruby') {
    document.body.style.transform = 'translate(3px, 2px)';
    setTimeout(() => { document.body.style.transform = 'translate(-3px, -2px)'; }, 50);
    setTimeout(() => { document.body.style.transform = ''; }, 100);
  }
  setTimeout(() => {
    flash.style.opacity = '0';
    setTimeout(() => flash.remove(), 500);
  }, 250);
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
