/**
 * O BARDO - MOTOR JAVASCRIPT EM TEMPO REAL (CLIENTE)
 * Sincronização Acústica Web Audio API + Server-Sent Events (SSE) + Tormenta20 / D&D
 */

let bardoAudioCtx = null;
let bardoMasterGain = null;
let bardoActiveOsc = null;
let bardoCurrentCampaignId = null;
let bardoSSE = null;

function initBardoAudio() {
  if (!bardoAudioCtx) {
    const AudioContextClass = window.AudioContext || window.webkitAudioContext;
    bardoAudioCtx = new AudioContextClass();
    bardoMasterGain = bardoAudioCtx.createGain();
    bardoMasterGain.gain.setValueAtTime(0.75, bardoAudioCtx.currentTime);
    bardoMasterGain.connect(bardoAudioCtx.destination);
  }
  if (bardoAudioCtx.state === 'suspended') {
    bardoAudioCtx.resume();
  }
  return bardoAudioCtx;
}

// Síntese de Efeitos Sonoros Instantâneos
function bardoPlaySFX(type) {
  const ctx = initBardoAudio();
  const now = ctx.currentTime;

  if (type === 'sword') {
    const osc = ctx.createOscillator();
    const gain = ctx.createGain();
    osc.type = 'triangle';
    osc.frequency.setValueAtTime(2800, now);
    osc.frequency.exponentialRampToValueAtTime(320, now + 0.28);
    gain.gain.setValueAtTime(0.7, now);
    gain.gain.exponentialRampToValueAtTime(0.001, now + 0.28);
    osc.connect(gain);
    gain.connect(bardoMasterGain);
    osc.start(now);
    osc.stop(now + 0.3);
  } else if (type === 'spell' || type === 'mana') {
    [523.25, 659.25, 783.99, 1046.50].forEach((freq, idx) => {
      const osc = ctx.createOscillator();
      const gain = ctx.createGain();
      osc.type = 'sine';
      osc.frequency.setValueAtTime(freq, now + idx * 0.05);
      gain.gain.setValueAtTime(0.25, now + idx * 0.05);
      gain.gain.exponentialRampToValueAtTime(0.001, now + idx * 0.05 + 0.8);
      osc.connect(gain);
      gain.connect(bardoMasterGain);
      osc.start(now + idx * 0.05);
      osc.stop(now + idx * 0.05 + 0.85);
    });
  } else if (type === 'thunder') {
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
    gain.connect(bardoMasterGain);
    noise.start(now);
  } else if (type === 'roar') {
    const osc = ctx.createOscillator();
    const gain = ctx.createGain();
    osc.type = 'sawtooth';
    osc.frequency.setValueAtTime(85, now);
    osc.frequency.linearRampToValueAtTime(45, now + 1.2);
    gain.gain.setValueAtTime(0.8, now);
    gain.gain.exponentialRampToValueAtTime(0.01, now + 1.3);
    osc.connect(gain);
    gain.connect(bardoMasterGain);
    osc.start(now);
    osc.stop(now + 1.35);
  } else if (type === 'dice') {
    for (let i = 0; i < 3; i++) {
      const t = now + i * 0.07;
      const osc = ctx.createOscillator();
      const gain = ctx.createGain();
      osc.type = 'sine';
      osc.frequency.setValueAtTime(900 + Math.random() * 300, t);
      gain.gain.setValueAtTime(0.25, t);
      gain.gain.exponentialRampToValueAtTime(0.01, t + 0.05);
      osc.connect(gain);
      gain.connect(bardoMasterGain);
      osc.start(t);
      osc.stop(t + 0.06);
    }
  } else if (type === 'crit') {
    [440, 554.37, 659.25, 880].forEach((freq, idx) => {
      const osc = ctx.createOscillator();
      const gain = ctx.createGain();
      osc.type = 'triangle';
      osc.frequency.setValueAtTime(freq, now + idx * 0.06);
      gain.gain.setValueAtTime(0.35, now + idx * 0.06);
      gain.gain.exponentialRampToValueAtTime(0.001, now + idx * 0.06 + 0.6);
      osc.connect(gain);
      gain.connect(bardoMasterGain);
      osc.start(now + idx * 0.06);
      osc.stop(now + idx * 0.06 + 0.65);
    });
  }
}

// Síntese de Trilha Ambiente
function bardoPlayAmbient(category) {
  const ctx = initBardoAudio();
  const now = ctx.currentTime;

  if (bardoActiveOsc) {
    try { bardoActiveOsc.stop(now); } catch(e) {}
    bardoActiveOsc = null;
  }

  const osc = ctx.createOscillator();
  const filter = ctx.createBiquadFilter();
  const gain = ctx.createGain();

  if (category === 'combat') {
    osc.type = 'sawtooth';
    osc.frequency.setValueAtTime(110, now);
    filter.frequency.setValueAtTime(380, now);
    gain.gain.setValueAtTime(0.18, now);
  } else if (category === 'dungeon') {
    osc.type = 'sine';
    osc.frequency.setValueAtTime(65.41, now);
    filter.frequency.setValueAtTime(220, now);
    gain.gain.setValueAtTime(0.22, now);
  } else {
    // Taverna / Viagem
    osc.type = 'sine';
    osc.frequency.setValueAtTime(130.81, now);
    filter.frequency.setValueAtTime(500, now);
    gain.gain.setValueAtTime(0.15, now);
  }

  osc.connect(filter);
  filter.connect(gain);
  gain.connect(bardoMasterGain);

  osc.start(now);
  bardoActiveOsc = osc;
}

// Conector Server-Sent Events (SSE) em Tempo Real
function connectBardoSSE(campaignId) {
  bardoCurrentCampaignId = campaignId;
  if (bardoSSE) {
    bardoSSE.close();
  }

  const url = `/api/v1/campaigns/${campaignId}/events/stream`;
  bardoSSE = new EventSource(url);

  bardoSSE.addEventListener('connected', (e) => {
    updateSyncBadge(true);
  });

  // Comando de sincronia de áudio recebido do Mestre
  bardoSSE.addEventListener('audio_sync_command', (e) => {
    const data = JSON.parse(e.data);
    bardoToast(`🎵 Mestre comandou: ${data.action}`);
    if (data.action === 'PLAY' || data.action === 'PLAY_TRACK') {
      const cat = data.track_category || 'tavern';
      bardoPlayAmbient(cat);
      const label = document.getElementById('current-track-label');
      if (label && data.track_title) label.textContent = data.track_title;
    } else if (data.action === 'PAUSE' || data.action === 'STOP') {
      if (bardoActiveOsc) {
        try { bardoActiveOsc.stop(); } catch(err) {}
        bardoActiveOsc = null;
      }
    }
  });

  // Efeito sonoro do soundboard recebido
  bardoSSE.addEventListener('audio_sfx_command', (e) => {
    const data = JSON.parse(e.data);
    bardoPlaySFX(data.sfx_name);
  });

  // Reviravolta da trama ativada ao vivo pelo Mestre!
  bardoSSE.addEventListener('plot_twist_broadcast', (e) => {
    const data = JSON.parse(e.data);
    triggerDramaticTwist(data.headline, data.description, data.twist_type);
  });

  // Rolagem de dados na sessão
  bardoSSE.addEventListener('dice_roll_broadcast', (e) => {
    const data = JSON.parse(e.data);
    bardoPlaySFX('dice');
    appendRollToFeed(
      data.roller_name + ' • ' + data.label,
      data.formula,
      data.dice_results || [],
      data.modifier || 0,
      data.total,
      data.is_critical,
      data.is_fumble
    );
  });

  // Atualização de PV/PM de personagem
  bardoSSE.addEventListener('character_status_updated', (e) => {
    const data = JSON.parse(e.data);
    updateCharacterVitalsInDOM(data);
  });

  bardoSSE.onerror = () => {
    updateSyncBadge(false);
  };
}

// Chamadas de API AJAX
async function bardoModifyHP(characterId, delta) {
  bardoPlaySFX(delta < 0 ? 'sword' : 'spell');
  const curEl = document.getElementById('hp-current-val');
  const maxEl = document.getElementById('hp-max-val');
  if (!curEl || !maxEl) return;

  let cur = parseInt(curEl.textContent, 10);
  const max = parseInt(maxEl.textContent, 10);
  cur = Math.max(0, Math.min(max, cur + delta));

  curEl.textContent = cur;
  const bar = document.getElementById('hp-fill-bar');
  if (bar) bar.style.width = Math.round((cur / max) * 100) + '%';

  try {
    await fetch(`/api/v1/characters/${characterId}/status`, {
      method: 'PATCH',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ hp_current: cur })
    });
    bardoToast(delta < 0 ? `⚔️ Sofreu ${Math.abs(delta)} de dano!` : `💚 Curou ${delta} PV!`);
  } catch(e) {
    console.error(e);
  }
}

async function bardoModifyPM(characterId, delta) {
  bardoPlaySFX('mana');
  const curEl = document.getElementById('pm-current-val');
  const maxEl = document.getElementById('pm-max-val');
  if (!curEl || !maxEl) return;

  let cur = parseInt(curEl.textContent, 10);
  const max = parseInt(maxEl.textContent, 10);
  cur = Math.max(0, Math.min(max, cur + delta));

  curEl.textContent = cur;
  const bar = document.getElementById('pm-fill-bar');
  if (bar) bar.style.width = Math.round((cur / max) * 100) + '%';

  try {
    await fetch(`/api/v1/characters/${characterId}/status`, {
      method: 'PATCH',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ mp_current: cur })
    });
    bardoToast(delta < 0 ? `🔮 Gastou ${Math.abs(delta)} PM!` : `✨ Recuperou ${delta} PM!`);
  } catch(e) {
    console.error(e);
  }
}

async function bardoRoll(campaignId, characterId, formula, label) {
  bardoPlaySFX('dice');
  try {
    const res = await fetch(`/api/v1/campaigns/${campaignId}/dice/roll`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        formula: formula,
        label: label,
        character_id: characterId
      })
    });
    const json = await res.json();
    if (json.status === 'success') {
      const roll = json.data.roll;
      bardoToast(`🎲 ${label}: ${roll.total_result || roll.total}`);
      if (roll.is_critical) bardoPlaySFX('crit');
    }
  } catch(e) {
    console.error(e);
  }
  closeDiceSheet();
}

async function bardoGMSFX(campaignId, sfxName) {
  bardoPlaySFX(sfxName);
  try {
    await fetch(`/api/v1/campaigns/${campaignId}/audio/sync`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        action: 'TRIGGER_SFX',
        sfx_name: sfxName
      })
    });
    bardoToast(`⚡ Efeito '${sfxName}' transmitido para a sessão!`);
  } catch(e) {
    console.error(e);
  }
}

async function bardoGMSync(campaignId, action, trackId, layer = 'music') {
  try {
    await fetch(`/api/v1/campaigns/${campaignId}/audio/sync`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        action: action,
        track_id: trackId,
        layer: layer
      })
    });
    bardoToast(`🎵 Comando de som transmitido!`);
  } catch(e) {
    console.error(e);
  }
}

async function bardoGMPlotTwist(campaignId, headline, description, type = 'traicao') {
  try {
    await fetch(`/api/v1/campaigns/${campaignId}/plots/trigger`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        headline: headline,
        description: description,
        twist_type: type,
        audio_sfx: type === 'combate' ? 'roar' : 'thunder'
      })
    });
    bardoToast(`🔥 Reviravolta da trama disparada ao vivo!`);
  } catch(e) {
    console.error(e);
  }
}

function updateCharacterVitalsInDOM(data) {
  const hpEl = document.getElementById(`party-hp-${data.character_id}`);
  if (hpEl) hpEl.textContent = `${data.hp_current}/${data.hp_max} PV`;

  const mpEl = document.getElementById(`party-mp-${data.character_id}`);
  if (mpEl) mpEl.textContent = `${data.mp_current}/${data.mp_max} PM`;
}

function updateSyncBadge(online) {
  document.querySelectorAll('.sync-badge').forEach(el => {
    el.innerHTML = online 
      ? '<span class="sync-dot"></span><span>Sincronizado (32ms)</span>'
      : '<span class="sync-dot" style="background:#e11d48;box-shadow:none;"></span><span>Desconectado</span>';
  });
}

function bardoToast(msg) {
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
  toast.textContent = msg;
  toast.style.opacity = '1';
  clearTimeout(toast._timeout);
  toast._timeout = setTimeout(() => {
    toast.style.opacity = '0';
  }, 2400);
}

function showToast(msg) {
  bardoToast(msg);
}

function openDiceSheet() {
  initBardoAudio();
  const sheet = document.getElementById('dice-bottom-sheet');
  if (sheet) sheet.classList.add('open');
}

function closeDiceSheet() {
  const sheet = document.getElementById('dice-bottom-sheet');
  if (sheet) sheet.classList.remove('open');
}

function openVitalsModal() {
  initBardoAudio();
  const modal = document.getElementById('vitals-modal-sheet');
  if (modal) modal.classList.add('open');
}

function closeVitalsModal() {
  const modal = document.getElementById('vitals-modal-sheet');
  if (modal) modal.classList.remove('open');
}

function triggerDramaticTwist(headline, desc, type = 'traicao') {
  initBardoAudio();
  bardoPlaySFX(type === 'combate' ? 'roar' : 'thunder');

  const screen = document.getElementById('dramatic-twist-screen');
  const headlineEl = document.getElementById('dramatic-headline');
  const descEl = document.getElementById('dramatic-desc');

  if (screen && headlineEl && descEl) {
    headlineEl.textContent = headline || 'REVIRAVOLTA NA TRAMA!';
    descEl.textContent = desc || 'O destino da campanha mudou repentinamente!';
    screen.classList.add('active');
  }
}

function closeDramaticTwist() {
  const screen = document.getElementById('dramatic-twist-screen');
  if (screen) screen.classList.remove('active');
}

function appendRollToFeed(label, formula, rolls, modifier, total, isCrit, isFumble) {
  const feed = document.getElementById('session-roll-feed');
  if (!feed) return;

  const entry = document.createElement('div');
  entry.className = 'rpg-card';
  entry.style.padding = '0.65rem 0.85rem';
  entry.style.marginBottom = '0.5rem';
  entry.style.borderLeft = isCrit ? '3px solid #10b981' : (isFumble ? '3px solid #e11d48' : '3px solid #d4af37');

  const rollsArr = Array.isArray(rolls) ? rolls : [rolls];
  const modStr = modifier !== 0 ? (modifier > 0 ? '+ ' + modifier : '- ' + Math.abs(modifier)) : '';

  entry.innerHTML = `
    <div style="display: flex; justify-content: space-between; align-items: center;">
      <div>
        <div style="font-weight: 700; font-size: 0.85rem; color: var(--gold-imperial);">
          ${label} ${isCrit ? '<span style="color:#10b981;">(CRÍTICO!)</span>' : ''} ${isFumble ? '<span style="color:#e11d48;">(FALHA CRÍTICA!)</span>' : ''}
        </div>
        <div style="font-size: 0.72rem; color: var(--text-muted);">
          Dados: [${rollsArr.join(', ')}] ${modStr} (${formula})
        </div>
      </div>
      <div style="font-family: var(--font-code); font-size: 1.3rem; font-weight: 900; color: ${isCrit ? '#10b981' : (isFumble ? '#e11d48' : '#fff')}; background: var(--bg-surface); padding: 2px 10px; border-radius: 6px; border: 1px solid var(--border-subtle);">
        ${total}
      </div>
    </div>
  `;
  if (isCrit) {
    bardoPlaySFX('crit');
    triggerRunicFlash('gold');
  } else if (isFumble) {
    bardoPlaySFX('thunder');
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
